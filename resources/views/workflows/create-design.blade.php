@extends('app.partials.workflow-design')
@section('title', 'Create Workflow - COLI')

@section('content')

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: @json(session('success')),
                confirmButtonText: 'OK'
            });
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: @json(session('error')),
                confirmButtonText: 'OK'
            });
        });
    </script>
@endif

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonText: 'OK'
            });
        });
    </script>
@endif

<form action="{{ route('workflows.store') }}" method="POST" id="workflowForm">
    @csrf
    <div class="tab-content" id="lineTabContent">
        
        <div class="tab-pane fade show active" id="workflow-designer" role="tabpanel" aria-labelledby="workflow-designer-line-tab">
            <div id="drawflow" style="position: relative; min-height: 500px;">
                <!-- Button Help -->
                <button 
                    type="button" class="btn btn-info btn-sm"
                    id="btn-help-drawflow"
                    data-bs-toggle="modal"
                    data-bs-target="#helpModal"
                >
                    <i class="mdi mdi-help-circle-outline me-1"></i> Help
                </button>
                <!-- Button Lock Drawflow -->
                <button 
                    type="button" class="btn btn-warning btn-sm"
                    id="btn-lock-drawflow"
                >
                    <i class="mdi mdi-lock-open-variant-outline me-1" id="icon-lock-drawflow"></i> Lock Drawflow
                </button>
                <!-- Button Insert Task (modal enabled) -->
                <button 
                    type="button" class="btn btn-success btn-sm"
                    style="" 
                    id="btn-insert-task"
                    data-bs-toggle="modal"
                    data-bs-target="#taskModal"
                >
                    <i class="mdi mdi-plus-circle-outline me-1"></i> Insert Task
                </button>
            </div>
        </div>
        <div class="tab-pane fade" id="workflow-information" role="tabpanel" aria-labelledby="workflow-information-line-tab">
            <div class="container mt-3">
            <div class="row">
                    <div class="col-md-12"> 
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title"><i class="mdi mdi-information-outline me-1"></i>Workflow Information</h5>
                            </div>
                            <div class="card-body">
                                <!-- this is for hidden data -->
                                <input type="hidden" id="type" name="type" value="design">
                                <textarea id="drawflow_data" name="drawflow_data" >{{ old('drawflow_data') }}</textarea>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="name" class="form-label"><i class="mdi mdi-format-title me-1"></i>Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="name" name="name" required placeholder="Enter workflow name" value="{{ old('name') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="category" class="form-label"><i class="mdi mdi-shape-outline me-1"></i>Category <span class="text-danger">*</span></label>
                                                    <select class="form-select form-select-sm mb-3" id="category" name="category" required>
                                                        <option value="" disabled {{ old('category') == '' ? 'selected' : '' }}>Select category</option>
                                                        <option value="single" {{ old('category') == 'single' ? 'selected' : '' }}>Single</option>
                                                        <option value="multiple" {{ old('category') == 'multiple' ? 'selected' : '' }}>Multiple</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="tags" class="form-label"><i class="mdi mdi-tag-multiple-outline me-1"></i>Tags</label>
                                                    <input type="text" class="form-control" id="tags" name="tags" placeholder="Enter tags (comma separated)" value="{{ old('tags') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="description" class="form-label"><i class="mdi mdi-text-box-outline me-1"></i>Description</label>
                                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter workflow description">{{ old('description') }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3 d-flex align-items-center">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="mdi mdi-content-save-outline me-1"></i>Save Workflow
                                                </button>
                                                <a href="{{ route('workflows.index') }}" class="btn bg-white text-dark ms-2">
                                                    <i class="mdi mdi-arrow-left me-1"></i>Back
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>   
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<!-- Modal for Help -->
<div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg ">
    <div class="modal-content">
      <div class="modal-header ">
        <h5 class="modal-title" id="helpModalLabel">
            <i class="mdi mdi-help-circle-outline me-2"></i>
            Workflow Designer Help
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-4">
            <h6 class="fw-bold mb-2">Workflow Designer Guide</h6>
            <ul class="mb-3 ps-3">
                <li class="mb-2">To add a new task, click the <b>Add Task</b> button.</li>
                <li class="mb-2">To lock or unlock diagram editing, use the <b>Lock Diagram</b> button.</li>
                <li class="mb-2">Enter workflow information in the <b>Workflow Information</b> tab.</li>
                <li class="mb-2">To save the workflow, click the <b>Save Workflow</b> button.</li>
                <li class="mb-2">To edit a task, right-click a node and select the <b>Edit</b> icon.</li>
                <li class="mb-2">To delete a task, right-click a node and select the <b>Delete</b> icon.</li>
            </ul>
        </div>
        <div class="mb-4">
            <h6 class="fw-bold mb-2">Task Fields</h6>
            <ul class="mb-3 ps-3">
                <li class="mb-2">Name: The name for the task.</li>
                <li class="mb-2">Description: Description for the task.</li>
                <li class="mb-2">Result File: Result file path for this task <span class="text-muted">(HUNT_PATH/scans/output/id/workflow-slug/result.txt)</span></li>
                <li class="mb-2">Command: Command to execute.</li>
                <li class="mb-2">Wait for all parents to finish: If this task has parent tasks, it will wait for all parent tasks to finish before running.</li>
            </ul>
        </div>
        <div class="mb-2">
            <h6 class="fw-bold mb-2">Available Placeholders for Command</h6>
            <ul class="mb-2 ps-3">
                <li class="mb-2"><code>{target}</code> : Target value</li>
                <li class="mb-2"><code>{result}</code> : Result file path for this task <span class="text-muted">(HUNT_PATH/scans/output/id/workflow-slug/result.txt)</span></li>
                <li class="mb-2"><code>{parent_result}</code> : Result file path for parent task <span class="text-muted">(HUNT_PATH/scans/output/id/workflow-slug/result.txt)</span></li>
                <li class="mb-2"><code>{name}</code> : Current task name</li>
                <li class="mb-2"><code>{parent_name}</code> : Parent task name (if any)</li>
                <li class="mb-2"><code>{output_path}</code> : Output path <span class="text-muted">(HUNT_PATH/scans/output/id/workflow-slug)</span></li>
            </ul>
        </div>

      </div>
      <div class="modal-footer ">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- End Help Modal -->


