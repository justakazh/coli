@extends('templates.v1')
@section('content')
@section('title', 'Workflows - Create Diagram')


@push('styles')
<style>

:root {
  --dfBackgroundColor: #ffffff;
  --dfBackgroundSize: 0px;
  --dfBackgroundImage: none;

  --dfNodeType: flex;
  --dfNodeTypeFloat: none;
  --dfNodeBackgroundColor: #212529;
  --dfNodeTextColor: #ffffff;
  --dfNodeBorderSize: 1px;
  --dfNodeBorderColor: #ffffff;
  --dfNodeBorderRadius: 4px;
  --dfNodeMinHeight: 40px;
  --dfNodeMinWidth: 160px;
  --dfNodePaddingTop: 15px;
  --dfNodePaddingBottom: 15px;
  --dfNodeBoxShadowHL: 0px;
  --dfNodeBoxShadowVL: 1px;
  --dfNodeBoxShadowBR: 15px;
  --dfNodeBoxShadowS: 1px;
  --dfNodeBoxShadowColor: #1B1B1D;

  --dfNodeHoverBackgroundColor: #212529;
  --dfNodeHoverTextColor: #ffffff;
  --dfNodeHoverBorderSize: 1px;
  --dfNodeHoverBorderColor: #d0d0d0;
  --dfNodeHoverBorderRadius: 4px;


  --dfNodeSelectedBackgroundColor: #212529;
  --dfNodeSelectedTextColor: #ffffff;
  --dfNodeTextColor: #ffffff;
  --dfNodeSelectedBorderSize: 1px;
  --dfNodeSelectedBorderColor: #d0d0d0;
  --dfNodeSelectedBorderRadius: 4px;


  --dfInputBackgroundColor: #ffffff;
  --dfInputBorderSize: 1px;
  --dfInputBorderColor: #1B1B1D;
  --dfInputBorderRadius: 50px;
  --dfInputLeft: -27px;
  --dfInputHeight: 20px;
  --dfInputWidth: 20px;

  --dfInputHoverBackgroundColor: #ffffff;
  --dfInputHoverBorderSize: 1px;
  --dfInputHoverBorderColor: #1B1B1D;
  --dfInputHoverBorderRadius: 50px;

  --dfOutputBackgroundColor: #ffffff;
  --dfOutputBorderSize: 1px;
  --dfOutputBorderColor: #1B1B1D;
  --dfOutputBorderRadius: 50px;
  --dfOutputRight: -3px;
  --dfOutputHeight: 20px;
  --dfOutputWidth: 20px;

  --dfOutputHoverBackgroundColor: #ffffff;
  --dfOutputHoverBorderSize: 1px;
  --dfOutputHoverBorderColor: #1B1B1D;
  --dfOutputHoverBorderRadius: 50px;

  --dfLineWidth: 5px;
  --dfLineColor: #4682b4;
  --dfLineHoverColor: #4682b4;
  --dfLineSelectedColor: #43b993;

  --dfRerouteBorderWidth: 1px;
  --dfRerouteBorderColor: #1B1B1D;
  --dfRerouteBackgroundColor: #ffffff;

  --dfRerouteHoverBorderWidth: 1px;
  --dfRerouteHoverBorderColor: #1B1B1D;
  --dfRerouteHoverBackgroundColor: #ffffff;

  --dfDeleteDisplay: block;
  --dfDeleteColor: #ffffff;
  --dfDeleteBackgroundColor: #1B1B1D;
  --dfDeleteBorderSize: 1px;
  --dfDeleteBorderColor: #ffffff;
  --dfDeleteBorderRadius: 50px;
  --dfDeleteTop: -15px;

  --dfDeleteHoverColor: #1B1B1D;
  --dfDeleteHoverBackgroundColor: #ffffff;
  --dfDeleteHoverBorderSize: 1px;
  --dfDeleteHoverBorderColor: #1B1B1D;
  --dfDeleteHoverBorderRadius: 50px;

}


