<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png">
    <title>Harmelo Music | Let your Music be heard!</title>
	<link rel="canonical" href="https://www.wrappixel.com/templates/adminwrap/" />
    <link rel="stylesheet" href="{{ asset('assets/vikinger/css/vendor/bootstrap.min.css') }}">
    <!-- styles -->
    <link rel="stylesheet" href="{{ asset('assets/vikinger/css/styles.min.css') }}">
    <!-- simplebar styles -->
    <link rel="stylesheet" href="{{ asset('assets/vikinger/css/vendor/simplebar.css') }}">
    <!-- tiny-slider styles -->
    <link rel="stylesheet" href="{{ asset('assets/vikinger/css/vendor/tiny-slider.css') }}">
    <!-- favicon -->
    <link rel="icon" href="img/favicon.ico">
    @yield('page_stylesheets')
    <link href="{{ mix('css/styles.css') }}" rel="stylesheet">
</head>
<body >
    <div id="vue-app" v-cloak>

        @yield('content-public')
    </div>
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="{{ asset('assets/vikinger/js/utils/app.js')}}"></script>
    <!-- SVG icons -->
    <script src="{{ asset('assets/vikinger/js/utils/svg-loader.js')}}"></script>
    <!-- XM_Plugins -->
    <script src="{{ asset('assets/vikinger/js/vendor/xm_plugins.min.js')}}"></script>
    <!-- form.utils -->
    <script src="{{ asset('assets/vikinger/js/form/form.utils.js')}}"></script>
    <!-- landing.tabs -->
    <script src="{{ asset('assets/vikinger/js/landing/landing.tabs.js')}}"></script>

</body>
</html>
