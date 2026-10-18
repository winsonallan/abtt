<?php

namespace App\Http\Requests;

use App\WorkOrderStatus;
use App\Http\Controllers\Api\ProductionOrderController;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class ListProductionOrdersRequest extends ApiFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'product' => ['nullable', 'string', 'max:20'],
            'machine' => ['nullable', 'string', 'max:10'],
            'status' => ['nullable', Rule::enum(WorkOrderStatus::class)],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'sort_by' => ['nullable', Rule::in(array_keys(ProductionOrderController::SORTABLE))],
            'sort_dir' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
