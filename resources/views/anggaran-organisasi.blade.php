@php
    $button = session('jabatan.code_jabatan') == 1 ? true : false;
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
          @if ($button)
          <div class="d-flex justify-content-between mb-2">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#cetakModal">
              Cetak
            </button>
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#setAnggaranModal">
              Set Anggaran
            </button>
          </div>

          <hr>
          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th>NO</th>
                <th>Total Anggaran</th>
                <th>Jenis Periode</th>
                <th>Total Periode</th>
                <th>Tanggal Set</th>
              </tr>
            </thead>
            <tbody id="anggaranTableBody">
              @foreach ($totalAnggaran as $idx => $i)
              <tr>
                  <td>{{ $idx + 1 }}</td>
                  <td>{{ $i['total_anggaran'] }}</td>
                  <td>{{ $i['jenis_periode'] }}</td>
                  <td>{{ $i['total_periode'] }}</td>
                  <td>{{ $i['tgl_mulai_periode'] ?? null }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <hr>

          @endif

          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th>NO</th>
                <th>Nama Organisasi</th>
                <th>Nama Program Kerja</th>
                <th>Dana Diajukan</th>
                <th>Dana Disetujui</th>
                <th>Sisa Anggaran Organisasi</th>
                @if ($button)
                <th>Total Sisa Anggaran</th>
                @endif
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
                  @if ($button)
                  <td>{{ $item['total_sisa_anggaran'] }}</td>
                  @endif
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
        <form id="cetakForm" action="{{ route('cetakLaporanAnggaran') }}" method="POST">
          @csrf
          <div class="form-group">
            <label for="organisasiDropdown">Pilih Organisasi</label>
            <select class="form-control" id="organisasiDropdown" name="nama_organisasi">
              <option value="semua">Semua</option>
              @foreach ($uniqueOrganisasi as $namaOrganisasi)
                <option value="{{ $namaOrganisasi }}">{{ $namaOrganisasi }}</option>
              @endforeach
            </select>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        <button type="submit" class="btn btn-primary">Cetak</button>
      </div>
        </form>
    </div>
  </div>
</div>

<!-- Modal untuk Set Anggaran -->
<div class="modal fade" id="setAnggaranModal" tabindex="-1" role="dialog" aria-labelledby="setAnggaranModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="setAnggaranModalLabel">Set Anggaran</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Form untuk Set Anggaran -->
        <form id="setAnggaranForm" action="{{ route('setAnggaran') }}" method="POST">
          @csrf
          <div class="form-group">
            <label for="totalAnggaran">Total Anggaran</label>
            <input type="number" class="form-control" id="totalAnggaran" name="total_anggaran" required>
          </div>
          <div class="form-group">
            <label>Jenis Periode</label><br>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="jenis_periode" id="periodeBulan" value="bulan" required>
              <label class="form-check-label" for="periodeBulan">Bulan</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="jenis_periode" id="periodeTahun" value="tahun" required>
              <label class="form-check-label" for="periodeTahun">Tahun</label>
            </div>
          </div>
          <div class="form-group">
            <label for="totalPeriode">Total Periode</label>
            <input type="number" class="form-control" id="totalPeriode" name="total_periode" required>
          </div>
          <div class="form-group">
            <label for="tglMulaiPeriode">Tanggal Mulai Periode</label>
            <input type="date" class="form-control" id="tglMulaiPeriode" name="tgl_mulai_periode" required>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-success">Set Anggaran</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


@endsection
