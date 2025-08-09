<nav x-data="{ open: false }" class="bg-white border-b border-gray-100" style="position: relative; z-index: 50; overflow: visible;">
    <!-- Primary Navigation Menu -->
    <div class="px-2 md:px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                        <x-application-logo class="block w-auto text-gray-800 fill-current h-9" />
                        <span class="text-xl font-bold text-gray-800 block md:hidden">レンタカー管理システム</span>
                        <span class="text-lg font-semibold text-gray-700 hidden md:block">Rensys</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 md:space-x-8 sm:-my-px sm:ms-10 sm:flex flex-nowrap overflow-visible md:overflow-x-auto scrollbar-hide">
                    <x-nav-link :href="route('admin.cars.index')" :active="request()->routeIs('admin.cars.index')">
                        {{ __('車両管理') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.reservations.index')" :active="request()->routeIs('admin.reservations.*')">
                        {{ __('予約管理') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.index')">
                        {{ __('顧客管理') }}
                    </x-nav-link>
                                            <x-nav-link :href="route('admin.reports.sales')" :active="request()->routeIs('admin.reports.sales')">
                            {{ __('売上管理') }}
                        </x-nav-link>
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('ダッシュボード') }}
                    </x-nav-link>
                    <!-- システム設定ドロップダウン（ポータル方式） -->
                    <div class="relative inline-flex items-center">
                        <button 
                            id="system-settings-trigger"
                            type="button"
                            class="inline-flex items-center h-full px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:border-indigo-400 focus:text-gray-900"
                            aria-haspopup="menu"
                            aria-expanded="false"
                        >
                            {{ __('システム設定') }}
                            <svg class="w-4 h-4 ml-1 transition-all duration-200 ease-in-out" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- ポータルに流し込むメニューのテンプレート（非表示） -->
                        <template id="system-menu-template">
                            <div role="menu" aria-label="System settings menu" style="background-color:#ffffff;border:1px solid #e5e7eb;border-radius:0.375rem;box-shadow:0 10px 15px -3px rgba(0,0,0,.1),0 4px 6px -2px rgba(0,0,0,.05);padding:0.25rem 0;width:14rem;">
                                <a href="{{ route('admin.shop.index') }}" role="menuitem" style="display:block;padding:0.5rem 1rem;font-size:0.875rem;color:#374151;text-decoration:none;">{{ __('店舗情報') }}</a>
                                <a href="{{ route('admin.price.index') }}" role="menuitem" style="display:block;padding:0.5rem 1rem;font-size:0.875rem;color:#374151;text-decoration:none;">{{ __('料金情報') }}</a>
                                <a href="{{ route('admin.car-type-prices.index') }}" role="menuitem" style="display:block;padding:0.5rem 1rem;font-size:0.875rem;color:#374151;text-decoration:none;">{{ __('車両タイプ別料金') }}</a>
                                <a href="{{ route('admin.settings.index') }}" role="menuitem" style="display:block;padding:0.5rem 1rem;font-size:0.875rem;color:#374151;text-decoration:none;">{{ __('システム設定') }}</a>
                                <a href="{{ route('admin.privacy.index') }}" role="menuitem" style="display:block;padding:0.5rem 1rem;font-size:0.875rem;color:#374151;text-decoration:none;">{{ __('プライバシーポリシー') }}</a>
                                <a href="{{ route('admin.terms.index') }}" role="menuitem" style="display:block;padding:0.5rem 1rem;font-size:0.875rem;color:#374151;text-decoration:none;">{{ __('利用規約') }}</a>
                                <a href="{{ route('admin.cancel.index') }}" role="menuitem" style="display:block;padding:0.5rem 1rem;font-size:0.875rem;color:#374151;text-decoration:none;">{{ __('キャンセルポリシー') }}</a>
                                <a href="{{ route('admin.options.index') }}" role="menuitem" style="display:block;padding:0.5rem 1rem;font-size:0.875rem;color:#374151;text-decoration:none;">{{ __('オプション設定') }}</a>
                            </div>
                        </template>
                    </div>

                    <script>
                    (function(){
                        const trigger = document.getElementById('system-settings-trigger');
                        const tpl = document.getElementById('system-menu-template');
                        let portal = null;
                        let open = false;

                        function createPortalOnce(){
                            if (portal) return portal;
                            portal = document.createElement('div');
                            portal.setAttribute('id','system-settings-portal');
                            portal.setAttribute('role','presentation');
                            portal.style.position = 'fixed';
                            portal.style.inset = '0px';
                            portal.style.pointerEvents = 'none';
                            portal.style.zIndex = '2147483647';
                            const panel = document.createElement('div');
                            panel.innerHTML = tpl.innerHTML.trim();
                            panel.firstElementChild.style.pointerEvents = 'auto';
                            panel.firstElementChild.addEventListener('mouseover', e => { if (e.target.tagName === 'A') e.target.style.backgroundColor = '#f3f4f6'; });
                            panel.firstElementChild.addEventListener('mouseout', e => { if (e.target.tagName === 'A') e.target.style.backgroundColor = 'transparent'; });
                            portal.appendChild(panel.firstElementChild);
                            document.body.appendChild(portal);
                            return portal;
                        }

                        function positionPortal(){
                            const rect = trigger.getBoundingClientRect();
                            const panel = portal.firstElementChild;
                            panel.style.position = 'fixed';
                            panel.style.top = `${Math.round(rect.bottom + 6)}px`;
                            panel.style.left = `${Math.round(rect.left)}px`;
                        }

                        function openMenu(){
                            createPortalOnce();
                            positionPortal();
                            portal.style.display = 'block';
                            open = true;
                            trigger.setAttribute('aria-expanded','true');
                        }

                        function closeMenu(){
                            if (!portal) return;
                            portal.style.display = 'none';
                            open = false;
                            trigger.setAttribute('aria-expanded','false');
                        }

                        function toggleMenu(){
                            if (!open) openMenu(); else closeMenu();
                        }

                        function onOutsideClick(e){
                            if (!open) return;
                            const panel = portal && portal.firstElementChild;
                            if (!panel) return;
                            if (e.target === trigger || trigger.contains(e.target)) return;
                            if (panel.contains(e.target)) return;
                            closeMenu();
                        }

                        function onEscape(e){
                            if (e.key === 'Escape') closeMenu();
                        }

                        function onRelayout(){
                            if (!open) return;
                            positionPortal();
                        }

                        if (trigger){
                            trigger.addEventListener('click', (e)=>{ e.preventDefault(); toggleMenu(); });
                            window.addEventListener('scroll', onRelayout, true);
                            window.addEventListener('resize', onRelayout, true);
                            document.addEventListener('click', onOutsideClick, true);
                            document.addEventListener('keydown', onEscape, true);
                        }
                    })();
                    </script>                
                </div>
            </div>
            <!-- Settings Dropdown -->
            @auth('admin')
            <div class="hidden sm:flex sm:items-center sm:ms-6 flex-shrink-0 ml-auto">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none whitespace-nowrap">
                            <div class="max-w-xs truncate">{{ Auth::guard('admin')->user()->name }}</div>

                            <div class="ms-1 flex-shrink-0">
                                <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('admin.profile.edit')">
                            {{ __('プロフィール編集') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('admin.logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('ログアウト') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            @endauth
            <!-- Hamburger -->
            <div class="flex items-center -me-2 sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('admin.cars.index')" :active="request()->routeIs('admin.cars.index')">
                {{ __('車両管理') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.reservations.index')" :active="request()->routeIs('admin.reservations.*')">
                {{ __('予約管理') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.index')">
                {{ __('顧客管理') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.reports.sales')" :active="request()->routeIs('admin.reports.sales')">
                {{ __('売上管理') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                {{ __('ダッシュボード') }}
            </x-responsive-nav-link>
            <!-- ここにシステム設定の単独リンクがあれば削除 -->
            <!-- ドロップダウンは既に追加済み -->
        </div>
        <!-- Responsive Settings Options -->
        @auth('admin')
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="text-base font-medium text-gray-800">{{ Auth::guard('admin')->user()->name }}</div>
                <div class="text-sm font-medium text-gray-500">{{ Auth::guard('admin')->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('admin.profile.edit')">
                    {{ __('プロフィール編集') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('admin.logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('ログアウト') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>
