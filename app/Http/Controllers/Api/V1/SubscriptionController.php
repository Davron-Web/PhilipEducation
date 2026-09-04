<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\SubscriptionResource;
use App\Models\Billing\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /** Действующая подписка или null, если её нет. */
    public function current(Request $request): JsonResponse
    {
        $subscription = $request->user()->activeSubscription();

        return response()->json([
            'data' => $subscription
                ? new SubscriptionResource($subscription->load('plan'))
                : null,
        ]);
    }

    public function plans(): JsonResponse
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return response()->json([
            'data' => $plans->map(fn (Plan $plan) => [
                'code' => $plan->code,
                'name' => $plan->name,
                'duration_days' => $plan->duration_days,
                // Цена в основных единицах, а не в дирамах: клиенту не
                // должен быть виден способ хранения.
                'price' => round($plan->price_minor / 100, 2),
                'currency' => $plan->currency,
            ]),
        ]);
    }
}
