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
  <div class="card-body">
    @if ($rows->isEmpty())
      <div class="h3">データが登録されてません</div>
    @else
      <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
          <thead>
            <tr>
              <th class="text-center col-1">ID</th>
              <th class="text-center col-6">内容</th>
              <th class="text-center col-2">画像</th>
              <th class="text-center col-2">作成日</th>
              <th class="text-center col-1">操作</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $row)
              <tr>
                  <td class="text-center">{{ $row->diary_id }}</td>
                  <td class="text-center">{{ $row->content }}</td>
                  <td class="text-center">
                    <img src="{{ asset('storage/images/diaries/' . $row->img_name) }}" alt="日記画像">
                  </td>
                  <td>{{ $row->created_at->format('Y-m-d H:i:s') }}</td>
                  <td class="text-center">
                    <div class="mb-4">
                      <a>
                        <button type="button" class="btn btn-success">編集</button>
                      </a>
                    </div>
                    <div>
                      <a>
                        <button type="button" class="btn btn-danger">削除</button>
                      </a>
                    </div>
                  </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="card-footer">
        {{ $rows->links() }}
      </div>
    @endif
</div>

@stop

@push('style')
@endpush

@push('js')
@endpush
