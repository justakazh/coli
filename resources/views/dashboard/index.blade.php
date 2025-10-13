@extends('templates.v1')
@section('content')
@section('title', 'Dashboard')

@push('styles')
<style>
    .card-stat {
        transition: transform 0.1s;
    }
    .card-stat:hover {
        transform: translateY(-4px) scale(1.025);
        box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.08) !important;
        z-index: 2;
    }

    .scan-period-card {
        /* Dark mode adaptation */
        background-color: #fff;
        color: #263238;
    }
    .scan-period-title {
        color: #263238;
    }
    .scan-period-stats .fw-bold {
        color: #1e293b;
    }
    .dark-mode .scan-period-card,
    [data-bs-theme="dark"] .scan-period-card {
        background-color: #1e293b !important;
        color: #f1f5f9 !important;
        border-color: #334155 !important;
    }
    .dark-mode .scan-period-title,
    [data-bs-theme="dark"] .scan-period-title {
        color: #f1f5f9 !important;
    }
    .dark-mode .scan-period-stats .fw-bold,
    [data-bs-theme="dark"] .scan-period-stats .fw-bold {
        color: #38bdf8 !important;
    }
    .dark-text-muted {
        color: #94a3b8 !important;
    }
    .dark-mode .dark-text-muted,
    [data-bs-theme="dark"] .dark-text-muted {
        color: #64748b !important;
    }

    /* Responsive tweaks for better layout */
    @media (max-width: 991.98px) {
        .card-stat {
            margin-bottom: 1rem;
        }
    }
    @media (max-width: 575.98px) {
        .card .card-body {
            padding: 0.5rem !important;
        }
    }
</style>
@endpush

