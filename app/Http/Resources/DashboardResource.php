<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $d = $this->resource;

        return [
            'summary' => [
                'total_machine' => $d['machine_count'],
                'running_order' => $d['running_orders'],
                'finished_order' => $d['finished_orders'],
                'today_target' => $d['today']['target'],
                'today_good' => $d['today']['good'],
                'today_reject' => $d['today']['reject'],
                'achievement' => $d['today']['achievement'],
            ],
            'trend_7_days' => array_map(fn (array $day) => [
                'date' => $day['date'],
                'good_qty' => $day['good'],
                'reject_qty' => $day['reject'],
                'achievement' => $day['achievement'],
            ], $d['trend']),
            'status_breakdown' => array_map(fn (array $row) => [
                'status' => $row['status'],
                'total' => $row['total'],
            ], $d['statuses']),
            'top_machines' => array_map(fn (array $machine) => [
                'machine_code' => $machine['code'],
                'machine_name' => $machine['name'],
                'good_qty' => $machine['good'],
                'achievement' => $machine['achievement'],
            ], $d['top_machines']),
        ];
    }
}
