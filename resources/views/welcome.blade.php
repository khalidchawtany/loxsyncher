<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Lox</title>

        <!-- Fonts -->
        {{-- <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css"> --}}

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 18px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">

          <div class="content">
            <div class="m-b-md">
              <img style="width: 350px;" src="img/login_bg.png" alt="login_bg"/>
            </div>

            @if (Route::has('login'))
              <div class="links">
                @auth
                  <a href="{{ url('home') }}">Home</a>
                @else
                  <a href="{{ route('login') }}">Login</a>
                  {{-- <a href="{{ route('register') }}">Register</a> --}}
                @endauth
              </div>
            @endif
            <div style="margin-top: 2rem;">
              <h2>
                {{ config('app.site_name') . ' ' . config('app.year') }}
              </h2>
            </div>
          </div>

        </div>
    </body>
</html>
