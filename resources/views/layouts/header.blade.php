<div class="container-fluid mb-3">
<!--begin::Row-->
<div class="row">
    <div class="col-sm-6">
        <h1 class="mb-0 fs-3">日記@yield('subtitle')</h1>
    </div>
</div>
<!--end::Row-->
</div>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container">
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a @class([
            'nav-link',
            'active' => request()->routeIs('diary.index')
            ]) 
            href="{{ route('diary.index') }}">
            一覧
            </a>
        </li>
        <li class="nav-item">
            <a @class([
                'nav-link',
                'active' => request()->routeIs('diary.create')
                ]) 
            href="{{ route('diary.create') }}">
            追加
            </a>
        </li>
        @if(request()->routeIs('diary.edit'))
            <li class="nav-item">
                <a class="nav-link active" href="">
                編集
                </a>
            </li>
        @endif
    </ul>
  </div>
</nav>