<div class="container-fluid">
  <!-- Server Statistics Card -->
  <div class="card shadow mb-4">
    <div class="card-body py-4">
      <h5 class="mb-4 fw-bold text-center" style="font-size: 1.3rem;">
        <i class="fas fa-server me-2"></i>Server Statistics
      </h5>
      <div class="row justify-content-center g-3">
        <!-- CPU -->
        <div class="col-12 col-sm-6 col-md-4 d-flex">
          <div class="card border-0 h-100 shadow-sm w-100 mx-auto" style="max-width:240px; min-width:180px;">
            <div class="card-body py-4 px-2 text-center d-flex flex-column align-items-center justify-content-center" style="height:250px;">
              <i class="fas fa-microchip fs-2 mb-3"></i>
              <div class="fw-bold mb-2" style="font-size: 1.25rem;">CPU</div>
              <div style="width:120px;height:120px;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                <div id="cpuChartApex" style="width: 120px; height: 120px;"></div>
              </div>
              <div class="small text-muted mt-2" style="font-size:1.05rem;">
                Usage: <span class="fw-semibold">{{ $data['server_stats']['cpu']['usage'] ?? 0 }}%</span>
                /
                Temp: <span class="fw-semibold">{{ $data['server_stats']['cpu']['temperature'] ?? '-' }}°C</span>
              </div>
            </div>
          </div>
        </div>
        <!-- MEMORY -->
        <div class="col-12 col-sm-6 col-md-4 d-flex">
          <div class="card border-0 h-100 shadow-sm w-100 mx-auto" style="max-width:240px; min-width:180px;">
            <div class="card-body py-4 px-2 text-center d-flex flex-column align-items-center justify-content-center" style="height:250px;">
              <i class="fas fa-memory fs-2 mb-3"></i>
              <div class="fw-bold mb-2" style="font-size: 1.25rem;">Memory</div>
              <div style="width:120px;height:120px;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                <div id="memoryChartApex" style="width: 120px; height: 120px;"></div>
              </div>
              <div class="small text-muted mt-2" style="font-size:1.05rem;">Usage: <span class="fw-semibold">{{ $data['server_stats']['memory']['usage'] ?? 0 }}%</span></div>
            </div>
          </div>
        </div>
        <!-- DISK -->
        <div class="col-12 col-sm-6 col-md-4 d-flex">
          <div class="card border-0 h-100 shadow-sm w-100 mx-auto" style="max-width:240px; min-width:180px;">
            <div class="card-body py-4 px-2 text-center d-flex flex-column align-items-center justify-content-center" style="height:250px;">
              <i class="fas fa-hdd fs-2 mb-3"></i>
              <div class="fw-bold mb-2" style="font-size: 1.25rem;">Disk</div>
              <div style="width:120px;height:120px;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                <div id="diskChartApex" style="width: 120px; height: 120px;"></div>
              </div>
              <div class="small text-muted mt-2" style="font-size:1.05rem;">Usage: <span class="fw-semibold">{{ $data['server_stats']['disk']['usage'] ?? 0 }}%</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Server Statistics Card -->
  <!-- Scan Statistics Card -->
  <div class="card shadow mb-4">
    <div class="card-body py-4">
      <h5 class="mb-4 fw-bold text-center">
        <i class="fas fa-layer-group me-2"></i>Scan Statistics
      </h5>
      <div class="row justify-content-center g-3">
        <div class="col-6 col-sm-4 col-md-2">
          <div class="card card-stat h-100 shadow-sm">
            <div class="card-body text-center py-3">
              <div class="fs-2 mb-2">
                <i class="fas fa-microchip"></i>
              </div>
              <div class="fs-4 fw-bold">{{ $data['scans_stats']['total'] ?? 0 }}</div>
              <div class="small text-muted">Total Scans</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
          <div class="card card-stat h-100 shadow-sm">
            <div class="card-body text-center py-3">
              <div class="fs-2 mb-2">
                <i class="fas fa-play"></i>
              </div>
              <div class="fs-4 fw-bold">{{ $data['scans_stats']['running'] ?? 0 }}</div>
              <div class="small text-muted">Running</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
          <div class="card card-stat h-100 shadow-sm">
            <div class="card-body text-center py-3">
              <div class="fs-2 mb-2">
                <i class="fas fa-check-circle"></i>
              </div>
              <div class="fs-4 fw-bold">{{ $data['scans_stats']['finished'] ?? 0 }}</div>
              <div class="small text-muted">Finished</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
          <div class="card card-stat h-100 shadow-sm">
            <div class="card-body text-center py-3">
              <div class="fs-2 mb-2">
                <i class="fas fa-times-circle"></i>
              </div>
              <div class="fs-4 fw-bold">{{ $data['scans_stats']['failed'] ?? 0 }}</div>
              <div class="small text-muted">Failed</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
          <div class="card card-stat h-100 shadow-sm">
            <div class="card-body text-center py-3">
              <div class="fs-2 mb-2">
                <i class="fas fa-stop-circle"></i>
              </div>
              <div class="fs-4 fw-bold">{{ $data['scans_stats']['stopped'] ?? 0 }}</div>
              <div class="small text-muted">Stopped</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Scan Statistics Card -->

  <!-- Scan Time Range ApexChart (Line Chart)-->
  <div class="card shadow mb-4 ">
    <div class="card-body py-4">
      <h5 class="mb-4 fw-bold text-center scan-period-title" style="font-size: 1.15rem;">
        <i class="fas fa-chart-line me-2"></i>Scans per Period 
      </h5>
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div id="scanPeriodApexChart"></div>
        </div>
      </div>
      <div class="row justify-content-center text-center mt-3 scan-period-stats">
        <div class="col-4">
          <div class="fw-bold">{{ $data['weekly_scans'] ?? 0 }}</div>
          <div class="small text-muted dark-text-muted">Last 7 Days</div>
        </div>
        <div class="col-4">
          <div class="fw-bold">{{ $data['monthly_scans'] ?? 0 }}</div>
          <div class="small text-muted dark-text-muted">Last 30 Days</div>
        </div>
        <div class="col-4">
          <div class="fw-bold">{{ $data['yearly_scans'] ?? 0 }}</div>
          <div class="small text-muted dark-text-muted">Last 365 Days</div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Scan Time Range ApexChart -->

