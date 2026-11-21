<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MmoProduct;
use App\Models\MmoProductItem;
use App\Models\MmoPurchase;
use App\Models\MmoServiceOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MmoDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'available_stock' => MmoProductItem::where('status', 'available')->count(),
            'total_revenue' => MmoPurchase::sum('total_amount') + MmoServiceOrder::where('status', '!=', 'error')->sum('total_amount'),
            'pending_services' => MmoServiceOrder::where('status', 'processing')->count(),
        ];

        $pendingOrders = MmoServiceOrder::with(['user', 'package.service'])
            ->where('status', 'processing')
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingOrders'));
    }

    public function updateServiceOrder(Request $request, MmoServiceOrder $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:success,error'],
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        return DB::transaction(function () use ($order, $data) {
            $locked = MmoServiceOrder::lockForUpdate()->findOrFail($order->id);
            if ($locked->status !== 'processing') {
                return back()->withErrors(['status' => 'Đơn hàng này đã được xử lý trước đó.']);
            }

            $locked->update([
                'status' => $data['status'],
                'admin_note' => $data['admin_note'] ?? null,
            ]);

            if ($data['status'] === 'error') {
                $user = User::lockForUpdate()->findOrFail($locked->user_id);
                $user->increment('balance', $locked->total_amount);
            }

            return back()->with('success', "Đã cập nhật trạng thái đơn #{$locked->order_code}.");
        });
    }
}
