<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Models\Billing\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Счета только просматривают: выписанный документ не редактируют. */
class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $invoices = Invoice::with('user')
            ->when($request->input('search'), fn ($q, $search) => $q
                ->where('number', 'like', "%{$search}%")
                ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$search}%")))
            ->latest('issued_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.billing.invoices.index', [
            'invoices' => $invoices,
            'total' => (int) Invoice::where('status', Invoice::STATUS_PAID)->sum('amount_minor'),
        ]);
    }

    public function show(Invoice $invoice): View
    {
        return view('admin.billing.invoices.show', [
            'invoice' => $invoice->load(['user', 'payment', 'subscription']),
        ]);
    }
}
