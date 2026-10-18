<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Http\Resources\MachinePerformanceResource;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function __construct(private readonly DashboardService $dashboard){}

    public function index(): DashboardResource
    {
        return new DashboardResource($this->dashboard->overview());
    }

    public function machine(string $id): MachinePerformanceResource | JsonResponse
    {
        $data = $this->dashboard->machine($id);

        if ($data === null) {
            return response()->json(['message' => "Machine '{$id}' not found."], 404);
        }

        return new MachinePerformanceResource($data);
    }
}
