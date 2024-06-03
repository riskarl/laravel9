@extends('layouts.organisasi-layout')

@section('content')

<div class="row">
    <div class="col-lg-12 mb-4">
      <!-- Simple Tables -->
      <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Manajemen Program Kerja</h6>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center table-flush">
            <a href="{{ url('/proker/create') }}" class="btn btn-primary btn-sm" title="Tambah Organisasi">
              Tambah Program Kerja
            </a>
            <thead class="thead-light">
              <tr>
                <th>NO</th>
                <th>Nama Organisasi</th>
                <th>Nama Program Kerja</th>
                <th>Nama Ketua Pelaksana</th>
                <th>Tanggal</th>
                <th>Tempat</th>
                <th>Dana yang Diajukan</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($listproker as $index => $proker)
                @if (($proker->organisasi && $proker->organisasi->nama_organisasi == $orguser))
                      <tr>
                          <td>{{ $index + 1 }}</td>
                          <td>{{ $proker->organisasi ? $proker->organisasi->nama_organisasi : 'Tidak ada organisasi' }}</td>
                          <td>{{ $proker->nama_proker }}</td>
                          <td>{{ $proker->nama_ketupel }}</td>
                          <td>{{ $proker->tanggal }}</td>
                          <td>{{ $proker->tempat }}</td>
                          <td>{{ $proker->dana_diajukan }}</td>
                          <td class="d-flex">
                            <a href="/proker/{{ $proker->id }}" class="btn btn-warning mr-2">Edit</a>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{ $proker->id }}">Hapus</button>
                            <!-- Delete Confirmation Modal -->
                            <div class="modal fade" id="deleteModal{{ $proker->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $proker->id }}" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                  <div class="modal-content">
                                      <div class="modal-header">
                                          <h5 class="modal-title" id="deleteModalLabel{{ $proker->id }}">Konfirmasi Hapus Organisasi</h5>
                                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                          </button>
                                      </div>
                                      <div class="modal-body">
                                          Apakah Anda yakin ingin menghapus data ini?
                                      </div>
                                      <div class="modal-footer">
                                          <form id="deleteForm{{ $proker->id }}" action="/proker/{{ $proker->id }}" method="POST">
                                              @method('DELETE')
                                              @csrf
                                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                              <button type="submit" class="btn btn-danger">Hapus</button>
                                          </form>
                                      </div>
                                  </div>
                              </div>
                            </div>
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