<x-user-layout>
    <div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-2xl p-6 space-y-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-green-600">予約が完了しました！</h1>
                <p class="text-gray-600 mt-2">ご予約ありがとうございます。</p>
            </div>

            {{-- 予約情報カード --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-800 border-b border-blue-200 pb-2">📋 予約情報</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <p><span class="font-medium text-gray-700">予約ID:</span> <span class="text-blue-600 font-bold">#{{ $reservation->id ?? 'RES12345' }}</span></p>
                    <p><span class="font-medium text-gray-700">車両名:</span> {{ $reservation->car->name ?? 'プリウス2' }}</p>
                    <p><span class="font-medium text-gray-700">利用開始:</span> {{ $reservation->start_datetime ?? '2025年7月26日 19:12' }}</p>
                    <p><span class="font-medium text-gray-700">利用終了:</span> {{ $reservation->end_datetime ?? '2025年7月27日 19:12' }}</p>
                </div>
                <div class="mt-4 p-4 bg-white rounded-lg border">
                    <p class="text-center"><span class="font-medium text-gray-700">合計金額:</span> 
                        <span class="text-2xl font-bold text-green-600">¥{{ number_format($reservation->total_price ?? 22800) }}</span>
                    </p>
                </div>
            </div>

            {{-- 予約者情報カード --}}
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-800 border-b border-green-200 pb-2">👤 予約者情報</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <p><span class="font-medium text-gray-700">お名前:</span> {{ $reservation->name_kanji ?? '荻堂 豪' }}</p>
                    <p><span class="font-medium text-gray-700">カナ:</span> {{ ($reservation->name_kana_sei ?? 'オギドウ') . ' ' . ($reservation->name_kana_mei ?? 'タケシ') }}</p>
                    <p><span class="font-medium text-gray-700">メールアドレス:</span> {{ $reservation->email ?? 'goiu@mail.com' }}</p>
                    <p><span class="font-medium text-gray-700">電話番号:</span> {{ $reservation->phone_main ?? '090-9989-8898' }}</p>
                </div>
            </div>

            {{-- 重要な案内 --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-3">📧 重要なお知らせ</h3>
                <ul class="text-yellow-700 space-y-2">
                    <li>• 予約完了メールを上記のメールアドレスに送信いたします</li>
                    <li>• メールに記載された予約詳細を必ずご確認ください</li>
                    <li>• 当日の受付時に身分証明書をご持参ください</li>
                    <li>• ご不明な点がございましたらお気軽にお問い合わせください</li>
                </ul>
            </div>

            {{-- アクションボタン --}}
            <div class="flex flex-col sm:flex-row gap-4 pt-6">
                <a href="{{ route('user.cars.index') }}" 
                   class="flex-1 text-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-full hover:bg-blue-700 transition-colors duration-200">
                    他の車両を見る
                </a>
                <a href="{{ url('/') }}" 
                   class="flex-1 text-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-full hover:bg-gray-700 transition-colors duration-200">
                    トップページへ
                </a>
            </div>

            {{-- お問い合わせ情報 --}}
            <div class="text-center text-sm text-gray-500 pt-4 border-t">
                <p>お問い合わせ: 📞 098-XXX-XXXX | ✉️ info@rentacar.com</p>
                <p class="mt-1">営業時間: 9:00 - 18:00（年中無休）</p>
            </div>
        </div>
    </div>
</x-user-layout>
