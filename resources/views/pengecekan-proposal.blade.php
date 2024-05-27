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
               @if(($proker->organisasi->nama_organisasi == $orguser) || $orguser == 'Ketua BEM')
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
                  <button type="submit" class="btn btn-success">Diterima</button>
                  <button type="submit" class="btn btn-danger">Ditolak</button>
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


  <!-- Modal untuk Revisi -->
<div class="modal fade" id="revisiModal" tabindex="-1" role="dialog" aria-labelledby="revisiModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="revisiModalLabel">Revisi Catatan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ url('path/to/update/note') }}">
          @csrf
          <div class="form-group">
            <label for="catatan">Catatan Baru:</label>
            <textarea class="form-control" id="catatan" name="catatan" rows="4" required></textarea>
          </div>
          <input type="hidden" name="proker_id" id="proker_id" value="">
          <button type="submit" class="btn btn-primary">Update Catatan</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Modal untuk Diterima -->
<div class="modal fade" id="diterimaModal" tabindex="-1" role="dialog" aria-labelledby="diterimaModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="diterimaModalLabel">Upload File Baru</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ url('path/to/update/file') }}" enctype="multipart/form-data">
          @csrf
          <div class="form-group">
            <label for="file">Pilih File:</label>
            <input type="file" class="form-control-file" id="file" name="file" required>
          </div>
          <input type="hidden" name="proker_id" id="proker_file_id" value="">
          <button type="submit" class="btn btn-success">Upload File</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
      var btnRevisi = document.querySelectorAll('.btn-warning');
      var btnDiterima = document.querySelectorAll('.btn-success');
  
      btnRevisi.forEach(function(button) {
          button.addEventListener('click', function() {
              var prokerId = this.closest('tr').querySelector('td:first-child').textContent;
              document.getElementById('proker_id').value = prokerId;
              var revisiModal = new bootstrap.Modal(document.getElementById('revisiModal'));
              revisiModal.show();
          });
      });
  
      btnDiterima.forEach(function(button) {
          button.addEventListener('click', function() {
              var prokerId = this.closest('tr').querySelector('td:first-child').textContent;
              document.getElementById('proker_file_id').value = prokerId;
              var diterimaModal = new bootstrap.Modal(document.getElementById('diterimaModal'));
              diterimaModal.show();
          });
      });
  });
  </script>
  
@endsection