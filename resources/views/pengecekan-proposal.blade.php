@extends('layouts.pengecek-layout')

@section('konten')

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
          <h4 class="m-0 font-weight-bold text-primary">Pengecekan Proposal</h4>
        </div>
        <div class="table-responsive">
          <table id="myDataTable" class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th>No</th>
                <th>Nama Program Kerja</th>
                <th>Nama Organisasi</th>
                <th>File Proposal</th>
                <th>Lembar Pengesahan</th>
                <th>Catatan</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($listproker as $index => $proker)
              @if($proker->proposal)
                @if(($codeJabatan == 6 && ($proker->proposal->status_flow == 1 || $proker->proposal->status_flow == 0 || $proker->proposal->status_flow == null || $proker->proposal->status_flow == "") && $proker->organisasi->nama_organisasi == $orguser)
                || ($codeJabatan == 5 && $orguser == 'BEM' && $proker->organisasi->nama_organisasi != 'BEM' && $proker->proposal->status_flow >= 2 && $proker->proposal->status_flow != 1)
                || ($codeJabatan == 5 && $orguser == 'BEM' && $proker->organisasi->nama_organisasi == 'BEM' && $proker->proposal->status_flow >= 3 && $proker->proposal->status_flow != 1)
                || ($codeJabatan == 5 && $orguser == 'BPM' && $proker->proposal->status_flow >= 3 && $proker->proposal->status_flow != 1)
                || ($codeJabatan == 4 && $proker->organisasi->nama_organisasi == $orguser && $proker->proposal->status_flow >= 4 && $proker->proposal->status_flow != 1)
                || ($codeJabatan == 5 && (($proker->proposal->status_flow == 0 || $proker->proposal->status_flow == null || $proker->proposal->status_flow == "") || $proker->proposal->status_flow >= 2 ) && $proker->organisasi->nama_organisasi == $orguser && $proker->proposal->status_flow != 1)
                || ($codeJabatan == 2 && ($proker->organisasi->nama_organisasi == 'BEM' || stripos($proker->organisasi->nama_organisasi, 'UKM') !== false) && 'Kampus' == $orguser && $proker->proposal->status_flow >= 5 && $proker->proposal->status_flow != 1)
                || ($codeJabatan == 8 && (stripos($proker->organisasi->nama_organisasi, 'HIMA') !== false) && ('Kampus' == $orguser || $proker->organisasi->nama_organisasi == $orguser ) && $proker->proposal->status_flow >= 5 && $proker->proposal->status_flow != 1)
                || ($codeJabatan == 3 && (stripos($proker->organisasi->nama_organisasi, 'HIMA') !== false) && ('Kampus' == $orguser || $proker->organisasi->nama_organisasi == $orguser ) && $proker->proposal->status_flow >= 6 && $proker->proposal->status_flow != 1)
                || ($codeJabatan == 2 && (stripos($proker->organisasi->nama_organisasi, 'HIMA') !== false) && 'Kampus' == $orguser && $proker->proposal->status_flow >= 7 && $proker->proposal->status_flow != 1)
                || ($codeJabatan == 1 && 'Kampus' == $orguser && ($proker->proposal->status_flow == 8 || $proker->proposal->status_flow == 9)))
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $proker->nama_proker }}</td>
                    <td>{{ $proker->organisasi->nama_organisasi }}</td>
                    <td>
                      @if ($proker->proposal)
                          <a href="{{ asset('files/' . $proker->proposal->file_proposal) }}" target="_blank">
                              {{ $proker->proposal->file_proposal }}</a>
                      @else
                          Tidak ada file proposal
                      @endif
                  </td>
                  <td>
                    @if ($proker->proposal && $proker->proposal->pengesahan)
                                            <a href="{{ asset('pengesahan/' . $proker->proposal->pengesahan) }}" target="_blank">
                                                {{ $proker->proposal->pengesahan }}
                                            </a>
                                        @else
                                            Tidak ada file
                                        @endif
                  </td>
                  <td>
                    @if ($proker->proposal)
                        {{ $proker->proposal->catatan }}
                    @else
                        Tidak Ada Catatan
                    @endif
                  </td>                
                  <td>
                    @if($proker->proposal)
                        {{ $proker->proposal->status }}
                    @else
                        Pending
                    @endif
                  </td>  
                  <td>
                    @if($proker->proposal->status_flow != 9 && (
                    ($codeJabatan == 5 && $orguser == 'BEM' && $proker->proposal->status_flow == 2 && $proker->organisasi->nama_organisasi != 'BEM' ) ||
                    ($codeJabatan == 5 && $orguser == 'BPM' && $proker->proposal->status_flow == 3 ) ||
                    ($codeJabatan == 5 && ($proker->proposal->status_flow == 0 || $proker->proposal->status_flow == null)) ||
                    ($codeJabatan == 4 && $proker->proposal->status_flow == 4 ) ||
                    ($codeJabatan == 8 && $proker->proposal->status_flow == 5 ) ||
                    ($codeJabatan == 3 && $proker->proposal->status_flow == 6 ) ||
                    ($codeJabatan == 2 && (($proker->proposal->status_flow == 7 && (stripos($proker->organisasi->nama_organisasi, 'HIMA') !== false)) || $proker->proposal->status_flow == 5))) ||
                    ($codeJabatan == 1 && $proker->proposal->status_flow == 8 )
                    )
                    <button type="button" class="btn btn-warning" onclick="openRevisiModal({{ $proker->proposal->id }})">Revisi</button>
                    @if($codeJabatan == 1)
                      <form action="{{ route('createSignaturePdf') }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="proposal_id" value="{{ $proker->proposal->id }}">
                        <input type="hidden" name="proker" value="{{ $proker->nama_proker }}">
                        <input type="hidden" name="organisasi" value="{{ $proker->organisasi->nama_organisasi }}">
                        <button type="submit" class="btn btn-success">Diterima</button>
                      </form>
                    @else
                    <a href="{{ route('proposals.approve', ['proposalId' => $proker->proposal->id]) }}"><button type="submit" class="btn btn-success">Diterima</button></a>
                    @endif
                    @else
                    @if($proker->proposal->status_flow != 9)
                    <span class="badge badge-warning">Diproses</span>
                    @else
                    <span class="badge badge-success">Selesai</span>
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

  <!-- Modal untuk Revisi Proposal -->
<div class="modal fade" id="revisiModal" tabindex="-1" role="dialog" aria-labelledby="revisiModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="revisiModalLabel">Catatan Revisi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('proposals.revisi') }}" method="POST">
          @csrf
          <input type="hidden" name="proposal_id" id="proposalId" value="">
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
  function openRevisiModal(proposalId) {
    // Mengatur nilai proposal_id ke dalam input tersembunyi di modal
    document.getElementById('proposalId').value = proposalId;
  
    // Menampilkan modal
    var revisiModal = new bootstrap.Modal(document.getElementById('revisiModal'));
    revisiModal.show();
  }
  </script>
  
  
@endsection