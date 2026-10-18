<?php

namespace App\Services;

use App\Models\Machine;
use App\Models\WorkOrder;
use App\WorkOrderStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    private const int TREND_DAYS = 7;
    private const int TOP_MACHINES = 10;

    private const string GOOD = 'COALESCE(SUM(r.good_qty), 0)';
    private const string REJECT = 'COALESCE(SUM(r.reject_qty), 0)';
    private const string TARGET = 'COALESCE(SUM(w.target_qty / c.result_count), 0)';

    public function overview(): array
    {
        $latest = $this->latestProductionDate();

        $statusTotals = WorkOrder::query()
            ->selectRaw('status, COUNT(*) AS total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $today = $this->resultQuery()
            ->whereBetween('r.actual_start', [
                $latest->startOfDay()->toDateTimeString(),
                $latest->endOfDay()->toDateTimeString(),
            ])
            ->selectRaw($this->aggregates())
            ->first();

        return [
            'machine_count' => Machine::count(),
            'running_orders' => (int) ($statusTotals[WorkOrderStatus::Running->value] ?? 0),
            'finished_orders' => (int) ($statusTotals[WorkOrderStatus::Finished->value] ?? 0),
            'today' => $this->metrics($today),
            'trend' => $this->trend($latest),
            'statuses' => array_map(
                fn (WorkOrderStatus $status) => [
                    'status' => $status->value,
                    'total' => (int) ($statusTotals[$status->value] ?? 0),
                ],
                WorkOrderStatus::cases()
            ),
            'top_machines' => $this->topMachines(),
        ];
    }

    public function machine(string $machineCode): ?array
    {
        $machine = Machine::find($machineCode);
        if (! $machine) {
            return null;
        }

        $totals = $this->metrics(
            $this->resultQuery()
                ->where('w.machine_code', $machineCode)
                ->selectRaw($this->aggregates())
                ->first()
        );

        $downtime = (int) DB::table('downtime as d')
            ->join('work_order as w', 'w.wo_number', '=', 'd.wo_number')
            ->where('w.machine_code', $machineCode)
            ->sum('d.duration_minutes');

        return [
            'code' => $machine->machine_code,
            'name' => $machine->machine_name,
            'total_orders' => WorkOrder::where('machine_code', $machineCode)->count(),
            'good' => $totals['good'],
            'reject' => $totals['reject'],
            'downtime_minutes' => $downtime,
            'achievement' => $totals['achievement'],
        ];
    }

    private function trend(CarbonImmutable $latest): array
    {
        $from = $latest->subDays(self::TREND_DAYS - 1)->startOfDay();

        $rows = $this->resultQuery()
            ->whereBetween('r.actual_start', [
                $from->toDateTimeString(),
                $latest->endOfDay()->toDateTimeString(),
            ])
            ->selectRaw('DATE(r.actual_start) AS day, '.$this->aggregates())
            ->groupByRaw('DATE(r.actual_start)')
            ->get()
            ->keyBy('day');

        $trend = [];
        for ($i = self::TREND_DAYS - 1; $i >= 0; $i--) {
            $date = $latest->subDays($i)->toDateString();
            $m = $this->metrics($rows->get($date));

            $trend[] = [
                'date' => $date,
                'good' => $m['good'],
                'reject' => $m['reject'],
                'achievement' => $m['achievement'],
            ];
        }

        return $trend;
    }
    private function latestProductionDate(): CarbonImmutable
    {
        $max = DB::table('production_result')->max('actual_start');

        return $max ? CarbonImmutable::parse($max) : CarbonImmutable::now();
    }

    private function topMachines(): array
    {
        return $this->resultQuery()
            ->join('machine as m', 'm.machine_code', '=', 'w.machine_code')
            ->selectRaw('m.machine_code, m.machine_name, '.$this->aggregates())
            ->groupBy('m.machine_code', 'm.machine_name')
            ->orderByDesc('good')
            ->orderBy('m.machine_code')
            ->limit(self::TOP_MACHINES)
            ->get()
            ->map(function ($row) {
                $m = $this->metrics($row);

                return [
                    'code' => $row->machine_code,
                    'name' => $row->machine_name,
                    'good' => $m['good'],
                    'achievement' => $m['achievement'],
                ];
            })
            ->all();
    }

    private function resultQuery(): Builder
    {
        $perWorkOrder = DB::table('production_result')
            ->select('wo_number')
            ->selectRaw('COUNT(*) AS result_count')
            ->groupBy('wo_number');

        return DB::table('production_result as r')
            ->join('work_order as w', 'w.wo_number', '=', 'r.wo_number')
            ->joinSub($perWorkOrder, 'c', 'c.wo_number', '=', 'r.wo_number');
    }

    private function aggregates(): string
    {
        return self::GOOD.' AS good, '.self::REJECT.' AS reject, '.self::TARGET.' AS target';
    }

    private function metrics(?object $row): array
    {
        $good = (int) ($row->good ?? 0);
        $reject = (int) ($row->reject ?? 0);
        $target = (float) ($row->target ?? 0);

        return [
            'good' => $good,
            'reject' => $reject,
            'target' => (int) round($target),
            'achievement' => $target > 0 ? round($good / $target * 100, 2) : 0.0,
        ];
    }

}
