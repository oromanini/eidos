<x-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gerenciamento de Usuários') }}
            </h2>
            <a href="{{ route('admin.topics.index') }}"
               class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                Gerenciar Tópicos
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-splade-table :for="$users">
                @cell('status', $user)
                @if ($user->approved_at)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Aprovado
                        </span>
                @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Pendente
                        </span>
                @endif
                @endcell

                @cell('actions', $user)
                @if (is_null($user->approved_at))
                    <x-splade-form :action="route('admin.users.approve', $user)" method="post">
                        <x-splade-submit label="Aprovar" />
                    </x-splade-form>
                @else
                    -
                @endif
                @endcell
            </x-splade-table>
        </div>
    </div>
</x-layout>
