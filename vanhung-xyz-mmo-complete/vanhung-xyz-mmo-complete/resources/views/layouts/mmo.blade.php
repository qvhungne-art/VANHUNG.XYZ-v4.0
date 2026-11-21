<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'VanHung.xyz — Sàn Tài Nguyên & Dịch Vụ MMO Tự Động')</title>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
</head>
<body class="bg-[#070b14] text-slate-200 min-h-screen flex flex-col">
  <header class="bg-[#090e1a]/90 border-b border-white/10 sticky top-0 z-30">
    <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between gap-4">
      <a href="{{ route('mmo.store.index') }}" class="font-extrabold text-white text-lg">VanHung.xyz <span class="text-emerald-400">MMO</span></a>
      <nav class="flex items-center gap-4 text-sm">
        <a href="{{ route('mmo.store.index') }}" class="hover:text-emerald-400">Tài nguyên</a>
        <a href="{{ route('mmo.services.index') }}" class="hover:text-emerald-400">Dịch vụ MMO</a>
        @auth
          <a href="{{ route('mmo.history.purchases') }}" class="hover:text-emerald-400">Lịch sử</a>
          <span class="px-3 py-1 rounded-xl bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 font-mono font-bold">{{ number_format(auth()->user()->balance) }}đ</span>
          @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-xl bg-white/10 text-cyan-300 font-bold">Admin</a>
          @endif
        @else
          <a href="{{ route('login') }}" class="px-4 py-1.5 rounded-xl bg-emerald-500 text-slate-950 font-bold">Đăng nhập</a>
        @endauth
      </nav>
    </div>
  </header>

  <main class="flex-1 max-w-7xl w-full mx-auto px-4 py-6">
    @if(session('success'))
      <div class="mb-4 p-4 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-sm font-semibold">{{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="mb-4 p-4 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-sm">{{ $errors->first() }}</div>
    @endif
    @yield('content')
  </main>

  <footer class="border-t border-white/10 py-5 text-center text-xs text-slate-500">
    © {{ date('Y') }} VanHung.xyz — Hệ thống bán tài nguyên &amp; dịch vụ MMO tự động 24/7
  </footer>
</body>
</html>
