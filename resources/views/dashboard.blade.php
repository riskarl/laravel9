@extends('layouts.admin-layout')

@section('kontainer')

<h1>Dashboard</h1>

<div class="row mb-3">
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-uppercase mb-1">Jumlah Akun</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalakun }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-primary"></i>
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
            <div class="text-xs font-weight-bold text-uppercase mb-1">Jumlah Organisasi</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalorganisasi }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-users fa-2x text-info"></i>
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
            <div class="text-xs font-weight-bold text-uppercase mb-1">Jumlah Proker</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalproker }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-clipboard-list fa-2x text-success"></i>
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
            <div class="text-xs font-weight-bold text-uppercase mb-1">Jumlah Proposal</div>
            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{$totalproposal}}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-file-alt fa-2x text-warning"></i>
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
            <div class="text-xs font-weight-bold text-uppercase mb-1">Jumlah LPJ</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totallpj }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-file-contract fa-2x text-danger"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
    
@endsection