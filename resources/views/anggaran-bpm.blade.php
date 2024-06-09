@extends('layouts.bpm-layout')

@section('konteng')

<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Manajemen Anggaran</h6>
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
                <button type="button" class="btn btn-primary" onclick="openAddModal()">
                    Tambah Anggaran
                </button>
                <!-- The Modal -->
                <div class="modal fade" id="anggaranModal" tabindex="-1" aria-labelledby="anggaranModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h4 class="modal-title" id="modalTitle">Tambah Anggaran Baru</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            
                            <!-- Modal Body -->
                            <div class="modal-body">
                                <form action="{{ route('anggaran.store') }}" id="anggaranForm" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="namaOrganisasi">Nama Organisasi:</label>
                                        <select class="form-control" id="namaOrganisasi" name="id_organisasi" required>
                                            @foreach ($organisasis as $organisasi)
                                                <option value="{{ $organisasi->id }}">{{ $organisasi->nama_organisasi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="jumlahMhs">Jumlah Mahasiswa:</label>
                                        <input type="number" class="form-control" id="jumlahMhs" name="jumlah_mhs" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="jumlahAnggaran">Jumlah Anggaran:</label>
                                        <input type="number" class="form-control" id="jumlahAnggaran" name="jumlah_anggaran" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="totalAnggaran">Total Anggaran:</label>
                                        <input type="number" class="form-control" id="totalAnggaran" name="total_anggaran" required readonly>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Modal Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" onclick="submitForm()">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteAnggaranModal" tabindex="-1" aria-labelledby="deleteAnggaranModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteAnggaranModalLabel">Hapus Data Anggaran</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Apakah Anda yakin ingin menghapus data anggaran ini?
                            </div>
                            <div class="modal-footer">
                                <form id="deleteAnggaranForm" method="POST" action="">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id" id="deleteAnggaranId">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Organisasi</th>
                            <th>Jumlah Mahasiswa</th>
                            <th>Jumlah Anggaran</th>
                            <th>Total Anggaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($anggaran as $index => $anggarans)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $anggarans->organisasi ? $anggarans->organisasi->nama_organisasi : 'Tidak ada organisasi' }}</td>
                            <td>{{ $anggarans->jumlah_mhs }}</td>    
                            <td>{{ $anggarans->jumlah_anggaran }}</td>
                            <td>{{ $anggarans->total_anggaran }}</td>        
                            <td>
                                <button type="button" class="btn btn-warning" data-id="{{ $anggarans->id_anggaran }}"
                                    data-id_organisasi="{{ $anggarans->id_organisasi }}" data-jumlah_mhs="{{ $anggarans->jumlah_mhs }}"
                                    data-jumlah_anggaran="{{ $anggarans->jumlah_anggaran }}" onclick="openEditModal(this)">
                                <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger" data-id="{{ $anggarans->id_anggaran }}" onclick="openDeleteModal(this)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
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

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

<script>
    function openAddModal() {
        $('#anggaranModal').modal('show');
    }

    function openEditModal(button) {
        var id = button.getAttribute('data-id');
        var idOrganisasi = button.getAttribute('data-id_organisasi');
        var jumlahMhs = button.getAttribute('data-jumlah_mhs');
        var jumlahAnggaran = button.getAttribute('data-jumlah_anggaran');

        document.getElementById('modalTitle').textContent = 'Update Anggaran';
        document.getElementById('anggaranForm').action = "{{ url('anggaran/update') }}/" + id;

        if (!document.querySelector('input[name="_method"]')) {
            var methodField = document.createElement('input');
            methodField.setAttribute('type', 'hidden');
            methodField.setAttribute('name', '_method');
            methodField.setAttribute('value', 'PUT');
            document.getElementById('anggaranForm').appendChild(methodField);
        }

        document.getElementById('namaOrganisasi').value = idOrganisasi;
        document.getElementById('jumlahMhs').value = jumlahMhs;
        document.getElementById('jumlahAnggaran').value = jumlahAnggaran;
        calculateTotalAnggaran(); 

        $('#anggaranModal').modal('show');
    }

    function openDeleteModal(button) {
        var id = button.getAttribute('data-id');
        var deleteForm = document.getElementById('deleteAnggaranForm');
        deleteForm.action = "{{ url('anggaran/delete') }}/" + id;
        $('#deleteAnggaranId').val(id);
        $('#deleteAnggaranModal').modal('show');
    }

    function submitForm() {
        document.getElementById('anggaranForm').submit();
    }
    function calculateTotalAnggaran() {
        var jumlahMhs = document.getElementById('jumlahMhs').value;
        var jumlahAnggaran = document.getElementById('jumlahAnggaran').value;
        var totalAnggaran = jumlahMhs * jumlahAnggaran;
        document.getElementById('totalAnggaran').value = totalAnggaran;
    }

    $(document).ready(function() {
        $('#jumlahMhs, #jumlahAnggaran').on('input', function() {
            var jumlahMhs = $('#jumlahMhs').val();
            var jumlahAnggaran = $('#jumlahAnggaran').val();
            var totalAnggaran = parseInt(jumlahMhs) * parseInt(jumlahAnggaran);
            $('#totalAnggaran').val(totalAnggaran);
        });
    });
</script>

@endsection
