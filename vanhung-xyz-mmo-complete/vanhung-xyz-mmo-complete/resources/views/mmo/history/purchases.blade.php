@extends('layouts.mmo')

@section('content')
<div class="space-y-4">
  <h1 class="text-2xl font-extrabold text-white">Tài Nguyên Đã Mua</h1>
  @forelse($purchases as $purchase)
    <div class="rounded-2xl bg-slate-900/80 border border-white/10 p-5 space-y-3">
      <div class="flex flex-wrap justify-between gap-2 text-sm">
        <div><span class="font-mono text-emerald-400 font-bold">#{{ $purchase->order_code }}</span> — <strong class="text-white">{{ $purchase->product->title }}</strong> (x{{ $purchase->quantity }})</div>
        <div class="font-mono text-slate-400">{{ $purchase->created_at }} • <strong class="text-emerald-400">{{ number_format($purchase->total_amount) }}đ</strong></div>
      </div>
      <pre class="bg-slate-950 text-emerald-300 p-3.5 rounded-xl text-xs font-mono overflow-x-auto border border-white/10">{{ $purchase->items->pluck('credential_payload')->implode("\n") }}</pre>
      <a href="{{ route('mmo.history.download', $purchase) }}" class="inline-block px-3.5 py-1.5 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold">Tải file .TXT</a>
    </div>
  @empty
    <div class="text-slate-400">Bạn chưa mua tài nguyên nào.</div>
  @endforelse
</div>
@endsection
