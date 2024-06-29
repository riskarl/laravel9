@extends('layouts.organisasi-layout')

@section('content')

<div class="row">
    <div class="col-lg-12 mb-4">

      @if (session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ session('error') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
      @endif

      @if (session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('success') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
      @endif

      <!-- Simple Tables -->
      <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h4 class="m-0 font-weight-bold text-primary">Manajemen Rencana Anggaran Biaya</h4>
        </div>
        <div class="table-responsive">
          <table id="" class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th>NO</th>
                <th>Nama Organisasi</th>
                <th>Nama Program Kerja</th>
                <th>File RAB</th>
                <th>File SRPD</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($listproker as $index => $proker)
              <tr>
                @if (($proker->organisasi && $proker->organisasi->nama_organisasi == $orguser))
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $proker->organisasi ? $proker->organisasi->nama_organisasi : 'Tidak ada organisasi' }}</td>
                  <td>{{ $proker->nama_proker }}</td>
               
                  <td> 
                    @if ($proker->rab && $proker->rab->file_rab)
                        <a href="{{ asset('rab/' . $proker->rab->file_rab) }}" target="_blank">
                            {{ $proker->rab->file_rab }}
                        </a>
                    @else
                        Tidak ada file
                    @endif
                  </td>
                  <td>
                    @if(isset($proker->rab->file_srpd) && $proker->rab->file_srpd)
                    <a href="{{ asset('srpd/' . $proker->rab->file_srpd) }}" target="_blank">
                      {{ $proker->rab->file_srpd }}
                    </a>
                    @else
                      Tidak ada file
                    @endif        
                  </td>
                  <td>
                    <button type="button" class="btn btn-primary mr-2 btnModal" data-toggle="modal" data-target="#uploadModal{{ $proker->id }}" data-id="{{ $proker->id }}">
                        Upload File
                    </button>
                    <div class="modal fade" id="uploadModal{{ $proker->id }}" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel{{  $proker->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('filerab.upload')}}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="uploadModalLabel{{  $proker->id }}">Upload File</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id_proker" value="{{ $proker->id }}">
                                        <input type="file" name="file_rab" id="fileInput" class="form-control" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Upload File</button>
                                    </div>
                                </form>
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

  

<script>
  document.addEventListener('DOMContentLoaded', function () {
      var modal = document.getElementById('uploadModal');
      var buttons = document.querySelectorAll('.btnModal');
      var prokerIdInput = document.getElementById('prokerId');
      var existingFileNameInput = document.getElementById('existingFileName');
  
      buttons.forEach(function (button) {
          button.addEventListener('click', function () {
              var idProker = this.getAttribute('data-id');
              var fileName = this.getAttribute('data-file');
              prokerIdInput.value = idProker;
              existingFileNameInput.value = fileName; 
              console.log('idProker:', idProker);
          });
      });
  });
  </script>

@endsection