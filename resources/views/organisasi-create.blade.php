@extends('layouts.admin-layout')

@section('kontainer')

<form action="/organisasi" method="POST">
    @csrf
    <h1>Tambah Organisasi</h1>
    <div class="form-group">
      <label for="exampleInputEmail1">Nama Organisasi</label>
      <input type="text" name="nama_organisasi" class="form-control" id="nama_organisasi" aria-describedby="emailHelp"
        placeholder="Masukkan Nama Organisasi">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Nama Pembina</label>
        <input type="text" name="nama_pembina" class="form-control" id="nama_pembina" aria-describedby="emailHelp"
          placeholder="Masukkan Nama Pembina">
    </div>
    <div class="form-group">
      <label for="exampleInputPassword1">Nama Ketua</label>
      <input type="text" name="nama_ketua" class="form-control" id="nama_ketua" placeholder="Masukkan Nama Ketua">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Periode</label>
        <input type="number" name="periode" class="form-control" id="periode" aria-describedby="emailHelp"
          placeholder="Masukkan Periode">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>

@endsection