<x-layout>
    <div class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-2xl bg-white rounded-xl shadow-lg p-8 text-center">

            <h1 class="text-3xl font-bold text-gray-800">Quiz Finalizado!</h1>
            <p class="text-lg text-gray-600 mt-2">Veja seu desempenho em <span class="font-semibold">{{ $history->topic->name }}</span></p>

            {{-- Animação de Troféu/Confete (Exemplo) --}}
            @if($history->percentage >= 80)
                <div class="text-6xl my-6 animate-bounce">🏆</div>
            @else
                <div class="text-6xl my-6">👍</div>
            @endif

            {{-- Grid de Resultados --}}
            <div class="grid grid-cols-2 gap-6 border-t border-b py-6 my-6 text-left">
                <div>
                    <div class="text-sm font-medium text-gray-500">ACERTOS</div>
                    <div class="text-2xl font-bold text-green-600">{{ $history->score }} / {{ $history->total_questions }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">ERROS</div>
                    <div class="text-2xl font-bold text-red-600">{{ $history->total_questions - $history->score }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">% DE ACERTO</div>
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($history->percentage, 0) }}%</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">DURAÇÃO</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $history->formatted_duration }}</div>
                </div>
            </div>

            <p class="text-gray-500">Você já completou este quiz <span class="font-bold text-gray-800">{{ $repetitionCount }}</span> vez(es).</p>

            {{-- Botões de Ação --}}
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <Link href="{{ route('quiz.start', ['topic' => $history->topic_id]) }}" class="w-full sm:w-auto text-center bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700 transition-all">
                Tentar Novamente
                </Link>
                <Link href="{{ route('home') }}" class="w-full sm:w-auto text-center bg-gray-200 text-gray-800 font-bold py-3 px-6 rounded-lg hover:bg-gray-300 transition-all">
                Voltar para a Home
                </Link>
            </div>

        </div>
    </div>
</x-layout>
