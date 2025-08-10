<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Terminal;
use Illuminate\Support\Str;
class TerminalController extends Controller
{
    public function index()
    {
        $terminal = Terminal::find(1);
        
        return view('terminal.index', compact('terminal'));
    }

    public function frame()
    {
        $terminal = Terminal::find(1);
        if($terminal->state == "running") {
            $token = $terminal->token;
            return view('terminal.frame', compact('token'));
        } else {
            return redirect()->route('terminal')->with('error', 'The terminal server is not running.');
        }

    }

    public function start(Request $request)
    {
        $terminal = Terminal::find(1);

        // Corrected command to start the server in the background and retrieve the PID
        $command = 'export PATH=$PATH:'. env('TOOLS_PATH') .' && cd ../terminal && nohup python3 coli_terminal_server.py --port 8080 --host 0.0.0.0 > /dev/null 2>&1 & echo $!';
        $pid = shell_exec($command);
        $pid = intval(trim($pid));

        if (!$pid) {
            return redirect()->route('terminal')->with('error', 'Failed to start the terminal server.');
        }

        $token = Str::random(32);

        $terminal->token = $token;
        $terminal->state = "running";
        $terminal->pid = $pid;
        $terminal->save();

        return redirect()->route('terminal')->with('success', 'The terminal server has been started successfully.');
    }

    public function stop(Request $request)
    {
        $terminal = Terminal::find(1);

        exec("ps aux | grep 'python3 coli_terminal_server.py' | grep -v grep | awk '{print $2}' | xargs -r kill -9 2>&1", $output, $status);
        $terminal->state = "stopped";
        $terminal->token = null;
        $terminal->pid = null;
        $terminal->save();
        return redirect()->route('terminal');
    }
    
}
