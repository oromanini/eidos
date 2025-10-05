<?php

namespace Tests\Feature;

use App\Repositories\TopicRepository;
use App\Services\TopicService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopicServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TopicService $topicService;

    protected function setUp(): void
    {
        parent::setUp();

        // O repositório agora interage com o banco de dados
        $this->topicService = new TopicService(new TopicRepository);
    }

    public function test_it_can_create_a_topic(): void
    {
        $this->topicService->createTopic('História', 'Um tema sobre a história mundial.');

        // Verifica se o registro foi criado no banco de dados
        $this->assertDatabaseHas('topics', [
            'name' => 'História',
        ]);
    }
}
