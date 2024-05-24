@extends('layouts.organisasi-layout')

@section('content')

<div class="row">
    <div class="col-lg-12 mb-4">
      <!-- Simple Tables -->
      <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Manajemen Proposal</h6>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th>ID</th>
                <th>Nama Organisasi</th>
                <th>Nama Program Kerja</th>
                <th>File</th>
                <th>Status</th>
                <th>Catatan</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($listproker as $proker)
              <tr>
                <td>{{ $proker->id }}</td> 
                <td>{{ $proker->organisasi ? $proker->organisasi->nama_organisasi : 'Tidak ada organisasi' }}</td>
                <td>{{ $proker->nama_proker }}</td>
                <td>{{ $proker->proposal ? $proker->proposal->file_proposal  : 'Tidak ada file' }}</td>
                <td>{{ $proker->proposal ? $proker->proposal->status : 'Pending'}}</td>
                <td>{{ $proker->proposal ? $proker->proposal->catatan : 'Tidak Ada Catatan' }}</td>       
                <td><a href="" type="button" class="btn btn-warning mr-2">Upload File</a></td>
              </tr> 
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="card-footer"></div>
      </div>
    </div>
  </div>

@endsection