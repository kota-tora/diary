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
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="{{ route('diary.index') }}">一覧</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('diary.create') }}">追加</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">編集</a>
        </li>
    </ul>
  </div>
</nav>
