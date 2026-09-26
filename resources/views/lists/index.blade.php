@extends('layouts.app')

@section('subtitle', '一覧')

@section('content')

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

@push('css')
    <!-- {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}} -->
@endpush

@push('js')
@endpush
