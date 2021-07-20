@extends('layouts.layout-app-admin')

@section('content')

    <main-container>

        @include('inc.inc-page-loader')

        @include('inc.inc-page-left-nav')

        @include('inc.inc-page-chat-widget')

        @include('inc.inc-page-header')

        @include('inc.inc-page-floaty-bar')

    </main-container>

@endsection

@section('page_stylesheets')

@endsection
