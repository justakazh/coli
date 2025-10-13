<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scans;
class TrackController extends Controller
{
    public function index($id)
    {
        $data['scan'] = Scans::find($id);
        $data['drawflow'] = json_encode($this->sync($data['scan']->workflow->diagram_data,$data['scan']->output .'/process_log.json'));
        return view('track.index', compact('data'));
    }


    public function sync($diagram_data,$progress_log){

        $drawflow = json_decode($diagram_data,true);
        $processLog = json_decode(file_get_contents($progress_log),true);


        // Fungsi rekursif untuk flatten tasks
        function flattenTasks(array $tasks): array {
            $flat = [];
            foreach ($tasks as $task) {
                $flat[] = $task;
                if (!empty($task['tasks'])) {
                    $flat = array_merge($flat, flattenTasks($task['tasks']));
                }
            }
            return $flat;
        }

        // Flatten semua tasks dari process_log
        $allTasks = flattenTasks($processLog['tasks']);

        // Loop setiap node di drawflow
        foreach ($drawflow['drawflow']['Home']['data'] as $nodeId => &$node) {
            $nodeName = $node['data']['name'];
            
            // Cari task di allTasks yang nama sama
            $foundTask = null;
            foreach ($allTasks as $task) {
                if ($task['name'] === $nodeName) {
                    $foundTask = $task;
                    break;
                }
            }
            
            // Tambahkan status
            if ($foundTask) {
                $node['data']['status'] = $foundTask['status'] ?? 'unknown';
            } else {
                $node['data']['status'] = 'not_found';
            }
        }

        // Simpan kembali ke file atau tampilkan
        // file_put_contents('drawflow_with_status.json', json_encode($drawflow, JSON_PRETTY_PRINT));
        //echo "Drawflow updated with status!\n";
        return $drawflow;

    }
}
