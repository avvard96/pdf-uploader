<!doctype html>
<html>
<head>
    @include('includes.head')
</head>
<body>
<div class="container">

    <div id="main" class="row">
        @yield('content')
    </div>

    <footer class="row">
        @include('includes.footer')
    </footer>
    @include('includes.scripts')
</div>
</body>
</html>