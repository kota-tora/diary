@extends('layouts.app')

@section('subtitle', '一覧')

@section('content')

<!-- 追加トースト -->
@if(session('success') || session('error'))
  <div id="storeToast"
    @class([
      'toast',
      'align-items-center',
      'border-0',
      'position-fixed',
      'top-0',
      'end-0',
      'm-3',
      'z-3',
      'text-bg-success' => session('success'), 
      'text-bg-danger' => session('error')
    ])
   role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        {{ session('success') ?? session('error') }}
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
@endif
<!-- 追加トースト -->

<div class="card card-secondary">
  <div class="card-header">
    <div class="card-title">Title</div>
  </div>
  <div class="card-body">
    
    <div class="table-responsive">
      <table class="table table-striped align-middle mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @for($i = 0; $i < 5; $i++)
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
          @endfor
        </tbody>
      </table>
  </div>
  <div class="card-footer">Footer</div>
</div>

@stop

@push('style')
@endpush

@push('js')
@endpush
