<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MmoProduct;
use App\Models\MmoProductItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MmoInventoryController extends Controller
{
    public function index()
    {
        $products = MmoProduct::withCount(['availableItems as available_count'])
            ->orderByDesc('id')
            ->get();

        return view('admin.inventory.import', compact('products'));
    }

    public function bulkImport(Request $request)
    {
        $data = $request->validate([
            'mmo_product_id' => ['required', 'exists:mmo_products,id'],
            'credentials_text' => ['nullable', 'string'],
            'credentials_file' => ['nullable', 'file', 'mimes:txt,csv', 'max:5120'],
        ]);

        $rawContent = (string) ($data['credentials_text'] ?? '');
        if ($request->hasFile('credentials_file')) {
            $rawContent .= "\n" . file_get_contents($request->file('credentials_file')->getRealPath());
        }

        $lines = collect(preg_split('/\r\n|\r|\n/', $rawContent))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->unique()
            ->values();

        if ($lines->isEmpty()) {
            return back()->withErrors(['credentials_text' => 'Không có dòng tài khoản hợp lệ nào để nhập kho.']);
        }

        $product = MmoProduct::findOrFail($data['mmo_product_id']);

        $existingPayloads = MmoProductItem::where('mmo_product_id', $product->id)
            ->whereIn('credential_payload', $lines->all())
            ->pluck('credential_payload')
            ->flip();

        $now = now();
        $rows = [];
        foreach ($lines as $payload) {
            if ($existingPayloads->has($payload)) {
                continue;
            }
            $rows[] = [
                'mmo_product_id' => $product->id,
                'credential_payload' => $payload,
                'status' => 'available',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::transaction(function () use ($rows, $product) {
            foreach (array_chunk($rows, 500) as $chunk) {
                MmoProductItem::insert($chunk);
            }
            if (count($rows) > 0 && $product->status === 'sold_out') {
                $product->update(['status' => 'available']);
            }
        });

        $inserted = count($rows);
        $skipped = $lines->count() - $inserted;

        return back()->with('success', "Đã nhập thành công {$inserted} tài khoản thật vào kho [{$product->sku}] (bỏ qua {$skipped} dòng trùng lặp).");
    }
}