.drawflow .drawflow-node {
  display: var(--dfNodeType);
  background: var(--dfNodeBackgroundColor);
  color: var(--dfNodeTextColor);
  border: var(--dfNodeBorderSize)  solid var(--dfNodeBorderColor);
  border-radius: var(--dfNodeBorderRadius);
  min-height: var(--dfNodeMinHeight);
  width: auto;
  min-width: var(--dfNodeMinWidth);
  padding-top: var(--dfNodePaddingTop);
  padding-bottom: var(--dfNodePaddingBottom);
  -webkit-box-shadow: var(--dfNodeBoxShadowHL) var(--dfNodeBoxShadowVL) var(--dfNodeBoxShadowBR) var(--dfNodeBoxShadowS) var(--dfNodeBoxShadowColor);
  box-shadow:  var(--dfNodeBoxShadowHL) var(--dfNodeBoxShadowVL) var(--dfNodeBoxShadowBR) var(--dfNodeBoxShadowS) var(--dfNodeBoxShadowColor);
}

.drawflow .drawflow-node:hover {
  background: var(--dfNodeHoverBackgroundColor);
  color: var(--dfNodeHoverTextColor);
  border: var(--dfNodeHoverBorderSize)  solid var(--dfNodeHoverBorderColor);
  border-radius: var(--dfNodeHoverBorderRadius);
  -webkit-box-shadow: var(--dfNodeHoverBoxShadowHL) var(--dfNodeHoverBoxShadowVL) var(--dfNodeHoverBoxShadowBR) var(--dfNodeHoverBoxShadowS) var(--dfNodeHoverBoxShadowColor);
  box-shadow:  var(--dfNodeHoverBoxShadowHL) var(--dfNodeHoverBoxShadowVL) var(--dfNodeHoverBoxShadowBR) var(--dfNodeHoverBoxShadowS) var(--dfNodeHoverBoxShadowColor);
}

.drawflow .drawflow-node.selected {
  background: var(--dfNodeSelectedBackgroundColor);
  color: var(--dfNodeSelectedTextColor);
  border: var(--dfNodeSelectedBorderSize)  solid var(--dfNodeSelectedBorderColor);
  border-radius: var(--dfNodeSelectedBorderRadius);
  -webkit-box-shadow: var(--dfNodeSelectedBoxShadowHL) var(--dfNodeSelectedBoxShadowVL) var(--dfNodeSelectedBoxShadowBR) var(--dfNodeSelectedBoxShadowS) var(--dfNodeSelectedBoxShadowColor);
  box-shadow:  var(--dfNodeSelectedBoxShadowHL) var(--dfNodeSelectedBoxShadowVL) var(--dfNodeSelectedBoxShadowBR) var(--dfNodeSelectedBoxShadowS) var(--dfNodeSelectedBoxShadowColor);
}

.drawflow .drawflow-node .input {
  left: var(--dfInputLeft);
  background: var(--dfInputBackgroundColor);
  border: var(--dfInputBorderSize)  solid var(--dfInputBorderColor);
  border-radius: var(--dfInputBorderRadius);
  height: var(--dfInputHeight);
  width: var(--dfInputWidth);
}

.drawflow .drawflow-node .input:hover {
  background: var(--dfInputHoverBackgroundColor);
  border: var(--dfInputHoverBorderSize)  solid var(--dfInputHoverBorderColor);
  border-radius: var(--dfInputHoverBorderRadius);
}

.drawflow .drawflow-node .outputs {
  float: var(--dfNodeTypeFloat);
}

.drawflow .drawflow-node .output {
  right: var(--dfOutputRight);
  background: var(--dfOutputBackgroundColor);
  border: var(--dfOutputBorderSize)  solid var(--dfOutputBorderColor);
  border-radius: var(--dfOutputBorderRadius);
  height: var(--dfOutputHeight);
  width: var(--dfOutputWidth);
}

.drawflow .drawflow-node .output:hover {
  background: var(--dfOutputHoverBackgroundColor);
  border: var(--dfOutputHoverBorderSize)  solid var(--dfOutputHoverBorderColor);
  border-radius: var(--dfOutputHoverBorderRadius);
}

.drawflow .connection .main-path {
  stroke-width: var(--dfLineWidth);
  stroke: var(--dfLineColor);
}

.drawflow .connection .main-path:hover {
  stroke: var(--dfLineHoverColor);
}

.drawflow .connection .main-path.selected {
  stroke: var(--dfLineSelectedColor);
}

.drawflow .connection .point {
  stroke: var(--dfRerouteBorderColor);
  stroke-width: var(--dfRerouteBorderWidth);
  fill: var(--dfRerouteBackgroundColor);
}

