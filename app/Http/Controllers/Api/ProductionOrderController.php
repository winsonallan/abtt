<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use App\Http\Requests\ListProductionOrdersRequest;
use App\Http\Resources\ProductionOrderResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductionOrderController extends Controller
{
    //
    public const SORTABLE = [
        'wo_number' => 'work_order.wo_number',
        'plan_start' => 'work_order.plan_start',
        'plan_finish' => 'work_order.plan_finish',
        'target_qty' => 'work_order.target_qty',
        'status' => 'work_order.status',
        'shift' => 'work_order.shift',
        'product' => 'p.product_name',
        'machine' => 'm.machine_name',
    ];

    public function index(ListProductionOrdersRequest $request): AnonymousResourceCollection
    {
        $f = $request->validated();

        $orders = WorkOrder::query()
            ->join('product as p', 'p.product_code', '=', 'work_order.product_code')
            ->join('machine as m', 'm.machine_code', '=', 'work_order.machine_code')
            ->join('employee as e', 'e.employee_no', '=', 'work_order.employee_no')
            ->select('work_order.*')
            ->with(['product', 'machine', 'employee'])
            ->withSum('results as good_qty', 'good_qty')
            ->withSum('results as reject_qty', 'reject_qty')
            ->when($f['search'] ?? null, function ($query, $term) {
                $like = '%'.addcslashes($term, '%_\\').'%';

                $query->where(fn ($q) => $q
                    ->where('work_order.wo_number', 'like', $like)
                    ->orWhere('p.product_name', 'like', $like)
                    ->orWhere('m.machine_name', 'like', $like)
                    ->orWhere('e.full_name', 'like', $like));
            })
            ->when($f['product'] ?? null, fn ($q, $v) => $q->where('work_order.product_code', $v))
            ->when($f['machine'] ?? null, fn ($q, $v) => $q->where('work_order.machine_code', $v))
            ->when($f['status'] ?? null, fn ($q, $v) => $q->where('work_order.status', $v))
            // date filters
            ->when($f['date'] ?? null, fn ($q, $d) => $q->whereBetween('work_order.plan_start', [
                "{$d} 00:00:00",
                "{$d} 23:59:59",
            ]))
            ->orderBy(self::SORTABLE[$f['sort_by'] ?? 'plan_start'], $f['sort_dir'] ?? 'desc')
            ->orderBy('work_order.wo_number')
            ->paginate($f['per_page'] ?? 15)
            ->withQueryString();

        return ProductionOrderResource::collection($orders);
    }
}
