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
                            <th>No</th>
                            <th>Nama Organisasi</th>
                            <th>Nama Program Kerja</th>
                            <th>File Proposal</th>
                            <th>Lembar Pengesahan</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($listproker as $index => $proker)
                            {{-- Hanya tampilkan jika organisasi cocok dengan orguser atau jika jabatan adalah Ketua BEM --}}
                            @if (($proker->organisasi && $proker->organisasi->nama_organisasi == $orguser))
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
                                    <td>
                                        @if ($proker->proposal && $proker->proposal->pengesahan)
                                            <a href="{{ asset('pengesahan/' . $proker->proposal->pengesahan) }}" target="_blank">
                                                {{ $proker->proposal->file_proposal }}
                                            </a>
                                        @else
                                            Tidak ada file
                                        @endif
                                    </td>
                                    <td>{{ $proker->proposal ? $proker->proposal->status : 'Pending' }}</td>
                                    <td>{{ $proker->proposal ? $proker->proposal->catatan : 'Tidak Ada Catatan' }}</td>
                                    <td>
                                        @if ($proker->proposal?->status_flow == 0 || $proker->proposal?->status_flow == 1 || $proker->proposal?->status_flow == "" || $proker->proposal?->status_flow === null)
                                        <button type="button" class="btn btn-primary mr-2 btnModal" data-toggle="modal" data-target="#uploadModal" data-id="{{ $proker->id }}"
                                            data-file="{{ $proker->proposal ? $proker->proposal->file_proposal : '' }}">
                                            Upload File
                                        </button>
                                        @elseif ($proker->proposal?->status_flow == 9)
                                        Disetujui
                                        @else
                                        Diproses
                                        @endif
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
                    <input type="hidden" name="existing_file_name" id="existingFileName">
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
        var existingFileNameInput = document.getElementById('existingFileName');
    
        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                var idProker = this.getAttribute('data-id');
                var fileName = this.getAttribute('data-file');
                prokerIdInput.value = idProker;
                existingFileNameInput.value = fileName;  // Set the existing file name
                console.log('idProker:', idProker, 'fileName:', fileName);
            });
        });
    });
    </script>    

@endsection