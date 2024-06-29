@extends('layouts.admin-layout')

@section('kontainer')

@if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
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
<form action="/organisasi/{{ $id }}"  method="POST">
    @method ('put')
    @csrf
    <h1>Edit Organisasi</h1>
    <div class="form-group">
      <label for="exampleInputEmail1">Nama Organisasi</label>
      <input type="text" name="nama_organisasi" class="form-control @error('nama_organisasi') is-invalid @enderror" id="nama_organisasi" value="{{ old('nama_organisasi', $organisasi->nama_organisasi) }}">
                        @error('nama_organisasi')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Periode</label>
        <input type="text" name="periode" class="form-control @error('periode') is-invalid @enderror" id="periode" value="{{ old('periode', $organisasi->periode) }}">
                        @error('periode')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
    </div>
    <button type="button" class="btn btn-secondary" onclick="window.location.href='/organisasi'">Batal</button>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>

@endsection