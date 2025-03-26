<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class TaskUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['exists:users,id'],
            'start_date' => ['date', 'after_or_equal:start_date'],
            'end_date' => ['date', 'after_or_equal:start_date'],
        ];
    }
    public function messages(): array
    {
        return [
            'start_date.required' => 'A data de início é obrigatória.',
            'start_date.date' => 'A data de início deve estar em um formato válido.',
            'start_date.before_or_equal' => 'A data de início deve ser menor ou igual à data final.',
            'end_date.required' => 'A data final é obrigatória.',
            'end_date.date' => 'A data final deve estar em um formato válido.',
            'end_date.after_or_equal' => 'A data final deve ser maior ou igual à data de início.',
        ];
    }

    public function getStartDate(): Carbon
{
    return Carbon::parse($this->validated()['start_date']);
}

public function getEndDate(): Carbon
{
    return Carbon::parse($this->validated()['end_date']);
}
}
