@php
    $content = session('jabatan.code_jabatan') == 1 ? 'konten' : 'content';
    $layout = session('jabatan.code_jabatan') == 1 ? 'layouts.pengecek-layout' : 'layouts.organisasi-layout';
    $uniqueOrganisasi = $anggaran->pluck('nama_organisasi')->unique();
@endphp

@extends($layout)

@section($content)

<div class="row">
    <div class="col-lg-12 mb-4">
      <!-- Simple Tables -->
      <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Rincian Anggaran Organisasi</h6>
        </div>
        <div class="table-responsive">
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#cetakModal">
            Cetak
          </button>
        
          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th>NO</th>
                <th>Nama Organisasi</th>
                <th>Nama Program Kerja</th>
                <th>Dana Diajukan</th>
                <th>Dana Disetujui</th>
                <th>Sisa Anggaran</th>
              </tr>
            </thead>
            <tbody id="anggaranTableBody">
              @foreach ($anggaran as $index => $item)
              <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $item['nama_organisasi'] }}</td>
                  <td>{{ $item['nama_proker'] }}</td>
                  <td>{{ $item['dana_diajukan'] }}</td>
                  <td>{{ $item['dana_disetujui'] }}</td>
                  <td>{{ $item['sisa_anggaran'] }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="card-footer"></div>
      </div>
    </div>
  </div>

<!-- Modal untuk Cetak -->
<div class="modal fade" id="cetakModal" tabindex="-1" role="dialog" aria-labelledby="cetakModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cetakModalLabel">Cetak Laporan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="cetakForm">
          <div class="form-group">
            <label for="organisasiDropdown">Pilih Organisasi</label>
            <select class="form-control" id="organisasiDropdown">
              <option value="semua">Semua</option>
              @foreach ($uniqueOrganisasi as $namaOrganisasi)
                <option value="{{ $namaOrganisasi }}">{{ $namaOrganisasi }}</option>
              @endforeach
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary">Cetak</button>
      </div>
    </div>
  </div>
</div>

@endsection
