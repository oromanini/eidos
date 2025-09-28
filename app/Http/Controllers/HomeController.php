<?php

namespace App\Http\Controllers;

use App\Repositories\TopicRepository;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(TopicRepository $topicRepository): View
    {
        $topics = $topicRepository->all();

        return view('home', [
            'topics' => $topics,
        ]);
    }
}
