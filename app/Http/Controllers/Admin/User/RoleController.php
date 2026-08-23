<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreRoleRequest;
use App\Http\Requests\Admin\User\UpdateRoleRequest;
use App\Models\User\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount('users')
            ->when(request('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('id')
            ->paginate(request('per_page', 20))
            ->withQueryString();

        return view('admin.user.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('admin.user.roles.create');
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        Role::create($request->validated());

        return redirect()
            ->route('admin.user.roles.index')
            ->with('success', 'Role created successfully');
    }

    public function show(Role $role): View
    {
        return view('admin.user.roles.show', [
            'role' => $role->load('users'),
        ]);
    }

    public function edit(Role $role): View
    {
        return view('admin.user.roles.edit', compact('role'));
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $role->update($request->validated());

        return redirect()
            ->route('admin.user.roles.index')
            ->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->users()->exists()) {
            return redirect()
                ->route('admin.user.roles.index')
                ->with('error', 'Cannot delete: this role is assigned to users');
        }

        try {
            $role->delete();

            return redirect()
                ->route('admin.user.roles.index')
                ->with('success', 'Role deleted successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
