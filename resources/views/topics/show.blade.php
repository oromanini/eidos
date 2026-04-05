<x-layout>
    <x-splade-data :default="[
        'activeTab' => request('tab', 'resumo'),
        'audioToDelete' => null,
        'videoToDelete' => null,
        'audioToEdit' => null,
        'videoToEdit' => null,
    ]">
        <div class="bg-gray-50 min-h-screen">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-10" id="topic-screen">
                <div class="mb-4 text-sm md:text-base">
                    <Link href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 transition-colors">Home</Link>
                    @if($topic->category)
                        <span class="text-gray-400 mx-2">/</span>
                        <Link href="{{ route('categories.show', $topic->category) }}" class="text-blue-600 hover:text-blue-800 transition-colors">{{ $topic->category->name }}</Link>
                    @endif
                    <span class="text-gray-400 mx-2">/</span>
                    <span class="text-gray-600">{{ $topic->name }}</span>
                </div>

                <div class="bg-white rounded-2xl shadow-md p-4 md:p-8">
                    <h1 class="text-2xl md:text-4xl font-bold text-gray-800">{{ $topic->name }}</h1>
                    <p class="text-gray-600 mt-3">{{ $topic->description ?: 'Explore os materiais deste tópico usando as abas abaixo.' }}</p>

                    <div class="mt-6 overflow-x-auto pb-2">
                        <div class="inline-flex w-max rounded-xl bg-gray-100 p-1 gap-1" id="topic-tabs" role="tablist">
                            @foreach(['resumo' => 'Resumo', 'infograficos' => 'Infográficos', 'audios' => 'Áudios', 'videos' => 'Vídeos', 'questoes' => 'Múltipla escolha', 'abertas-ia' => 'Questões abertas IA', 'pergunte-ia' => 'Pergunte à IA'] as $key => $label)
                                <button @click.prevent="data.activeTab = '{{ $key }}'"
                                        :class="data.activeTab === '{{ $key }}' ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-600'"
                                        class="px-4 py-2 rounded-lg text-sm font-semibold transition"
                                        type="button">{{ $label }}</button>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-6 space-y-6">
                        <section v-show="data.activeTab === 'resumo'" class="space-y-4" id="summary-tab">
                            <form method="POST" action="{{ route('topics.summary.update', $topic) }}" class="space-y-4 bg-blue-50 border border-blue-100 rounded-xl p-4">
                                @csrf
                                <h2 class="text-lg font-semibold text-gray-900">Resumo (editor interno + tabela de conteúdos)</h2>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Editor de resumo</label>
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" data-editor-command="bold" class="px-3 py-1.5 rounded-lg bg-white border border-gray-300 text-sm font-semibold text-gray-700">Negrito</button>
                                        <button type="button" data-editor-command="italic" class="px-3 py-1.5 rounded-lg bg-white border border-gray-300 text-sm font-semibold text-gray-700">Itálico</button>
                                        <button type="button" data-editor-command="underline" class="px-3 py-1.5 rounded-lg bg-white border border-gray-300 text-sm font-semibold text-gray-700">Sublinhado</button>
                                        <button type="button" data-editor-command="insertUnorderedList" class="px-3 py-1.5 rounded-lg bg-white border border-gray-300 text-sm font-semibold text-gray-700">Lista</button>
                                        <button type="button" data-editor-command="formatBlock" data-editor-value="h2" class="px-3 py-1.5 rounded-lg bg-white border border-gray-300 text-sm font-semibold text-gray-700">Título</button>
                                    </div>
                                    <div id="summary-editor" class="min-h-[240px] rounded-lg border border-gray-300 bg-white p-3 text-gray-800" contenteditable="true">{!! old('summary_html', $knowledge->summary_html ?? '') !!}</div>
                                    <textarea id="summary-html-input" name="summary_html" class="hidden">{{ old('summary_html', $knowledge->summary_html ?? '') }}</textarea>
                                </div>
                                <input name="summary_doc_url" value="{{ old('summary_doc_url', $knowledge->summary_doc_url) }}" class="w-full rounded-lg border-gray-300" placeholder="(Opcional) URL externa de apoio (Google Docs, etc.)" />
                                <textarea name="summary_toc_text" rows="5" class="w-full rounded-lg border-gray-300" placeholder="Formato: Título|Descrição (uma linha por seção)">@foreach($summarySections as $section){{ $section['title'] }}|{{ $section['body'] }}
