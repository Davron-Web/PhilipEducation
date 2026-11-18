<?php

namespace App\Http\Controllers\Admin\Chat;

use App\Http\Controllers\Controller;
use App\Models\Chat\ChatReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $reports = ChatReport::with(['message.user', 'reporter'])
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.chat.reports.index', compact('reports'));
    }

    /** "Отклонить"/снять жалобу с рассмотрения — саму жалобу удаляем, сообщение не трогаем. */
    public function destroy(ChatReport $report): RedirectResponse
    {
        $report->delete();

        return redirect()
            ->route('admin.chat.reports.index')
            ->with('success', 'Жалоба снята с рассмотрения');
    }
}
