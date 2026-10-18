<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MachinePerformanceResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $m = $this->resource;

        return [
            'machine_code' => $m['code'],
            'machine_name' => $m['name'],
            'total_order' => $m['total_orders'],
            'good_qty' => $m['good'],
            'reject_qty' => $m['reject'],
            'downtime_minutes' => $m['downtime_minutes'],
            'achievement' => $m['achievement'],
        ];
    }
}
