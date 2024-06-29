@extends('layouts.bpm-layout')

@section('konteng')

<h1>Dashboard</h1>

<div class="row mb-3">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Jumlah Format</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalformat }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-alt fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Jumlah Anggaran</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalanggaran }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Jumlah RAB</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalrab }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Jumlah SRPD yang perlu di upload</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalnosrpd }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-envelope fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title text-center">Perbandingan Jumlah RAB dan SRPD</h5>
                <div style="max-width: 500px; margin: auto;">
                    <canvas id="barChart" width="500" height="200"></canvas> <!-- Atur width dan height sesuai kebutuhan -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title text-center">Pemetaan Anggaran Dana</h5>
                <div style="max-width: 500px; margin: auto;">
                    <canvas id="pieChart" width="500" height="200"></canvas> <!-- Atur width dan height sesuai kebutuhan -->
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
    var ctx = document.getElementById('barChart').getContext('2d');
    var barChart = new Chart(ctx, {
        type: 'bar', // Ganti tipe chart menjadi 'bar'
        data: {
            labels: ['RAB', 'SRPD'], // Label untuk sumbu x
            datasets: [{
                label: 'Jumlah',
                data: [{{ $totalrab }}, {{ $totalsrpd }}], // Ganti dengan data aktual Anda
                backgroundColor: [
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 99, 132, 0.2)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                datalabels: {
                    anchor: 'end',
                    align: 'end',
                    formatter: (value, ctx) => {
                        return value;
                    },
                    color: '#000',
                    font: {
                        weight: 'bold',
                        size: 14
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
</script>
<script>
    var ctx = document.getElementById('pieChart').getContext('2d');
    var pieChart = new Chart(ctx, {
        type: 'pie',
        data: @json($data),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                datalabels: {
                    formatter: (value, ctx) => {
                        let sum = ctx.dataset.data.reduce((a, b) => a + b, 0);
                        let percentage = (value * 100 / sum).toFixed(2) + "%";
                        return percentage;
                    },
                    color: '#fff',
                    font: {
                        weight: 'bold',
                        size: 14
                    }
                }
            }
        }
    });
</script>
@endsection