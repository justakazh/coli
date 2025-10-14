<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workflows;
use Illuminate\Support\Str;

class WorkflowsController extends Controller
{
    public function index(Request $request)
    {
        
        // Build query with combinable search parameters
        $query = Workflows::query();

        // Search by target (actually matches related 'scopes.target')
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by workflow_id (from form: 'workflow')
        if ($request->filled('description') || $request->filled('description_id')) {
            $workflowId = $request->description ?? $request->description_id;
            $query->where('description', 'like', '%' . $workflowId . '%');
        }

        // Filter by status
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $data['workflows'] = $query->get();

        return view('workflows.index', compact('data'));
    }

    public function createScript()
    {
        return view('workflows.create.script');
    }

    public function createDiagram()
    {
        return view('workflows.create.diagram');
    }

    public function storeScript(Request $request)
    {
        //validate request
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'script' => 'required|file|mimes:json',
            'type' => 'required|in:script',
        ]);

        //create slug
        $slug = Str::slug($request->name);
        
        
        //create directory workflow if not exists
        $directory = env('WORKDIR').'/workflows';
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        //save script
        $script = $request->file('script');
        //save to $directory
        $filename = $slug.'.'.$script->getClientOriginalExtension();
        $script_path = $directory.'/'.$filename;
        $script->move($directory, $filename);

        //save to database
        $workflow = Workflows::create([
            'name' => $request->name,
            'slug' => $slug,
            'type' => $request->type,
            'description' => $request->description,
            'script_path' => $script_path,
        ]);
        return redirect()->route('workflows')->with('success', 'Workflow created successfully');

    }


    public function storeDiagram(Request $request)
    {
        // Validate request
        $request->validate([
            'workflow_name' => 'required',
            'workflow_description' => 'required',
            'diagram_data' => 'required',
            'type' => 'required|in:diagram',
        ]);

        $slug = Str::slug($request->workflow_name);

        // Decode diagram_data from JSON
        $diagram = json_decode($request->diagram_data, true);

        if (!$diagram || !isset($diagram['drawflow']['Home']['data'])) {
            return back()->with('error', 'Diagram data not valid');
        }

        //save workflow script to file json
        $directory = env('WORKDIR').'/workflows';
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }

        $filename = $slug.'.json';
        $script_path = $directory.'/'.$filename;
        file_put_contents($script_path, $request->diagram_data);
        // Save to database
        $workflow = Workflows::create([
            'name' => $request->workflow_name,
            'slug' => $slug,
            'type' => $request->type,
            'description' => $request->workflow_description,
            'diagram_data' => $request->diagram_data,
            'script_path' => $script_path
        ]);
        return redirect()->route('workflows')->with('success', 'Workflow created successfully');
    }


    public function editScript($id)
    {
        $data['workflow'] = Workflows::find($id);
        return view('workflows.edit.script', compact('data'));
    }

    public function editDiagram($id)
    {
        $data['workflow'] = Workflows::find($id);
        return view('workflows.edit.diagram', compact('data'));
    }

    public function updateScript(Request $request, $id)
    {
        //validate request
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'script' => 'file|mimes:json',
            'type' => 'required|in:script,diagram',
        ]);

        $workflow = Workflows::find($id);
        $directory = env('WORKDIR') . '/workflows';
        $slug = Str::slug($request->name);

        if ($request->hasFile('script')) {
            // Hapus script lama jika ada, kemudian upload yang baru
            if (file_exists($workflow->script_path)) {
                unlink($workflow->script_path);
            }
            $script = $request->file('script');
            $filename = $slug . '.' . $script->getClientOriginalExtension();
            $script_path = $directory . '/' . $filename;
            $script->move($directory, $filename);
            $workflow->script_path = $script_path;
        } else {
            // Jika nama workflow diubah, rename file lama agar sesuai slug baru
            $old_path = $workflow->script_path;
            $ext = pathinfo($old_path, PATHINFO_EXTENSION);
            $new_path = $directory . '/' . $slug . '.' . $ext;
            if ($old_path !== $new_path && file_exists($old_path)) {
                rename($old_path, $new_path);
                $workflow->script_path = $new_path;
            }
        }

        $workflow->name = $request->name;
        $workflow->slug = $slug;
        $workflow->description = $request->description;
        $workflow->type = $request->type;
        $workflow->save();
        return redirect()->route('workflows')->with('success', 'Workflow updated successfully');
    }


    public function updateDiagram(Request $request, $id)
    {
        // Validate request
        $request->validate([
            'workflow_name' => 'required',
            'workflow_description' => 'required',
            'diagram_data' => 'required',
            'type' => 'required|in:diagram',
        ]);
        


        $workflow = Workflows::find($id);

        if($request->workflow_name != $workflow->name){
            $slug = Str::slug($request->workflow_name);
            $workflow->slug = $slug;
        }

        $workflow->name = $request->workflow_name;
        $workflow->description = $request->workflow_description;
        $workflow->type = $request->type;

        // Decode diagram_data from JSON
        $diagram = json_decode($request->diagram_data, true);

        if (!$diagram || !isset($diagram['drawflow']['Home']['data'])) {
            return back()->with('error', 'Diagram data not valid');
        }


        //save workflow script to file json
        $directory = env('WORKDIR').'/workflows';
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        $filename = $workflow->slug.'.json';
        $script_path = $directory.'/'.$filename;
        file_put_contents($script_path, $request->diagram_data);
        
        $workflow->diagram_data = $request->diagram_data;
        $workflow->script_path = $script_path;
        $workflow->save();
        return redirect()->route('workflows')->with('success', 'Workflow updated successfully');

    }
    

    public function download($id)
    {
        $workflow = Workflows::find($id);
        //get file
        if(file_exists($workflow->script_path)){
            return response()->download($workflow->script_path);
        }else{
            return redirect()->route('workflows')->with('error', 'Workflow script not found');
        }
    }


    public function destroy($id)
    {
        $workflow = Workflows::find($id);
        if(file_exists($workflow->script_path)){
            unlink($workflow->script_path);
        }
        $workflow->delete();
        return redirect()->route('workflows')->with('success', 'Workflow deleted successfully');

    }
}
