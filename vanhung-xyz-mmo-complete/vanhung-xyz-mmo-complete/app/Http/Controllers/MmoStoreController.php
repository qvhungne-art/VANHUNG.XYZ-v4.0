<?php

namespace App\Http\Controllers;

use App\Models\MmoCategory;
use App\Models\MmoProduct;
use App\Models\MmoProductItem;
use App\Models\MmoPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MmoStoreController extends Controller
{
    public function index(Request $request)
    {
        $categories = MmoCategory::where('is_active', true)->get();
        $products = MmoProduct::with('category')
            ->withCount(['availableItems as stock_count'])
            ->where('status', '!=', 'locked')
            ->when($request->category, fn ($q, $c) => $q->whereHas('category', fn ($s) => $s->where('slug', $c)))
            ->when($request->country, fn ($q, $co) => $q->where('country', $co))
            ->when($request->q, fn ($q, $kw) => $q->where(fn ($sub) => $sub->where('title', 'like', "%{$kw}%")->orWhere('sku', 'like', "%{$kw}%")))
            ->latest()
            ->paginate(18);

        return view('mmo.store.index', compact('categories', 'products'));
    }

    public function purchase(Request $request, MmoProduct $product)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:500'],
            'discount_code' => ['nullable', 'string', 'max:50'],
        ]);

        return DB::transaction(function () use ($request, $product, $data) {
            $user = $request->user()->newQuery()->lockForUpdate()->findOrFail($request->user()->id);
            $items = MmoProductItem::where('mmo_product_id', $product->id)
                ->where('status', 'available')
                ->lockForUpdate()
                ->limit($data['quantity'])
                ->get();

            if ($items->count() < $data['quantity']) {
                return back()->withErrors(['quantity' => 'Số lượng tồn kho thực tế không đủ.']);
            }

            $subtotal = $product->price * $data['quantity'];
            $discountAmount = 0;
            $appliedCode = null;

            if (!empty($data['discount_code']) && DB::getSchemaBuilder()->hasTable('discount_codes')) {
                $code = strtoupper(trim($data['discount_code']));
                $coupon = DB::table('discount_codes')->where('code', $code)->where('is_active', true)->lockForUpdate()->first();
                if ($coupon && ($coupon->usage_limit === null || $coupon->usage_count < $coupon->usage_limit)) {
                    $discountAmount = $coupon->discount_type === 'percentage'
                        ? (int) round($subtotal * ($coupon->discount_value / 100))
                        : min($subtotal, (int) $coupon->discount_value);
                    $appliedCode = $code;
                    DB::table('discount_codes')->where('id', $coupon->id)->increment('usage_count');
                }
            }

            $total = max(0, $subtotal - $discountAmount);
            if ($user->balance < $total) {
                return back()->withErrors(['balance' => 'Số dư tài khoản không đủ. Vui lòng nạp thêm tiền.']);
            }

            $balanceBefore = $user->balance;
            $user->decrement('balance', $total);

            $purchase = MmoPurchase::create([
                'order_code' => 'ORD-' . strtoupper(Str::random(6)),
                'user_id' => $user->id,
                'mmo_product_id' => $product->id,
                'quantity' => $data['quantity'],
                'unit_price' => $product->price,
                'discount_amount' => $discountAmount,
                'total_amount' => $total,
                'discount_code' => $appliedCode,
            ]);

            MmoProductItem::whereIn('id', $items->pluck('id'))->update([
                'status' => 'sold',
                'buyer_id' => $user->id,
                'mmo_purchase_id' => $purchase->id,
                'sold_at' => now(),
            ]);

            if (MmoProductItem::where('mmo_product_id', $product->id)->where('status', 'available')->count() === 0) {
                $product->update(['status' => 'sold_out']);
            }

            if (DB::getSchemaBuilder()->hasTable('money_transactions')) {
                DB::table('money_transactions')->insert([
                    'user_id' => $user->id,
                    'type' => 'purchase',
                    'amount' => $total,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceBefore - $total,
                    'description' => "Mua {$data['quantity']}x {$product->title} (#{$purchase->order_code})",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return redirect()->route('mmo.history.purchases')
                ->with('success', "Mua thành công {$data['quantity']}x {$product->title} (#{$purchase->order_code}).");
        });
    }

    public function purchaseHistory(Request $request)
    {
        $purchases = MmoPurchase::with(['product', 'items'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return view('mmo.history.purchases', compact('purchases'));
    }

    public function downloadTxt(Request $request, MmoPurchase $purchase)
    {
        abort_unless($purchase->user_id === $request->user()->id || $request->user()->role === 'admin', 403);
        $lines = $purchase->items()->pluck('credential_payload')->implode("\n");
        return response($lines, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$purchase->order_code}.txt\"",
        ]);
    }
}
