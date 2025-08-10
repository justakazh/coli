<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Scans;
use Symfony\Component\Process\Process;

class Ewe extends Command
{
    protected $signature = 'run:ewe {id}';
    protected $description = 'Execution Workflow Engine';

    public $scan;

    public function handle()
    {
        $scanId = $this->argument('id');
        $this->scan = Scans::findOrFail($scanId);

        // Penentuan target berdasarkan kategori workflow
        if ($this->scan->workflow->category == 'multiple') {
            $target = env("HUNT_PATH") . '/' . $this->scan->target;
        } else {
            $target = $this->scan->target;
        }

        // Ambil path file workflow
        $workflowPath = $this->scan->workflow->script_path;

        // Validasi file workflow
        if (!file_exists($workflowPath)) {
            $this->error('Workflow file not found.');
            $this->scan->status = 'error';
            $this->scan->save();
            return;
        }

        // Baca dan validasi struktur workflow (harus JSON valid dan ada tasks)
        $workflowContent = file_get_contents($workflowPath);
        $workflow = json_decode($workflowContent, true);

        if (!is_array($workflow) || !isset($workflow['tasks']) || !is_array($workflow['tasks'])) {
            $this->error('Invalid workflow structure.');
            $this->scan->status = 'error';
            $this->scan->save();
            return;
        }

        // Set path log ke kolom process
        $this->scan->process = $this->scan->output_path . '/logs/logs.json';
        $this->scan->save();

        // Jalankan engine ewe-cli.py
        $workflowScriptPath = $workflowPath;
        $outputPath = $this->scan->output_path;

        $command = "python3 " . base_path("engine/ewe.py") .
            " --target " . escapeshellarg($target) .
            " --workflow " . escapeshellarg($workflowScriptPath) .
            " --output " . escapeshellarg($outputPath) .
            " ".env("EWE_CLI_OPTIONS");

        $final_command = 'export HOME=' . escapeshellarg(env('HOME_PATH')) .
            ' && export PATH=$PATH:' . escapeshellarg(env('TOOLS_PATH')) .
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
            $this->scan->status = 'done';
            $this->scan->finished_at = now();
            $this->scan->save();
        } else {
            $this->scan->status = 'error';
            $this->scan->finished_at = now();
            $this->scan->error = $process->getErrorOutput();
            $this->scan->save();
        }
    }

}