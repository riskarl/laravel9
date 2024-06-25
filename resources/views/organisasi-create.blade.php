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
<form action="/organisasi" method="POST">
    @csrf
    <h1>Tambah Organisasi</h1>
    <div class="form-group">
      <label for="exampleInputEmail1">Nama Organisasi</label>
      <input type="text" name="nama_organisasi" class="form-control @error('nama_organisasi') is-invalid @enderror" id="nama_organisasi" placeholder="Masukkan nama organisasi" value="{{ old('nama_organisasi') }}">
                        @error('nama_organisasi')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Periode</label>
        <input type="number" name="periode" class="form-control @error('periode') is-invalid @enderror" id="periode" placeholder="Masukkan periode" value="{{ old('periode') }}">
                        @error('periode')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>

@endsection