@endforeach</textarea>
                                <textarea name="open_questions_json" rows="4" class="w-full rounded-lg border-gray-300" placeholder='Perguntas abertas (json)'>{{ old('open_questions_json', json_encode($knowledge->open_questions ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)) }}</textarea>
                                <button class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold">Salvar resumo</button>
                            </form>

                            <div class="bg-white border border-gray-200 rounded-xl p-4">
                                <h3 class="font-semibold text-gray-900">Sumário</h3>
                                <ul class="mt-2 list-decimal list-inside text-blue-700 space-y-1">
                                    @forelse($summarySections as $section)
                                        <li><a href="#{{ $section['id'] }}" class="hover:underline">{{ $section['title'] }}</a></li>
                                    @empty
                                        <li class="text-gray-500 list-none">Sem seções cadastradas.</li>
                                    @endforelse
                                </ul>
                            </div>

                            @if($knowledge->summary_html)
                                <article class="bg-white border border-gray-200 rounded-xl p-5 prose max-w-none">
                                    {!! $knowledge->summary_html !!}
                                </article>
                            @endif

                            @if($knowledge->summary_doc_embed_url)
                                <iframe src="{{ $knowledge->summary_doc_embed_url }}" class="w-full h-[520px] rounded-xl border border-gray-200" loading="lazy"></iframe>
                            @endif
                        </section>

                        <section v-show="data.activeTab === 'infograficos'" class="space-y-4">
                            <form method="POST" action="{{ route('topics.infographics.store', $topic) }}" enctype="multipart/form-data" class="grid md:grid-cols-3 gap-3 bg-white border border-gray-200 rounded-xl p-4">
                                @csrf
                                <input name="title" class="rounded-lg border-gray-300" placeholder="Título do infográfico" required>
                                <input name="file" type="file" accept=".pdf,.png" class="rounded-lg border-gray-300" required>
                                <button class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold">Upload</button>
                            </form>
                            <div class="bg-white border border-gray-200 rounded-xl p-4 overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead><tr class="text-left border-b"><th class="py-2">Título</th><th>Arquivo</th><th>Visualizar</th></tr></thead>
                                    <tbody>
                                        @forelse($infographics as $item)
                                            <tr class="border-b"><td class="py-2">{{ $item->title }}</td><td>{{ $item->file_name }}</td><td><a href="{{ $item->file_url }}" target="_blank" class="text-blue-600">Abrir</a></td></tr>
                                        @empty
                                            <tr><td colspan="3" class="py-3 text-gray-500">Nenhum infográfico enviado.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <section v-show="data.activeTab === 'audios'" class="space-y-4">
                            <form method="POST" action="{{ route('topics.audios.store', $topic) }}" enctype="multipart/form-data" class="grid md:grid-cols-4 gap-3 bg-white border border-gray-200 rounded-xl p-4">
                                @csrf
                                <input name="title" class="rounded-lg border-gray-300" placeholder="Título" required>
                                <input name="description" class="rounded-lg border-gray-300" placeholder="Descrição">
                                <input name="file" type="file" accept="audio/*" class="rounded-lg border-gray-300" required>
                                <button class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold">Adicionar áudio (máx 5MB)</button>
                            </form>

                            @forelse($audios as $audio)
                                <div class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $audio->title }}</h4>
                                        <p class="text-sm text-gray-600">{{ $audio->description }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <audio controls src="{{ $audio->file_url }}" class="max-w-56"></audio>
                                        <button type="button" @click="data.audioToEdit = '{{ $audio->id }}'" class="px-3 py-2 rounded bg-amber-100 text-amber-800 text-sm">Editar</button>
                                        <button type="button" @click="data.audioToDelete = '{{ $audio->id }}'" class="px-3 py-2 rounded bg-red-100 text-red-700 text-sm">Excluir</button>
                                    </div>
                                </div>

                                <div v-if="data.audioToEdit === '{{ $audio->id }}'" class="fixed inset-0 z-40 bg-black/40 flex items-center justify-center p-4">
                                    <div class="bg-white rounded-xl w-full max-w-lg p-5">
                                        <h4 class="font-semibold mb-3">Editar áudio</h4>
                                        <form method="POST" action="{{ route('topics.audios.update', [$topic, $audio]) }}" class="space-y-3">
                                            @csrf @method('PUT')
                                            <input name="title" value="{{ $audio->title }}" class="w-full rounded-lg border-gray-300" required>
                                            <textarea name="description" class="w-full rounded-lg border-gray-300">{{ $audio->description }}</textarea>
                                            <div class="flex justify-end gap-2"><button type="button" @click="data.audioToEdit = null" class="px-3 py-2 rounded bg-gray-200">Cancelar</button><button class="px-3 py-2 rounded bg-blue-600 text-white">Salvar</button></div>
                                        </form>
                                    </div>
                                </div>

                                <div v-if="data.audioToDelete === '{{ $audio->id }}'" class="fixed inset-0 z-40 bg-black/40 flex items-center justify-center p-4">
                                    <div class="bg-white rounded-xl w-full max-w-md p-5">
                                        <h4 class="font-semibold">Confirmar exclusão</h4>
                                        <p class="text-sm text-gray-600 mt-1">Deseja excluir o áudio <strong>{{ $audio->title }}</strong>?</p>
                                        <div class="flex justify-end gap-2 mt-4">
                                            <button type="button" @click="data.audioToDelete = null" class="px-3 py-2 rounded bg-gray-200">Cancelar</button>
                                            <form method="POST" action="{{ route('topics.audios.destroy', [$topic, $audio]) }}">@csrf @method('DELETE')<button class="px-3 py-2 rounded bg-red-600 text-white">Excluir</button></form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-gray-500">Nenhum áudio cadastrado.</div>
                            @endforelse
                        </section>

                        <section v-show="data.activeTab === 'videos'" class="space-y-4">
                            <form method="POST" action="{{ route('topics.videos.store', $topic) }}" class="grid md:grid-cols-4 gap-3 bg-white border border-gray-200 rounded-xl p-4">
                                @csrf
                                <input name="title" class="rounded-lg border-gray-300" placeholder="Título" required>
                                <input name="description" class="rounded-lg border-gray-300" placeholder="Descrição">
                                <input name="video_url" type="url" class="rounded-lg border-gray-300" placeholder="https://..." required>
                                <button class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold">Adicionar vídeo (link)</button>
                            </form>

                            @forelse($videos as $video)
                                <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center justify-between gap-3">
                                    <div>
                                        <h4 class="font-semibold">{{ $video->title }}</h4>
                                        <a href="{{ $video->video_url }}" target="_blank" class="text-blue-600 text-sm">{{ $video->video_url }}</a>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ $video->video_url }}" target="_blank" class="px-3 py-2 rounded bg-green-100 text-green-700 text-sm">Play</a>
                                        <button type="button" @click="data.videoToEdit = '{{ $video->id }}'" class="px-3 py-2 rounded bg-amber-100 text-amber-800 text-sm">Editar</button>
                                        <button type="button" @click="data.videoToDelete = '{{ $video->id }}'" class="px-3 py-2 rounded bg-red-100 text-red-700 text-sm">Excluir</button>
                                    </div>
                                </div>

                                <div v-if="data.videoToEdit === '{{ $video->id }}'" class="fixed inset-0 z-40 bg-black/40 flex items-center justify-center p-4">
                                    <div class="bg-white rounded-xl w-full max-w-lg p-5">
                                        <h4 class="font-semibold mb-3">Editar vídeo</h4>
                                        <form method="POST" action="{{ route('topics.videos.update', [$topic, $video]) }}" class="space-y-3">
                                            @csrf @method('PUT')
                                            <input name="title" value="{{ $video->title }}" class="w-full rounded-lg border-gray-300" required>
                                            <input name="description" value="{{ $video->description }}" class="w-full rounded-lg border-gray-300">
                                            <input name="video_url" type="url" value="{{ $video->video_url }}" class="w-full rounded-lg border-gray-300" required>
                                            <div class="flex justify-end gap-2"><button type="button" @click="data.videoToEdit = null" class="px-3 py-2 rounded bg-gray-200">Cancelar</button><button class="px-3 py-2 rounded bg-blue-600 text-white">Salvar</button></div>
                                        </form>
                                    </div>
                                </div>

                                <div v-if="data.videoToDelete === '{{ $video->id }}'" class="fixed inset-0 z-40 bg-black/40 flex items-center justify-center p-4">
                                    <div class="bg-white rounded-xl w-full max-w-md p-5">
                                        <h4 class="font-semibold">Confirmar exclusão</h4>
                                        <div class="flex justify-end gap-2 mt-4">
                                            <button type="button" @click="data.videoToDelete = null" class="px-3 py-2 rounded bg-gray-200">Cancelar</button>
                                            <form method="POST" action="{{ route('topics.videos.destroy', [$topic, $video]) }}">@csrf @method('DELETE')<button class="px-3 py-2 rounded bg-red-600 text-white">Excluir</button></form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-gray-500">Nenhum vídeo cadastrado.</div>
                            @endforelse
                        </section>

                        <section v-show="data.activeTab === 'questoes'" class="tab-panel">
                            <div class="rounded-xl border border-gray-200 bg-white p-6 md:p-8 text-center">
                                <h3 class="text-xl font-semibold text-gray-900">Questões de múltipla escolha</h3>
                                <p class="text-gray-600 mt-2">Treine com questões objetivas já disponíveis para este tópico.</p>
                                <div class="mt-6"><Link href="{{ route('quiz.start', $topic) }}" class="inline-block bg-blue-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-blue-700">🚀 Iniciar Quiz</Link></div>
                            </div>
                        </section>

                        <section v-show="data.activeTab === 'abertas-ia'" class="rounded-xl border border-gray-200 bg-white p-4 md:p-6">
                            <h3 class="text-xl font-semibold text-gray-900">Questões abertas com IA</h3>
                            <p class="text-gray-600 mt-2">Aba criada. Implementação será feita em próxima PR.</p>
                        </section>

                        <section v-show="data.activeTab === 'pergunte-ia'" class="rounded-xl border border-gray-200 bg-white p-4 md:p-6">
                            <h3 class="text-xl font-semibold text-gray-900">Pergunte à IA</h3>
                            <div class="mt-4 border rounded-xl p-4 h-72 bg-gray-50">Chat placeholder pronto para integração futura.</div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </x-splade-data>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const summaryTab = document.getElementById('summary-tab');
            if (!summaryTab) {
                return;
            }

            const editor = summaryTab.querySelector('#summary-editor');
            const hiddenInput = summaryTab.querySelector('#summary-html-input');
            const form = summaryTab.querySelector('form');
            const commandButtons = summaryTab.querySelectorAll('[data-editor-command]');

            if (!editor || !hiddenInput || !form) {
                return;
            }

            const syncEditorHtml = () => {
                hiddenInput.value = editor.innerHTML.trim();
            };

            commandButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const command = button.getAttribute('data-editor-command');
                    const value = button.getAttribute('data-editor-value');

                    if (!command) {
                        return;
                    }

                    document.execCommand(command, false, value ?? '');
                    syncEditorHtml();
                    editor.focus();
                });
            });

            editor.addEventListener('input', syncEditorHtml);
            form.addEventListener('submit', syncEditorHtml);
        });
    </script>
</x-layout>
