<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">

	@if(isset($archive) && $archive)
		<link rel="stylesheet" type="text/css" href="{{ './pure-min.css' }}">
		<link href="{{ './paper.css' }}" rel="stylesheet">
	@else
		<link rel="stylesheet" type="text/css" href="{{asset('css/pure-min.css')}}">
		<link href="{{ asset('css/paper.css') }}" rel="stylesheet">
	@endif

    <style type="text/css" media="all">
        p {
            margin: 5px 0;
        }

        table.header_table {
            border-style:none;
            text-align: right;
            line-height:18px;
            font-size: 16px;
            height:2cm;
        }

        .header_table_seperator {
            padding:5px 0;
        }

        @if(isElectron())
        section.print_using_special_paper {
            display: none;
        }
        @endif

        @stack('styles')
    </style>


</head>


<body dir="rtl" lang="ar" @yield('body-attributes')>
    <div class="container">

        @yield('content')

    </div>


    <script>
        (function () {
            var userAgent = navigator.userAgent.toLowerCase();
            if ( userAgent.indexOf(' electron/') == -1) {
                window.print();
            }
        })();
    </script>
</body>

</html>
