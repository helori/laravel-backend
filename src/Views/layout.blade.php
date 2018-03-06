<!DOCTYPE html>
<html>
<head>

    <title>Backend</title>

    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="white" />

    <link rel="stylesheet" type="text/css" href="{{ mix('css/backend.css') }}">

</head>

<body>

<!-- - - - - - - - - - - - - - - - - - - - - - - - - -->
<!-- Content -->
<!-- - - - - - - - - - - - - - - - - - - - - - - - - -->
<main id="backend">
    <div class="container">
        @yield('content')
    </div>
</main>

<!-- - - - - - - - - - - - - - - - - - - - - - - - - -->
<!-- Scripts -->
<!-- - - - - - - - - - - - - - - - - - - - - - - - - -->
<script src="{{ mix('js/backend.js') }}"></script>

</body>
