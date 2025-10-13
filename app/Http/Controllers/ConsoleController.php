<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utils;
use Illuminate\Support\Str;

class ConsoleController extends Controller
{
    public function index()
    {
        $data['utils'] = Utils::find(1);
        return view('console.index', compact('data'));
    }

    public function action(Request $request)
    {

        //validate request
        $request->validate([
            'action' => 'required|in:start,stop',
        ]);

        //create key for console
        $utils = Utils::find(1);
        if($request->action === 'start'){

            // Jalankan engine terminal.py sebagai background process dan ambil PID-nya
            $command = 'export HOME=' . env('USER_HOME') . " && ";
            $command .= 'export PATH=$PATH:' . env('USER_PATH') . " && ";
            $command .= 'setsid python3 ' . base_path('engine/terminal.py') . ' > /dev/null 2>&1 & echo $!';
            $exec = shell_exec($command);
            sleep(1);
            $pid = intval(trim($exec));
            $utils->pid = $pid > 0 ? $pid : null;
            $utils->state = $pid > 0 ? 'active' : 'inactive';
            $utils->save();

            return redirect()->route('console')->with('success', 'Console started successfully');
        }
        
        if($request->action === 'stop'){

            $pgid = intval($utils->pid);
            exec("kill -TERM -$pgid 2>/dev/null");
            sleep(1);
            exec("pgrep -g $pgid | xargs kill -9 2>/dev/null");

            $utils->state = 'inactive';
            $utils->pid = null;
            $utils->save();


            return redirect()->route('console')->with('success', 'Console stopped successfully');
        }
    }
}