<!-- Modal for Add/Edit Task -->
<div class="modal fade" id="taskModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="taskForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="taskModalLabel">Task Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="taskNodeId" name="taskNodeId">
          <div class="mb-3">
            <label for="taskName" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="taskName" name="taskName" required>
          </div>
          <div class="mb-3">
            <label for="taskDescription" class="form-label">Description</label>
            <textarea class="form-control" id="taskDescription" name="taskDescription" rows="2"></textarea>
          </div>
          <div class="mb-3">
            <label for="result_file" class="form-label">Result File</label>
            <input type="text" class="form-control" id="result_file" name="result_file">
          </div>
          <div class="mb-3">
            <label for="taskCommand" class="form-label">Command <span class="text-danger">*</span></label>
            <textarea class="form-control" id="taskCommand" name="taskCommand" rows="3" required></textarea>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" value="1" id="taskWaitAll" name="taskWaitAll">
            <label class="form-check-label" for="taskWaitAll">
              Wait for all parent done
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
    let editor = null;
    let selectedNodeId = null;
    let isLocked = false;

    // Helper to generate node HTML with edit/delete buttons
    function getTaskNodeHtml(name, wait_all, nodeId = null) {
        // nodeId is only used for event delegation, not for inline handlers
        return `<div class="task-node">
            <div style="text-align: center; margin: 10px;">
                <strong><i class="mdi mdi-console-line me-1"></i>${name}</strong>
                ${wait_all ? '<br><small>(Wait All)</small>' : ''}
            </div>
            <div class="node-action-btns" style="position: absolute; top: 0; right: 0;">
                <button type="button" class="btn-edit-task" title="Edit Task" data-node-id="${nodeId ? nodeId : ''}" >
                    <i class="mdi mdi-pencil-outline"></i>
                </button>
                <button type="button" class="btn-delete-task" title="Delete Task" data-node-id="${nodeId ? nodeId : ''}">
                    <i class="mdi mdi-delete-outline"></i>
                </button>
            </div>
        </div>`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Drawflow init
        const drawflowElem = document.getElementById('drawflow');
        editor = new Drawflow(drawflowElem);
        editor.reroute = true;
        editor.start();

        // Load old drawflow data if exists
        @if(old('drawflow_data'))
            try {
                let oldDrawflow = JSON.parse(@json(old('drawflow_data')));
                // Patch: after import, update all task node HTMLs to include action buttons
                editor.import(oldDrawflow);
                setTimeout(() => {
                    for (const id in editor.drawflow.Home.data) {
                        const node = editor.drawflow.Home.data[id];
                        if (node.name === 'task') {
                            let nodeElem = document.getElementById('node-' + id);
                            if (nodeElem) {
                                let contentElem = nodeElem.querySelector('.drawflow_content_node');
                                if (contentElem) {
                                    contentElem.innerHTML = getTaskNodeHtml(
                                        node.data.name || '',
                                        node.data.wait_all || false,
                                        id
                                    );
                                }
                            }
                        }
                    }
                }, 100);
            } catch (e) {}
        @endif

        // Save drawflow data on form submit
        document.getElementById('workflowForm').addEventListener('submit', function() {
            document.getElementById('drawflow_data').value = JSON.stringify(editor.export());
        });

        // Add new task
        document.getElementById('btn-insert-task').addEventListener('click', function() {
            // Add mode: clear fields
            document.getElementById('taskForm').reset();
            document.getElementById('taskNodeId').value = '';
            selectedNodeId = null;
            // Set modal title
            document.getElementById('taskModalLabel').textContent = 'Add Task';
        });

        // Lock/unlock drawflow
        document.getElementById('btn-lock-drawflow').addEventListener('click', function() {
            isLocked = !isLocked;
            if(isLocked) {
                editor.editor_mode = 'fixed';
                this.innerHTML = '<i class="mdi mdi-lock-outline me-1" id="icon-lock-drawflow"></i> Unlock Drawflow';
                this.classList.remove('btn-warning');
                this.classList.add('btn-secondary');
            } else {
                editor.editor_mode = 'edit';
                this.innerHTML = '<i class="mdi mdi-lock-open-variant-outline me-1" id="icon-lock-drawflow"></i> Lock Drawflow';
                this.classList.remove('btn-secondary');
                this.classList.add('btn-warning');
            }
        });

        // Open edit modal for a node
        function openEditTaskModal(nodeId) {
            let nodeData = editor.getNodeFromId(nodeId);
            if(nodeData && nodeData.name === 'task') {
                document.getElementById('taskNodeId').value = nodeId;
                document.getElementById('taskName').value = nodeData.data.name || '';
                document.getElementById('taskDescription').value = nodeData.data.description || '';
                document.getElementById('result_file').value = nodeData.data.result || '';
                document.getElementById('taskCommand').value = nodeData.data.command || '';
                document.getElementById('taskWaitAll').checked = !!nodeData.data.wait_all;
                selectedNodeId = nodeId;
                document.getElementById('taskModalLabel').textContent = 'Edit Task';
                let modal = new bootstrap.Modal(document.getElementById('taskModal'));
                modal.show();
            }
        }

        // Event delegation for edit/delete buttons inside nodes
        drawflowElem.addEventListener('click', function(e) {
            if(isLocked) return;
            // Edit button
            let editBtn = e.target.closest('.btn-edit-task');
            if(editBtn) {
                let nodeElem = e.target.closest('.drawflow-node');
                let nodeId = editBtn.getAttribute('data-node-id') || (nodeElem ? nodeElem.id.replace('node-', '') : null);
                if(nodeId) {
                    openEditTaskModal(nodeId);
                }
                e.stopPropagation();
                return;
            }
            // Delete button
            let deleteBtn = e.target.closest('.btn-delete-task');
            if(deleteBtn) {
                let nodeElem = e.target.closest('.drawflow-node');
                let nodeId = deleteBtn.getAttribute('data-node-id') || (nodeElem ? nodeElem.id.replace('node-', '') : null);
                if(nodeId) {
                    // Confirm before delete
                    Swal.fire({
                        icon: 'warning',
                        title: 'Delete Task',
                        text: 'Are you sure you want to delete this task?',
                        showCancelButton: true,
                        confirmButtonText: 'Delete',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if(result.isConfirmed) {
                            editor.removeNodeId('node-' + nodeId);
                        }
                    });
                }
                e.stopPropagation();
                return;
            }
        });

        // Save task (add or edit)
        document.getElementById('taskForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let name = document.getElementById('taskName').value.trim();
            let description = document.getElementById('taskDescription').value.trim();
            let result = document.getElementById('result_file').value.trim();
            let command = document.getElementById('taskCommand').value.trim();
            let wait_all = document.getElementById('taskWaitAll').checked;
            let nodeId = document.getElementById('taskNodeId').value;

            // Manual validation
            if(!name) {
                document.getElementById('taskName').focus();
                return;
            }
            if(!command) {
                document.getElementById('taskCommand').focus();
                return;
            }

            // For new node, nodeId is not known yet, so pass empty, will patch after addNode
            let html = getTaskNodeHtml(name, wait_all, '');

            if(nodeId) {
                // Edit existing node
                let node = editor.getNodeFromId(nodeId);
                if(node) {
                    // Update data
                    editor.updateNodeDataFromId(nodeId, {name, description, command, result, wait_all});
                    // Update node HTML in DOM
                    let nodeElem = document.getElementById('node-' + nodeId);
                    if(nodeElem) {
                        let contentElem = nodeElem.querySelector('.drawflow_content_node');
                        if(contentElem) {
                            // Update with correct nodeId in buttons
                            contentElem.innerHTML = getTaskNodeHtml(name, wait_all, nodeId);
                        }
                    }
                }
            } else {
                // Add new node
                let newId = editor.addNode('task', 1, 1, 100, 100, 'task', {name, description, command, result, wait_all}, html);
                // Patch: update node HTML to include correct nodeId in buttons
                setTimeout(() => {
                    let nodeElem = document.getElementById('node-' + newId);
                    if(nodeElem) {
                        let contentElem = nodeElem.querySelector('.drawflow_content_node');
                        if(contentElem) {
                            contentElem.innerHTML = getTaskNodeHtml(name, wait_all, newId);
                        }
                    }
                }, 50);
            }
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('taskModal')).hide();
        });
    });
</script>

@endsection