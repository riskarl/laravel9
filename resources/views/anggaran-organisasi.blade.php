@php
    $layout = session('jabatan.code_jabatan') == 1 ? 'layouts.pengecek-layout' : 'layouts.organisasi-layout';
@endphp

@extends($layout)

@section('content')

<div class="row">
    <div class="col-lg-12 mb-4">
      <!-- Simple Tables -->
      <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Rincian Anggaran Organisasi</h6>
        </div>
        <div class="table-responsive">
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
            <tbody>
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

@endsection
