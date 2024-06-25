@extends('layouts.admin-layout')

@section('kontainer')

<head>
  <style>
    .btn-margin-left {
      margin-left: 10px;
    }
    .btn-large {
      padding: 6px 12px; /* Sesuaikan padding untuk memperbesar ukuran tombol */
      font-size: 1rem; /* Sesuaikan ukuran font untuk memperbesar teks tombol */
    }
  </style>
</head>

<div class="row">
    <div class="col-lg-12 mb-4">
      <!-- Simple Tables -->
      <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h5 class="m-0 font-weight-bold text-primary">Manajemen Akun</h5>
        </div>
        <div class="table-responsive">
          <table id="myDataTable" class="table align-items-center table-flush">
            <a href="{{ url('/usermanajemen/create') }}" class="btn btn-primary btn-sm btn-margin-left btn-large" title="Tambah Akun">
              Tambah Akun
            </a>
            <thead class="thead-light">
              <tr>
                <th>NO</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>Jenis ID</th>
                <th>Nomer ID</th>
                <th>Organisasi</th>
                <th>Jabatan</th>
                <th>Role</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($users as $index => $user)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->username }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->code_id }}</td>
                <td>{{ $user->number_id }}</td>
                <td>{{ $user->organization }}</td>
                <td>{{ $user->jabatan->jabatan }}</td>
                <td>{{ $user->role }}</td>
                <td class="d-flex">
                  <a href="/usermanajemen/{{ $user->id }}" type="button" class="btn btn-warning mr-2">Edit</a>
                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal" data-userid="{{ $user->id }}">Hapus</button>
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

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus Akun</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus akun ini?
            </div>
            <div class="modal-footer">
                <form id="deleteForm" action="" method="POST">
                    @method('DELETE')
                    @csrf
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
        var deleteButtons = document.querySelectorAll('[data-target="#deleteModal"]');
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var userId = button.getAttribute('data-userid');
                var formAction = '/usermanajemen/' + userId;
                document.getElementById('deleteForm').action = formAction;
            });
        });
    });
    </script>
@endsection
