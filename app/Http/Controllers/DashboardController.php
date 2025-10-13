<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scans;
class DashboardController extends Controller
{
    public function index()
    {
        $data['scans_stats'] = [
            'total' => Scans::count(),
            'running' => Scans::where('status', 'running')->count(),
            'finished' => Scans::where('status', 'finished')->count(),
            'failed' => Scans::where('status', 'failed')->count(),
            'stopped' => Scans::where('status', 'stopped')->count(),
        ];
        $data['server_stats'] = [
            'cpu' => $this->getCpuStats(),
            'memory' => $this->getMemoryStats(),
            'disk' => $this->getDiskStats(),
        ];

        $data['weekly_scans'] = Scans::where('created_at', '>=', now()->subDays(7))->count();
        $data['monthly_scans'] = Scans::where('created_at', '>=', now()->subDays(30))->count();
        $data['yearly_scans'] = Scans::where('created_at', '>=', now()->subDays(365))->count();

        return view('dashboard.index', compact('data'));
    }

    public function getCpuStats()
    {
        // Mengambil persentase penggunaan CPU secara real-time (untuk Linux)
        $usage = null;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows: dummy value
            $usage = null; // Atau implementasi lain, jika dibutuhkan
        } else {
            $load = sys_getloadavg();
            $cpu_cores = (int) trim(shell_exec("nproc"));
            $usage = isset($load[0]) && $cpu_cores ? round(($load[0] / $cpu_cores) * 100, 2) : null;
        }

        // Suhu CPU bisa dicek via shell, jika ada sensors terbaca
        $temperature = null;
        if (file_exists('/sys/class/thermal/thermal_zone0/temp')) {
            $temperature = round(intval(file_get_contents('/sys/class/thermal/thermal_zone0/temp')) / 1000, 1);
        } elseif (function_exists('shell_exec')) {
            $output = @shell_exec("sensors | grep -m 1 'Core 0' | awk '{print $3}' | tr -d '+' | tr -d '°C'");
            $temperature = is_numeric($output) ? floatval($output) : null;
        }

        return [
            'usage' => $usage ?? 0,
            'temperature' => $temperature ?? 0
        ];
    }

    public function getMemoryStats()
    {
        $usage = null;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $usage = null; // Atau implementasi untuk Windows
        } else {
            $meminfo = file_get_contents("/proc/meminfo");
            preg_match("/MemTotal:\s+(\d+)/", $meminfo, $total);
            preg_match("/MemAvailable:\s+(\d+)/", $meminfo, $available);
            if ($total && $available) {
                $totalMem = (float)$total[1];
                $availMem = (float)$available[1];
                $usedMem = $totalMem - $availMem;
                $usage = round(($usedMem / $totalMem) * 100, 2);
            }
        }

        return [
            'usage' => $usage ?? 0,
        ];
    }

    public function getDiskStats()
    {
        $usage = null;
        $directory = '/';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $directory = 'C:';
        }
        $total = @disk_total_space($directory);
        $free = @disk_free_space($directory);
        if ($total && $free !== false) {
            $used = $total - $free;
            $usage = round(($used / $total) * 100, 2);
        }

        return [
            'usage' => $usage ?? 0,
        ];
    }

}
