@extends('layouts.organisasi-layout')

@section('content')
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Manajemen Proposal</h6>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Nama Organisasi</th>
                            <th>Nama Program Kerja</th>
                            <th>File</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($listproker as $proker)
                            @php
                                $jabatanWords = explode(' ', $jabatan);
                                $lastWord = end($jabatanWords);
                                $wordCount = count($jabatanWords);
                            @endphp
                            @if ($wordCount > 2 || ($proker->organisasi && stripos($proker->organisasi->nama_organisasi, $lastWord) !== false) || $jabatan == 'Ketua BPM')
                                <tr>
                                    <td>{{ $proker->id }}</td>
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
                                    <td>{{ $proker->proposal ? $proker->proposal->status : 'Pending'}}</td>
                                    <td>{{ $proker->proposal ? $proker->proposal->catatan : 'Tidak Ada Catatan' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary mr-2 btnModal" data-toggle="modal" data-target="#uploadModal" data-id="{{ $proker->id }}">
                                            Upload File
                                        </button>
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

<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('file.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_proker" id="prokerId">
                    <input type="file" name="file" id="fileInput" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload File</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
      var modal = document.getElementById('uploadModal');
      var buttons = document.querySelectorAll('.btnModal');
      var prokerIdInput = document.getElementById('prokerId');
  
      buttons.forEach(function (button) {
          button.addEventListener('click', function () {
              var idProker = this.getAttribute('data-id');
              prokerIdInput.value = idProker;
              console.log('idProker:', idProker);
          });
      });
  });
  </script>

@endsection