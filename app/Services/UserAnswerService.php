<?php

namespace App\Services;

use App\Models\UserAnswer;
use App\Repositories\UserAnswerRepository;

class UserAnswerService
{
    protected UserAnswerRepository $userAnswerRepository;

    public function __construct(UserAnswerRepository $userAnswerRepository)
    {
        $this->userAnswerRepository = $userAnswerRepository;
    }

    /**
     * Salva a resposta de um usuário.
     */
    public function saveUserAnswer(int|string $userId, int|string $questionId, string $userAnswer, bool $isCorrect): UserAnswer
    {
        return $this->userAnswerRepository->create([
            'user_id' => $userId,
            'question_id' => $questionId,
            'user_answer' => $userAnswer,
            'is_correct' => $isCorrect,
        ]);
    }
}
