<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductionResultRequest;
use App\Http\Resources\ProductionResultResource;
use App\Models\ProductionResult;
use App\Models\WorkOrder;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductionResultController extends Controller
{
    //
    public function store(StoreProductionResultRequest $request): JsonResponse
    {
        $data = $request->validated();
        $order = WorkOrder::findOrFail($data['wo_number']);

        $start = CarbonImmutable::parse(
            $data['production_date'].' '.$order->plan_start->format('H:i:s')
        );
        $plannedMinutes = intdiv(
            $order->plan_finish->getTimestamp() - $order->plan_start->getTimestamp(),
            60
        );
        $runtime = (int) ($data['runtime_minutes'] ?? max($plannedMinutes, 1));

        $achievement = $order->target_qty > 0
            ? round($data['qty_good'] / $order->target_qty * 100, 2)
            : 0;

        $result = ProductionResult::create([
            'wo_number' => $order->wo_number,
            'actual_start' => $start,
            'actual_finish' => $start->addMinutes($runtime),
            'runtime_minutes' => $runtime,
            'good_qty' => $data['qty_good'],
            'reject_qty' => $data['qty_reject'],
            'achievement' => $achievement,
        ]);

        return (new ProductionResultResource($result))
            ->additional(['message' => 'Production result submitted.'])
            ->response()
            ->setStatusCode(201);
    }
}
