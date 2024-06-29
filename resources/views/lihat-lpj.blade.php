@extends('layouts.admin-layout')

@section('kontainer')

<div class="row">
    <div class="col-lg-12 mb-4">
      <!-- Simple Tables -->
      <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h5 class="m-0 font-weight-bold text-primary">Arsip Laporan Pertanggungjawaban</h5>
        </div>
        <div class="table-responsive">
          <table id="myDataTable" class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th>NO</th>
                <th>Nama Organisasi</th>
                <th>Nama Program Kerja</th>
                <th>File</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($listproker as $index => $proker)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $proker->organisasi ? $proker->organisasi->nama_organisasi : 'Tidak ada organisasi' }}</td>
                <td>{{ $proker->nama_proker }}</td>
                  <td>
                    @if ($proker->lpj)
                    <a href="{{ asset('lpj/' . $proker->lpj->file_lpj) }}" target="_blank">{{ $proker->lpj->file_lpj }}</a>
                    @else
                    Tidak ada file
                    @endif
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