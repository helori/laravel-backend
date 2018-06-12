<!DOCTYPE html>
<html>
<head>

    <title>{{ env('APP_NAME') }} Backend</title>

    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="white" />

    <link rel="stylesheet" type="text/css" href="{{ mix('css/backend.css') }}">

</head>

<body>

    <div id="vue-app">

        @include('laravel-backend::menu')

        <!-- - - - - - - - - - - - - - - - - - - - - - - - - -->
        <!-- Content -->
        <!-- - - - - - - - - - - - - - - - - - - - - - - - - -->
        <main id="backend" class="py-4">
            <div class="container">
                <router-view></router-view>
            </div>
        </main>

    </div>
    
    <!-- - - - - - - - - - - - - - - - - - - - - - - - - -->
    <!-- Scripts -->
    <!-- - - - - - - - - - - - - - - - - - - - - - - - - -->
    <script>
        window.editor_css = "{{ mix('css/editor.css') }}";
        window.editor_options = {!! json_encode(config('laravel-backend.editorOptions', [])) !!};
    </script>

    <script src="{{ mix('js/backend.js') }}"></script>

</body>
