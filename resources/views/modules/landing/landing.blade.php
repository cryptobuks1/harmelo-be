@extends('layouts.layout-app-public')
@section('content-public')
    <landing inline-template>
        <div class="landing">

            <!-- SNACKBARS -->
            <div id="snackbar_success"><p style="color: #fff">@{{ snackbar_message }}</p></div>
            <div id="snackbar_error"><p style="color: #fff">@{{ snackbar_message }}</p></div>
            <div class="landing-decoration"></div>

            <div class="landing-info">
                <div class="logo">
                    <img src="{{ asset('assets/harmelo/logo/harmelo-logo-no-word-white.png') }}" height="100">
                </div>

                <h1 class="landing-info-pretitle" style="font-family: Poppins, sans-serif !important; font-weight: 900 !important">HARMELO  •  SIGN IN</h1>

                <h1 class="landing-info-title" style="font-family: Poppins, sans-serif !important; font-weight: 900 !important; line-height: 1.2">COMMUNITY</h1>

                <p class="landing-info-text" style="font-family: Poppins, sans-serif !important; font-weight: 700 !important; margin: 0 auto 0 !important">
                    Get inspired and express your passion for music by innovating an original piece of your own.
                </p>
            </div>

            <div class="landing-form">
                <div class="form-box login-register-form-element c-form">
                    <div class="social-links">
                        <a class="button medium facebook width-100-perc text-white" type="submit">
                            <svg class="icon-facebook mr-1">
                                <use xlink:href="#svg-facebook"></use>
                            </svg> Sign in with Facebook
                        </a>

                        <a class="button medium google width-100-perc text-white form-mt-10" type="submit" href="{{ url('login/google') }}"> 
                            <svg class="icon-google mr-1">
                                <use xlink:href="#svg-google"></use>
                            </svg> Sign in with Google
                        </a>
                    </div>
                    <div class="form-mt-35">
                        <h6 class="text-white">Don't have an account? 
                            <a class="font-700" href="http://127.0.0.1:8080/register" data-toggle="modal" data-target="#exampleModalCenter" style="color: #0d6efd">Register now!</a>
                        </h6>
                        
                    </div>
                </div>
            </div>
        </div>
        
    </landing>
    <apptitle title="Sign in | Harmelo Community"></apptitle>
    <link href="{{ asset('assets/harmelo/css/landing.css') }}" rel="stylesheet">
@endsection

@section('page_stylesheets')

@endsection

