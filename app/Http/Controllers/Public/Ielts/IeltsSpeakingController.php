<?php

namespace App\Http\Controllers\Public\Ielts;

use App\Http\Controllers\Controller;
use App\Models\Ielts\IeltsSpeakingCard;
use Illuminate\View\View;

class IeltsSpeakingController extends Controller
{
    public function index(): View
    {
        $cards = IeltsSpeakingCard::orderBy('id')->get();

        return view('public.ielts.speaking.index', compact('cards'));
    }

    /**
     * Cue card + таймер подготовки/ответа. Без записи и распознавания речи —
     * инструмент для самостоятельной практики по формату экзамена.
     */
    public function show(IeltsSpeakingCard $card): View
    {
        return view('public.ielts.speaking.show', compact('card'));
    }
}
