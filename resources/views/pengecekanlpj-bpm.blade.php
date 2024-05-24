@extends('layouts.bpm-layout')

@section('konteng')

<div class="row">
    <div class="col-lg-12 mb-4">
      <!-- Simple Tables -->
      <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Pengecekan Laporan Pertanggungjawaban</h6>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th>ID</th>
                <th>Nama Program Kerja</th>
                <th>Jabatan Pengecek</th>
                <th>Catatan</th>
                <th>Status</th>
                <th>Tanda Tangan</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              {{-- @foreach ($listproker as $proker) --}}
              <tr>
                {{-- <td>{{ $proker->id }}</td>
                <td>{{ $proker->organisasi ? $proker->organisasi->nama_organisasi : 'Tidak ada organisasi' }}</td>
                <td>{{ $proker->nama_proker }}</td>
                <td>{{ $proker->nama_ketupel }}</td>
                <td>{{ $proker->tanggal }}</td>
                <td>{{ $proker->tempat }}</td>
                <td>{{ $proker->dana_diajukan }}</td> --}}              
              </tr> 
              {{-- @endforeach --}}
            </tbody>
          </table>
        </div>
        <div class="card-footer"></div>
      </div>
    </div>
  </div>

@endsection