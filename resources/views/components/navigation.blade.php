<x-splade-data default="{ open: false, adminOpen: false }">
    <nav class="bg-white border-b border-gray-100">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <Link href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/eidos-title.png') }}" alt="Logo Eidos" class="h-20 w-auto">
                        </Link>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            {{ __('Home') }}
                        </x-nav-link>

                        <x-nav-link href="{{ route('eidos.import.form') }}" :active="request()->routeIs('eidos.import.form')">
                            {{ __('Importar') }}
                        </x-nav-link>

                        @if(Auth::user()->email === 'oscar.romanini.jr@gmail.com')
                            <div class="relative flex items-center" @click.outside="data.adminOpen = false">
                                <button type="button"
                                        @click="data.adminOpen = !data.adminOpen"
                                        class="inline-flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition-colors"
                                        :class="@js(request()->routeIs('admin.*')) ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'">
                                    <span>{{ __('Admin') }}</span>
                                    <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': data.adminOpen }" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div v-show="data.adminOpen"
                                     @click.stop
                                     class="absolute left-0 top-full z-40 mt-2 w-52 rounded-lg border border-gray-200 bg-white p-2 shadow-lg">
                                    <Link href="{{ route('admin.users.index') }}"
                                          class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 font-semibold text-indigo-700' : '' }}">
                                        {{ __('Usuários') }}
                                    </Link>
                                    <Link href="{{ route('admin.topics.index') }}"
                                          class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.topics.*') ? 'bg-gray-100 font-semibold text-indigo-700' : '' }}">
                                        {{ __('Tópicos') }}
                                    </Link>
                                    <Link href="{{ route('admin.categories.index') }}"
                                          class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-100 font-semibold text-indigo-700' : '' }}">
                                        {{ __('Categorias') }}
                                    </Link>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- User Info and Logout Button -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <div class="flex items-center text-sm font-medium text-gray-500">
                        <!-- Avatar do Usuário -->
                        <img class="h-8 w-8 rounded-full object-cover me-3" src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" />

                        <!-- Nome do Usuário -->
                        <div class="me-4">{{ Auth::user()->name }}</div>

                        <!-- Botão de Sair (Logout) -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Sair
                            </button>
                        </form>
                    </div>
                </div>


                <!-- Hamburger -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="data.open = ! data.open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path v-bind:class="{'hidden': data.open, 'inline-flex': ! data.open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path v-bind:class="{'hidden': ! data.open, 'inline-flex': data.open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div v-bind:class="{'block': data.open, 'hidden': ! data.open }" class="sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    {{ __('Home') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('eidos.import.form') }}" :active="request()->routeIs('eidos.import.form')">
                    {{ __('Importar') }}
                </x-responsive-nav-link>

                @if(Auth::user()->email === 'oscar.romanini.jr@gmail.com')
                    <div class="px-4 pt-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Admin</div>
                    <x-responsive-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')">
                        {{ __('Usuários') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.topics.index') }}" :active="request()->routeIs('admin.topics.*')">
                        {{ __('Tópicos') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.categories.index') }}" :active="request()->routeIs('admin.categories.*')">
                        {{ __('Categorias') }}
                    </x-responsive-nav-link>
                @endif
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="flex items-center">
                        <div class="shrink-0 me-3">
                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" />
                        </div>
                        <div>
                            <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link as="button">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </nav>
</x-splade-data>
