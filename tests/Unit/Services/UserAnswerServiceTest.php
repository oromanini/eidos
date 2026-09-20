<?php

namespace Tests\Unit\Services;

use App\Models\UserAnswer;
use App\Repositories\UserAnswerRepository;
use App\Services\UserAnswerService;
use Mockery;
use Tests\TestCase;

class UserAnswerServiceTest extends TestCase
{
    public function test_it_delegates_the_complete_answer_payload_to_the_repository(): void
    {
        $answer = new UserAnswer;
        $repository = Mockery::mock(UserAnswerRepository::class);
        $repository
            ->shouldReceive('create')
            ->once()
            ->with([
                'user_id' => 'user-1',
                'question_id' => 'question-1',
                'user_answer' => 'c',
                'is_correct' => false,
            ])
            ->andReturn($answer);

        $result = (new UserAnswerService($repository))->saveUserAnswer(
            'user-1',
            'question-1',
            'c',
            false
        );

        $this->assertSame($answer, $result);
    }
}