.drawflow .connection .point:hover {
  stroke: var(--dfRerouteHoverBorderColor);
  stroke-width: var(--dfRerouteHoverBorderWidth);
  fill: var(--dfRerouteHoverBackgroundColor);
}

.drawflow-delete {
  display: var(--dfDeleteDisplay);
  color: var(--dfDeleteColor);
  background: var(--dfDeleteBackgroundColor);
  border: var(--dfDeleteBorderSize) solid var(--dfDeleteBorderColor);
  border-radius: var(--dfDeleteBorderRadius);
}

.parent-node .drawflow-delete {
  top: var(--dfDeleteTop);
}

.drawflow-delete:hover {
  color: var(--dfDeleteHoverColor);
  background: var(--dfDeleteHoverBackgroundColor);
  border: var(--dfDeleteHoverBorderSize) solid var(--dfDeleteHoverBorderColor);
  border-radius: var(--dfDeleteHoverBorderRadius);
}

    #drawflow {
        height: 100%;
        width: 100%;
        overflow: hidden;
        min-height: calc(100vh - 195px);
        display: flex;
        flex-direction: column;
        background-color: #212529;
        background-image: 
            radial-gradient(#444 1px, transparent 1px),
            radial-gradient(#444 1px, transparent 1px);
        background-size: 20px 20px;
        background-position: 0 0, 10px 10px;
        position: relative;
    }
    .card {
        height: 100%;
    }
    .card > #drawflow {
        flex: 1 1 auto;
        height: 100%;
    }
    .drawflow-floating-buttons {
        position: absolute;
        right: 16px;
        bottom: 16px;
        z-index: 10;
        display: flex;
        flex-direction: row;
        gap: 8px;
        align-items: center;
    }
    /* Tambahan untuk tampilan CodeMirror dalam modal */
    .CodeMirror {
        width: 100% !important;
        min-height: 100px;
        max-height: 300px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        font-family: 'Fira Mono', 'Roboto Mono', monospace;
    }
    .cm-s-dracula.CodeMirror, .cm-s-dracula {
        border: var(--bs-border-width) solid var(--bs-border-color);
    }

    .cm-s-dracula.CodeMirror, .cm-s-dracula .CodeMirror-gutters{
        background-color:  #212529 !important;
    }

    
    @media (max-width: 576px) {
        .drawflow-btn-group {
        flex-direction: column !important;
        gap: 8px !important;
        align-items: stretch !important;
        }
        .drawflow-btn-group .btn {
        width: 100%;
        }
    }
</style>
@endpush


<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div id="drawflow" style="height:600px;">
            <div class="drawflow-floating-buttons">
                <div class="d-flex flex-wrap flex-md-nowrap gap-2 align-items-center drawflow-btn-group">
                    <button type="button" class="btn btn-primary" id="btn-help-drawflow">
                        <i class="fas fa-question-circle me-1" id="icon-help-drawflow"></i>
                        <span class="d-none d-sm-inline">Help</span>
                    </button>
                    <button type="button" class="btn btn-primary" id="btn-lock-drawflow">
                        <i class="fas fa-lock-open me-1" id="icon-lock-drawflow"></i>
                        <span class="d-none d-sm-inline">Lock Drawflow</span>
                    </button>
                    <button type="button" class="btn btn-primary" id="btn-insert-task" data-bs-toggle="modal" data-bs-target="#taskModal">
                        <i class="fas fa-plus"></i>
                        <span class="d-none d-sm-inline">Add Task</span>
                    </button>
                    <button type="button" class="btn btn-primary floating-save-btn" data-bs-toggle="modal" data-bs-target="#modalSave">
                        <i class="fas fa-save"></i>
                        <span class="d-none d-sm-inline">Save</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Modal Help Drawflow -->
