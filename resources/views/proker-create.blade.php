@extends('layouts.organisasi-layout')

@section('content')

<form action="/proker" method="POST" enctype="multipart/form-data">
    @csrf
    <h1>Tambah Program Kerja</h1>
    <div class="form-group">
      <label for="exampleInputEmail1">Nama Organisasi</label>
      <select name="nama_organisasi" id="selectOrganisasi" required>
        <option value="">Pilih Organisasi</option>
    
        @foreach($organisasi as $org) 
            @if($org->nama_organisasi == $orguser)
                <option value="{{ $org->id }}">{{ $org->nama_organisasi }}</option>
            @endif
        @endforeach
    </select>
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Nama Program Kerja</label>
        <input type="text" name="nama_proker" class="form-control" id="nama_proker" aria-describedby="emailHelp" 
          placeholder="Masukkan Nama Program Kerja" required>
    </div>
    <div class="form-group">
      <label for="exampleInputPassword1">Nama Ketua Pelaksana</label>
      <input type="text" name="nama_ketupel" class="form-control" id="nama_ketupel" placeholder="Masukkan Nama Ketua Pelaksana" required>
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Tanggal</label>
        <input type="date" name="tanggal" class="form-control" id="tanggal" aria-describedby="emailHelp"
          placeholder="Masukkan Tanggal" required>
    </div>
    <div class="form-group">
      <label for="exampleInputPassword1">Tempat</label>
      <input type="text" name="tempat" class="form-control" id="tempat" placeholder="Masukkan Tempat" required>
    </div>
    <div class="form-group">
      <label for="exampleInputPassword1">Dana yang diajukan</label>
      <input type="number" name="dana_diajukan" class="form-control" id="dana_diajukan" placeholder="Masukkan Dana yang diajukan" required>
    </div>
    <div class="form-group">
      <label for="ttd">Upload Tanda Tangan Ketua Pelaksana</label>
      <input type="file" name="ttd_ketupel" class="form-control-file" id="ttd_ketupel">
    </div>
    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
  </form>

  <script>
    $(document).ready(function()
  {
    $('#selectOrganisasi').select2();
  });
  </script>

@endsection