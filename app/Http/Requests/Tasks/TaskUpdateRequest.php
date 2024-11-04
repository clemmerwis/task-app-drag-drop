<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class TaskUpdateRequest extends FormRequest
{
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
        $projectId = $this->route('project')->id;
        $taskId = $this->route('task')->id;

        return [
            'name' => [
                'required',
                'string',
                'min:1',
                'max:255',
                'not_regex:/^[\s]*$/',
                // Ensure task name is unique within the project (excluding the current task)
                // Parameters:
                // - tasks: table name
                // - name: column to check uniqueness
                // - $task->id: ignore this ID when checking uniqueness
                // - id: primary key column name
                // - project_id: additional column for scoping uniqueness
                // - $project->id: value for the project_id scope
                'unique:tasks,name,' . $taskId . ',id,project_id,' . $projectId,
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a task name.',
            'name.max' => 'Task name cannot be longer than 255 characters.',
            'name.min' => 'Task name cannot be empty.',
            'name.not_regex' => 'Task name cannot contain only whitespace.',
            'name.unique' => 'A task with this name already exists in this project.',
        ];
    }
}
