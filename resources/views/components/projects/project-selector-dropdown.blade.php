<form action="{{ route('projects.tasks.index', $project) }}"
      method="GET"
      class="d-flex gap-2"
      id="project-select-form">
    <select
        name="switch_to"
        class="form-select"
        onchange="this.form.submit()">
        @foreach($projects as $projectOption)
            <option value="{{ $projectOption->id }}"
                    {{ $project->id == $projectOption->id ? 'selected' : '' }}>
                {{ $projectOption->name }}
            </option>
        @endforeach
    </select>
</form>
