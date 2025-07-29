<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    private $defaultShop = [
        'name' => 'オージーアイレンタカー',
        'tel' => '09812345678',
        'email' => 'aiueo@okinawa-rentacar.jp',
        'address' => '〒901-0224 沖縄県国頭郡今帰仁村字渡帰仁',
        'business_hours' => '8:00〜20:00',
        'access' => "那覇空港またはゆいレール赤嶺駅より無料送迎あります。\n那覇空港送迎希望の方は、航空便名を備考にご記入ください。",
        'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3578.9840673359845!2d127.97416937608583!3d26.235844077775736!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x34e56bd2d8f0f357%3A0x4a6c5957c61b73cc!2z44CSOTAxLTAyMjQg5rKW57iE55yM5Zu96aCt5Z-O5biC5LiO5qC577yV77yQ4oiS77yV77yW!5e0!3m2!1sja!2sjp!4v1710925161645!5m2!1sja!2sjp" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
    ];

    public function index()
    {
        $shop = Shop::first();
        if (!$shop) {
            $shop = new Shop($this->defaultShop);
        }
        return view('admin.shop.index', compact('shop'));
    }

    public function edit()
    {
        $shop = Shop::first();
        if (!$shop) {
            $shop = new Shop($this->defaultShop);
        }
        return view('admin.shop.edit', compact('shop'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'tel' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'business_hours' => 'required|string|max:255',
            'access' => 'required|string',
            'map_iframe' => 'required|string|max:2000',
        ]);

        $shop = Shop::first();
        if (!$shop) {
            $shop = new Shop();
        }

        $shop->fill($request->only([
            'name',
            'tel',
            'email',
            'address',
            'business_hours',
            'access',
            'map_iframe',
        ]));
        $shop->save();

        return redirect()->route('admin.shop.index')->with('status', '店舗情報を更新しました。');
    }
}
