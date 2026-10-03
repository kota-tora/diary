@extends('layouts.app')

@section('subtitle', $is_update ? '編集' : '追加')

@section('content')
<div class="col-md-12">
  <div class="card card-primary card-outline mb-4">
    <div class="card-header">
      <div class="card-title">日記を{{ $is_update ? '編集' : '追加' }}</div>
    </div>
    <form action="{{ $is_update ? route('diary.update', $diary) : route('diary.store') }}" method="post" enctype="multipart/form-data">
      @if($is_update)
        @method('PUT')
      @endif
      <div class="card-body">
        <div class="mb-3">
          <label for="content" class="form-label">本文</label>
          <input type="text" name="content" class="form-control" id="content" value="{{ old('content', !empty($diary) ? $diary->content : '') }}">
          @error('content')
              <div class="alert alert-danger mt-1">
                {{ $message }}
              </div>
          @enderror
        </div>
        <div class="row mb-2">
          <label>画像を追加</label>
        </div>
        <div class="input-group mb-3 w-50">
          <input id="image" name="image" type="file" class="form-control">
          <label class="input-group-text" for="image">Upload</label>
        </div>
        <div class="card" style="width: 18rem;">
          <img id="preview" src="{{ !empty($diary?->img_name) ? asset('storage/images/diaries/' . $diary->img_name) : asset('images/noimage.jpg') }}" class="card-img-top" alt="日記画像">
        </div>
        @error('image')
          <div class="alert alert-danger mt-1">
            {{ $message }}
          </div>
        @enderror
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ $is_update ? '編集' : '追加' }}</button>
      </div>
      @csrf
    </form>
  </div>
</div>
@stop

@push('js')
  <script>
      document.getElementById('image').addEventListener('change', function (e) {
        const preview = document.getElementById('preview');
        const image = e.target.files[0];
        if (image) {
          const reader = new FileReader();
          reader.onload = function (e) {
            preview.setAttribute('src', e.target.result)
          }
          reader.readAsDataURL(image)
        }
      });
    </script>
@endpush

