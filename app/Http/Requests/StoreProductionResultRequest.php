<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\WorkOrderStatus;
use App\Models\WorkOrder;

class StoreProductionResultRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'wo_number' => ['required', 'string', 'exists:work_order,wo_number'],
            'production_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'qty_good' => ['required', 'integer', 'min:0'],
            'qty_reject' => ['required', 'integer', 'min:0'],
            'runtime_minutes' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->has('wo_number')) {
                return;
            }

            $status = WorkOrder::whereKey($this->input('wo_number'))->value('status');

            if ($status !== WorkOrderStatus::Running->value) {
                $validator->errors()->add(
                    'wo_number',
                    "The production order must have status RUNNING (current status: {$status})."
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'production_date.before_or_equal' => 'The production date may not be later than today.',
            'qty_good.min' => 'qty_good must be greater than or equal to 0.',
            'qty_reject.min' => 'qty_reject must be greater than or equal to 0.',
        ];
    }
}
