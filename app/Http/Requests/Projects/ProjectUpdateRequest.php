<?php

namespace App\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;

class ProjectUpdateRequest extends FormRequest
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
        $projectId = $this->route('project')->id;

        return [
            'name' => [
                'required',
                'string',
                'min:1',
                'max:100',
                'not_regex:/^[\s]*$/',
                // Ensure project name is unique (excluding current project)
                // Parameters:
                // - projects: table name
                // - name: column to check uniqueness
                // - $project->id: ignore this ID when checking uniqueness
                'unique:projects,name,' . $projectId,
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The project name is required.',
            'name.max' => 'The project name cannot be longer than 100 characters.',
            'name.min' => 'The project name cannot be empty.',
            'name.not_regex' => 'The project name cannot contain only whitespace.',
            'name.unique' => 'The project name has already been taken.',
        ];
    }
}
