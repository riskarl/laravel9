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
                <h6 class="m-0 font-weight-bold text-primary">Manajemen Laporan Pertanggungjawaban</h6>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Organisasi</th>
                            <th>Nama Program Kerja</th>
                            <th>File LPJ</th>
                            <th>File Pengesahan</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Dana Disetujui</th>
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
                                @if ($proker->lpj)
                                    <a href="{{ asset('lpj/' . $proker->lpj->file_lpj) }}" target="_blank">{{ $proker->lpj->file_lpj }}</a>
                                @else
                                    Tidak ada file
                                @endif
                            </td>
                            <td>
                                @if ($proker->lpj)
                                    <a href="{{ asset('lpj/' . $proker->lpj->pengesahan) }}" target="_blank">{{ $proker->lpj->pengesahan }}</a>
                                @else
                                    Tidak ada file
                                @endif
                            </td>
                            <td>{{ $proker->lpj ? $proker->lpj->status : 'Pending'}}</td>
                            <td>{{ $proker->lpj ? $proker->lpj->catatan : 'Tidak Ada Catatan' }}</td> 
                            <td>{{ $proker->lpj ? $proker->lpj->dana_disetujui : 'Tidak ada dana' }}</td> 
                            <td>
                                @if ($proker->lpj)
                                    @if ($proker->lpj->status_flow_lpj == 0 || $proker->lpj->status_flow_lpj == 1)
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="button" class="btn btn-primary mr-1" data-toggle="modal" data-target="#uploadModal" 
                                                data-id="{{ $proker->id }}" 
                                                data-dana="{{ $proker->lpj->dana_disetujui }}" 
                                                data-file="{{ $proker->lpj->file_lpj }}" 
                                                onclick="openModal(this)">
                                            <i class="fas fa-upload"></i>
                                        </button>
                                    </div>
                                    @elseif ($proker->lpj->status_flow_lpj == 9)
                                    <span class="badge badge-success">Selesai</span>
                                    @else
                                        Diproses
                                    @endif
                                @else
                                <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="button" class="btn btn-primary mr-1" data-toggle="modal" data-target="#uploadModal" 
                                                data-id="{{ $proker->id }}" 
                                                data-dana="" 
                                                data-file="" 
                                                onclick="openModal(this)">
                                            <i class="fas fa-upload"></i>
                                        </button>
                                </div>
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
            <form id="lpjForm" action="{{ route('filelpj.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Tambahkan input hidden untuk menentukan aksi -->
                <input type="hidden" id="actionType" name="action_type" value="upload">
                <div class="modal-header">
                    <!-- Ubah judul modal untuk menunjukkan aksi yang sedang dilakukan -->
                    <h5 class="modal-title" id="uploadModalLabel">Upload File LPJ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mt-3">
                        <label for="danaDisetujui">Dana Disetujui:</label>
                        <input type="number" name="dana_disetujui" id="danaDisetujui" class="form-control" required>
                    </div>
                    <div class="form-group mt-3">
                        <label for="currentFileLpj">Current File LPJ:</label>
                        <span id="currentFileLpj">Tidak ada file</span>
                    </div>
                    <div class="form-group mt-3">
                        <label for="fileInput">Upload New File:</label>
                        <input type="file" name="file_lpj" id="fileInput" class="form-control" required>
                    </div>
                    <input type="hidden" name="id_proker" id="prokerId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <!-- Ubah label button untuk menunjukkan aksi yang sedang dilakukan -->
                    <button type="submit" class="btn btn-primary" id="actionButton">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(button) {
        // Set judul modal
        document.getElementById('uploadModalLabel').innerText = 'Upload File LPJ';
    
        // Reset form
        document.getElementById('lpjForm').reset();
    
        // Set aksi form menjadi upload
        document.getElementById('actionType').value = 'upload';
    
        // Set ID proker
        document.getElementById('prokerId').value = button.getAttribute('data-id');
    
        // Set nilai dana disetujui
        const danaDisetujui = button.getAttribute('data-dana');
        document.getElementById('danaDisetujui').value = danaDisetujui ? danaDisetujui : '';
    
        // Set file yang ada atau kosongkan
        const fileLpj = button.getAttribute('data-file');
        document.getElementById('currentFileLpj').innerText = fileLpj ? fileLpj : 'Tidak ada file';
    
        // Clear file input value
        document.getElementById('fileInput').value = '';
    
        // Tampilkan modal
        $('#uploadModal').modal('show');
    }
    </script>
    

@endsection
