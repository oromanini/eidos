<x-layout>
    <div class="bg-gray-50 min-h-screen flex flex-col items-center justify-center p-4 antialiased">
        <div class="w-full max-w-2xl">
            {{-- Header do Quiz --}}
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-700">{{ $topic->name }}</h1>
                <div class="w-full bg-gray-300 rounded-full h-2.5 mt-2">
                    <div class="bg-blue-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ ($questionNumber / $totalQuestions) * 100 }}%"></div>
                </div>
                <p class="text-sm text-gray-500 mt-1">{{ $questionNumber }} de {{ $totalQuestions }}</p>
            </div>

            <x-splade-data default="{
                userAnswer: null,
                isAnswered: false,
                wasCorrect: false,
                correctAnswer: ''
            }">
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <h2 class="text-xl font-semibold mb-6 text-gray-800">{{ $question->question_text }}</h2>

                    <x-splade-form
                        :action="route('quiz.answer', $question)"
                        @success="(response, form) => {
                        console.log('Resposta do servidor:', response);
                        data.isAnswered = true;
                        data.wasCorrect = response.data.is_correct; // ✅ pega do data
                        data.correctAnswer = response.data.correct_answer; // ✅ pega do data
                        data.userAnswer = form.answer;
}"
                        stay
                        class="space-y-4"
                    >
                        <div v-for="(optionText, key) in @js($question->options)">
                            <button
                                @click.prevent="form.answer = key; form.submit()"
                                :disabled="form.processing || data.isAnswered"
                                :aria-busy="form.processing && form.answer === key"
                                data-loading-text="Verificando resposta..."
                                type="button"
                                class="flex w-full items-center text-left p-4 rounded-lg border-2 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                :class="{
                'border-gray-300 hover:border-blue-500 hover:bg-blue-50': !data.isAnswered,
                'cursor-not-allowed text-gray-500': data.isAnswered,
                'cursor-wait opacity-70': form.processing,
                'border-green-500 bg-green-100 font-bold': data.isAnswered && key === data.correctAnswer,
                'border-red-500 bg-red-100': data.isAnswered && key === data.userAnswer && !data.wasCorrect
            }"
                            >
            <span
                class="font-bold mr-3 py-1 px-3 rounded-md"
                :class="data.isAnswered && key === data.correctAnswer
                    ? 'bg-green-500 text-white'
                    : 'bg-gray-200 text-gray-700'"
            >
                @{{ key.toUpperCase() }}
            </span>
                                @{{ optionText }}
                                <svg v-if="form.processing && form.answer === key" class="ml-auto h-5 w-5 animate-spin text-blue-600 motion-reduce:animate-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </div>
                    </x-splade-form>

                    {{-- Painel de Feedback e Botão "Continuar" --}}
                    <div v-if="data.isAnswered"
                         class="mt-6 p-4 rounded-lg text-center"
                         :class="{ 'bg-green-100 text-green-800': data.wasCorrect, 'bg-red-100 text-red-800': !data.wasCorrect }"
                    >
                        <h3 class="text-lg font-bold" v-text="data.wasCorrect ? 'Correto!' : 'Ops, não foi dessa vez!'"></h3>

                        @if($questionNumber < $totalQuestions)
                            <Link href="{{ route('quiz.question', ['topic' => $topic, 'questionNumber' => $questionNumber + 1]) }}"
                                  data-loading-text="Carregando próxima pergunta..."
                                  class="mt-4 inline-block bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 transition-transform hover:scale-105">
                            Continuar
                            </Link>
                        @else
                            <Link href="{{ route('quiz.finish', ['topic' => $topic]) }}" data-loading-text="Calculando resultado..." class="mt-4 inline-block bg-green-600 text-white font-bold py-3 px-8 rounded-lg text-lg shadow-lg hover:bg-green-700 transition-all duration-300 transform hover:scale-110 animate-pulse">
                            🏆 Ver Resultado!
                            </Link>
                        @endif
                    </div>
                </div>
            </x-splade-data>

        </div>
    </div>
</x-layout>
