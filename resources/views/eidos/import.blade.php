<x-layout>
    <div class="bg-gray-50 min-h-screen flex items-center justify-center">
        <div class="container max-w-xl mx-auto px-4">
            <div class="bg-white rounded-xl shadow-lg p-12">
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-bold text-gray-800">Importar Perguntas</h1>
                    <p class="text-gray-500 mt-2">Envie um arquivo no formato CSV para adicionar novos tópicos e perguntas.</p>
                </div>

                <div class="mb-8 rounded-xl border border-blue-200 bg-blue-50 p-5">
                    <h2 class="font-semibold text-blue-950">Precisa de um arquivo de exemplo?</h2>
                    <p class="mt-1 text-sm leading-6 text-blue-800">
                        Baixe o modelo com os cabeçalhos aceitos pelo EIDOS, preencha suas perguntas e importe o arquivo nesta página.
                    </p>
                    <a href="{{ asset('downloads/modelo-importacao-eidos.csv') }}"
                       download="modelo-importacao-eidos.csv"
                       class="mt-4 inline-flex items-center rounded-lg border border-blue-600 bg-white px-4 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-100">
                        ↓ Baixar modelo CSV
                    </a>
                </div>

                <x-splade-form :action="route('eidos.import')" data-loading-text="Importando perguntas..." class="space-y-8">

                    <x-splade-select
                        name="category_id"
                        label="Categoria do novo tópico"
                        :options="$categories->mapWithKeys(fn ($category) => [(string) $category->getKey() => $category->name])->all()"
                        placeholder="Selecione uma categoria"
                        choices
                    />

                    <x-splade-file name="csv_file" label="Selecione seu arquivo CSV" />

                    <x-splade-submit
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-all duration-300"
                        label="Importar Agora"
                    />
                </x-splade-form>

                <div class="text-center mt-8">
                    <Link href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 hover:underline">
                    &larr; Voltar para a Home
                    </Link>
                </div>
            </div>
        </div>
    </div>
</x-layout>
