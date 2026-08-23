<?php

namespace App\Http\Controllers\Admin\Certificate;

use App\Http\Controllers\Controller;
use App\Models\Certificate\Certificate;
use App\Models\System\Level;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(): View
    {
        $certificates = Certificate::with(['user', 'level'])
            ->when(request('search'), function ($query, $search) {
                $query->where('certificate_number', 'like', "%{$search}%");
            })
            ->when(request('user_id'), function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.certificate.certificates.index', compact('certificates'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();
        $levels = Level::orderBy('name')->get();

        return view('admin.certificate.certificates.create', compact('users', 'levels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'level_id' => ['required', 'integer', 'exists:levels,id'],
            'certificate_number' => ['required', 'string', 'max:255', 'unique:certificates,certificate_number'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'issued_at' => ['required', 'date'],
        ]);

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('certificates', 'public');
        }
        unset($validated['file']);

        Certificate::create($validated);

        return redirect()
            ->route('admin.certificate.certificates.index')
            ->with('success', 'Сертификат создан');
    }

    public function show(Certificate $certificate): View
    {
        return view('admin.certificate.certificates.show', [
            'certificate' => $certificate->load(['user', 'level']),
        ]);
    }

    public function edit(Certificate $certificate): View
    {
        $users = User::orderBy('name')->get();
        $levels = Level::orderBy('name')->get();

        return view('admin.certificate.certificates.edit', compact('certificate', 'users', 'levels'));
    }

    public function update(Request $request, Certificate $certificate): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'level_id' => ['required', 'integer', 'exists:levels,id'],
            'certificate_number' => ['required', 'string', 'max:255', 'unique:certificates,certificate_number,'.$certificate->id],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'issued_at' => ['required', 'date'],
        ]);

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('certificates', 'public');
        }
        unset($validated['file']);

        $certificate->update($validated);

        return redirect()
            ->route('admin.certificate.certificates.index')
            ->with('success', 'Сертификат обновлён');
    }

    public function destroy(Certificate $certificate): RedirectResponse
    {
        $certificate->delete();

        return redirect()
            ->route('admin.certificate.certificates.index')
            ->with('success', 'Сертификат удалён');
    }
}
