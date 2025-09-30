<x-layout>
    <div class="bg-gray-50 min-h-screen flex items-center justify-center">
        <div class="container max-w-xl mx-auto px-4">
            <div class="bg-white rounded-xl shadow-lg p-12">
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-bold text-gray-800">Importar Perguntas</h1>
                    <p class="text-gray-500 mt-2">Envie um arquivo no formato CSV para adicionar novos tópicos e perguntas.</p>
                </div>

                <x-splade-form :action="route('eidos.import')" class="space-y-8">

                    {{-- COMPONENTE SIMPLIFICADO --}}
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
