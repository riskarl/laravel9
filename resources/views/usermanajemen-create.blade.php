@extends('layouts.admin-layout')

@section('kontainer')

<form action="/usermanajemen" method="POST" enctype="multipart/form-data">
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
        @foreach($organisasi as $org)
        <option value="{{ $org->nama_organisasi }}">{{ $org->nama_organisasi }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label for="jabatan">Pilih Jabatan</label>
      <select class="form-control" name="jabatan_id" id="jabatan">
          @foreach($jabatans as $jabatan)
              <option value="{{ $jabatan->jabatan_id }}">{{ $jabatan->jabatan }}</option>
          @endforeach
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
    <div class="form-group">
      <label for="ttd">Upload Tanda Tangan</label>
      <input type="file" name="ttd" class="form-control-file" id="ttd">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>

@endsection