<div class="modal fade" id="helpDrawflowModal" tabindex="-1" aria-labelledby="helpDrawflowModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="helpDrawflowModalLabel"><i class="fas fa-question-circle me-2"></i> Drawflow Help</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h5>How to Use Workflow Diagram Builder</h5>
        <ul>
          <li><b>Add Task:</b> Click <span class="badge bg-secondary"><i class="fas fa-plus"></i> Add Task</span> to insert a new task node.</li>
          <li><b>Edit Task:</b> Double-click a node to update its information.</li>
          <li><b>Connect Tasks:</b> Drag from the port of one node to the port of another node to create a relationship (parent-to-child).</li>
          <li><b>Delete Node:</b> Select a node and press <kbd>Del</kbd>/<kbd>Backspace</kbd>, or right-click and choose delete.</li>
          <li><b>Save Workflow:</b> Click <span class="badge bg-secondary"><i class="fas fa-save"></i> Save</span> to store your workflow.</li>
          <li><b>Lock Diagram:</b> Use <span class="badge bg-secondary"><i class="fas fa-lock-open"></i> Lock Drawflow</span> to prevent changes.</li>
          <li><b>Drag & Zoom:</b> You can pan the canvas (drag background) and zoom (scroll).</li>
          <li><b>Node Details:</b> Hover over nodes for quick info. Double-click for full edit form.</li>
        </ul>
        <hr>
        <h6>Task Command Placeholders</h6>
        <p>Within each command, you can use the following placeholders:</p>
        <ul>
          <li><code><?= "{{target}}" ?></code>: Target value to scan or process.</li>
          <li><code><?= "{{name}}" ?></code>: Task name.</li>
          <li><code><?= "{{output}}" ?></code>: Output folder of scan.</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const btnHelp = document.getElementById('btn-help-drawflow');
  if (btnHelp) {
    btnHelp.addEventListener('click', function() {
      const helpModal = new bootstrap.Modal(document.getElementById('helpDrawflowModal'));
      helpModal.show();
    });
  }
});
</script>


<!-- Modal Add/Edit Task -->
<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog">
    <form id="taskForm" class="modal-content" novalidate>
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="taskModalLabel"><i class="fas fa-tasks"></i> Add Task</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="taskNodeId" name="taskNodeId">
        <div class="mb-3">
          <label for="taskName" class="form-label">Task Name</label>
          <input type="text" class="form-control" id="taskName" name="taskName" required>
          <div class="invalid-feedback">Task name is required.</div>
        </div>
        <div class="mb-3">
          <label for="taskDescription" class="form-label">Description</label>
          <textarea class="form-control" id="taskDescription" name="taskDescription"></textarea>
        </div>
        <div class="mb-3">
          <label for="taskCommand" class="form-label">Command</label>
          <!-- Note: textarea is hidden so it can be required but not interfere with focusability -->
          <textarea class="form-control d-none" id="taskCommand" name="taskCommand" required></textarea>
          <div id="codemirror-taskCommand"></div>
          <div class="invalid-feedback" id="taskCommand_invalid_feedback">Command is required.</div>
        </div>
        <div class="mb-3">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="wait" name="wait" value="true">
            <label class="form-check-label" for="wait">Wait for all parent tasks to finish before executing</label>
          </div>
        </div>
        <!-- wait_all removed -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Cancel
        </button>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save me-1"></i> Save Task
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Save Workflow -->
<div class="modal fade" id="modalSave" tabindex="-1" aria-labelledby="modalSaveLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modalSaveLabel"><i class="fas fa-save"></i> Save Workflow</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('workflows.store.diagram') }}" method="POST" id="save-workflow-form">
          @csrf
          <input type="hidden" name="type" value="diagram">
          <textarea class="form-control" id="diagram_data" name="diagram_data" style="display: none;" readonly> {{ old('diagram_data') }}</textarea>
          <div class="mb-3">
            <label for="workflow-name" class="col-form-label">Workflow Name:</label>
            <input type="text" class="form-control" id="workflow-name" name="workflow_name" required value="{{ old('workflow_name') }}">
          </div>
          <div class="mb-3">
            <label for="workflow-description" class="col-form-label">Workflow Description:</label>
            <textarea class="form-control" id="workflow-description" name="workflow_description" required>{{ old('workflow_description') }}</textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Close
        </button>
        <button type="submit" form="save-workflow-form" class="btn btn-primary" id="btn-save-workflow">
          <i class="fas fa-save me-1"></i> Save Workflow
        </button>
      </div>
    </div>
  </div>
</div>

<input type="hidden" id="drawflow_data" name="drawflow_data">

@push('scripts')
<script>
let editor = null;
let selectedNodeId = null;
let isLocked = false;
let cmTaskCommand = null;

