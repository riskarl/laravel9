@extends('layouts.organisasi-layout')

@section('content')

<div class="row">
    <div class="col-lg-12 mb-4">
      <!-- Simple Tables -->
      <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Rincian Anggaran Organisasi</h6>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th>NO</th>
                <th>Nama Organisasi</th>
                <th>Nama Program Kerja</th>
                <th>Dana Diajukan</th>
                <th>Dana Disetujui</th>
                <th>Sisa Anggaran</th>
              </tr>
            </thead>
            <tbody>
              {{-- @foreach ($format as $index => $formats)
              <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $formats->jenis_format}}</td>
                  <td>
                    <a href="{{ asset('format/' . $formats->file_format) }}" target="_blank">
                        {{ $formats->file_format }}
                    </a>
                  </td>
                </tr>
              @endforeach --}}
          </tbody>
          </table>
        </div>
        <div class="card-footer"></div>
      </div>
    </div>
  </div>

@endsection