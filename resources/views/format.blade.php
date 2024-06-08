@extends('layouts.bpm-layout')

@section('konteng')

<div class="row">
    <div class="col-lg-12 mb-4">
        <!-- Simple Tables -->
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Manajemen Format</h6>
            </div>
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#formatModal" onclick="openAddModal()">
                        Tambah Format
                    </button>
                    <!-- The Modal -->
                    <div class="modal fade" id="formatModal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            
                                <!-- Modal Header -->
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modalTitle">Tambah Format Baru</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                
                                <!-- Modal Body -->
                                <div class="modal-body">
                                    <form action="{{ route('format.store') }}" id="formatForm" method="POST" enctype="multipart/form-data">
                                     @csrf
                                        <input type="hidden" id="formatId" name="id_format">
                                        <div class="form-group">
                                            <label for="jenisFile">Jenis File:</label>
                                            <input type="text" class="form-control" id="jenisFile" name="jenis_format" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="fileFormat">File Format:</label>
                                            <input type="file" class="form-control" id="fileFormat" name="file_format" required>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Modal Footer -->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="submitForm()">Submit</button>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Jenis Format</th>
                            <th>File Format</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($format as $index => $formats)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $formats->jenis_format }}</td>
                            <td>
                                <a href="{{ asset('format/' . $formats->file_format) }}" target="_blank">
                                    {{ $formats->file_format }}
                                </a>
                            </td>    
                            <td>
                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#formatModal" 
                                        data-id="{{ $formats->id_format }}" data-jenis="{{ $formats->jenis_format }}" data-file="{{ $formats->file_format }}"
                                        onclick="openEditModal(this)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal" 
                                        data-id="{{ $formats->id_format }}" onclick="openDeleteModal(this)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                <div class="modal fade" id="deleteModal">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h4 class="modal-title">Konfirmasi Hapus</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            
                                            <!-- Modal Body -->
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin ingin menghapus format ini?</p>
                                            </div>
                                            
                                            <!-- Modal Footer -->
                                            <div class="modal-footer">
                                                <form id="deleteForm" action="{{ route('file-format.delete', '') }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" id="formatIdToDelete" name="id_format">
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

<script>
    function openAddModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Format Baru';
        document.getElementById('formatForm').action = "{{ route('format.store') }}";
        document.getElementById('formatForm').reset();
        document.getElementById('formatId').value = '';
    }

    function openEditModal(button) {
        var id = button.getAttribute('data-id');
        var jenis = button.getAttribute('data-jenis');
        var file = button.getAttribute('data-file');

        document.getElementById('modalTitle').innerText = 'Edit Format';
        document.getElementById('formatForm').action = "{{ route('format.update', '') }}/" + id;
        document.getElementById('jenisFile').value = jenis;
        document.getElementById('formatId').value = id;
        // File input cannot be prefilled for security reasons

        // Menampilkan nama file yang sudah ada di input file
        var fileInput = document.getElementById('fileFormat');
        fileInput.labels[0].innerText = file; // Menampilkan nama file di label input file
    }
    function openDeleteModal(button) {
    var id = button.getAttribute('data-id');
    var deleteForm = document.getElementById('deleteForm');
    deleteForm.action = "{{ route('file-format.delete', '') }}/" + id;
    document.getElementById('formatIdToDelete').value = id;
    }
    function submitForm() {
        var form = document.getElementById('formatForm');
        if (form.checkValidity()) {
            form.submit();
            $('#formatModal').modal('hide');
        } else {
            form.reportValidity();
        }
    }
</script>

@endsection
