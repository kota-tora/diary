<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>1行日記@yield('title')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    
    <div class="container">
        <div class="header mt-5 mb-5">
            @include('layouts.header')
        </div>
        @yield('content')
    </div>
    
    @push('js')
        <script></script>
    @endpush
    @push('css')
        <style type="text/css"></style>
    @endpush
</body>
</html>
