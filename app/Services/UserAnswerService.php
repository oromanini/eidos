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
     *
     * @param int $userId
     * @param int $questionId
     * @param string $userAnswer
     * @param bool $isCorrect
     * @return UserAnswer
     */
    public function saveUserAnswer(int $userId, int $questionId, string $userAnswer, bool $isCorrect): UserAnswer
    {
        return $this->userAnswerRepository->create([
            'user_id' => $userId,
            'question_id' => $questionId,
            'user_answer' => $userAnswer,
            'is_correct' => $isCorrect,
        ]);
    }
}
