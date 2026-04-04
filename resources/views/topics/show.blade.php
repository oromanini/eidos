<x-layout>
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
                        <button data-tab="resumo" class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold text-gray-600" role="tab" type="button">Resumo</button>
                        <button data-tab="infograficos" class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold text-gray-600" role="tab" type="button">Infográficos</button>
                        <button data-tab="audios" class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold text-gray-600" role="tab" type="button">Áudios</button>
                        <button data-tab="videos" class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold text-gray-600" role="tab" type="button">Vídeos</button>
                        <button data-tab="questoes" class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold text-gray-600" role="tab" type="button">Múltipla escolha</button>
                        <button data-tab="abertas-ia" class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold text-gray-600" role="tab" type="button">Questões abertas IA</button>
                        <button data-tab="pergunte-ia" class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold text-gray-600" role="tab" type="button">Pergunte à IA</button>
                    </div>
                </div>

                <div class="mt-6" id="topic-panels">
                    <section data-panel="resumo" class="tab-panel space-y-6">
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 md:p-6">
                            <h2 class="text-xl font-bold text-gray-900">Sumário</h2>
                            @if(!empty($summarySections))
                                <ul class="mt-3 space-y-2 list-decimal list-inside text-blue-700">
                                    @foreach($summarySections as $section)
                                        <li>
                                            <a href="#{{ $section['id'] }}" class="hover:underline">{{ $section['title'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-600 mt-2">Ainda não há resumo estruturado. Adicione conteúdo ao tópico para gerar um índice dinâmico.</p>
                            @endif
                        </div>

                        <div class="space-y-4">
                            @forelse($summarySections as $section)
                                <article id="{{ $section['id'] }}" class="scroll-mt-24 bg-white border border-gray-200 rounded-xl p-4 md:p-6">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $section['title'] }}</h3>
                                    <p class="text-gray-700 whitespace-pre-line mt-2">{{ $section['body'] }}</p>
                                </article>
                            @empty
                                <article class="bg-white border border-gray-200 rounded-xl p-4 md:p-6">
                                    <h3 class="text-lg font-bold text-gray-900">Resumo do tópico</h3>
                                    <p class="text-gray-700 mt-2">{{ $topic->description ?: 'Conteúdo em breve.' }}</p>
                                </article>
                            @endforelse
                        </div>
                    </section>

                    <section data-panel="infograficos" class="tab-panel hidden">
                        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                            <h3 class="text-xl font-semibold text-gray-900">Infográficos</h3>
                            <p class="text-gray-600 mt-2">Sem arquivos por enquanto. Esta aba receberá os materiais hospedados no Cloudflare R2.</p>
                        </div>
                    </section>

                    <section data-panel="audios" class="tab-panel hidden">
                        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                            <h3 class="text-xl font-semibold text-gray-900">Áudios</h3>
                            <p class="text-gray-600 mt-2">Sem arquivos por enquanto. Esta aba receberá os materiais hospedados no Cloudflare R2.</p>
                        </div>
                    </section>

                    <section data-panel="videos" class="tab-panel hidden">
                        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                            <h3 class="text-xl font-semibold text-gray-900">Vídeos</h3>
                            <p class="text-gray-600 mt-2">Sem arquivos por enquanto. Esta aba receberá os materiais hospedados no Cloudflare R2.</p>
                        </div>
                    </section>

                    <section data-panel="questoes" class="tab-panel hidden">
                        <div class="rounded-xl border border-gray-200 bg-white p-6 md:p-8 text-center">
                            <h3 class="text-xl font-semibold text-gray-900">Questões de múltipla escolha</h3>
                            <p class="text-gray-600 mt-2">Treine com questões objetivas já disponíveis para este tópico.</p>
                            <div class="mt-6">
                                <Link href="{{ route('quiz.start', $topic) }}"
                                      class="inline-block bg-blue-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-blue-700 transition-all text-lg transform hover:scale-105">
                                    🚀 Iniciar Quiz
                                </Link>
                            </div>
                        </div>
                    </section>

                    <section data-panel="abertas-ia" class="tab-panel hidden">
                        <div class="rounded-xl border border-gray-200 bg-white p-4 md:p-6">
                            <h3 class="text-xl font-semibold text-gray-900">Questões abertas com IA</h3>
                            <p class="text-gray-600 mt-2">Layout preparado. A correção com IA será integrada em uma próxima PR.</p>
                            <div class="mt-4 space-y-3">
                                <textarea rows="4" disabled class="w-full rounded-lg border-gray-300 bg-gray-50" placeholder="Digite sua resposta aberta..."></textarea>
                                <button disabled class="w-full md:w-auto px-4 py-2 rounded-lg bg-gray-200 text-gray-500 font-semibold">Enviar para avaliação (em breve)</button>
                            </div>
                        </div>
                    </section>

                    <section data-panel="pergunte-ia" class="tab-panel hidden">
                        <div class="rounded-xl border border-gray-200 bg-white p-4 md:p-6">
                            <h3 class="text-xl font-semibold text-gray-900">Pergunte à IA</h3>
                            <p class="text-gray-600 mt-2">Interface de chat pronta. Integraremos o Groq em uma próxima PR.</p>

                            <div class="mt-5 border border-gray-200 rounded-xl overflow-hidden">
                                <div class="h-64 md:h-80 bg-gray-50 p-4 space-y-3 overflow-y-auto">
                                    <div class="max-w-[85%] rounded-xl bg-blue-100 text-blue-900 p-3">Olá! Em breve eu vou responder suas dúvidas sobre este tópico.</div>
                                    <div class="max-w-[85%] ml-auto rounded-xl bg-gray-200 text-gray-800 p-3">Perfeito, vou estudar por aqui 👋</div>
                                </div>
                                <div class="border-t border-gray-200 p-3 flex gap-2">
                                    <input disabled type="text" class="flex-1 rounded-lg border-gray-300 bg-gray-100" placeholder="Digite sua pergunta..." />
                                    <button disabled class="px-4 py-2 rounded-lg bg-gray-200 text-gray-500 font-semibold">Enviar</button>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabButtons = Array.from(document.querySelectorAll('#topic-tabs .tab-btn'));
            const panels = Array.from(document.querySelectorAll('#topic-panels .tab-panel'));
            const panelContainer = document.getElementById('topic-panels');
            const tabOrder = tabButtons.map((button) => button.dataset.tab);
            let currentTab = tabOrder[0];

            const setActiveTab = (tabName) => {
                currentTab = tabName;

                tabButtons.forEach((button) => {
                    const isActive = button.dataset.tab === tabName;
                    button.classList.toggle('bg-white', isActive);
                    button.classList.toggle('text-blue-700', isActive);
                    button.classList.toggle('shadow-sm', isActive);
                    button.classList.toggle('text-gray-600', !isActive);
                });

                panels.forEach((panel) => {
                    panel.classList.toggle('hidden', panel.dataset.panel !== tabName);
                });
            };

            tabButtons.forEach((button) => {
                button.addEventListener('click', () => setActiveTab(button.dataset.tab));
            });

            let touchStartX = 0;
            let touchEndX = 0;

            panelContainer?.addEventListener('touchstart', (event) => {
                touchStartX = event.changedTouches[0].screenX;
            }, { passive: true });

            panelContainer?.addEventListener('touchend', (event) => {
                touchEndX = event.changedTouches[0].screenX;
                const swipeDistance = touchStartX - touchEndX;

                if (Math.abs(swipeDistance) < 50) {
                    return;
                }

                const currentIndex = tabOrder.indexOf(currentTab);
                if (swipeDistance > 0 && currentIndex < tabOrder.length - 1) {
                    setActiveTab(tabOrder[currentIndex + 1]);
                }

                if (swipeDistance < 0 && currentIndex > 0) {
                    setActiveTab(tabOrder[currentIndex - 1]);
                }
            }, { passive: true });

            setActiveTab(currentTab);
        });
    </script>
</x-layout>
