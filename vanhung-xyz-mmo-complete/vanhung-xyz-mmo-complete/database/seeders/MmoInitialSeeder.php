<?php

namespace Database\Seeders;

use App\Models\MmoCategory;
use App\Models\MmoProduct;
use App\Models\MmoService;
use App\Models\MmoServicePackage;
use Illuminate\Database\Seeder;

class MmoInitialSeeder extends Seeder
{
    public function run(): void
    {
        $catSocial = MmoCategory::firstOrCreate(['slug' => 'social-via'], ['name' => 'Social & Via']);
        $catAi = MmoCategory::firstOrCreate(['slug' => 'ai-phan-mem'], ['name' => 'AI & Phần mềm']);
        $catProxy = MmoCategory::firstOrCreate(['slug' => 'proxy-vpn'], ['name' => 'Proxy & VPN']);

        MmoProduct::firstOrCreate(['sku' => 'VIA-VN-XMDT'], [
            'mmo_category_id' => $catSocial->id,
            'title' => 'Via Việt Cổ XMDT Tích Xanh',
            'platform' => 'Facebook',
            'country' => 'VN',
            'format_spec' => 'UID|Pass|2FA|Hotmail|PassMail',
            'warranty_policy' => 'Bảo hành sai pass / checkpoint khi login lần đầu (24h)',
            'price' => 85000,
            'original_price' => 110000,
            'status' => 'sold_out', // Sẽ tự bật available khi bạn nạp kho thật
        ]);

        MmoProduct::firstOrCreate(['sku' => 'GPT-PLUS-1M'], [
            'mmo_category_id' => $catAi->id,
            'title' => 'Tài khoản ChatGPT Plus 1 Tháng',
            'platform' => 'OpenAI',
            'country' => 'Global',
            'format_spec' => 'Email|Pass|RecoveryMail',
            'warranty_policy' => 'Bảo hành Full Time 30 ngày lỗi đổi mới',
            'price' => 165000,
            'original_price' => 220000,
            'status' => 'sold_out',
        ]);

        MmoProduct::firstOrCreate(['sku' => 'PROXY-RES-VN'], [
            'mmo_category_id' => $catProxy->id,
            'title' => 'Proxy Dân Cư Việt Nam IPv4 Tĩnh (30 Ngày)',
            'platform' => 'Proxy IPv4',
            'country' => 'VN',
            'format_spec' => 'IP:Port:User:Pass',
            'warranty_policy' => 'Uptime 99.9%, đổi IP miễn phí nếu lỗi kết nối',
            'price' => 45000,
            'original_price' => 60000,
            'status' => 'sold_out',
        ]);

        $svcTiktok = MmoService::firstOrCreate(['slug' => 'tiktok-seeding'], [
            'name' => 'Tăng Follow & View TikTok Thực',
            'service_type' => 'seeding',
            'description' => 'Tăng trưởng kênh bán hàng TikTok Shop ổn định, có bảo hành.',
        ]);

        MmoServicePackage::firstOrCreate(['mmo_service_id' => $svcTiktok->id, 'name' => 'SV1 - Follow Việt Nam (BH 30N)'], [
            'unit_price' => 85,
            'unit_label' => 'follow',
            'min_qty' => 100,
        ]);
    }
}
