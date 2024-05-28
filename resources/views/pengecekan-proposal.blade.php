@extends('layouts.pengecek-layout')

@section('konten')

<div class="row">
    <div class="col-lg-12 mb-4">
      <!-- Simple Tables -->
      <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Pengecekan Proposal</h6>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th>ID</th>
                <th>Nama Program Kerja</th>
                <th>Nama Organisasi</th>
                <th>File</th>
                <th>Catatan</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($listproker as $proker)
              @if($proker->proposal)
               @if(($proker->organisasi->nama_organisasi == $orguser) || ($proker->proposal->status_flow == 2))
                <tr>
                    <td>{{ $proker->id }}</td>
                    <td>{{ $proker->nama_proker }}</td>
                    <td>{{ $proker->organisasi->nama_organisasi }}</td>
                    <td>
                      @if ($proker->proposal)
                          <a href="{{ asset('path/to/proposals/' . $proker->proposal->file_proposal) }}" target="_blank">
                              {{ $proker->proposal->file_proposal }}</a>
                      @else
                          Tidak ada file proposal
                      @endif
                  </td>
                  <td>
                    @if ($proker->proposal)
                        {{ $proker->proposal->catatan }}
                    @else
                        Tidak Ada Catatan
                    @endif
                  </td>                
                  <td>
                    @if($proker->proposal)
                        {{ $proker->proposal->status }}
                    @else
                        Pending
                    @endif
                  </td>  
                  <td>
                  <button type="button" class="btn btn-warning">Revisi</button>
                  <a href="{{ route('proposals.approve', ['proposalId' => $proker->proposal->id]) }}"><button type="submit" class="btn btn-success">Diterima</button></a>
                  <button type="submit" class="btn btn-danger">Ditolak</button>
                  </td>              
                </tr>
                @endif
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