<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scans;
use App\Models\Workflows;
class ExplorerController extends Controller
{
    public function index(Request $request, $id)
    {
        $data['scan'] = Scans::find($id);
        $data['workflows'] = Workflows::all();
        //get item from directory
        if($request->has('path')){
            //prevent directory traversal
            if(strpos($request->path, '..') !== false){
                return redirect()->route('explorer', $id)->with('error', 'Invalid path');
            }
            $path = $data['scan']->output . DIRECTORY_SEPARATOR . $request->path;
        }
        else{
            $path = $data['scan']->output;
        }
        $items = scandir($path);
        
        $itemList = [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $fullPath = $path . DIRECTORY_SEPARATOR . $item;
            // function to convert permission to rwx string

            if (is_dir($fullPath)) {
                $itemList[] = [
                    'name' => $item,
                    'type' => 'directory',
                    'permissions' => $this->_permsToRwx(fileperms($fullPath)),
                ];
            } else {
                $itemList[] = [
                    'name' => $item,
                    'type' => 'file',
                    'size' => filesize($fullPath),
                    'modified' => filemtime($fullPath),
                    'permissions' => $this->_permsToRwx(fileperms($fullPath)),
                ];
            }
        }
        $data['items'] = $itemList;
        return view('explorer.index', compact('data'));
    }


    public function view(Request $request, $id)
    {
        $data['scan'] = Scans::find($id);
        //prevent directory traversal
        if(strpos($request->path, '..') !== false){
            return redirect()->route('explorer', $id)->with('error', 'Invalid path');
        }
        $path = $data['scan']->output . DIRECTORY_SEPARATOR . $request->path;
        $data['info'] = array(
            'name' => $request->path,
            'type' => 'file',
            'size' => filesize($path),
            'modified' => filemtime($path),
            'permissions' => $this->_permsToRwx(fileperms($path)),
        );
        $data['content'] = file_get_contents($path);
        return view('explorer.view', compact('data'));
    }

    public function download(Request $request, $id)
    {

        //prevent directory traversal
        if(strpos($request->path, '..') !== false){
            return redirect()->route('explorer', $id)->with('error', 'Invalid path');
        }
        $data['scan'] = Scans::find($id);
        $path = $data['scan']->output . DIRECTORY_SEPARATOR . $request->path;
        return response()->download($path);
    }

    public function export(Request $request, $id)
    {
        $data['scan'] = Scans::find($id);
        $path = $data['scan']->output;
        $zipFileName = 'scan_' . $id . '_export_' . date('Ymd_His') . '.zip';
        $zipFullPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipFileName;

        $zip = new \ZipArchive();
        if ($zip->open($zipFullPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return redirect()->route('explorer', $id)->with('error', 'Failed to create zip archive.');
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = ltrim(str_replace($path, '', $filePath), DIRECTORY_SEPARATOR);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();

        return response()->download($zipFullPath, $zipFileName)->deleteFileAfterSend(true);
    }


    public function _permsToRwx($perms) {
        $info = '';
        // Owner
        $info .= (($perms & 0x0100) ? 'r' : '-');
        $info .= (($perms & 0x0080) ? 'w' : '-');
        $info .= (($perms & 0x0040) ?
                    (($perms & 0x0800) ? 's' : 'x') :
                    (($perms & 0x0800) ? 'S' : '-'));
        // Group
        $info .= (($perms & 0x0020) ? 'r' : '-');
        $info .= (($perms & 0x0010) ? 'w' : '-');
        $info .= (($perms & 0x0008) ?
                    (($perms & 0x0400) ? 's' : 'x') :
                    (($perms & 0x0400) ? 'S' : '-'));
        // World
        $info .= (($perms & 0x0004) ? 'r' : '-');
        $info .= (($perms & 0x0002) ? 'w' : '-');
        $info .= (($perms & 0x0001) ?
                    (($perms & 0x0200) ? 't' : 'x') :
                    (($perms & 0x0200) ? 'T' : '-'));
        return $info;
    }



}
