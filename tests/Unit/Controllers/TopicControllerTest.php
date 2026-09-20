<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\TopicController;
use App\Models\Infographic;
use App\Models\Topic;
use App\Repositories\InfographicRepository;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class TopicControllerTest extends TestCase
{
    public function test_store_infographic_accepts_an_svg_file(): void
    {
        Storage::fake('public');

        $topic = new Topic;
        $topic->setAttribute('id', 'topic-1');
        $file = UploadedFile::fake()->createWithContent(
            'arquitetura.svg',
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10"><circle cx="5" cy="5" r="4" /></svg>'
        );
        $request = Request::create(
            '/topics/topic-1/infographics',
            'POST',
            ['title' => 'Arquitetura'],
            [],
            ['file' => $file]
        );

        $repository = Mockery::mock(InfographicRepository::class);
        $repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $data): bool {
                return $data['topic_id'] === 'topic-1'
                    && $data['title'] === 'Arquitetura'
                    && $data['file_name'] === 'arquitetura.svg'
                    && $data['file_type'] === 'svg'
                    && str_ends_with($data['file_url'], '.svg')
                    && $data['file_size'] > 0;
            }))
            ->andReturn(new Infographic);

        $response = (new TopicController($repository))->storeInfographic($request, $topic);

        $this->assertSame(route('topics.show', [
            'topic' => $topic,
            'tab' => 'infograficos',
        ]), $response->getTargetUrl());

        $storedFiles = Storage::disk('public')->allFiles('topic-assets/topic-1/infographics');
        $this->assertCount(1, $storedFiles);
        $this->assertStringEndsWith('.svg', $storedFiles[0]);
    }
}
