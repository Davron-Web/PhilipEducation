<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Billing\StorePlanRequest;
use App\Http\Requests\Admin\Billing\UpdatePlanRequest;
use App\Models\Billing\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::withCount('subscriptions')->orderBy('sort_order')->paginate(20);

        return view('admin.billing.plans.index', compact('plans'));
    }

    public function create(): View
    {
        return view('admin.billing.plans.create');
    }

    public function store(StorePlanRequest $request): RedirectResponse
    {
        Plan::create($this->toMinorUnits($request->validated()));

        return redirect()->route('admin.billing.plans.index')->with('success', 'Тариф создан');
    }

    public function edit(Plan $plan): View
    {
        return view('admin.billing.plans.edit', compact('plan'));
    }

    public function update(UpdatePlanRequest $request, Plan $plan): RedirectResponse
    {
        $plan->update($this->toMinorUnits($request->validated()));

        return redirect()->route('admin.billing.plans.index')->with('success', 'Тариф обновлён');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        // Тариф, по которому есть подписки, удалять нельзя: на него ссылаются
        // платежи и счета, и удаление обнулило бы историю оплат.
        if ($plan->subscriptions()->exists()) {
            return redirect()
                ->route('admin.billing.plans.index')
                ->with('error', 'Нельзя удалить: по тарифу есть подписки. Снимите галочку «активен», чтобы он исчез со страницы цен.');
        }

        $plan->delete();

        return redirect()->route('admin.billing.plans.index')->with('success', 'Тариф удалён');
    }

    /**
     * Цена в форме вводится в сомони, а хранится в дирамах.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function toMinorUnits(array $data): array
    {
        $data['price_minor'] = (int) round(((float) $data['price']) * 100);
        unset($data['price']);

        return $data;
    }
}
