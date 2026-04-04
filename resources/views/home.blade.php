<x-layout>
    <div class="bg-gray-50 min-h-screen">
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white">
            <div class="flex justify-center">
                <img src="{{ asset('images/eidos-dark-logo.png') }}" alt="Logo Eidos" class="w-1/6 mx-auto" style="width: 16.66%;">
            </div>
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-10 text-center">
                <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl md:text-6xl">
                    Escolha uma <span class="text-indigo-300">categoria</span>
                </h1>
                <p class="mt-4 text-lg text-indigo-100">Agora você seleciona primeiro a categoria e depois o tópico.</p>
            </div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 -mt-10">
            @if($categories->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($categories as $category)
                        <Link href="{{ route('categories.show', $category) }}" class="block p-8 bg-white rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300 ease-in-out group">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">{{ $category->name }}</h3>
                        <p class="text-gray-600 mb-4 h-12 overflow-hidden">{{ $category->description ?: 'Sem descrição para esta categoria.' }}</p>
                        <div class="mt-4 text-right">
                                <span class="text-blue-500 font-semibold text-lg">
                                    {{ $category->topics_count }} tópico(s) &rarr;
                                </span>
                        </div>
                        </Link>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <h3 class="mt-2 text-2xl font-semibold text-gray-900">Nenhuma categoria encontrada</h3>
                    <p class="mt-1 text-base text-gray-500">Cadastre uma categoria no painel de administração.</p>
                </div>
            @endif
        </div>
    </div>
</x-layout>
