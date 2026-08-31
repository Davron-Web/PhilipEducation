<?php

namespace App\Http\Controllers\Public\Ielts;

use App\Http\Controllers\Controller;
use App\Models\Ielts\IeltsPassage;
use App\Models\Ielts\IeltsSpeakingCard;
use App\Models\Ielts\IeltsTask;
use Illuminate\View\View;

class IeltsHubController extends Controller
{
    /**
     * Точка входа в раздел IELTS: 4 карточки по навыкам с числом
     * доступных заданий в каждом.
     */
    public function index(): View
    {
        $counts = [
            'writing' => IeltsTask::count(),
            'reading' => IeltsPassage::where('skill', 'reading')->count(),
            'listening' => IeltsPassage::where('skill', 'listening')->count(),
            'speaking' => IeltsSpeakingCard::count(),
        ];

        return view('public.ielts.hub', compact('counts'));
    }
}
