<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;

class AssistantController extends Controller
{
    public function __construct(protected GeminiService $gemini) {}

    public function chat(Request $request)
    {
        $request->validate(['question' => 'required|string|max:2000']);

        try {
            $answer = $this->gemini->answerQuestion($request->question);
        } catch (\Throwable $e) {
            return response()->json(['answer' => 'Ошибка: '.$e->getMessage()], 500);
        }

        return response()->json(['answer' => $answer]);
    }
}
