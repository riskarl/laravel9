@extends('layouts.pengecek-layout')

@section('konten')

<div class="row">
    <div class="col-lg-12 mb-4">
      <!-- Simple Tables -->
      <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Pengecekan Proposal</h6>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th>ID</th>
                <th>Nama Program Kerja</th>
                <th>Nama Organisasi</th>
                <th>File</th>
                <th>Catatan</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($listproker as $proker)
              @if($proker->proposal)
                @if(($codeJabatan == 6 && ($proker->proposal->status_flow == 0 || $proker->proposal->status_flow == null || $proker->proposal->status_flow == "") && $proker->organisasi->nama_organisasi == $orguser)
                || ($codeJabatan == 5 && $orguser == 'BEM' && $proker->organisasi->nama_organisasi != 'BEM' && $proker->proposal->status_flow == 2 && $proker->proposal->status_flow != 0)
                || ($codeJabatan == 5 && $orguser == 'BPM' && $proker->proposal->status_flow == 3 && $proker->proposal->status_flow != 0)
                || ($codeJabatan == 4 && $proker->organisasi->nama_organisasi == $orguser && $proker->proposal->status_flow == 4 && $proker->proposal->status_flow != 0)
                || ($codeJabatan == 5 && ($proker->proposal->status_flow == 0 || $proker->proposal->status_flow == null || $proker->proposal->status_flow == "") && $proker->organisasi->nama_organisasi == $orguser && $proker->proposal->status_flow != 0)
                || ($codeJabatan == 2 && ($proker->organisasi->nama_organisasi == 'BEM' || strpos($proker->organisasi->nama_organisasi, 'UKM') !== false) && 'Kampus' == $orguser && $proker->proposal->status_flow == 5 && $proker->proposal->status_flow != 0)
                || ($codeJabatan == 8 && (strpos($proker->organisasi->nama_organisasi, 'HIMA') !== false) && 'Kampus' == $orguser && $proker->proposal->status_flow == 5 && $proker->proposal->status_flow != 0)
                || ($codeJabatan == 3 && (strpos($proker->organisasi->nama_organisasi, 'HIMA') !== false) && 'Kampus' == $orguser && $proker->proposal->status_flow == 6 && $proker->proposal->status_flow != 0)
                || ($codeJabatan == 2 && (strpos($proker->organisasi->nama_organisasi, 'HIMA') !== false) && 'Kampus' == $orguser && $proker->proposal->status_flow == 7 && $proker->proposal->status_flow != 0)
                || ($codeJabatan == 1 && 'Kampus' == $orguser && $proker->proposal->status_flow == 8))
                <tr>
                    <td>{{ $proker->id }}</td>
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
                    <button type="button" class="btn btn-warning" onclick="openRevisiModal({{ $proker->proposal->id }})">Revisi</button>
                    <a href="{{ route('proposals.approve', ['proposalId' => $proker->proposal->id]) }}"><button type="submit" class="btn btn-success">Diterima</button></a>
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