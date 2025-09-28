<x-layout>
    <div class="bg-gray-50 min-h-screen">
        {{-- Hero Section --}}
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white">
            <div class="flex justify-center">
                <img src="{{ asset('images/eidos-dark-logo.png') }}" alt="Logo Eidos" class="w-1/6 mx-auto">
            </div>
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-10 text-center">
                <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl md:text-6xl">
                    Bem-vindo ao <span class="text-indigo-300">Eidos</span>
                </h1>
                <br>
            </div>
        </div>

        {{-- Conteúdo Principal --}}
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 -mt-10">

            {{-- Grid de Tópicos ou Mensagem de Estado Vazio --}}
            @if($topics->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($topics as $topic)
                        {{-- Card do Tópico --}}
                        <Link href="{{ route('topics.show', $topic) }}" class="block p-8 bg-white rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300 ease-in-out group">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">{{ $topic->name }}</h3>
                        <p class="text-gray-600 mb-4 h-12 overflow-hidden">
                            {{ $topic->description ?: 'Nenhuma descrição disponível.' }}
                        </p>
                        <div class="mt-4 text-right">
                                <span class="text-blue-500 font-semibold text-lg">
                                    Iniciar Estudo &rarr;
                                </span>
                        </div>
                        </Link>
                    @endforeach
                </div>
            @else
                {{-- Mensagem de "Nenhum Tópico" Melhorada --}}
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    <h3 class="mt-2 text-2xl font-semibold text-gray-900">Nenhum Tópico Encontrado</h3>
                    <p class="mt-1 text-base text-gray-500">
                        Parece que ainda não há material de estudo. Que tal começar importando algumas perguntas?
                    </p>
                    <div class="mt-6">
                        <Link href="{{ route('eidos.import.form') }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all duration-200">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-11.25a.75.75 0 0 0-1.5 0v2.5h-2.5a.75.75 0 0 0 0 1.5h2.5v2.5a.75.75 0 0 0 1.5 0v-2.5h2.5a.75.75 0 0 0 0-1.5h-2.5v-2.5Z" clip-rule="evenodd" />
                        </svg>
                        Importar um arquivo CSV
                        </Link>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layout>
