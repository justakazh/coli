@extends('app.template')
@section('title', 'Dashboard - COLI')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <h2 class="fw-bold mb-2"><i class="mdi mdi-view-dashboard"></i> Dashboard Overview</h2>
        <p class="text-muted mb-0">
            Welcome back, <span class="fw-semibold">{{ auth()->user()?->name ?? 'User' }}</span>! Here’s a summary of your system and scan activities.
        </p>
    </div>

    <!-- System Summary Section (Single Row) -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header border-bottom-0 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-semibold"><i class="mdi mdi-desktop-classic"></i> System Summary</h5>
                <span class="text-muted small d-none d-md-inline">Last update: {{ now()->format('d/m/Y H:i') }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <div class="d-flex flex-column align-items-center text-center">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width:56px;height:56px;">
                                <i class="mdi mdi-memory mdi-24px"></i>
                            </div>
                            <div class="fw-bold mb-0 fs-5">
                                {{ $systemInfo['memory'] ?? '-' }}
                            </div>
                            <small class="text-muted">Memory Usage</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="d-flex flex-column align-items-center text-center">
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width:56px;height:56px;">
                                <i class="mdi mdi-harddisk mdi-24px"></i>
                            </div>
                            <div class="fw-bold mb-0 fs-5">
                                {{ $systemInfo['disk'] ?? '-' }}
                            </div>
                            <small class="text-muted">Disk Usage</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="d-flex flex-column align-items-center text-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width:56px;height:56px;">
                                <i class="mdi mdi-lan mdi-24px"></i>
                            </div>
                            <div class="fw-bold mb-0 fs-5">
                                {{ $systemInfo['ip'] ?? '-' }}
                            </div>
                            <small class="text-muted">Server IP</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="d-flex flex-column align-items-center text-center">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width:56px;height:56px;">
                                <i class="mdi mdi-clock-outline mdi-24px"></i>
                            </div>
                            <div class="fw-bold mb-0 fs-5">
                                {{ $systemInfo['uptime'] ?? '-' }}
                            </div>
                            <small class="text-muted">Uptime</small>
                        </div>
                    </div>
                </div>
                <!-- Compact memory and disk stats -->
                <div class="row mt-4">
                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-sm mb-2">
                            <div class="card-body py-3 px-3 d-flex align-items-center">
                                <div class="me-3">
                                    <span class="badge bg-success p-3"><i class="mdi mdi-memory mdi-18px"></i></span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold">Memory Usage</span>
                                        <span class="fw-bold">{{ $systemInfo['memory_percent'] ?? 0 }}%</span>
                                    </div>
                                    <div class="progress mt-2" style="height: 10px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $systemInfo['memory_percent'] ?? 0 }}%;" aria-valuenow="{{ $systemInfo['memory_percent'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-sm mb-2">
                            <div class="card-body py-3 px-3 d-flex align-items-center">
                                <div class="me-3">
                                    <span class="badge bg-warning p-3"><i class="mdi mdi-harddisk mdi-18px"></i></span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold">Disk Usage</span>
                                        <span class="fw-bold">{{ $systemInfo['disk_percent'] ?? 0 }}%</span>
                                    </div>
                                    <div class="progress mt-2" style="height: 10px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $systemInfo['disk_percent'] ?? 0 }}%;" aria-valuenow="{{ $systemInfo['disk_percent'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End compact stats -->
            </div>
        </div>
    </div>

    <!-- Statistics with Charts -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header border-bottom-0">
                <h5 class="mb-0 fw-semibold"><i class="mdi mdi-chart-line"></i> Scan Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6 col-12 mb-3 mb-lg-0">
                        <h6 class="fw-semibold mb-2">Scans per Month <span class="text-muted small">(12 mo)</span></h6>
                        <canvas id="scansPerMonthChart" height="160"></canvas>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 mb-3 mb-lg-0">
                        <h6 class="fw-semibold mb-2">Scans per Week <span class="text-muted small">(7 wks)</span></h6>
                        <canvas id="scansPerWeekChart" height="160"></canvas>
                    </div>
                    <div class="col-lg-4 col-md-12 col-12">
                        <h6 class="fw-semibold mb-2">Scans per Day <span class="text-muted small">(30 d)</span></h6>
                        <canvas id="scansPerDayChart" height="160"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scan Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width:40px;height:40px;">
                        <i class="mdi mdi-folder-multiple mdi-20px"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">{{ $totalScopes ?? 0 }}</h5>
                    <span class="text-muted small">Total Scopes</span>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width:40px;height:40px;">
                        <i class="mdi mdi-bug mdi-20px"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">{{ $totalScans ?? 0 }}</h5>
                    <span class="text-muted small">Total Scans</span>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width:40px;height:40px;">
                        <i class="mdi mdi-play mdi-20px"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">{{ $runningScans ?? 0 }}</h5>
                    <span class="text-muted small">Running Scans</span>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width:40px;height:40px;">
                        <i class="mdi mdi-timer-sand mdi-20px"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">{{ $pendingScans ?? 0 }}</h5>
                    <span class="text-muted small">Pending Scans</span>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width:40px;height:40px;">
                        <i class="mdi mdi-alert-circle mdi-20px"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">{{ $failedScans ?? 0 }}</h5>
                    <span class="text-muted small">Failed Scans</span>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width:40px;height:40px;">
                        <i class="mdi mdi-stop-circle-outline mdi-20px"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">{{ $stoppedScans ?? 0 }}</h5>
                    <span class="text-muted small">Stopped Scans</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Scans Table -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header border-bottom-0">
                <h5 class="mb-0 fw-semibold"><i class="mdi mdi-history"></i> Recent Scans</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:40px;">#</th>
                                <th>Target</th>
                                <th>Workflow</th>
                                <th>Status</th>
                                <th>Started At</th>
                                <th class="text-center" style="width:90px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentScans ?? [] as $scan)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-break">{{ $scan->target }}</td>
                                    <td>
                                        <span class="badge bg-info text-dark">{{ ucfirst($scan->workflow?->name ?? '-') }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'secondary',
                                                'running' => 'warning',
                                                'done' => 'success',
                                                'error' => 'danger',
                                                'stopped' => 'secondary'
                                            ];
                                            $statusColor = $statusColors[$scan->status] ?? 'secondary';
                                            $statusIcons = [
                                                'pending' => 'mdi mdi-timer-sand',
                                                'running' => 'mdi mdi-progress-clock',
                                                'done' => 'mdi mdi-check-circle-outline',
                                                'error' => 'mdi mdi-close-circle-outline',
                                                'stopped' => 'mdi mdi-cancel'
                                            ];
                                            $statusIcon = $statusIcons[$scan->status] ?? 'mdi mdi-help-circle-outline';
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }} d-inline-flex align-items-center gap-1 px-2 py-1">
                                            <i class="{{ $statusIcon }}"></i> {{ ucfirst($scan->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $scan->created_at ? $scan->created_at->format('Y-m-d H:i') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('scans.logs', $scan->id) }}" class="btn btn-sm btn-outline-secondary me-1" title="View Logs">
                                            <i class="mdi mdi-file-document-outline"></i>
                                        </a>
                                        <a href="{{ route('scans.run', $scan->id) }}" class="btn btn-sm btn-outline-primary" title="Re-run">
                                            <i class="mdi mdi-refresh"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="mdi mdi-information-outline h4"></i>
                                        <div>No recent scans found.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN and Chart Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // PHP data to JS
    const monthsLabels = {!! json_encode(array_keys($months ?? [])) !!};
    const monthsData = {!! json_encode(array_values($months ?? [])) !!};

    // Fix for Scans per Week (7 wks): Only use the last 7 weeks
    let weeksLabels = {!! json_encode(array_keys($weeks ?? [])) !!};
    let weeksData = {!! json_encode(array_values($weeks ?? [])) !!};
    if (weeksLabels.length > 7) {
        weeksLabels = weeksLabels.slice(-7);
        weeksData = weeksData.slice(-7);
    }

    const daysLabels = {!! json_encode(array_keys($days ?? [])) !!};
    const daysData = {!! json_encode(array_values($days ?? [])) !!};

    // Scans per Month Chart
    if (document.getElementById('scansPerMonthChart')) {
        new Chart(document.getElementById('scansPerMonthChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: monthsLabels,
                datasets: [{
                    label: 'Scans',
                    data: monthsData,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderRadius: 6,
                    maxBarThickness: 24
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { title: { display: true, text: 'Month' }, grid: { display: false } },
                    y: { beginAtZero: true, title: { display: true, text: 'Number of scans' }, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    // Scans per Week Chart
    if (document.getElementById('scansPerWeekChart')) {
        new Chart(document.getElementById('scansPerWeekChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: weeksLabels,
                datasets: [{
                    label: 'Scans',
                    data: weeksData,
                    fill: true,
                    borderColor: 'rgba(255, 206, 86, 1)',
                    backgroundColor: 'rgba(255, 206, 86, 0.15)',
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { title: { display: true, text: 'Week' }, grid: { display: false } },
                    y: { beginAtZero: true, title: { display: true, text: 'Number of scans' }, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    // Scans per Day Chart
    if (document.getElementById('scansPerDayChart')) {
        new Chart(document.getElementById('scansPerDayChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: daysLabels,
                datasets: [{
                    label: 'Scans',
                    data: daysData,
                    fill: true,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.15)',
                    tension: 0.3,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { title: { display: true, text: 'Day' }, grid: { display: false } },
                    y: { beginAtZero: true, title: { display: true, text: 'Number of scans' }, ticks: { stepSize: 1 } }
                }
            }
        });
    }
</script>
@endsection