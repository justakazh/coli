@extends('app.template')
@section('title', 'Tasks Management - COLI')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-checkbox-marked-outline me-1"></i> Tasks
                    </h5>
                    <a href="{{ route('tasks.create') }}" class="btn btn-success">
                        <i class="mdi mdi-plus me-1"></i> Add New Task
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('tasks.search') }}" method="GET" class="row g-3">
                            
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-magnify"></i>
                                    </span>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Search by name..." value="{{ request('name') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="description" class="form-label">Description</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-text-long"></i>
                                    </span>
                                    <input type="text" class="form-control" id="description" name="description" placeholder="Search by description..." value="{{ request('description') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-magnify me-1"></i> Search
                                </button>
                                <a href="{{ route('tasks.index') }}" class="btn btn-light">
                                    <i class="mdi mdi-refresh me-1"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 50px">#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th style="width: 120px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tasks as $task)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $task->name }}</strong>
                                    </td>
                                    <td>
                                        {{ $task->description }}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button 
                                               class="btn btn-sm btn-outline-warning show-commands"
                                               title="Edit task" data-result-file-name="{{ $task->result }}" data-command-target="{{ $task->from_target_command }}" data-command-parent-result="{{ $task->from_parent_result_command }}" data-bs-toggle="modal" data-bs-target="#commandsModal"
                                               data-bs-toggle="tooltip">
                                                <i class="mdi mdi-console-line"></i>
                                                <span class="d-none d-sm-inline">Commands</span>
                                            </button>
                                            <a href="{{ route('tasks.edit', $task->id) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               data-bs-toggle="tooltip"
                                               title="Edit task">
                                                <i class="mdi mdi-pencil"></i>
                                                <span class="d-none d-sm-inline">Edit</span>
                                            </a>
                                            <form action="{{ route('tasks.delete', $task->id) }}" 
                                                  method="POST" 
                                                  class="d-inline delete-task-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="tooltip"
                                                        title="Delete task">
                                                    <i class="mdi mdi-delete"></i>
                                                    <span class="d-none d-sm-inline">Delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="mdi mdi-clipboard-text mdi-24px d-block mb-2"></i>
                                        No tasks found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $tasks->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="commandsModal" tabindex="-1" aria-labelledby="commandsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Task Commands</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-12 mb-3">
                <h6>Result File Name</h6>
                <input type="text" id="result" class="form-control" rows="3" readonly>
            </div>            
            <div class="col-md-12 mb-3">
                <h6>Target Command</h6>
                <textarea id="commandTarget" class="form-control" rows="3" readonly></textarea>
            </div>
            <div class="col-md-12 mt-3">
                <h6>Parent Result Command</h6>
                <textarea id="commandParentResult" class="form-control" rows="3" readonly></textarea>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Show commands in modal
    $('.show-commands').on('click', function() {   
        var resultFileName = $(this).data('result-file-name');
        var targetCommand = $(this).data('command-target');
        var parentResultCommand = $(this).data('command-parent-result');
        
        $('#commandTarget').val(targetCommand || 'No command');
        $('#commandParentResult').val(parentResultCommand || 'No command');
        $('#result').val(resultFileName || 'No result file name');
    });

    // SweetAlert for Delete Task
    $('.delete-task-form').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Are you sure you want to delete this task?',
            text: "Workflow will be affected if you delete this task.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-danger mx-2',
                cancelButton: 'btn btn-light mx-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

});
</script>

@endsection