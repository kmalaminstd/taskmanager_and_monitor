<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canMonitor();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:task_categories,id'],
            'priority' => ['required', 'in:high,medium,low'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
            'task_type' => ['required', 'in:one_time,recurring'],
            'frequency' => ['nullable', 'required_if:task_type,recurring', 'in:daily,weekly,monthly'],
            'instructions' => ['nullable', 'string'],
            'assignee_ids' => ['required', 'array', 'min:1'],
            'assignee_ids.*' => ['exists:users,id'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,zip'],
        ];
    }

    public function messages(): array
    {
        return [
            'assignee_ids.required' => 'Please select at least one assignee.',
            'assignee_ids.min' => 'Please select at least one assignee.',
        ];
    }
}
