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
                                :disabled="data.isAnswered"
                                type="button"
                                class="w-full text-left p-4 rounded-lg border-2 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                :class="{
                'border-gray-300 hover:border-blue-500 hover:bg-blue-50': !data.isAnswered,
                'cursor-not-allowed text-gray-500': data.isAnswered,
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
                                  class="mt-4 inline-block bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 transition-transform hover:scale-105">
                            Continuar
                            </Link>
                        @else
                            <Link href="{{ route('quiz.finish', ['topic' => $topic]) }}" class="mt-4 inline-block bg-green-600 text-white font-bold py-3 px-8 rounded-lg text-lg shadow-lg hover:bg-green-700 transition-all duration-300 transform hover:scale-110 animate-pulse">
                            🏆 Ver Resultado!
                            </Link>
                        @endif
                    </div>
                </div>
            </x-splade-data>

        </div>
    </div>
</x-layout>
