@extends('layouts.layout-app-admin')
@section('content')
    <notfound inline-template>
        <div>
            <div class="content-grid">
                <div class="under-construction" style="text-align: center; margin-top: 10px">
                    <h1 style="font-family: 'Titillium Web', sans-serif !important; font-size: 7rem; font-weight: 900 !important">&#9839 PAGE NOT FOUND &#9835</h1>
                    <img src="{{ asset('assets/harmelo/svg/404-not-found.svg') }}" style="width: 80%">
                </div>
            </div>
        </div>
    </notfound>

@endsection

@section('page_stylesheets')

@endsection
