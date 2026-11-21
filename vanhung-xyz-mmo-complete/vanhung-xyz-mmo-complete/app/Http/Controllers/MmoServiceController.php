<?php

namespace App\Http\Controllers;

use App\Models\MmoService;
use App\Models\MmoServiceOrder;
use App\Models\MmoServicePackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MmoServiceController extends Controller
{
    public function index()
    {
        $services = MmoService::with('packages')->where('is_active', true)->get();
        $orders = auth()->check()
            ? MmoServiceOrder::with('package.service')->where('user_id', auth()->id())->latest()->limit(20)->get()
            : collect();

        return view('mmo.services.index', compact('services', 'orders'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mmo_service_package_id' => ['required', 'exists:mmo_service_packages,id'],
            'target_link' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000000'],
            'user_note' => ['nullable', 'string', 'max:500'],
        ]);

        return DB::transaction(function () use ($request, $data) {
            $package = MmoServicePackage::findOrFail($data['mmo_service_package_id']);
            if ($data['quantity'] < $package->min_qty) {
                return back()->withErrors(['quantity' => "Số lượng tối thiểu là {$package->min_qty}."]);
            }

            $user = $request->user()->newQuery()->lockForUpdate()->findOrFail($request->user()->id);
            $total = $package->unit_price * $data['quantity'];

            if ($user->balance < $total) {
                return back()->withErrors(['balance' => 'Số dư không đủ để đặt dịch vụ.']);
            }

            $balanceBefore = $user->balance;
            $user->decrement('balance', $total);

            $order = MmoServiceOrder::create([
                'order_code' => 'SVC-' . strtoupper(Str::random(6)),
                'user_id' => $user->id,
                'mmo_service_package_id' => $package->id,
                'target_link' => $data['target_link'],
                'quantity' => $data['quantity'],
                'total_amount' => $total,
                'status' => 'processing',
                'user_note' => $data['user_note'] ?? null,
            ]);

            if (DB::getSchemaBuilder()->hasTable('money_transactions')) {
                DB::table('money_transactions')->insert([
                    'user_id' => $user->id,
                    'type' => 'purchase',
                    'amount' => $total,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceBefore - $total,
                    'description' => "Đặt dịch vụ {$package->name} (#{$order->order_code})",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return back()->with('success', "Đã tạo đơn dịch vụ #{$order->order_code} thành công!");
        });
    }
}
