@extends('layouts.admin-layout')

@section('kontainer')

<form action="/usermanajemen/{{ $id }}"  method="POST">
    @method ('put')
    @csrf
    <h1>Edit Akun</h1>
    <div class="form-group">
      <label for="exampleInputEmail1">Nama</label>
      <input type="text" value="{{ old('name', $user->name ) }}" name="name" class="form-control" id="name" aria-describedby="emailHelp"
        placeholder="Masukkan Nama">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Username</label>
        <input type="text" value="{{ old('username', $user->username ) }}" name="username" class="form-control" id="username" aria-describedby="emailHelp"
          placeholder="Masukkan Username">
    </div>
    {{-- <div class="form-group">
      <label for="exampleInputPassword1">Password</label>
      <input type="text" value="{{ old('password', $user->password ) }}" name="password" class="form-control" id="password" placeholder="Masukkan Password">
    </div> --}}
    <div class="form-group">
      <label for="exampleFormControlSelect1">Pilih Organisasi</label>
      <select class="form-control" name="organization" id="organization">
        <option {{ old('organization', $user->organization) == 'Kampus' ? 'selected' : '' }}>Kampus</option>
        <option {{ old('organization', $user->organization) == 'Organisasi Kemahasiswaan' ? 'selected' : '' }}>Organisasi Kemahasiswaan</option>
        <option {{ old('organization', $user->organization) == 'UKM' ? 'selected' : '' }}>UKM</option>
      </select>
    </div> 
    <div class="form-group">
      <label for="exampleFormControlSelect1">Pilih Jabatan</label>
      <select class="form-control" name="jabatan" id="jabatan">
        <option {{ old('jabatan', $user->jabatan) == 'Ketua BEM' ? 'selected' : '' }}>Ketua BEM</option> 
        <option {{ old('jabatan', $user->jabatan) == 'Sekretaris BEM' ? 'selected' : '' }}>Sekretaris BEM</option>
        <option {{ old('jabatan', $user->jabatan) == 'Pembina BEM' ? 'selected' : '' }}>Pembina BEM</option>
        <option {{ old('jabatan', $user->jabatan) == 'Ketua BPM' ? 'selected' : '' }}>Ketua BPM</option>
        <option {{ old('jabatan', $user->jabatan) == 'Wakil Direktur III Bidang Kemahasiswaan' ? 'selected' : '' }}>Wakil Direktur III Bidang Kemahasiswaan</option>
        <option {{ old('jabatan', $user->jabatan) == 'Koordinator Subbagian Akademik dan Kemahasiswaan' ? 'selected' : '' }}>Koordinator Subbagian Akademik dan Kemahasiswaan</option>
        <option {{ old('jabatan', $user->jabatan) == 'Ketua HIMA' ? 'selected' : '' }}>Ketua HIMA</option>
        <option {{ old('jabatan', $user->jabatan) == 'Sekretaris HIMA' ? 'selected' : '' }}>Sekretaris HIMA</option>
        <option {{ old('jabatan', $user->jabatan) == 'Ketua Jurusan / Prodi' ? 'selected' : '' }}>Ketua Jurusan / Prodi</option>
        <option {{ old('jabatan', $user->jabatan) == 'Pembina HIMA' ? 'selected' : '' }}>Pembina HIMA</option>
        <option {{ old('jabatan', $user->jabatan) == 'Ketua UKM' ? 'selected' : '' }}>Ketua UKM</option>
        <option {{ old('jabatan', $user->jabatan) == 'Sekretaris UKM' ? 'selected' : '' }}>Sekretaris UKM</option>
        <option {{ old('jabatan', $user->jabatan) == 'Pembina UKM' ? 'selected' : '' }}>Pembina UKM</option>
      </select>
    </div>    
    {{-- <div class="form-group">
        <label for="exampleInputEmail1">Jabatan</label>
        <input type="text" value="{{ old('jabatan', $user->jabatan ) }} "name="jabatan" class="form-control" id="jabatan" aria-describedby="emailHelp"
          placeholder="Masukkan Jabatan">
    </div> --}}
    <div class="form-group">
      <label for="exampleFormControlSelect1">Pilih Role</label>
      <select class="form-control" name="role" id="role">
        <option value="1" {{ old('role', $user->role) == 1 ? 'selected' : '' }}>1</option>
        <option value="2" {{ old('role', $user->role) == 2 ? 'selected' : '' }}>2</option>
        <option value="3" {{ old('role', $user->role) == 3 ? 'selected' : '' }}>3</option>
        <option value="4" {{ old('role', $user->role) == 4 ? 'selected' : '' }}>4</option>
      </select>
    </div>    
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>

@endsection