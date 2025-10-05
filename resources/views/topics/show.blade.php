<x-layout>
    <div class="bg-gray-50 min-h-screen">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">

            {{-- Navegação "Breadcrumb" --}}
            <div class="mb-6">
                <Link href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                Home
                </Link>
                <span class="text-gray-400 mx-2">/</span>
                <span class="text-gray-600">{{ $topic->name }}</span>
            </div>

            {{-- Card Principal --}}
            <div class="bg-white rounded-xl shadow-lg p-8 md:p-12 text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800">{{ $topic->name }}</h1>

                <p class="text-lg text-gray-600 mt-4 max-w-3xl mx-auto">
                    {{ $topic->description ?: 'Prepare-se para testar seus conhecimentos neste tópico. Quando estiver pronto, clique no botão abaixo para começar!' }}
                </p>

                <div class="mt-8">
                    <Link href="{{ route('quiz.start', $topic) }}"
                          class="inline-block bg-blue-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-blue-700 transition-all text-lg transform hover:scale-105">
                    🚀 Iniciar Quiz!
                    </Link>
                </div>
            </div>

        </div>
    </div>
</x-layout>
