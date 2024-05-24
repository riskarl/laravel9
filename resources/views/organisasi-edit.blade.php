@extends('layouts.admin-layout')

@section('kontainer')

<form action="/organisasi/{{ $id }}"  method="POST">
    @method ('put')
    @csrf
    <h1>Edit Organisasi</h1>
    <div class="form-group">
      <label for="exampleInputEmail1">Nama Organisasi</label>
      <input type="text" value="{{ old('nama_organisasi', $organisasi->nama_organisasi ) }}" name="nama_organisasi" class="form-control" id="nama_organisasi" aria-describedby="emailHelp"
        placeholder="Masukkan Nama Organisasi">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Nama Pembina</label>
        <input type="text" value="{{ old('nama_pembina', $organisasi->nama_pembina ) }}" name="nama_pembina" class="form-control" id="nama_pembina" aria-describedby="emailHelp"
          placeholder="Masukkan Nama Pembina">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Nama Ketua</label>
        <input type="text" value="{{ old('nama_ketua', $organisasi->nama_ketua ) }}" name="nama_ketua" class="form-control" id="nama_ketua" aria-describedby="emailHelp"
          placeholder="Masukkan Nama Ketua">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Periode</label>
        <input type="text" value="{{ old('periode', $organisasi->periode ) }} "name="periode" class="form-control" id="periode" aria-describedby="emailHelp"
          placeholder="Masukkan Periode">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>

@endsection