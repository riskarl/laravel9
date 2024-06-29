@extends('layouts.organisasi-layout')

@section('content')

<h1>Dashboard</h1>

<div class="row mb-3">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Jumlah Proker</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalproker }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Jumlah Proposal</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalproposal }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Jumlah LPJ</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totallpj }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 col-md-12 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title text-center">Status Proposal</h5>
                <div style="max-width: 300px; margin: auto;">
                    <canvas id="pieChartProposal"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-md-12 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title text-center">Status LPJ</h5>
                <div style="max-width: 300px; margin: auto;">
                    <canvas id="pieChartLpj"></canvas>
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
    var ctxProposal = document.getElementById('pieChartProposal').getContext('2d');
    var pieChartProposal = new Chart(ctxProposal, {
        type: 'pie',
        data: {
            labels: ['Diproses', 'Disetujui'],
            datasets: [{
                data: [{{ $processedProposals }}, {{ $approvedProposals }}],
                backgroundColor: ['#FF6384', '#36A2EB']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                datalabels: {
                    formatter: (value, ctx) => {
                        let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
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
        },
        plugins: [ChartDataLabels]
    });

    var ctxLpj = document.getElementById('pieChartLpj').getContext('2d');
    var pieChartLpj = new Chart(ctxLpj, {
        type: 'pie',
        data: {
            labels: ['Diproses', 'Disetujui'],
            datasets: [{
                data: [{{ $processedLpj }}, {{ $approvedLpj }}],
                backgroundColor: ['#FF6384', '#36A2EB']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                datalabels: {
                    formatter: (value, ctx) => {
                        let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
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
        },
        plugins: [ChartDataLabels]
    });
</script>
@endsection