</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function isDarkMode() {
        // Check whether the "dark-mode" class exists on body, or data-bs-theme is "dark"
        return document.body.classList.contains('dark-mode') ||
               document.body.getAttribute('data-bs-theme') === 'dark' ||
               // Support prefers-color-scheme if not triggered above
               (window.matchMedia &&
                window.matchMedia('(prefers-color-scheme: dark)').matches);
    }

    // Apex Donut Chart For Server Stats
    function makeApexDonut(elId, usage, usedLabel, freeLabel) {
      var isDark = isDarkMode();
      var usedColor = isDark ? "#38bdf8" : "#3b82f6";
      var freeColor = isDark ? "#334155" : "#e5e7eb";
      var options = {
          chart: {
              type: 'donut',
              width: 120,
              height: 120,
              sparkline: { enabled: true }
          },
          labels: [usedLabel, freeLabel],
          series: [usage, 100-usage],
          colors: [usedColor, freeColor],
          stroke: { width: 0 },
          dataLabels: { enabled: false },
          legend: { show: false },
          tooltip: { enabled: false },
          plotOptions: {
            pie: {
              donut: {
                size: '75%'
              }
            }
          }
      };
      var chart = new ApexCharts(document.querySelector(elId), options);
      chart.render();
    }

    makeApexDonut(
      '#cpuChartApex',
      {{ $data['server_stats']['cpu']['usage'] ?? 0 }},
      'Used', 'Free'
    );
    makeApexDonut(
      '#memoryChartApex',
      {{ $data['server_stats']['memory']['usage'] ?? 0 }},
      'Used', 'Free'
    );
    makeApexDonut(
      '#diskChartApex',
      {{ $data['server_stats']['disk']['usage'] ?? 0 }},
      'Used', 'Free'
    );

    // ApexCharts Line Chart for Scan Periods (Dark Mode Support)
    var isDark = isDarkMode();
    var apexLineOptions = {
        chart: {
            type: 'line',
            height: 230,
            toolbar: { show: false },
            zoom: { enabled: false },
            background: isDark ? "#1e293b" : "#fff",
            foreColor: isDark ? "#f1f5f9" : "#263238",
        },
        series: [{
            name: 'Number of Scans',
            data: [
                {{ $data['weekly_scans'] ?? 0 }},
                {{ $data['monthly_scans'] ?? 0 }},
                {{ $data['yearly_scans'] ?? 0 }}
            ]
        }],
        xaxis: {
            categories: ['7 Days', '30 Days', '365 Days'],
            labels: {
                style: {
                    fontSize: '13px',
                    colors: isDark ? "#a6adba" : "#263238"
                }
            }
        },
        yaxis: {
            min: 0,
            forceNiceScale: true,
            labels: {
                style: {
                    fontSize: '13px',
                    colors: isDark ? "#a6adba" : "#263238"
                }
            }
        },
        dataLabels: {
            enabled: true,
            style: {
                colors: [isDark ? "#ffffff" : "#263238"]
            }
        },
        markers: {
            size: 6,
            colors: [isDark ? '#38bdf8' : '#3b82f6'],
            strokeColors: isDark ? '#1e293b' : '#fff',
            strokeWidth: 2
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: { formatter: function (val) { return val + " scan"; } }
        },
        colors: [isDark ? "#38bdf8" : "#3b82f6"],
        stroke: { curve: 'smooth', width: 3 },
        grid: {
            show: true,
            borderColor: isDark ? "#334155" : "#e5e7eb",
            strokeDashArray: 4
        }
    };

    var scanPeriodApexChart = new ApexCharts(document.querySelector('#scanPeriodApexChart'), apexLineOptions);
    scanPeriodApexChart.render();
});
</script>
@endpush

@endsection
