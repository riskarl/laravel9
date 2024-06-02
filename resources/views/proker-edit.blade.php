@extends('layouts.organisasi-layout')

@section('content')

<form action="/proker/{{ $proker->id }}"  method="POST" enctype="multipart/form-data">
    @method ('put')
    @csrf
    <h1>Edit Program Kerja</h1>
    <div class="form-group">
      <label for="nama_organisasi">Nama Organisasi</label>
      <select name="nama_organisasi" id="nama_organisasi" class="form-control" required>
          @foreach($organisasi as $org)
            @if($org->nama_organisasi == $orguser)
              <option value="{{ $org->id }}" {{ $org->id == old('nama_organisasi', $proker->organisasi->id) ? 'selected' : '' }}>
                {{ $org->nama_organisasi }}
              </option>
            @endif
          @endforeach
      </select>
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Nama Program Kerja</label>
        <input type="text" value="{{ old('nama_proker', $proker->nama_proker ) }}" name="nama_proker" class="form-control" id="nama_proker" aria-describedby="emailHelp"
          placeholder="Masukkan Nama Program Kerja" required>
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Nama Ketua Pelaksana</label>
        <input type="text" value="{{ old('nama_ketupel', $proker->nama_ketupel ) }}" name="nama_ketupel" class="form-control" id="nama_ketupel" aria-describedby="emailHelp"
          placeholder="Masukkan Nama Ketua Pelaksana" required>
    </div>
    <div class="form-group">
      <label for="exampleInputEmail1">NIM Ketua Pelaksana</label>
      <input type="text" value="{{ old('nim_ketupel', $proker->nim_ketupel ) }}" name="nim_ketupel" class="form-control" id="nim_ketupel" aria-describedby="emailHelp"
        placeholder="Masukkan NIM Ketua Pelaksana" required>
  </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Tanggal</label>
        <input type="date" value="{{ old('tanggal', $proker->tanggal)}}"name="tanggal" class="form-control" id="tanggal" aria-describedby="emailHelp"
          placeholder="Masukkan Tanggal" required>
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Tempat</label>
        <input type="text" value="{{ old('tempat', $proker->tempat ) }} "name="tempat" class="form-control" id="tempat" aria-describedby="emailHelp"
          placeholder="Masukkan Tempat" required>
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Dana yang diajukan</label>
        <input type="number" value="{{ old('dana_diajukan', $proker->dana_diajukan)}}"name="dana_diajukan" class="form-control" id="dana_diajukan" aria-describedby="emailHelp"
          placeholder="Masukkan Jumlah Dana yang diajukan" required>
    </div>
    <div class="form-group">
      <label for="ttd">Upload Tanda Tangan Ketua Pelaksana</label>
      <input type="file" name="ttd_ketupel" class="form-control-file" id="ttd_ketupel">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>

@endsection