// Remove wait_all from task node html
function getTaskNodeHtml(name, nodeId = null) {
    return `<div class="task-node" style="color: #fff; padding: 18px 14px 40px 14px; position: relative; min-width: 180px; min-height: 78px; box-shadow: 0 2px 12px 0 rgba(30,40,60,.16);">
        <div style="text-align: center; margin: 0 0 16px 0;">
            <span style="font-size: 1.1rem; display: inline-flex; align-items: center; gap: 0.30em;">
                <i class="fas fa-terminal me-1" style="color: #43b993;"></i>
                <b style="display:inline;vertical-align:middle;">${name || '<em style=&quot;color:#aaa;font-weight:400;&quot;>Task Name</em>'}</b>
            </span>
        </div>
        <div class="node-action-btns d-flex gap-2" style="position: absolute; bottom: 12px; left: 50%; transform: translateX(-50%);">
            <button type="button" class="btn-edit-task btn btn-sm btn-primary px-2 py-1 rounded-circle" title="Edit Task" data-node-id="${nodeId ? nodeId : ''}" style="box-shadow: none;">
                <i class="fas fa-pencil-alt"></i>
            </button>
            <button type="button" class="btn-delete-task btn btn-sm btn-primary px-2 py-1 rounded-circle" title="Delete Task" data-node-id="${nodeId ? nodeId : ''}" style="box-shadow: none;">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    </div>`;
}

function updateDiagramData(editor){
  let diagramData = editor.export();
  document.getElementById('diagram_data').value = JSON.stringify(diagramData);


  //simpan ke local storage untuk create workflow
  localStorage.setItem('diagram_data', JSON.stringify(diagramData));
}

function setupCodeMirrorCommand() {
    if (cmTaskCommand) {
        cmTaskCommand.toTextArea();
        cmTaskCommand = null;
    }
    const textarea = document.getElementById('taskCommand');
    const codemirrorDiv = document.getElementById('codemirror-taskCommand');
    // Clear old codemirror node (in case of bootstrap modal shenanigans)
    codemirrorDiv.innerHTML = '';
    // Clone textarea, re-parent it, necessary for correct attach (sometimes textarea gets lost due to BS modal DOM)
    codemirrorDiv.appendChild(textarea);
    textarea.classList.remove('d-none');
    cmTaskCommand = CodeMirror.fromTextArea(textarea, {
        lineNumbers: true,
        theme: "dracula",
        mode: "shell",
        indentUnit: 2,
        tabSize: 2,
        autofocus: true,
        viewportMargin: 30,
        extraKeys: {"Ctrl-Space":"autocomplete"}
    });
    setTimeout(()=>cmTaskCommand.refresh(), 100);
}
function getTaskCommandValue() {
    // Use CodeMirror value if inited, else textarea value
    if(cmTaskCommand) {
        return cmTaskCommand.getValue();
    }
    return document.getElementById('taskCommand').value || '';
}
function setTaskCommandValue(val) {
    if(cmTaskCommand) {
        cmTaskCommand.setValue(val || '');
    } else {
        document.getElementById('taskCommand').value = val || '';
    }
}
function showInvalid(element, msg) {
    element.classList.add('is-invalid');
    if (msg) {
        let feedback = element.parentNode.querySelector('.invalid-feedback');
        if (feedback) feedback.textContent = msg;
    }
}
function hideInvalid(element) {
    element.classList.remove('is-invalid');
}
function showCommandInvalid(msg) {
    document.getElementById('codemirror-taskCommand').classList.add('is-invalid');
    let feedback = document.getElementById('taskCommand_invalid_feedback');
    if (feedback) feedback.textContent = msg;
}
function hideCommandInvalid() {
    document.getElementById('codemirror-taskCommand').classList.remove('is-invalid');
}

