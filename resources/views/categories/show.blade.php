<x-layout>
    <div class="bg-gray-50 min-h-screen">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="mb-6">
                <Link href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 transition-colors">Home</Link>
                <span class="text-gray-400 mx-2">/</span>
                <span class="text-gray-600">{{ $category->name }}</span>
            </div>

            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800">{{ $category->name }}</h1>
                <p class="text-lg text-gray-600 mt-3">{{ $category->description ?: 'Selecione um tópico para começar seu plano de estudos.' }}</p>
                <p class="text-sm text-gray-500 mt-2">Tópicos ordenados do mais recente para o mais antigo.</p>
            </div>

            @if($topics->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($topics as $topic)
                        <Link href="{{ route('topics.show', $topic) }}"
                              class="block p-5 bg-blue-600 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 group min-h-44">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <h3 class="text-lg md:text-xl font-semibold text-white leading-snug">{{ $topic->name }}</h3>
                                <span class="text-[11px] whitespace-nowrap px-2 py-1 rounded-full bg-white/20 text-blue-100">
                                    {{ optional($topic->created_at)->format('d/m/Y') }}
                                </span>
                            </div>
                            <p class="text-blue-100 text-sm leading-relaxed">
                                {{ $topic->description ?: 'Nenhuma descrição disponível.' }}
                            </p>
                            <div class="mt-4 text-right">
                                <span class="text-white font-semibold text-sm group-hover:underline">Abrir tópico &rarr;</span>
                            </div>
                        </Link>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $topics->onEachSide(1)->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <h3 class="mt-2 text-2xl font-semibold text-gray-900">Nenhum tópico nesta categoria</h3>
                    <p class="mt-1 text-base text-gray-500">Cadastre tópicos e associe a esta categoria no painel de administração.</p>
                </div>
            @endif
        </div>
    </div>
</x-layout>
