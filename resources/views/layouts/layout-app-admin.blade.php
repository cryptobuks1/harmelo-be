<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Harmelo Music | Let your Music be heard!</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/harmelo/css/icon-only.png') }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="canonical" href="https://www.wrappixel.com/templates/adminwrap/" />
    <link rel="stylesheet" href="{{ asset('assets/vikinger/css/vendor/bootstrap.min.css') }}">
    <!-- styles -->
    <link rel="stylesheet" href="{{ asset('assets/vikinger/css/styles.min.css') }}">
    <!-- simplebar styles -->
    <link rel="stylesheet" href="{{ asset('assets/vikinger/css/vendor/simplebar.css') }}">
    <!-- tiny-slider styles -->
    <link rel="stylesheet" href="{{ asset('assets/vikinger/css/vendor/tiny-slider.css') }}">

    <!-- custom master css -->
    <link rel="stylesheet" href="{{ asset('assets/harmelo/css/master.css') }}">
    @yield('page_stylesheets')
    <link href="{{ mix('css/styles.css') }}" rel="stylesheet">
</head>
<body id="dynamic-page-body" class="dynamic-page-body-class">
    <div id="vue-app" v-cloak>

        @include('inc.inc-page-loader')

        @include('inc.inc-page-left-nav')

        @include('inc.inc-page-floaty-bar')

        @yield('content')

        @include('inc.inc-page-header')

    </div>

    <script src="{{ mix('js/app.js') }}" ></script>

    <script src="{{ asset('assets/vikinger/js/utils/app.js')}}"></script>
    <!-- page loader -->
    <script src="{{ asset('assets/vikinger/js/utils/page-loader.js')}}"></script>
    <!-- simplebar -->
    <script src="{{ asset('assets/vikinger/js/vendor/simplebar.min.js')}}"></script>
    <!-- XM_Plugins -->
    <script src="{{ asset('assets/vikinger/js/vendor/xm_plugins.min.js')}}"></script>
    <!-- tiny-slider -->
    <script src="{{ asset('assets/vikinger/js/vendor/tiny-slider.min.js')}}"></script>
    <!-- chartJS -->
    <script src="{{ asset('assets/vikinger/js/vendor/Chart.bundle.min.js')}}"></script>
    <!-- global.hexagons -->
    <script src="{{ asset('assets/vikinger/js/global/global.hexagons.js')}}"></script>
    <!-- global.tooltips -->
    <script src="{{ asset('assets/vikinger/js/global/global.tooltips.js')}}"></script>
     <!-- global.charts -->
    <script src="{{ asset('assets/vikinger/js/global/global.charts.js')}}"></script>
    <!-- header -->
    <script src="{{ asset('assets/vikinger/js/header/header.js')}}"></script>
    <!-- sidebar -->
    <script src="{{ asset('assets/vikinger/js/sidebar/sidebar.js')}}"></script>
    <!-- global.accordion -->
    <script src="{{ asset('assets/vikinger/js/global/global.accordions.js')}}"></script>
    <!-- form.utils -->
    <script src="{{ asset('assets/vikinger/js/form/form.utils.js')}}"></script>
    <!-- SVG icons -->
    <script src="{{ asset('assets/vikinger/js/utils/svg-loader.js')}}"></script>
    <!-- landing.tabs -->
    <script src="{{ asset('assets/vikinger/js/landing/landing.tabs.js')}}"></script>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>
