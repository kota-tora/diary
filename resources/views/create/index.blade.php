@extends('layouts.app')

@section('subtitle', '追加')

@section('content')
<div class="col-md-12">
  <div class="card card-primary card-outline mb-4">
    <div class="card-header">
      <div class="card-title">日記を追加</div>
    </div>
    <form>
      <div class="card-body">
        <div class="mb-3">
          <label for="content" class="form-label">本文</label>
          <input type="text" name="content" class="form-control" id="content" value="{{ old('content') }}">
        </div>
        <div class="row mb-2">
          <label>画像を追加</label>
        </div>
        <div class="input-group mb-3 w-50">
          <input type="file" class="form-control" id="inputGroupFile02">
          <label class="input-group-text" for="inputGroupFile02">Upload</label>
        </div>
        
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">追加</button>
      </div>
    </form>
  </div>
</div>
@stop

@push('css')
    <!-- {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}} -->
@endpush

@push('js')
@endpush

