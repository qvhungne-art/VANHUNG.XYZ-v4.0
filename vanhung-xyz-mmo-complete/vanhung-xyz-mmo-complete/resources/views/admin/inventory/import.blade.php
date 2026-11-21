@extends('admin.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
  <h1 class="text-2xl font-bold text-slate-900 mb-2">Nhập Kho Tài Nguyên MMO Thật (vanhung.xyz)</h1>
  <p class="text-sm text-slate-500 mb-6">Dán trực tiếp danh sách <code>UID|Pass|2FA|Mail...</code> (mỗi dòng 1 tài khoản) hoặc tải lên tệp <code>.txt</code> để lưu vào bảng <code>mmo_product_items</code>.</p>

  @if(session('success'))
    <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('admin.inventory.import.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4 shadow-sm">
    @csrf
    <div>
      <label class="block text-sm font-semibold text-slate-700 mb-1">Chọn sản phẩm MMO</label>
      <select name="mmo_product_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm">
        @foreach($products as $product)
          <option value="{{ $product->id }}">[{{ $product->sku }}] {{ $product->title }} (Tồn kho thật: {{ $product->available_count }})</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm font-semibold text-slate-700 mb-1">Danh sách tài khoản (mỗi dòng 1 tài khoản)</label>
      <textarea name="credentials_text" rows="8" placeholder="UID|Pass|2FA|Mail|PassMail" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono text-xs"></textarea>
    </div>

    <div>
      <label class="block text-sm font-semibold text-slate-700 mb-1">Hoặc tải file .TXT (tối đa 5MB)</label>
      <input type="file" name="credentials_file" accept=".txt,.csv" class="block w-full text-sm text-slate-600">
    </div>

    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm">Nạp vào kho thực tế</button>
  </form>
</div>
@endsection
