<?php

namespace App\Http\Controllers;

use App\Models\Topic;
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

        $summarySections = $this->buildSummarySections($topic->description);

        return view('topics.show', [
            'topic' => $topic,
            'summarySections' => $summarySections,
        ]);
    }

    /**
     * @return array<int, array{id: string, title: string, body: string}>
     */
    private function buildSummarySections(?string $description): array
    {
        if (blank($description)) {
            return [];
        }

        $blocks = preg_split('/\n\s*\n/', trim($description)) ?: [];

        return collect($blocks)
            ->filter(fn (string $block): bool => filled(trim($block)))
            ->values()
            ->map(function (string $block, int $index): array {
                $lines = collect(preg_split('/\n/', trim($block)) ?: [])->values();
                $firstLine = trim((string) $lines->first());

                $hasHeading = Str::startsWith($firstLine, ['# ', '## ', '### ', '- ', '* ']);
                $title = $hasHeading
                    ? trim((string) preg_replace('/^(#\s*|##\s*|###\s*|-\s*|\*\s*)/', '', $firstLine))
                    : "Seção ".($index + 1);

                $bodyLines = $hasHeading ? $lines->slice(1) : $lines;
                $body = trim($bodyLines->implode("\n"));

                return [
                    'id' => 'section-'.($index + 1),
                    'title' => $title !== '' ? $title : "Seção ".($index + 1),
                    'body' => $body !== '' ? $body : trim($block),
                ];
            })
            ->all();
    }
}
