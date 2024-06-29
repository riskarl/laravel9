@php
    $content = session('jabatan.code_jabatan') == 1 ? 'konten' : 'kontainer';
    $layout = session('jabatan.code_jabatan') == 1 ? 'layouts.pengecek-layout' : 'layouts.admin-layout';
@endphp

@extends($layout)

@section($content)

<head>
    <style>
      .btn-margin-left {
        margin-left: 10px;
      }
      .btn-large {
        padding: 6px 12px; /* Sesuaikan padding untuk memperbesar ukuran tombol */
        font-size: 1rem; /* Sesuaikan ukuran font untuk memperbesar teks tombol */
      }
    </style>
  </head>

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

        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h4 class="m-0 font-weight-bold text-primary">Laporan Program Kerja</h4>
                <button type="button" class="btn btn-primary btn-sm btn-margin-left btn-large" data-toggle="modal" data-target="#cetakModal">
                    Cetak Laporan 
                </button>
            </div>
            <div class="table-responsive">
                <table id="myDataTable" class="table align-items-center table-flush">
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