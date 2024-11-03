<div class="row mb-4">
    <div class="col">
        <form action="{{ route('projects.tasks.store', $project) }}" method="POST">
            @csrf
            <div class="input-group">
                <input type="text"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="New task name"
                       value="{{ old('name') }}"
                       required>
                <button type="submit" class="btn btn-primary">Add Task</button>
            </div>
            @error('name')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </form>
    </div>
</div>
