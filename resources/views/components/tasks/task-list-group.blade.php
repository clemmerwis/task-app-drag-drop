<div class="row">
    <div class="col">
        <div id="taskList" data-project-id="{{ $project->id }}" class="list-group">
            @if($tasks->isEmpty())
                <div class="alert alert-info">
                    No tasks yet. Add your first task above!
                </div>
            @else
                @foreach($tasks as $task)
                    <div class="list-group-item d-flex justify-content-between align-items-center"
                         data-task-id="{{ $task->id }}">
                        <div class="d-flex align-items-center gap-2">
                            <span class="drag-handle text-muted">☰</span>
                            <span class="task-name">{{ $task->name }}</span>
                        </div>

                        <div class="btn-group">
                            <button class="btn btn-outline-secondary btn-sm edit-task-btn">Edit</button>
                            <button class="btn btn-outline-danger btn-sm">Delete</button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<x-tasks.task-edit-modal />
