<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function toggle(Request $request)
    {
        $current = session('theme', 'dark');
        $new = $current === 'dark' ? 'light' : 'dark';
        session(['theme' => $new]);

        return back();
    }
}
