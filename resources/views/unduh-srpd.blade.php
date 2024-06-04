@extends('layouts.organisasi-layout')

@section('content')

<div class="row">
    <div class="col-lg-12 mb-4">
      <!-- Simple Tables -->
      <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Surat Rekomendasi Pencairan Dana</h6>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center table-flush">
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
                @if (($proker->organisasi && $proker->organisasi->nama_organisasi == $orguser && $proker->rab && $proker->rab->file_srpd))
                <td>{{ $index + 1 }}</td>
                <td>{{ $proker->organisasi ? $proker->organisasi->nama_organisasi : 'Tidak ada organisasi' }}</td>
                <td>{{ $proker->nama_proker }}</td>
                <td>
                  <a href="{{ asset('srpd/' . $proker->rab->file_srpd) }}" target="_blank">
                    {{ $proker->rab->file_srpd }}
                </a>
                </td>    
              </tr> 
              @endif
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="card-footer"></div>
      </div>
    </div>
  </div>

@endsection