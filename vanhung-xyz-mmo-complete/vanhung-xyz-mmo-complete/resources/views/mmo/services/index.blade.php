@extends('layouts.mmo')

@section('content')
<div class="grid lg:grid-cols-12 gap-6">
  <div class="lg:col-span-7 space-y-4">
    <h1 class="text-2xl font-extrabold text-white">Dịch Vụ MMO Chuyên Nghiệp</h1>
    @foreach($services as $service)
      <div class="rounded-2xl bg-slate-900/80 border border-white/10 p-5 space-y-3">
        <h2 class="font-bold text-white text-lg">{{ $service->name }}</h2>
        <p class="text-xs text-slate-400">{{ $service->description }}</p>
        <form method="POST" action="{{ route('mmo.services.order') }}" class="space-y-3 pt-2">
          @csrf
          <select name="mmo_service_package_id" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-white/10 text-sm text-white">
            @foreach($service->packages as $pkg)
              <option value="{{ $pkg->id }}">{{ $pkg->name }} — {{ number_format($pkg->unit_price) }}đ/{{ $pkg->unit_label }} (Tối thiểu {{ $pkg->min_qty }})</option>
            @endforeach
          </select>
          <input type="text" name="target_link" required placeholder="Nhập Link / UID / Username" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-white/10 text-sm text-white">
          <div class="grid grid-cols-2 gap-2">
            <input type="number" name="quantity" min="1" value="100" required class="px-3 py-2 rounded-xl bg-slate-950 border border-white/10 text-sm text-white font-mono">
            <input type="text" name="user_note" placeholder="Ghi chú (tùy chọn)" class="px-3 py-2 rounded-xl bg-slate-950 border border-white/10 text-sm text-white">
          </div>
          <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-sm">Đặt dịch vụ</button>
        </form>
      </div>
    @endforeach
  </div>

  <div class="lg:col-span-5 space-y-3">
    <h2 class="text-lg font-bold text-white">Đơn dịch vụ gần đây</h2>
    @forelse($orders as $ord)
      <div class="rounded-2xl bg-slate-900/80 border border-white/10 p-4 text-xs space-y-1">
        <div class="flex justify-between font-bold text-white"><span>#{{ $ord->order_code }} • {{ $ord->package->name }}</span><span class="text-emerald-400">{{ number_format($ord->total_amount) }}đ</span></div>
        <div class="text-slate-400">SL: {{ $ord->quantity }} • Target: {{ $ord->target_link }}</div>
        <div class="text-amber-300 uppercase font-bold">Trạng thái: {{ $ord->status }}</div>
      </div>
    @empty
      <div class="text-xs text-slate-500">Chưa có đơn dịch vụ nào.</div>
    @endforelse
  </div>
</div>
@endsection
