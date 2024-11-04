<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
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

        return [
            'name' => [
                'required',
                'string',
                'min:1',
                'max:255',
                'not_regex:/^[\s]*$/', // Prevent only-whitespace names
                // Ensure task name is unique within the project
                // Parameters:
                // - tasks: table name
                // - name: column to check uniqueness
                // - NULL: no ID to ignore (creating new task)
                // - id: primary key column name
                // - project_id: additional column for scoping uniqueness
                // - $project->id: value for the project_id scope
                'unique:tasks,name,NULL,id,project_id,' . $projectId,
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
