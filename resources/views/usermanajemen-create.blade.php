@extends('layouts.admin-layout')

@section('kontainer')

<form action="/usermanajemen" method="POST">
    @csrf
    <h1>Tambah Akun</h1>
    <div class="form-group">
      <label for="exampleInputEmail1">Nama</label>
      <input type="text" name="name" class="form-control" id="name" aria-describedby="emailHelp"
        placeholder="Masukkan Nama">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Username</label>
        <input type="text" name="username" class="form-control" id="username" aria-describedby="emailHelp"
          placeholder="Masukkan Username">
    </div>
    <div class="form-group">
      <label for="exampleInputPassword1">Password</label>
      <input type="password" name="password" class="form-control" id="password" placeholder="Masukkan Password">
    </div>
    <div class="form-group">
      <label for="exampleFormControlSelect1">Pilih Organisasi</label>
      <select class="form-control" name="organization" id="organization">
        <option>Kampus</option> 
        <option>Organisasi Kemahasiswaan</option>
        <option>UKM</option>
      </select>
    </div>
    <div class="form-group">
      <label for="exampleFormControlSelect1">Pilih Jabatan</label>
      <select class="form-control" name="jabatan" id="jabatan">
        <option>Ketua BEM</option> 
        <option>Sekretaris BEM</option>
        <option>Pembina BEM</option>
        <option>Ketua BPM</option>
        <option>Bendahara BPM</option>
        <option>Wakil Direktur III Bidang Kemahasiswaan</option>
        <option>Koordinator Subbagian Akademik dan Kemahasiswaan</option>
        <option>Ketua HIMA</option>
        <option>Sekretaris HIMA</option>
        <option>Ketua Jurusan / Prodi</option>
        <option>Pembina HIMA</option>
        <option>Ketua UKM</option>
        <option>Sekretaris UKM</option>
        <option>Pembina UKM</option>
      </select>
    </div>
    <div class="form-group">
      <label for="exampleFormControlSelect1">Pilih Role</label>
      <select class="form-control" name="role" id="role">
        <option>1</option> 
        <option>2</option>
        <option>3</option>
        <option>4</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>

@endsection