@extends('layouts.admin-layout')

@section('kontainer')

<form action="/usermanajemen/{{ $user->id }}" method="POST" enctype="multipart/form-data">
    @method('PUT')
    @csrf
    <h1>Edit Akun</h1>
    <div class="form-group">
        <label for="exampleInputEmail1">Nama</label>
        <input type="text" value="{{ old('name', $user->name) }}" name="name" class="form-control" id="name" aria-describedby="emailHelp" placeholder="Masukkan Nama">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Username</label>
        <input type="text" value="{{ old('username', $user->username) }}" name="username" class="form-control" id="username" aria-describedby="emailHelp" placeholder="Masukkan Username">
    </div>
    <div class="form-group">
        <label for="exampleFormControlSelect1">Pilih Organisasi</label>
        <select class="form-control" name="organization" id="organization">
            <option {{ old('organization', $user->organization) == 'Kampus' ? 'selected' : '' }}>Kampus</option>
            @foreach($organisasi as $org)
            <option value="{{ $org->nama_organisasi }}" {{ $org->nama_organisasi == old('organization', $user->organization) ? 'selected' : '' }}>
                {{ $org->nama_organisasi }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="jabatan">Pilih Jabatan</label>
        <select class="form-control" name="jabatan_id" id="jabatan">
            @foreach($jabatans as $jabatan)
            <option value="{{ $jabatan->jabatan_id }}" {{ old('jabatan_id', $user->jabatan_id) == $jabatan->jabatan_id ? 'selected' : '' }}>
                {{ $jabatan->jabatan }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="exampleFormControlSelect1">Pilih Role</label>
        <select class="form-control" name="role" id="role">
            <option value="1" {{ old('role', $user->role) == 1 ? 'selected' : '' }}>1</option>
            <option value="2" {{ old('role', $user->role) == 2 ? 'selected' : '' }}>2</option>
            <option value="3" {{ old('role', $user->role) == 3 ? 'selected' : '' }}>3</option>
            <option value="4" {{ old('role', $user->role) == 4 ? 'selected' : '' }}>4</option>
        </select>
    </div>
    <div class="form-group">
        <label for="ttd">Upload Tanda Tangan</label>
        <input type="file" name="ttd" class="form-control-file" id="ttd">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

@endsection
