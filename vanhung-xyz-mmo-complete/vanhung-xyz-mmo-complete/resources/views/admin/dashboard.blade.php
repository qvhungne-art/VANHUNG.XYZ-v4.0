@extends('layouts.mmo')

@section('content')
<div class="space-y-6">
  <div class="flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-extrabold text-white">Admin Dashboard — VanHung.xyz</h1>
    <a href="{{ route('admin.inventory.import') }}" class="px-4 py-2 rounded-xl bg-emerald-500 text-slate-950 font-extrabold text-sm">+ Nhập Kho Tài Nguyên Thật</a>
  </div>

  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="rounded-2xl bg-slate-900 border border-white/10 p-4"><div class="text-xs text-slate-400">Thành viên</div><div class="text-2xl font-black text-white mt-1">{{ number_format($stats['users']) }}</div></div>
    <div class="rounded-2xl bg-slate-900 border border-white/10 p-4"><div class="text-xs text-slate-400">Tồn kho thật</div><div class="text-2xl font-black text-cyan-400 mt-1">{{ number_format($stats['available_stock']) }}</div></div>
    <div class="rounded-2xl bg-slate-900 border border-white/10 p-4"><div class="text-xs text-slate-400">Tổng doanh thu</div><div class="text-2xl font-black text-emerald-400 mt-1">{{ number_format($stats['total_revenue']) }}đ</div></div>
    <div class="rounded-2xl bg-slate-900 border border-white/10 p-4"><div class="text-xs text-slate-400">Đơn dịch vụ chờ duyệt</div><div class="text-2xl font-black text-amber-400 mt-1">{{ number_format($stats['pending_services']) }}</div></div>
  </div>

  <div class="rounded-2xl bg-slate-900 border border-white/10 p-5 space-y-3">
    <h2 class="font-bold text-white">Đơn Dịch Vụ Chờ Xử Lý</h2>
    @forelse($pendingOrders as $order)
      <form method="POST" action="{{ route('admin.service-orders.update', $order) }}" class="p-4 rounded-xl bg-slate-950 border border-white/10 flex flex-wrap items-center justify-between gap-3 text-xs">
        @csrf
        <div>
          <div class="font-bold text-white">#{{ $order->order_code }} • {{ $order->package->name }} ({{ number_format($order->total_amount) }}đ)</div>
          <div class="text-slate-400">User: {{ $order->user->email }} • Target: {{ $order->target_link }} • SL: {{ $order->quantity }}</div>
        </div>
        <div class="flex items-center gap-2">
          <input type="text" name="admin_note" placeholder="Ghi chú admin" class="px-2.5 py-1.5 rounded-lg bg-slate-900 border border-white/10 text-white">
          <button type="submit" name="status" value="success" class="px-3 py-1.5 rounded-lg bg-emerald-500 text-slate-950 font-bold">Hoàn thành</button>
          <button type="submit" name="status" value="error" class="px-3 py-1.5 rounded-lg bg-rose-500 text-white font-bold">Từ chối &amp; Hoàn</button>
        </div>
      </form>
    @empty
      <div class="text-xs text-slate-500">Không có đơn dịch vụ chờ xử lý.</div>
    @endforelse
  </div>
</div>
@endsection
