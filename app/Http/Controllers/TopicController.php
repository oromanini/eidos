<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\View\View;

class TopicController extends Controller
{
    public function index(): View
    {
        return view('topics.index');
    }

    public function show(Topic $topic): View
    {
        return view('topics.show', compact('topic'));
    }
}
