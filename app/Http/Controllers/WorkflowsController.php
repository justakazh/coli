<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workflows;
use Illuminate\Support\Str;
use App\Models\Tasks;
use Exception;

class WorkflowsController extends Controller
{
    public function index()
    {
        try {
            $workflows = Workflows::paginate(15);
            return view('workflows.index', compact('workflows'));
        } catch (Exception $e) {
            return redirect()->route('workflows.index')->with('error', 'An error occurred while loading workflows: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        try {
            $name = $request->input('name');
            $tags = $request->input('tags');
            $category = $request->input('category');
            $description = $request->input('description');

            $workflows = Workflows::query()
                ->when($name, function ($query, $name) {
                    return $query->where('name', 'like', '%' . $name . '%');
                })
                ->when($category, function ($query, $category) {
                    return $query->where('category', 'like', '%' . $category . '%');
                })
                ->when($tags, function ($query, $tags) {
                    return $query->where('tags', 'like', '%' . $tags . '%');
                })
                ->when($description, function ($query, $description) {
                    return $query->where('description', 'like', '%' . $description . '%');
                })
                ->orderBy('id', 'desc')
                ->paginate(15)
                ->withQueryString();

            return view('workflows.index', compact('workflows'));
        } catch (Exception $e) {
            return redirect()->route('scopes.index')->with('error', 'An error occurred during search: ' . $e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {
            if($request->type == 'design') {
                return view('workflows.create-design');
            } else if($request->type == 'json') {
                return view('workflows.create-json');
            } else {
                return redirect()->route('workflows.index')->with('error', 'Invalid workflow type');
            }
        } catch (Exception $e) {
            return redirect()->route('workflows.index')->with('error', 'An error occurred while loading the create form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        

        try {
            //check type of workflow

            //if design
            if($request->type == 'design') {
                try {
                    $request->validate([
                        'name' => 'required|string|max:255',
                        'description' => 'nullable|string',
                        'tags' => 'nullable|string',
                        'drawflow_data' => 'required|string'
                    ]);
                } catch (Exception $e) {
                    return redirect()->back()->withInput()->with('error', 'Validation failed: ' . $e->getMessage());
                }
                
                //create file for workflow script
                $script = $request->drawflow_data;
                
                //check if json valid
                if(!json_decode($script, true)) {
                    return redirect()->back()->withInput()->with('error', 'Invalid JSON format');
                }


                //build tree of tasks
                $script = json_decode($script, true);
                $tasks = $this->buildTaskTreeFromDrawflow($script);




                //create workflow directory and file
                $filename = Str::slug($request->name) . '-workflow.json';
                $workflow_path = env('HUNT_PATH') . "/workflows/" . $filename;
                if (!is_dir(env('HUNT_PATH') . "/workflows")) {
                    mkdir(env('HUNT_PATH') . "/workflows", 0777, true);
                }

                //build json for workflows
                $workflow_json = [
                    "name" => $request->name,
                    "slug" => Str::slug($request->name),
                    "description" => $request->description,
                    "tags" => $request->tags,
                    "category" => $request->category,
                    "tasks" => $tasks
                ];


                
                //save workflow json to file
                file_put_contents($workflow_path, json_encode($workflow_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));


                //save workflow information in database
                $workflow = Workflows::create([
                    'name' => $request->name,
                    'type' => 'design',
                    'slug' => Str::slug($request->name),
                    'description' => $request->description,
                    'tags' => $request->tags,
                    'category' => $request->category,
                    'script_path' => $workflow_path,
                    'drawflow_data' => $request->drawflow_data,
                    'tasks_data' => $request->task_data
                ]);

                return redirect()->route('workflows.index')->with('success', 'Workflow created successfully');
            }


            //if json
            elseif($request->type == 'json') {

                try {
                    $request->validate([
                        'name' => 'required|string|max:255',
                        'description' => 'nullable|string',
                        'tags' => 'nullable|string',
                        'script_workflow' => 'required|string'
                    ]);
                } catch (Exception $e) {
                    return redirect()->back()->withInput()->with('error', 'Validation failed: ' . $e->getMessage());
                }



                try {
                    //validate json script
                    if(!json_decode($request->script_workflow, true)) {
                        return redirect()->back()->withInput()->with('error', 'Invalid JSON format');
                    }

                    //create workflow directory and file
                    $filename = Str::slug($request->name) . '-workflow.json';
                    $workflow_path = env('HUNT_PATH') . "/workflows/" . $filename;
                    if (!is_dir(env('HUNT_PATH') . "/workflows")) {
                        mkdir(env('HUNT_PATH') . "/workflows", 0777, true);
                    }

                    //pretty json
                    $pretty_json = json_encode(json_decode($request->script_workflow, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

                    //save workflow json to file
                    file_put_contents($workflow_path, $pretty_json);

                    //save workflow information in database
                    $workflow = Workflows::create([
                        'name' => $request->name,
                        'type' => 'json',
                        'slug' => Str::slug($request->name),
                        'description' => $request->description,
                        'tags' => $request->tags,
                        'category' => $request->category,
                        'script_path' => $workflow_path
                    ]);
                    return redirect()->route('workflows.index')->with('success', 'Workflow created successfully');

                } catch (\Throwable $e) {
                    return redirect()->back()->withInput()->with('error', 'Invalid JSON format: ' . $e->getMessage());
                }
            }
            //if not design or json
            else {
                return redirect()->route('workflows.index')->with('error', 'Invalid workflow type');
            }
            return redirect()->route('workflows.index')->with('success', 'Workflow created successfully');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the workflow: ' . $e->getMessage());
        }
    }

    function buildTaskTreeFromDrawflow(array $drawflow)
    {
        $nodes = $drawflow['drawflow']['Home']['data'] ?? [];
        $nodeMap = [];

        // 1. Mapping semua node by ID
        foreach ($nodes as $id => $node) {
            $nodeMap[$id] = [
                'id' => $id,
                'name' => $node['data']['name'],
                'description' => $node['data']['description'] ?? null,
                'result' => $node['data']['result'] ?? null,
                'command' => $node['data']['command'] ?? null,
                'wait_all' => $node['data']['wait_all'] ?? false,
                'parents' => [],
                'children' => [],
            ];
        }

        // 2. Hubungkan parent-child berdasarkan koneksi output
        foreach ($nodes as $id => $node) {
            foreach ($node['outputs'] as $output) {
                foreach ($output['connections'] as $connection) {
                    $targetId = $connection['node'];
                    $nodeMap[$id]['children'][] = $targetId;
                    $nodeMap[$targetId]['parents'][] = $id;
                }
            }
        }

        // 3. Bangun tree dari root node (tidak punya parent)
        $rootNodes = array_filter($nodeMap, fn($node) => count($node['parents']) === 0);

        $buildTree = function($nodeId) use (&$buildTree, $nodeMap) {
            $node = $nodeMap[$nodeId];

            return [
                'name' => $node['name'],
                'description' => $node['description'],
                'result' => $node['result'],
                'command' => $node['command'],
                'wait_all' => $node['wait_all'] ?? false,
                'tasks' => array_map(fn($childId) => $buildTree($childId), $node['children'])
            ];
        };

        $tree = [];
        foreach (array_keys($rootNodes) as $rootId) {
            $tree[] = $buildTree($rootId);
        }

        return $tree;
    }

    public function edit($id)
    {
        try {
            $workflow = Workflows::find($id);
            if (!$workflow) {
                return redirect()->route('workflows.index')->with('error', 'Workflow not found');
            }
            if ($workflow->type == 'design') {
                return view('workflows.edit-design', compact('workflow'));
            } else if ($workflow->type == 'json') {
                return view('workflows.edit-json', compact('workflow'));
            } else {
                return redirect()->route('workflows.index')->with('error', 'Invalid workflow type');
            }
        } catch (Exception $e) {
            return redirect()->route('workflows.index')->with('error', 'An error occurred while loading the edit form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            //check type of workflow
            $workflow = Workflows::find($id);
            if (!$workflow) {
                return redirect()->route('workflows.index')->with('error', 'Workflow not found');
            }

            //if design
            if($request->type == 'design') {
                try {
                    $request->validate([
                        'name' => 'required|string|max:255',
                        'description' => 'nullable|string',
                        'tags' => 'nullable|string',
                        'drawflow_data' => 'required|string'
                    ]);
                } catch (Exception $e) {
                    return redirect()->back()->withInput()->with('error', 'Validation failed: ' . $e->getMessage());
                }



                //create file for workflow script
                $script = $request->drawflow_data;
                
                //check if json valid
                if(!json_decode($script, true)) {
                    return redirect()->back()->withInput()->with('error', 'Invalid JSON format');
                }


                //build tree of tasks
                $script = json_decode($script, true);
                $tasks = $this->buildTaskTreeFromDrawflow($script);

                //build json for workflows
                $workflow_json = [
                    "name" => $request->name,
                    "slug" => Str::slug($request->name),
                    "description" => $request->description,
                    "tags" => $request->tags,
                    "category" => $request->category,
                    "tasks" => $tasks
                ];

                //create workflow directory and file
                $filename = Str::slug($request->name) . '-workflow.json';
                $workflow_path = env('HUNT_PATH') . "/workflows/" . $filename;
                if (!is_dir(env('HUNT_PATH') . "/workflows")) {
                    mkdir(env('HUNT_PATH') . "/workflows", 0777, true);
                }

                //build json for workflows
                $workflow->name = $request->name;
                $workflow->description = $request->description;
                $workflow->tags = $request->tags;
                $workflow->category = $request->category;
                $workflow->drawflow_data = $request->drawflow_data;
                $workflow->save();
                
                //save workflow json to file
                file_put_contents($workflow_path, json_encode($workflow_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));


                return redirect()->route('workflows.index')->with('success', 'Workflow created successfully');
            }


            //if json
            elseif($request->type == 'json') {


                try {
                    $request->validate([
                        'name' => 'required|string|max:255',
                        'description' => 'nullable|string',
                        'tags' => 'nullable|string',
                        'script_workflow' => 'required|string'
                    ]);
                } catch (Exception $e) {
                    return redirect()->back()->withInput()->with('error', 'Validation failed: ' . $e->getMessage());
                }



                try {
                    //validate json script
                    if(!json_decode($request->script_workflow, true)) {
                        return redirect()->back()->withInput()->with('error', 'Invalid JSON format');
                    }

                    //create workflow directory and file
                    $filename = Str::slug($request->name) . '-workflow.json';
                    $workflow_path = env('HUNT_PATH') . "/workflows/" . $filename;
                    if (!is_dir(env('HUNT_PATH') . "/workflows")) {
                        mkdir(env('HUNT_PATH') . "/workflows", 0777, true);
                    }

                    //pretty json
                    $pretty_json = json_encode(json_decode($request->script_workflow, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

                    //save workflow json to file
                    file_put_contents($workflow_path, $pretty_json);

                    //save workflow information in database
                    $workflow->name = $request->name;
                    $workflow->description = $request->description;
                    $workflow->tags = $request->tags;
                    $workflow->category = $request->category;
                    $workflow->script_path = $workflow_path;
                    $workflow->save();
                    
                    return redirect()->route('workflows.index')->with('success', 'Workflow created successfully');

                } catch (\Throwable $e) {
                    return redirect()->back()->withInput()->with('error', 'Invalid JSON format: ' . $e->getMessage());
                }
            }
            //if not design or json
            else {
                return redirect()->route('workflows.index')->with('error', 'Invalid workflow type');
            }
            return redirect()->route('workflows.index')->with('success', 'Workflow created successfully');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the workflow: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $workflow = Workflows::find($id);
            if (!$workflow) {
                return redirect()->route('workflows.index')->with('error', 'Workflow not found');
            }

            

            $scans = $workflow->scans;
            foreach ($scans as $scan) {
                // Force delete the scan's directory, even if it is not empty and if the scan is not running
                if (!empty($scan->output_path) && is_dir($scan->output_path) && $scan->status !== 'running') {
                    try {
                        $it = new \RecursiveDirectoryIterator($scan->output_path, \FilesystemIterator::SKIP_DOTS);
                        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
                        foreach ($files as $file) {
                            if ($file->isDir()) {
                                @rmdir($file->getRealPath());
                            } else {
                                @unlink($file->getRealPath());
                            }
                        }
                        @rmdir($scan->output_path);
                    } catch (\Exception $e) {
                        // Ignore errors, but you may want to log them if needed
                    }
                }
            }
            //delete all scans related to the workflow
            $workflow->scans()->delete();

            //delete workflow
            $workflow->delete();

            //check if workflow json file exists
            $filename = Str::slug($workflow->name) . '-workflow.json';
            $workflow_path = env('HUNT_PATH') . "/workflows/" . $filename;
            if (file_exists($workflow_path)) {
                unlink($workflow_path);
            }

            return redirect()->route('workflows.index')->with('success', 'Workflow deleted successfully');
        } catch (Exception $e) {
            return redirect()->route('workflows.index')->with('error', 'An error occurred while deleting the workflow: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        try {
            $workflow = Workflows::find($id);
            if (!$workflow) {
                return redirect()->route('workflows.index')->with('error', 'Workflow not found');
            }

            //check file is exist or not
            if(!file_exists($workflow->script_path)){
                return redirect()->route('workflows.index')->with('error', 'Workflow script not found');
            } 
            $script = file_get_contents($workflow->script_path);
            $script = json_decode($script, true);

            $filename = Str::slug($workflow->name) . '-workflow.json';
            $jsonContent = json_encode($script, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            return response($jsonContent)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (Exception $e) {
            return redirect()->route('workflows.index')->with('error', 'An error occurred while downloading the workflow: ' . $e->getMessage());
        }
    }


    public function checkTools(Request $request)
    {
        // Check tool availability using 'which'
        $toolsInput = $request->input('tools', []);
        $results = [];

        // If $toolsInput is not an array, convert it to an array
        if (!is_array($toolsInput)) {
            $toolsInput = [$toolsInput];
        }

        foreach ($toolsInput as $tool) {
            // If the string contains a comma, split into multiple tools
            if (strpos($tool, ',') !== false) {
                $subTools = array_map('trim', explode(',', $tool));
            } else {
                $subTools = [trim($tool)];
            }

            foreach ($subTools as $subTool) {
                if ($subTool === '') continue;

                // Allow only valid tool names (alphanumeric, dashes, underscores, dots)
                if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $subTool)) {
                    $results[$subTool] = false;
                    continue;
                }

                // Use proc_open to avoid code injection
                $process = proc_open(
                    ['which', $subTool],
                    [
                        1 => ['pipe', 'w'],
                        2 => ['pipe', 'w'],
                    ],
                    $pipes
                );

                if (is_resource($process)) {
                    $output = stream_get_contents($pipes[1]);
                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    $returnCode = proc_close($process);
                    $results[$subTool] = !empty(trim($output));
                } else {
                    $results[$subTool] = false;
                }
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }
    
}
