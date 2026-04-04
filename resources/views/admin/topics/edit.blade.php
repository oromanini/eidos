<x-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin · Editar tópico') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div>
                <a href="{{ route('admin.topics.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                    ← Voltar para lista de tópicos
                </a>
            </div>

            <form method="POST" action="{{ route('admin.topics.update', $topic) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800">Dados do tópico</h3>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Título</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $topic->name) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>


                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Categoria</label>
                        <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $topic->category_id) == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                        <textarea id="description" name="description" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $topic->description) }}</textarea>
                        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach($topic->questions as $index => $question)
                        <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                            <h4 class="text-md font-semibold text-gray-800">Pergunta {{ $index + 1 }}</h4>

                            <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $question->id }}">

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Texto da pergunta</label>
                                <textarea name="questions[{{ $index }}][question_text]" rows="2"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old("questions.$index.question_text", $question->question_text) }}</textarea>
                                @error("questions.$index.question_text")<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach(['a', 'b', 'c', 'd'] as $option)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Alternativa {{ strtoupper($option) }}</label>
                                        <input type="text" name="questions[{{ $index }}][options][{{ $option }}]"
                                               value="{{ old("questions.$index.options.$option", $question->options[$option] ?? '') }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error("questions.$index.options.$option")<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                @endforeach
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Resposta correta</label>
                                <select name="questions[{{ $index }}][correct_answer]"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach(['a', 'b', 'c', 'd'] as $option)
                                        <option value="{{ $option }}" @selected(old("questions.$index.correct_answer", $question->correct_answer) === $option)>
                                            {{ strtoupper($option) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error("questions.$index.correct_answer")<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center rounded-md bg-green-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-green-700">
                        Salvar alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
