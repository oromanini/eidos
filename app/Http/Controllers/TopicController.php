<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\Infographic;
use App\Models\Knowledge;
use App\Models\Topic;
use App\Models\Video;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TopicController extends Controller
{
    public function index(): View
    {
        return view('topics.index');
    }

    public function show(Topic $topic): View
    {
        $topic->load('category:id,name');

        $knowledge = Knowledge::query()->firstOrCreate(
            ['topic_id' => $topic->id],
            [
                'summary_toc' => [],
                'open_questions' => [],
            ]
        );

        $summarySections = collect($knowledge->summary_toc ?? [])->filter(fn ($item) => is_array($item))->values()->all();

        return view('topics.show', [
            'topic' => $topic,
            'knowledge' => $knowledge,
            'summarySections' => $summarySections,
            'infographics' => Infographic::query()->where('topic_id', $topic->id)->latest()->get(),
            'audios' => Audio::query()->where('topic_id', $topic->id)->latest()->get(),
            'videos' => Video::query()->where('topic_id', $topic->id)->latest()->get(),
        ]);
    }

    public function updateSummary(Request $request, Topic $topic): RedirectResponse
    {
        $data = $request->validate([
            'summary_doc_url' => ['nullable', 'url'],
            'summary_html' => ['nullable', 'string'],
            'summary_toc_text' => ['nullable', 'string'],
            'open_questions_json' => ['nullable', 'string'],
        ]);

        $summaryToc = $this->parseSummaryToc((string) ($data['summary_toc_text'] ?? ''));
        $openQuestions = $this->parseOpenQuestions((string) ($data['open_questions_json'] ?? ''));
        $summaryDocUrl = (string) ($data['summary_doc_url'] ?? '');
        $summaryHtml = $this->sanitizeSummaryHtml((string) ($data['summary_html'] ?? ''));

        Knowledge::query()->updateOrCreate(
            ['topic_id' => $topic->id],
            [
                'summary_doc_url' => $summaryDocUrl !== '' ? $summaryDocUrl : null,
                'summary_doc_embed_url' => $summaryDocUrl !== '' ? $this->toGoogleEmbedUrl($summaryDocUrl) : null,
                'summary_html' => $summaryHtml !== '' ? $summaryHtml : null,
                'summary_toc' => $summaryToc,
                'open_questions' => $openQuestions,
            ]
        );

        return redirect()
            ->route('topics.show', ['topic' => $topic, 'tab' => 'resumo'])
            ->with('status', 'Resumo atualizado com sucesso.');
    }

    public function storeInfographic(Request $request, Topic $topic): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'file' => ['required', 'file', 'mimes:pdf,png', 'max:5120'],
        ]);

        $storedPath = $data['file']->store("topic-assets/{$topic->id}/infographics", 'public');

        Infographic::query()->create([
            'topic_id' => $topic->id,
            'title' => $data['title'],
            'file_name' => $data['file']->getClientOriginalName(),
            'file_url' => Storage::disk('public')->url($storedPath),
            'file_type' => $data['file']->getClientOriginalExtension(),
            'file_size' => $data['file']->getSize(),
        ]);

        return redirect()->route('topics.show', ['topic' => $topic, 'tab' => 'infograficos']);
    }

    public function storeAudio(Request $request, Topic $topic): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'file' => ['required', 'file', 'mimetypes:audio/mpeg,audio/wav,audio/x-wav,audio/mp4,audio/aac,audio/ogg', 'max:5120'],
        ]);

        $storedPath = $data['file']->store("topic-assets/{$topic->id}/audios", 'public');

        Audio::query()->create([
            'topic_id' => $topic->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'file_name' => $data['file']->getClientOriginalName(),
            'file_url' => Storage::disk('public')->url($storedPath),
            'file_size' => $data['file']->getSize(),
        ]);

        return redirect()->route('topics.show', ['topic' => $topic, 'tab' => 'audios']);
    }

    public function updateAudio(Request $request, Topic $topic, Audio $audio): RedirectResponse
    {
        abort_if((string) $audio->topic_id !== (string) $topic->id, 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $audio->update($data);

        return redirect()->route('topics.show', ['topic' => $topic, 'tab' => 'audios']);
    }

    public function destroyAudio(Topic $topic, Audio $audio): RedirectResponse
    {
        abort_if((string) $audio->topic_id !== (string) $topic->id, 404);

        $audio->delete();

        return redirect()->route('topics.show', ['topic' => $topic, 'tab' => 'audios']);
    }

    public function storeVideo(Request $request, Topic $topic): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'video_url' => ['required', 'url', 'max:500'],
        ]);

        Video::query()->create([
            'topic_id' => $topic->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'video_url' => $data['video_url'],
        ]);

        return redirect()->route('topics.show', ['topic' => $topic, 'tab' => 'videos']);
    }

    public function updateVideo(Request $request, Topic $topic, Video $video): RedirectResponse
    {
        abort_if((string) $video->topic_id !== (string) $topic->id, 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'video_url' => ['required', 'url', 'max:500'],
        ]);

        $video->update($data);

        return redirect()->route('topics.show', ['topic' => $topic, 'tab' => 'videos']);
    }

    public function destroyVideo(Topic $topic, Video $video): RedirectResponse
    {
        abort_if((string) $video->topic_id !== (string) $topic->id, 404);

        $video->delete();

        return redirect()->route('topics.show', ['topic' => $topic, 'tab' => 'videos']);
    }

    private function toGoogleEmbedUrl(string $url): string
    {
        if (Str::contains($url, '/document/d/')) {
            preg_match('#/document/d/([^/]+)#', $url, $matches);
            if (! empty($matches[1])) {
                return "https://docs.google.com/document/d/{$matches[1]}/preview";
            }
        }

        return $url;
    }

    /**
     * @return array<int, array{id: string, title: string, body: string}>
     */
    private function parseSummaryToc(string $raw): array
    {
        return collect(preg_split('/\r\n|\r|\n/', trim($raw)) ?: [])
            ->filter(fn ($line): bool => filled(trim((string) $line)))
            ->values()
            ->map(function ($line, $index): array {
                [$title, $body] = array_pad(explode('|', (string) $line, 2), 2, '');

                $cleanTitle = trim($title) !== '' ? trim($title) : 'Seção '.($index + 1);

                return [
                    'id' => 'secao-'.($index + 1),
                    'title' => $cleanTitle,
                    'body' => trim($body),
                ];
            })->all();
    }

    /**
     * @return array<int, mixed>
     */
    private function parseOpenQuestions(string $raw): array
    {
        if (blank($raw)) {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function sanitizeSummaryHtml(string $html): string
    {
        $cleanHtml = strip_tags(
            $html,
            '<p><br><strong><b><em><i><u><ul><ol><li><h2><h3><h4><blockquote><a><span>'
        );

        return trim($cleanHtml);
    }
}
