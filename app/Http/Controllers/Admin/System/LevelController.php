<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Models\System\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index()
    {
        return view('admin.system.levels.index', [
            'levels' => Level::orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:levels,name'],
            'code' => ['required', 'string', 'max:50', 'unique:levels,code'],
        ], [
            'name.required' => 'Укажите название уровня.',
            'name.unique' => 'Такой уровень уже существует.',
            'code.required' => 'Укажите код уровня (например, A1).',
            'code.unique' => 'Такой код уже существует.',
        ]);

        Level::create($data);

        return redirect()->route('admin.system.levels.index')
            ->with('success', 'Уровень добавлен!');
    }

    public function destroy(Level $level)
    {
        $level->delete();

        return redirect()->route('admin.system.levels.index')
            ->with('success', 'Уровень удалён.');
    }
}