document.addEventListener('DOMContentLoaded', function() {
    // Drawflow init
    const drawflowElem = document.getElementById('drawflow');
    editor = new Drawflow(drawflowElem);
    editor.reroute = true;
    editor.start();



    // CodeMirror setup for Command on modal show/hide
    const taskModalElem = document.getElementById('taskModal');
    taskModalElem.addEventListener('shown.bs.modal', function () {
        setupCodeMirrorCommand();
        setTimeout(() => {
            if (cmTaskCommand) cmTaskCommand.refresh();
        }, 150);
    });
    taskModalElem.addEventListener('hidden.bs.modal', function () {
        if (cmTaskCommand) {
            cmTaskCommand.save(); // sync with textarea
            cmTaskCommand.toTextArea();
            cmTaskCommand = null;
        }
        // Reset validations (for both codemirror and others)
        hideInvalid(document.getElementById('taskName'));
        hideCommandInvalid();
    });

    // Restore diagram on PHP validation error (prefer diagram_data, fallback to drawflow_data)
    @php
    $restoreDiagram = null;
    if ($errors->any()) {
        // Priority 1: diagram_data (newer field), else drawflow_data (legacy/fallback)
        $restoreDiagram = old('diagram_data') ?: old('drawflow_data');
    }
    @endphp

    // Restore diagram on localStorage
    if(localStorage.getItem('diagram_data')) {
        try {
            let revivedDrawflow = JSON.parse(localStorage.getItem('diagram_data'));
            editor.import(revivedDrawflow);
        } catch(e) {}
    }

    @if(isset($restoreDiagram) && filled($restoreDiagram))
        try {
            let revivedDrawflow = JSON.parse(@json($restoreDiagram));
            // Remove wait_all from any previous node data if present
            if(revivedDrawflow && revivedDrawflow.drawflow && revivedDrawflow.drawflow.Home && revivedDrawflow.drawflow.Home.data) {
                for (const id in revivedDrawflow.drawflow.Home.data) {
                    if (revivedDrawflow.drawflow.Home.data[id].data && revivedDrawflow.drawflow.Home.data[id].data.hasOwnProperty('wait_all')) {
                        delete revivedDrawflow.drawflow.Home.data[id].data.wait_all;
                    }
                }
            }
            editor.import(revivedDrawflow);
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
                                    id
                                );
                            }
                        }
                    }
                }
            }, 100);
        } catch (e) {
            // Could not restore, fail silently
        }
    @else
        // Load old drawflow data if exists (for blade: old input restoration, fallback for < 2024)
        @if(old('drawflow_data'))
            try {
                let oldDrawflow = JSON.parse(@json(old('drawflow_data')));
                // Remove wait_all from any previous node data if present
                if(oldDrawflow && oldDrawflow.drawflow && oldDrawflow.drawflow.Home && oldDrawflow.drawflow.Home.data) {
                    for (const id in oldDrawflow.drawflow.Home.data) {
                        if (oldDrawflow.drawflow.Home.data[id].data && oldDrawflow.drawflow.Home.data[id].data.hasOwnProperty('wait_all')) {
                            delete oldDrawflow.drawflow.Home.data[id].data.wait_all;
                        }
                    }
                }
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
                                        id
                                    );
                                }
                            }
                        }
                    }
                }, 100);
            } catch (e) {}
        @endif

    @endif

    // Save drawflow data on form submit
    if(document.getElementById('workflowForm')) {
        document.getElementById('workflowForm').addEventListener('submit', function() {
            document.getElementById('drawflow_data').value = JSON.stringify(editor.export());
        });
    }

    // Add new task (open modal in add mode)
    document.getElementById('btn-insert-task').addEventListener('click', function() {
        document.getElementById('taskForm').reset();
        document.getElementById('taskNodeId').value = '';
        selectedNodeId = null;
        document.getElementById('taskModalLabel').textContent = 'Add Task';
        setTaskCommandValue('');
        // Also reset CodeMirror and remove validation
        hideInvalid(document.getElementById('taskName'));
        hideCommandInvalid();
    });

    // Lock/unlock drawflow move/connection
    document.getElementById('btn-lock-drawflow').addEventListener('click', function() {
        isLocked = !isLocked;
        if(isLocked) {
            editor.editor_mode = 'fixed';
            this.innerHTML = '<i class="fas fa-lock me-1" id="icon-lock-drawflow"></i> Unlock Drawflow';
            this.classList.remove('btn-primary');
            this.classList.add('btn-primary');
        } else {
            editor.editor_mode = 'edit';
            this.innerHTML = '<i class="fas fa-lock-open me-1" id="icon-lock-drawflow"></i> Lock Drawflow';
            this.classList.remove('btn-primary');
            this.classList.add('btn-primary');
        }
    });

    // Open edit modal for node
    function openEditTaskModal(nodeId) {
        let nodeData = editor.getNodeFromId(nodeId);
        if(nodeData && nodeData.name === 'task') {
            document.getElementById('taskNodeId').value = nodeId;
            document.getElementById('taskName').value = nodeData.data.name || '';
            document.getElementById('taskDescription').value = nodeData.data.description || '';
            setTaskCommandValue(nodeData.data.command || '');
            document.getElementById('wait').checked = !!nodeData.data.wait;
            selectedNodeId = nodeId;
            document.getElementById('taskModalLabel').textContent = 'Edit Task';
            hideInvalid(document.getElementById('taskName'));
            hideCommandInvalid();
            let modal = new bootstrap.Modal(document.getElementById('taskModal'));
            modal.show();
        }
    }

    // Delegate click on edit/delete inside nodes
    drawflowElem.addEventListener('click', function(e) {
        if(isLocked) return;
        // Edit
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
        // Delete
        let deleteBtn = e.target.closest('.btn-delete-task');
        if(deleteBtn) {
            let nodeElem = e.target.closest('.drawflow-node');
            let nodeId = deleteBtn.getAttribute('data-node-id') || (nodeElem ? nodeElem.id.replace('node-', '') : null);
            if(nodeId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Delete Task',
                    text: 'Are you sure you want to delete this task?',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> Delete',
                    cancelButtonText: '<i class="fas fa-times me-1"></i> Cancel',
                    background: '#212529',
                    color: '#fff',
                    customClass: {
                      confirmButton: 'btn btn-primary me-2', // Add right margin
                      cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if(result.isConfirmed) {
                        editor.removeNodeId('node-' + nodeId);
                    }
                });
                
            }
            e.stopPropagation();
            return;
        }
        updateDiagramData(editor);
    });

    // Handle add/edit submit with custom validation
    document.getElementById('taskForm').addEventListener('submit', function(e) {
        e.preventDefault();

        let valid = true;

        let nameEl = document.getElementById('taskName');
        let name = nameEl.value.trim();
        if(!name) {
            showInvalid(nameEl, 'Task name is required.');
            valid = false;
        } else {
            hideInvalid(nameEl);
        }

        let description = document.getElementById('taskDescription').value.trim();
        let wait = document.getElementById('wait').checked ? true : false;
        let command = getTaskCommandValue().trim(); // use codemirror value
        let commandTextarea = document.getElementById('taskCommand');

        if(!command) {
            showCommandInvalid('Command is required.');
            // Focus CodeMirror
            if (cmTaskCommand) {
                cmTaskCommand.focus();
            }
            valid = false;
        } else {
            hideCommandInvalid();
        }

        if (!valid) {
            return;
        }

        // Set value to textarea for form compatibility if submitted by browser
        commandTextarea.value = command;

        let nodeId = document.getElementById('taskNodeId').value;
        let html = getTaskNodeHtml(name, '');

        if(nodeId) {
            // Edit existing node
            let node = editor.getNodeFromId(nodeId);
            if(node) {
                editor.updateNodeDataFromId(nodeId, {name, description, command,status: "", wait: wait, stdout: "", error: "", pid: ""});
                let nodeElem = document.getElementById('node-' + nodeId);
                if(nodeElem) {
                    let contentElem = nodeElem.querySelector('.drawflow_content_node');
                    if(contentElem) {
                        contentElem.innerHTML = getTaskNodeHtml(name, nodeId);
                    }
                }
                updateDiagramData(editor);
            }
        } else {
            // Add new node
            let newId = editor.addNode('task', 1, 1, 100, 100, 'task', {name, description, command,status: "", wait: wait, stdout: "", error: "", pid: ""}, html);
            setTimeout(() => {
                let nodeElem = document.getElementById('node-' + newId);
                if(nodeElem) {
                    let contentElem = nodeElem.querySelector('.drawflow_content_node');
                    if(contentElem) {
                        contentElem.innerHTML = getTaskNodeHtml(name, newId);
                    }
                }
            }, 50);
            updateDiagramData(editor);
        }
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('taskModal')).hide();
        updateDiagramData(editor);
    });

    //delete local storage
    $("#btn-save-workflow").on('click', function() {
        localStorage.removeItem('diagram_data');
    });

    // Double click node to edit (edit mode)
    drawflowElem.addEventListener('dblclick', function(e) {
        if (isLocked) return;
        let node = e.target.closest('.drawflow-node');
        if (node) {
            let id = node.id.replace('node-', '');
            openEditTaskModal(id);
        }
    });


});
</script>
@endpush



@endsection
