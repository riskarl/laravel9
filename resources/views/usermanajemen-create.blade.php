@extends('layouts.admin-layout')

@section('kontainer')

@if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif
            @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

<form action="/usermanajemen" method="POST" enctype="multipart/form-data">
    @csrf
    <h1>Tambah Akun</h1>
    <div class="form-group">
      <label for="exampleInputEmail1">Nama</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Masukkan Nama" value="{{ old('name') }}">
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Username</label>
          <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" id="username" placeholder="Masukkan Username" value="{{ old('username') }}">
          @error('username')
          <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
          </span>
          @enderror
    </div>
    <div class="form-group">
      <label for="exampleInputEmail1">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Masukkan Email" value="{{ old('email') }}">
        @error('email')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
  </div>
    <div class="form-group">
      <label for="exampleInputPassword1">Password</label>
      <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Masukkan Password" value="{{ old('password') }}">
        @error('password')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
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
      <label for="exampleFormControlSelect1">Jenis ID</label>
      <select class="form-control" name="code_id" id="code_id">
        <option>NIM</option> 
        <option>NIDN</option>
        <option>NIP</option>
      </select>
    </div>
    <div class="form-group">
      <label for="exampleInputPassword1">Nomer ID</label>
      <input type="text" name="number_id" class="form-control @error('number_id') is-invalid @enderror" id="number_id" placeholder="Masukkan Nomor ID" value="{{ old('number_id') }}">
      @error('number_id')
          <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
          </span>
      @enderror
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
      <input type="file" name="ttd" class="form-control-file @error('ttd') is-invalid @enderror" id="ttd">
        @error('ttd')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <button type="button" class="btn btn-secondary" onclick="window.location.href='/usermanajemen'">Batal</button>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>

@endsection
