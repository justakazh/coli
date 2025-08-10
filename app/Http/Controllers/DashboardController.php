<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Scopes;
use App\Models\Scans;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // System information for the dashboard view
        $systemInfo = [
            'memory' => $this->getMemoryUsageString(),
            'memory_percent' => $this->getMemoryUsagePercent(),
            'disk' => $this->getDiskUsageString(),
            'disk_percent' => $this->getDiskUsagePercent(),
            'ip' => request()->server('SERVER_ADDR') ?: getHostByName(getHostName()),
            'uptime' => $this->getUptime(),
        ];

        // Dashboard statistics
        $totalScopes = Scopes::count();
        $totalScans = Scans::count();
        $runningScans = Scans::where('status', 'running')->count();
        $failedScans = Scans::where('status', 'error')->count();
        $stoppedScans = Scans::where('status', 'stopped')->count();
        $pendingScans = Scans::where('status', 'pending')->count();
        $recentScans = Scans::with('workflow')->latest()->take(5)->get();

        $now = Carbon::now();

        // Scans per month (last 12 months)
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $label = $date->format('Y-m');
            $months[$label] = 0;
        }
        $scansPerMonth = Scans::select(
                DB::raw("strftime('%Y-%m', created_at) as month"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', $now->copy()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        foreach ($scansPerMonth as $row) {
            if (isset($months[$row->month])) {
                $months[$row->month] = $row->total;
            }
        }

        // Scans per week (last 7 weeks)
        $weeks = [];
        for ($i = 6; $i >= 0; $i--) {
            $startOfWeek = $now->copy()->subWeeks($i)->startOfWeek();
            $label = $startOfWeek->format('Y-\WW') . $startOfWeek->format('W');
            $weeks[$label] = 0;
        }
        $scansPerWeek = Scans::select(
                DB::raw("strftime('%Y', created_at) as year"),
                DB::raw("strftime('%W', created_at) as week"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', $now->copy()->subWeeks(6)->startOfWeek())
            ->groupBy('year', 'week')
            ->orderBy('year')
            ->orderBy('week')
            ->get();
        foreach ($scansPerWeek as $row) {
            $label = $row->year . '-W' . str_pad($row->week, 2, '0', STR_PAD_LEFT);
            if (isset($weeks[$label])) {
                $weeks[$label] = $row->total;
            }
        }

        // Scans per day (last 30 days)
        $days = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $label = $date->format('Y-m-d');
            $days[$label] = 0;
        }
        $scansPerDay = Scans::select(
                DB::raw("strftime('%Y-%m-%d', created_at) as day"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', $now->copy()->subDays(29)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->get();
        foreach ($scansPerDay as $row) {
            if (isset($days[$row->day])) {
                $days[$row->day] = $row->total;
            }
        }

        return view('dashboard.index', compact(
            'systemInfo',
            'totalScopes',
            'totalScans',
            'runningScans',
            'failedScans',
            'stoppedScans',
            'pendingScans',
            'recentScans',
            'months',
            'weeks',
            'days'
        ));
    }

    // Helper for memory usage (display)
    private function getMemoryUsageString()
    {
        if (PHP_OS_FAMILY === 'Linux') {
            $meminfo = @file_get_contents('/proc/meminfo');
            if ($meminfo) {
                preg_match('/MemTotal:\s+(\d+) kB/', $meminfo, $total);
                preg_match('/MemAvailable:\s+(\d+) kB/', $meminfo, $avail);
                if (isset($total[1], $avail[1])) {
                    $used = $total[1] - $avail[1];
                    return sprintf(
                        '%.1f MB / %.1f MB',
                        $used / 1024,
                        $total[1] / 1024
                    );
                }
            }
        }
        if (PHP_OS_FAMILY === 'Windows') {
            $output = [];
            @exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value', $output);
            $free = 0; $total = 0;
            foreach ($output as $line) {
                if (strpos($line, 'FreePhysicalMemory=') === 0) {
                    $free = (int)str_replace('FreePhysicalMemory=', '', $line);
                }
                if (strpos($line, 'TotalVisibleMemorySize=') === 0) {
                    $total = (int)str_replace('TotalVisibleMemorySize=', '', $line);
                }
            }
            if ($total > 0) {
                $used = $total - $free;
                return sprintf(
                    '%.1f MB / %.1f MB',
                    $used / 1024,
                    $total / 1024
                );
            }
        }
        return '-';
    }

    // Helper for memory usage (percent)
    private function getMemoryUsagePercent()
    {
        if (PHP_OS_FAMILY === 'Linux') {
            $meminfo = @file_get_contents('/proc/meminfo');
            if ($meminfo) {
                preg_match('/MemTotal:\s+(\d+) kB/', $meminfo, $total);
                preg_match('/MemAvailable:\s+(\d+) kB/', $meminfo, $avail);
                if (isset($total[1], $avail[1]) && $total[1] > 0) {
                    $used = $total[1] - $avail[1];
                    return round(($used / $total[1]) * 100, 1);
                }
            }
        }
        if (PHP_OS_FAMILY === 'Windows') {
            $output = [];
            @exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value', $output);
            $free = 0; $total = 0;
            foreach ($output as $line) {
                if (strpos($line, 'FreePhysicalMemory=') === 0) {
                    $free = (int)str_replace('FreePhysicalMemory=', '', $line);
                }
                if (strpos($line, 'TotalVisibleMemorySize=') === 0) {
                    $total = (int)str_replace('TotalVisibleMemorySize=', '', $line);
                }
            }
            if ($total > 0) {
                $used = $total - $free;
                return round(($used / $total) * 100, 1);
            }
        }
        return 0;
    }

    // Helper for disk usage (display)
    private function getDiskUsageString()
    {
        $total = @disk_total_space('/');
        $free = @disk_free_space('/');
        if ($total && $free) {
            $used = $total - $free;
            return sprintf(
                '%.1f GB / %.1f GB',
                $used / 1073741824,
                $total / 1073741824
            );
        }
        return '-';
    }

    // Helper for disk usage (percent)
    private function getDiskUsagePercent()
    {
        $total = @disk_total_space('/');
        $free = @disk_free_space('/');
        if ($total && $free && $total > 0) {
            $used = $total - $free;
            return round(($used / $total) * 100, 1);
        }
        return 0;
    }

    // Helper for uptime
    private function getUptime()
    {
        if (PHP_OS_FAMILY === 'Linux') {
            $uptime = @file_get_contents('/proc/uptime');
            if ($uptime) {
                $seconds = (int)floatval(explode(' ', $uptime)[0]);
                $dtF = new \DateTime('@0');
                $dtT = new \DateTime("@$seconds");
                return $dtF->diff($dtT)->format('%a days, %h:%I:%S');
            }
        }
        if (PHP_OS_FAMILY === 'Windows') {
            $output = [];
            @exec('net stats workstation', $output);
            foreach ($output as $line) {
                if (stripos($line, 'Statistics since') !== false) {
                    $since = trim(str_ireplace('Statistics since', '', $line));
                    $sinceTime = strtotime($since);
                    if ($sinceTime) {
                        $seconds = time() - $sinceTime;
                        $dtF = new \DateTime('@0');
                        $dtT = new \DateTime("@$seconds");
                        return $dtF->diff($dtT)->format('%a days, %h:%I:%S');
                    }
                }
            }
        }
        return '-';
    }
}
