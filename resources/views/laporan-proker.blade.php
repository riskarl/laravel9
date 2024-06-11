@php
    $content = session('jabatan.code_jabatan') == 1 ? 'konten' : 'kontainer';
    $layout = session('jabatan.code_jabatan') == 1 ? 'layouts.pengecek-layout' : 'layouts.admin-layout';
@endphp

@extends($layout)

@section($content)

<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Laporan Program Kerja</h6>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#cetakModal">
                    Cetak Laporan 
                </button>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Organisasi</th>
                            <th>Nama Program Kerja</th>
                            <th>Proposal</th>
                            <th>LPJ</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($listproker as $index => $proker)
                            @if ($proker->organisasi && $proker->organisasi->nama_organisasi)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $proker->organisasi->nama_organisasi }}</td>
                                    <td>{{ $proker->nama_proker }}</td>
                                    <td>
                                        @if ($proker->proposal)
                                            @if ($proker->proposal->status_flow == 9)
                                                Disetujui
                                            @elseif ($proker->proposal->status_flow < 9)
                                                Belum Selesai
                                            @else
                                                Tidak ada
                                            @endif
                                        @else
                                            Tidak ada
                                        @endif
                                    </td>
                                    <td>
                                        @if ($proker->lpj)
                                            @if ($proker->lpj->status_flow_lpj == 9)
                                                Disetujui
                                            @elseif ($proker->lpj->status_flow_lpj < 9)
                                                Belum Selesai
                                            @else
                                                Tidak ada
                                            @endif
                                        @else
                                            Tidak ada
                                        @endif
                                    </td>                                  
                                    <td>
                                        @if ($proker->proposal)
                                        @if ($proker->proposal->status_flow == 9 && $proker->lpj && $proker->lpj->status_flow_lpj == 9)
                                        <span class="badge badge-success">Terlaksana</span>
                                        @elseif ($proker->proposal->status_flow == 9 && (!$proker->lpj || $proker->lpj->status_flow_lpj < 9))
                                        <span class="badge badge-warning">Belum Selesai</span>
                                        @else
                                        <span class="badge badge-danger">Belum Terlaksana</span>
                                        @endif
                                    @else
                                    <span class="badge badge-danger">Belum Terlaksana</span>
                                    @endif
                                    </td>                                  
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
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
          <form id="cetakForm" action="{{ route('cetakLaporan') }}" method="POST">
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

@endsection