@extends('layouts.admin-layout')

@section('kontainer')

<div class="row">
    <div class="col-lg-12 mb-4">
      <!-- Simple Tables -->
      <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h5 class="m-0 font-weight-bold text-primary">Arsip Proposal</h5>
        </div>
        <div class="table-responsive">
          <table id="myDataTable" class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th>NO</th>
                <th>Nama Organisasi</th>
                <th>Nama Program Kerja</th>
                <th>File Proposal</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($listproker as $index => $proker)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $proker->organisasi ? $proker->organisasi->nama_organisasi : 'Tidak ada organisasi' }}</td>
                <td>{{ $proker->nama_proker }}</td>
                  <td>
                    @if ($proker->proposal && $proker->proposal->file_proposal)
                    <a href="{{ asset('files/' . $proker->proposal->file_proposal) }}" target="_blank">
                        {{ $proker->proposal->file_proposal }}
                    </a>
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