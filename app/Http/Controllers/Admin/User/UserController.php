<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('role')
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(request('role_id'), function ($query, $roleId) {
                $query->where('role_id', $roleId);
            })
            ->when(request('level_id'), function ($query, $levelId) {
                $query->where('level_id', $levelId);
            })
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.user.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.user.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()
            ->route('admin.user.users.index')
            ->with('success', 'users created successfully');
    }

    public function show(User $user): View
    {
        return view('admin.user.users.show', [
            'user' => $user->load(['role', 'level', 'achievements', 'progress', 'results']),
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.user.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.user.users.index')
            ->with('success', 'users updated successfully');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.user.users.index')
                ->with('error', 'Cannot delete yourself');
        }

        $user->delete();

        return redirect()
            ->route('admin.user.users.index')
            ->with('success', 'users deleted successfully');
    }
}
