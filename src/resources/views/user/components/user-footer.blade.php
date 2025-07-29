<footer class="bg-gray-800 text-white">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:justify-between">
            <!-- 左側：会社情報とお問い合わせ -->
            <div class="md:w-1/2">
                <!-- 会社情報 -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">{{ config('app.name') }}</h3>
                    @if(isset($shop))
                    <p class="text-gray-300 text-sm">
                        〒{{ $shop->postal_code }}<br>
                        {{ $shop->address }}<br>
                        TEL: {{ $shop->tel }}<br>
                        営業時間: {{ $shop->business_hours }}
                    </p>
                    @endif
                </div>

                <!-- お問い合わせ -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">お問い合わせ</h3>
                    <p class="text-gray-300 text-sm mb-4">
                        ご質問やご不明な点がございましたら、お気軽にお問い合わせください。
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="tel:{{ $shop->tel ?? '' }}" 
                           class="inline-flex items-center justify-center bg-white text-gray-800 px-4 py-2 rounded hover:bg-gray-100 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                            </svg>
                            電話でのお問い合わせ
                        </a>
                        <a href="mailto:{{ $shop->email ?? '' }}" 
                           class="inline-flex items-center justify-center bg-white text-gray-800 px-4 py-2 rounded hover:bg-gray-100 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                            メールでのお問い合わせ
                        </a>
                    </div>
                </div>
            </div>

            <!-- 右側：ポリシー -->
            <div class="mt-8 md:mt-0 md:w-1/3">
                <h3 class="text-lg font-semibold mb-4">ポリシー</h3>
                <ul class="space-y-4">
                    <li>
                        <a href="{{ route('user.privacy') }}" 
                           class="inline-flex items-center text-gray-300 hover:text-white text-sm group">
                            <svg class="w-4 h-4 mr-2 text-gray-400 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            プライバシーポリシー
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.terms') }}" 
                           class="inline-flex items-center text-gray-300 hover:text-white text-sm group">
                            <svg class="w-4 h-4 mr-2 text-gray-400 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            利用規約
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.cancel') }}" 
                           class="inline-flex items-center text-gray-300 hover:text-white text-sm group">
                            <svg class="w-4 h-4 mr-2 text-gray-400 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            キャンセルポリシー
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- コピーライト -->
        <div class="mt-12 pt-8 border-t border-gray-700 text-center">
            <p class="text-gray-400 text-sm">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</footer> 