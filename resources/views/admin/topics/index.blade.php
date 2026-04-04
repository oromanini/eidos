<x-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin · Tópicos') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Gerenciar tópicos</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Edite título, descrição, perguntas e respostas corretas de cada tópico.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tópico</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perguntas</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($topics as $topic)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $topic->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $topic->category->name ?? 'Assuntos gerais' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $topic->description ?: '—' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $topic->questions_count }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.topics.edit', $topic) }}"
                                           class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                            Editar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                        Nenhum tópico encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6">
                    {{ $topics->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
