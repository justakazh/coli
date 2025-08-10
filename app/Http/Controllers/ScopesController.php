<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Scopes;
use App\Models\Scans;
use Illuminate\Support\Str;
use Exception;

class ScopesController extends Controller
{
    public function index()
    {
        try {
            $scopes = Scopes::orderBy('id', 'desc')->paginate(15);
            return view('scopes.index', compact('scopes'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading scopes: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        try {
            $scopes = Scopes::when($request->target, function ($query) use ($request) {
                    return $query->where('target', 'like', '%' . $request->target . '%');
                })
                ->when($request->type, function ($query) use ($request) {
                    return $query->where('type', 'like', '%' . $request->type . '%');
                })
                ->orderBy('id', 'desc')
                ->paginate(15)
                ->withQueryString();
            return view('scopes.index', compact('scopes'));
        } catch (Exception $e) {
            return redirect()->route('scopes.index')->with('error', 'An error occurred during search: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('scopes.create');
        } catch (Exception $e) {
            return redirect()->route('scopes.index')->with('error', 'An error occurred while loading the create form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'target' => 'required|string',
                'type' => 'required|string|max:255',
            ]);
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Validation failed: ' . $e->getMessage());
        }

        try {
            $targets = preg_split('/\r\n|\r|\n/', $request->target);
            $createdScopes = [];
            foreach ($targets as $target) {
                $target = trim($target);
                if ($target === '') continue;

                $scope = new Scopes();
                $scope->target = $target;
                $scope->type = $request->type;
                $scope->description = $request->description ?? '';
                $scope->output_path = '';
                $scope->contact = $request->contact ?? '';
                $scope->save();

                $directory = rtrim(env('HUNT_PATH'), '/') . '/scans/outputs/' . $scope->id;

                if (!file_exists($directory)) {
                    if (!@mkdir($directory, 0777, true)) {
                        // If directory cannot be created, delete the scope and return error
                        $scope->delete();
                        return redirect()->route('scopes.index')->with('error', 'Failed to create directory for the scope.');
                    }
                }

                $scope->output_path = $directory;
                $scope->save();

                $createdScopes[] = $scope;
            }
            return redirect()->route('scopes.index')->with('success', 'Scope(s) created successfully');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the scope: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $scope = Scopes::find($id);
            if (!$scope) {
                return redirect()->route('scopes.index')->with('error', 'Scope not found');
            }
            return view('scopes.edit', compact('scope'));
        } catch (Exception $e) {
            return redirect()->route('scopes.index')->with('error', 'An error occurred while loading the edit form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $scope = Scopes::find($id);
            if (!$scope) {
                return redirect()->route('scopes.index')->with('error', 'Scope not found');
            }
            $request->validate([
                'target' => 'required|string',
                'type' => 'required|string|max:255',
            ]);
            $scope->target = $request->target;
            $scope->type = $request->type;
            $scope->description = $request->description ?? '';
            $scope->contact = $request->contact ?? '';
            $scope->save();
            return redirect()->route('scopes.index')->with('success', 'Scope updated successfully');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'An error occurred while updating the scope: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $scope = Scopes::find($id);
            if (!$scope) {
                return redirect()->route('scopes.index')->with('error', 'Scope not found');
            }

            $directory = rtrim(env('HUNT_PATH'), '/') . '/scans/outputs/' . $scope->id;
            if (file_exists($directory)) {
                try {
                    $it = new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS);
                    $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
                    foreach ($files as $file) {
                        if ($file->isDir()) {
                            @rmdir($file->getRealPath());
                        } else {
                            @unlink($file->getRealPath());
                        }
                    }
                    @rmdir($directory);
                } catch (Exception $e) {
                    // Even if directory deletion fails, continue and notify user
                    $scope->delete();
                    return redirect()->route('scopes.index')->with('error', 'Scope deleted, but failed to delete directory: ' . $e->getMessage());
                }
            }

            $scope->delete();

            // Delete related scans
            try {
                $scans = Scans::where('scope_id', $id)->get();
                foreach ($scans as $scan) {
                    $scan->delete();
                }
            } catch (Exception $e) {
                return redirect()->route('scopes.index')->with('error', 'Scope deleted, but failed to delete related scans: ' . $e->getMessage());
            }

            return redirect()->route('scopes.index')->with('success', 'Scope deleted successfully');
        } catch (Exception $e) {
            return redirect()->route('scopes.index')->with('error', 'An error occurred while deleting the scope: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('scope_ids');
        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('scopes.index')->with('error', 'No scopes selected for deletion.');
        }

        $deleted = [];
        $errors = [];

        foreach ($ids as $id) {
            try {
                $scope = Scopes::find($id);
                if (!$scope) {
                    $errors[] = "Scope ID $id not found.";
                    continue;
                }

                $directory = rtrim(env('HUNT_PATH'), '/') . '/scans/outputs/' . $scope->id;
                if (file_exists($directory)) {
                    try {
                        $it = new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS);
                        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
                        foreach ($files as $file) {
                            if ($file->isDir()) {
                                @rmdir($file->getRealPath());
                            } else {
                                @unlink($file->getRealPath());
                            }
                        }
                        @rmdir($directory);
                    } catch (Exception $e) {
                        $errors[] = "Failed to delete directory for Scope ID $id: " . $e->getMessage();
                    }
                }

                // Delete related scans
                try {
                    $scans = Scans::where('scope_id', $id)->get();
                    foreach ($scans as $scan) {
                        $scan->delete();
                    }
                } catch (Exception $e) {
                    $errors[] = "Failed to delete scans for Scope ID $id: " . $e->getMessage();
                }

                $scope->delete();
                $deleted[] = $id;
            } catch (Exception $e) {
                $errors[] = "Error deleting Scope ID $id: " . $e->getMessage();
            }
        }

        $message = '';
        if (!empty($deleted)) {
            $message .= 'Deleted scopes: ' . implode(', ', $deleted) . '. ';
        }
        if (!empty($errors)) {
            $message .= 'Errors: ' . implode(' | ', $errors);
            return redirect()->route('scopes.index')->with('error', $message);
        }

        return redirect()->route('scopes.index')->with('success', $message ?: 'Selected scopes deleted successfully.');
    }
}
