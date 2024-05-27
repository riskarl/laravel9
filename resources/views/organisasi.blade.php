@extends('layouts.admin-layout')

@section('kontainer')

<div class="row">
    <div class="col-lg-12 mb-4">
      <!-- Simple Tables -->
      <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Manajemen Organisasi</h6>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center table-flush">
            <a href="{{ url('/organisasi/create') }}" class="btn btn-primary btn-sm" title="Tambah Organisasi">
              Tambah Organisasi
            </a>
            <thead class="thead-light">
              <tr>
                <th>ID</th>
                <th>Nama Organisasi</th>
                <th>Periode</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($listorganisasi as $organisasi)
              <tr>
                <td>{{ $organisasi->id }}</td>
                <td>{{ $organisasi->nama_organisasi }}</td>
                <td>{{ $organisasi->periode }}</td>
                <td class="d-flex">
                  <a href="/organisasi/{{ $organisasi->id }}" type="button" class="btn btn-warning mr-2">Edit</a>
                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal" data-userid="{{ $organisasi->id }}">Hapus</button>
                  <!-- Delete Confirmation Modal -->
                  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus Organisasi</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Apakah Anda yakin ingin menghapus data ini?
                            </div>
                            <div class="modal-footer">
                                <form id="deleteForm" action="/organisasi/{{ $organisasi->id }}" method="POST">
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
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="card-footer"></div>
      </div>
    </div>
  </div>
@endsection