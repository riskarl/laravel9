@extends('layouts.pengecek-layout')

@section('konten')

<div class="row">
    <div class="col-lg-12 mb-4">
      <!-- Simple Tables -->
      <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Pengecekan Laporan Pertanggungjawaban</h6>
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
            <thead class="thead-light">
              <tr>
                <th>No</th>
                <th>Nama Program Kerja</th>
                <th>Nama Organisasi</th>
                <th>File Lpj</th>
                <th>Lembar pengesahan</th>
                <th>Catatan</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($listproker as $index => $proker)
              @if($proker->lpj)
              @if(
                ($codeJabatan == 6 && ($proker->lpj->status_flow_lpj <= 1 || $proker->lpj->status_flow_lpj == null) && $proker->organisasi->nama_organisasi == $orguser)
                || ($codeJabatan == 5 && $orguser == 'BEM' && $proker->organisasi->nama_organisasi != 'BEM' && $proker->lpj->status_flow_lpj >= 2 && $proker->lpj->status_flow_lpj != 1)
                || ($codeJabatan == 5 && $orguser == 'BEM' && $proker->organisasi->nama_organisasi == 'BEM' && $proker->lpj->status_flow_lpj >= 3 && $proker->lpj->status_flow_lpj != 1)
                || ($codeJabatan == 5 && $orguser == 'BPM' && $proker->lpj->status_flow_lpj >= 3 && $proker->lpj->status_flow_lpj != 1)
                || ($codeJabatan == 4 && $proker->organisasi->nama_organisasi == $orguser && $proker->lpj->status_flow_lpj >= 4 && $proker->lpj->status_flow_lpj != 1)
                || ($codeJabatan == 5 && (($proker->lpj->status_flow_lpj == 0 || $proker->lpj->status_flow_lpj == null || $proker->lpj->status_flow_lpj == "") || $proker->lpj->status_flow_lpj >= 2 ) && $proker->organisasi->nama_organisasi == $orguser && $proker->lpj->status_flow_lpj != 1)
                || ($codeJabatan == 2 && ($proker->organisasi->nama_organisasi == 'BEM' || stripos($proker->organisasi->nama_organisasi, 'UKM') !== false) && 'Kampus' == $orguser && $proker->lpj->status_flow_lpj >= 5 && $proker->lpj->status_flow_lpj != 1)
                || ($codeJabatan == 8 && (stripos($proker->organisasi->nama_organisasi, 'HIMA') !== false) && ('Kampus' == $orguser || $proker->organisasi->nama_organisasi == $orguser ) && $proker->lpj->status_flow_lpj >= 5 && $proker->lpj->status_flow_lpj != 1)
                || ($codeJabatan == 3 && (stripos($proker->organisasi->nama_organisasi, 'HIMA') !== false) && ('Kampus' == $orguser || $proker->organisasi->nama_organisasi == $orguser ) && $proker->lpj->status_flow_lpj >= 6 && $proker->lpj->status_flow_lpj != 1)
                || ($codeJabatan == 2 && (stripos($proker->organisasi->nama_organisasi, 'HIMA') !== false) && 'Kampus' == $orguser && $proker->lpj->status_flow_lpj >= 7 && $proker->lpj->status_flow_lpj != 1)
                || ($codeJabatan == 1 && 'Kampus' == $orguser && ($proker->lpj->status_flow_lpj == 8 || $proker->lpj->status_flow_lpj == 9))
            )            
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $proker->nama_proker }}</td>
                    <td>{{ $proker->organisasi->nama_organisasi }}</td>
                    <td>
                      @if ($proker->lpj)
                          <a href="{{ asset('lpj/' . $proker->lpj->file_lpj) }}" target="_blank">
                              {{ $proker->lpj->file_lpj }}</a>
                      @else
                          Tidak ada file LPJ
                      @endif
                  </td>
                  <td>
                    @if ($proker->lpj)
                        <a href="{{ asset('lpj/' . $proker->lpj->pengesahan) }}" target="_blank">
                            {{ $proker->lpj->pengesahan }}</a>
                    @else
                        Tidak ada file LPJ
                    @endif
                </td>
                  <td>
                    @if ($proker->lpj)
                        {{ $proker->lpj->catatan }}
                    @else
                        Tidak Ada Catatan
                    @endif
                  </td>                
                  <td>
                    @if($proker->lpj)
                        {{ $proker->lpj->status }}
                    @else
                        Pending
                    @endif
                  </td>  
                  <td>
                    @if($proker->lpj->status_flow_lpj != 9 && (
                        ($codeJabatan == 5 && $orguser == 'BEM' && $proker->lpj->status_flow_lpj == 2 && $proker->organisasi->nama_organisasi != 'BEM' ) ||
                        ($codeJabatan == 5 && $orguser == 'BPM' && $proker->lpj->status_flow_lpj == 3 ) ||
                        ($codeJabatan == 5 && ($proker->lpj->status_flow_lpj == 0 || $proker->lpj->status_flow_lpj == null)) ||
                        ($codeJabatan == 4 && $proker->lpj->status_flow_lpj == 4 ) ||
                        ($codeJabatan == 8 && $proker->lpj->status_flow_lpj == 5 ) ||
                        ($codeJabatan == 3 && $proker->lpj->status_flow_lpj == 6 ) ||
                        ($codeJabatan == 2 && (($proker->lpj->status_flow_lpj == 7 && (stripos($proker->organisasi->nama_organisasi, 'HIMA') !== false)) || $proker->lpj->status_flow_lpj == 5)) ||
                        ($codeJabatan == 1 && $proker->lpj->status_flow_lpj == 8 )
                    ))
                        <button type="button" class="btn btn-warning" onclick="openRevisiModal({{ $proker->lpj->id }})">Revisi</button>
                        @if($codeJabatan == 1)
                            <form action="{{ route('createSignaturePdfLpj') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="lpj_id" value="{{ $proker->lpj->id }}">
                                <input type="hidden" name="proker" value="{{ $proker->nama_proker }}">
                                <input type="hidden" name="organisasi" value="{{ $proker->organisasi->nama_organisasi }}">
                                <button type="submit" class="btn btn-success">Diterima</button>
                            </form>
                        @else
                            <a href="{{ route('lpjs.approve', ['lpjId' => $proker->lpj->id]) }}"><button type="submit" class="btn btn-success">Diterima</button></a>
                        @endif
                    @else
                        @if($proker->lpj->status_flow_lpj != 9)
                            Diproses
                        @else
                            Selesai
                        @endif
                    @endif
                </td>                
                </tr>
                @endif
                @endif
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="card-footer"></div>
      </div>
    </div>
  </div>

  <!-- Modal untuk Revisi LPJ -->
<div class="modal fade" id="revisiModal" tabindex="-1" role="dialog" aria-labelledby="revisiModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="revisiModalLabel">Catatan Revisi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('lpjs.revisi') }}" method="POST">
          @csrf
          <input type="hidden" name="lpj_id" id="lpjId" value="">
          <div class="form-group">
            <label for="catatan">Catatan:</label>
            <textarea class="form-control" id="catatan" name="catatan" required></textarea>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit Revisi</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
  function openRevisiModal(lpjId) {
    // Mengatur nilai lpj_id ke dalam input tersembunyi di modal
    document.getElementById('lpjId').value = lpjId;
  
    // Menampilkan modal
    var revisiModal = new bootstrap.Modal(document.getElementById('revisiModal'));
    revisiModal.show();
  }
</script>
  
@endsection
