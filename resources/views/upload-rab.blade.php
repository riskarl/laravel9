@extends('layouts.organisasi-layout')

@section('content')

<div class="row">
    <div class="col-lg-12 mb-4">
      <!-- Simple Tables -->
      <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Manajemen Rencana Anggaran Biaya</h6>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th>ID</th>
                <th>Nama Organisasi</th>
                <th>Nama Program Kerja</th>
                <th>File</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($listproker as $proker)
              <tr>
                <td>{{ $proker->id }}</td> 
                <td>{{ $proker->organisasi ? $proker->organisasi->nama_organisasi : 'Tidak ada organisasi' }}</td>
                <td>{{ $proker->nama_proker }}</td>
                <td>{{ $proker->lpj ? $proker->proposal->file_rab  : 'Tidak ada file' }}</td>
                <td>
                  <button type="button" class="btn btn-primary mr-2 btnModal" data-toggle="modal" data-target="#uploadModal" data-id="{{ $proker->id }}">
                    Upload File
                </button>
                </td>
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