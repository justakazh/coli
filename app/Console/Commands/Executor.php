<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Scans;
use Symfony\Component\Process\Process;

class Executor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:executor {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'COLI Executor';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //get scan info
        $id = $this->argument('id');
        $scan = Scans::find($id);


        $target = $scan->scope->target;
        $workflow_path = $scan->workflow->script_path;
        $output_dir = $scan->output;
        

        $command = "python3 " . base_path("engine/ewe.py") .
            " --target " . escapeshellarg($target) .
            " --workflow " . escapeshellarg($workflow_path) .
            " --output " . escapeshellarg($output_dir) .
            " ";

        $final_command = 'export HOME=' . escapeshellarg(env('USER_HOME')) .
            ' && export PATH=$PATH:' . escapeshellarg(env('USER_PATH')) .
            ' && ' . $command;

        $process = Process::fromShellCommandline($final_command);
        $process->setTimeout(null);

        $process->start();


        // Tunggu proses selesai
        while ($process->isRunning()) {
            usleep(100000);
        }

        // Update status scan setelah proses selesai
        if ($process->isSuccessful()) {
            $scan->status = 'finished';
            $scan->finished_at = now();
            $scan->save();
        } else {
            $scan->status = 'failed';
            $scan->finished_at = now();
            $scan->log = $process->getErrorOutput();
            $scan->save();
        }


        

    }
}
