@extends('layouts.mmo')

@section('content')
<div class="space-y-6">
  <div class="rounded-3xl bg-gradient-to-r from-emerald-950/80 via-slate-900 to-cyan-950/70 border border-white/10 p-6 sm:p-8">
    <h1 class="text-2xl sm:text-4xl font-black text-white">Kho Tài Nguyên MMO Tự Động — VanHung.xyz</h1>
    <p class="text-slate-300 text-sm mt-2">Giao ngay sau thanh toán • Auto Bank VietQR (cú pháp <code class="text-emerald-300 font-mono">MMO{{ auth()->id() ?? 'ID' }}</code>)</p>
  </div>

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($products as $product)
      <div class="rounded-2xl bg-slate-900/80 border border-white/10 p-5 flex flex-col justify-between gap-4">
        <div class="space-y-2">
          <div class="flex items-center justify-between text-xs">
            <span class="font-bold text-emerald-400">{{ $product->platform }} • {{ $product->country }}</span>
            <span class="font-mono {{ $product->stock_count > 0 ? 'text-cyan-300' : 'text-rose-400' }}">Kho: {{ $product->stock_count }}</span>
          </div>
          <h2 class="font-bold text-white text-base">{{ $product->title }}</h2>
          <div class="text-xs text-slate-400 font-mono bg-slate-950 p-2.5 rounded-xl border border-white/5">{{ $product->format_spec }}</div>
          <p class="text-xs text-slate-400">{{ $product->warranty_policy }}</p>
        </div>

        <form method="POST" action="{{ route('mmo.store.purchase', $product) }}" class="pt-3 border-t border-white/10 space-y-2.5">
          @csrf
          <div class="flex items-center justify-between">
            <span class="text-xl font-black text-emerald-400 font-mono">{{ number_format($product->price) }}đ</span>
            <input type="number" name="quantity" value="1" min="1" max="{{ max(1, $product->stock_count) }}" class="w-20 px-2.5 py-1.5 rounded-lg bg-slate-950 border border-white/10 text-white text-sm font-mono">
          </div>
          <input type="text" name="discount_code" placeholder="Mã giảm giá (nếu có)" class="w-full px-3 py-1.5 rounded-lg bg-slate-950 border border-white/10 text-xs text-amber-300 uppercase">
          <button type="submit" @disabled($product->stock_count <= 0) class="w-full py-2.5 rounded-xl text-sm font-extrabold {{ $product->stock_count > 0 ? 'bg-emerald-500 hover:bg-emerald-400 text-slate-950' : 'bg-white/5 text-slate-500 cursor-not-allowed' }}">
            {{ $product->stock_count > 0 ? 'Mua ngay' : 'Tạm hết kho' }}
          </button>
        </form>
      </div>
    @empty
      <div class="col-span-full text-center text-slate-400 py-10">Chưa có sản phẩm nào.</div>
    @endforelse
  </div>
</div>
@endsection
