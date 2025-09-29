<x-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Aprovação de Usuários') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-splade-table :for="$users">
                {{-- Coluna customizada para mostrar o Status --}}
                @cell('status', $user)
                @if ($user->approved_at)
                    <span class="text-green-600 font-semibold">Aprovado</span>
                @else
                    <span class="text-orange-600 font-semibold">Pendente</span>
                @endif
                @endcell

                {{-- Coluna customizada para o botão de Ações --}}
                @cell('actions', $user)
                @if (is_null($user->approved_at))
                    <x-splade-form
                        action="{{ route('admin.users.approve', $user) }}"
                        method="post"
                    >
                        <x-splade-submit label="Aprovar" />
                    </x-splade-form>
                @endif
                @endcell
            </x-splade-table>
        </div>
    </div>
</x-layout>
