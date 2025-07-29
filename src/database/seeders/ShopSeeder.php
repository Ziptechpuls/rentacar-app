<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shop;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        Shop::create([
            'name' => 'オージーアイレンタカー',
            'tel' => '098-851-4444',
            'email' => 'info@ogi-rentacar.com',
            'address' => '〒901-0224 沖縄県豊見城市与根50-56',
            'business_hours' => '8:00〜20:00',
            'access' => "那覇空港から車で約10分\n那覇空港または豊見城とよみの駅より無料送迎あり\n※送迎をご希望の方は、予約時にお申し付けください。",
            'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3579.1927245620186!2d127.66824147604618!3d26.229772077076825!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x34e56bd0b4a42ef1%3A0x1e2aa6c3ee6e8dd9!2z44CSOTAxLTAyMjQg5rKW57iE55yM6LGK6KaL5Z-O5biC5LiO5qC577yV77yQ4oiS77yV77yW!5e0!3m2!1sja!2sjp!4v1707436455345!5m2!1sja!2sjp" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
        ]);
    }
} 