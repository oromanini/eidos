<?php

namespace Tests\Unit\Services;

use App\Models\QuizHistory;
use App\Repositories\QuizHistoryRepository;
use App\Services\QuizHistoryService;
use Mockery;
use Tests\TestCase;

class QuizHistoryServiceTest extends TestCase
{
    public function test_it_delegates_the_quiz_result_to_the_repository(): void
    {
        $data = [
            'user_id' => 'user-1',
            'topic_id' => 'topic-1',
            'score' => 8,
            'total_questions' => 10,
            'percentage' => 80.0,
            'duration_in_seconds' => 95,
        ];
        $history = new QuizHistory;
        $repository = Mockery::mock(QuizHistoryRepository::class);
        $repository->shouldReceive('create')->once()->with($data)->andReturn($history);

        $result = (new QuizHistoryService($repository))->logQuizCompletion($data);

        $this->assertSame($history, $result);
    }
}
