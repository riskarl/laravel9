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

<form action="/usermanajemen/{{ $user->id }}" method="POST" enctype="multipart/form-data">
    @method('PUT')
    @csrf
    <h1>Edit Akun</h1>
    <div class="form-group">
        <label for="exampleInputEmail1">Nama</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $user->name) }}">
        @error('name')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Username</label>
        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" id="username" value="{{ old('username', $user->username) }}">
        @error('username')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email', $user->email) }}">
        @error('email')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="form-group">
        <label for="exampleFormControlSelect1">Pilih Organisasi</label>
        <select class="form-control @error('organization') is-invalid @enderror" name="organization" id="organization">
            <option {{ old('organization', $user->organization) == 'Kampus' ? 'selected' : '' }}>Kampus</option>
            @foreach($organisasi as $org)
            <option value="{{ $org->nama_organisasi }}" {{ $org->nama_organisasi == old('organization', $user->organization) ? 'selected' : '' }}>
                {{ $org->nama_organisasi }}
            </option>
            @endforeach
        </select>
        @error('organization')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
    </div>
    <div class="form-group">
        <label for="jabatan">Pilih Jabatan</label>
        <select class="form-control @error('jabatan_id') is-invalid @enderror" name="jabatan_id" id="jabatan_id">
            @foreach($jabatans as $jabatan)
            <option value="{{ $jabatan->jabatan_id }}" {{ old('jabatan_id', $user->jabatan_id) == $jabatan->jabatan_id ? 'selected' : '' }}>
                {{ $jabatan->jabatan }}
            </option>
            @endforeach
        </select>
        @error('jabatan_id')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
    </div>
    <div class="form-group">
        <label for="exampleFormControlSelect1">Jenis ID</label>
        <select class="form-control @error('code_id') is-invalid @enderror" name="code_id" id="code_id">
            <option value="NIM" {{ old('code_id', $user->code_id) == 'NIM' ? 'selected' : '' }}>NIM</option>
            <option value="NIDN" {{ old('code_id', $user->code_id) == 'NIDN' ? 'selected' : '' }}>NIDN</option>
            <option value="NIP" {{ old('code_id', $user->code_id) == 'NIP' ? 'selected' : '' }}>NIP</option>
        </select>
        @error('code_id')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Nomer ID</label>
        <input type="text" name="number_id" class="form-control @error('number_id') is-invalid @enderror" id="number_id" value="{{ old('number_id', $user->number_id) }}">
        @error('number_id')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="form-group">
        <label for="exampleFormControlSelect1">Pilih Role</label>
        <select class="form-control @error('role') is-invalid @enderror" name="role" id="role">
            <option value="1" {{ old('role', $user->role) == 1 ? 'selected' : '' }}>1</option>
            <option value="2" {{ old('role', $user->role) == 2 ? 'selected' : '' }}>2</option>
            <option value="3" {{ old('role', $user->role) == 3 ? 'selected' : '' }}>3</option>
            <option value="4" {{ old('role', $user->role) == 4 ? 'selected' : '' }}>4</option>
        </select>
        @error('role')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
    </div>
    <div class="form-group">
        <label for="ttd">Upload Tanda Tangan</label>
        <input type="file" name="ttd" class="form-control @error('ttd') is-invalid @enderror" id="ttd" value="{{ old('ttd', $user->ttd) }}">
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
