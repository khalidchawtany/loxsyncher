<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Lox'). ' - ' . config('app.site_name') . ' ' . config('app.year') }}</title>

    @if(env('APP_ENV') == 'production')
        <script>if (typeof module === 'object') {window.module = module; module = undefined;}</script>
            <script type="text/javascript" src="{{asset('js/all.js')}}"></script>
        <script>if (window.module) module = window.module;</script>
        <link rel="stylesheet" type="text/css" href="{{asset('css/all.css')}}">
    @else
        <script>if (typeof module === 'object') {window.module = module; module = undefined;}</script>
            <script type="text/javascript" src="{{asset('jeasyui/jquery.min.js')}}"></script>
            <script type="text/javascript" src="{{asset('jeasyui/jquery.easyui.min.js')}}"></script>
            <script type="text/javascript" src="{{asset('jeasyui/jquery.edatagrid.customized.js')}}"></script>
            <script type="text/javascript" src="{{asset('jeasyui/datagrid-filter.customized.js')}}"></script>
            <script type="text/javascript" src="{{asset('js/lodash.min.js')}}"></script>
            <script type="text/javascript" src="{{asset('js/application.js')}}"></script>
            <script type="text/javascript" src="{{asset('jeasyui/jeasyui.customized.js')}}"></script>
        <script>if (window.module) module = window.module;</script>

        <link rel="stylesheet" type="text/css" href="{{asset('jeasyui/themes/default/easyui.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('jeasyui/themes/icon.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('jeasyui/themes/color.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('jeasyui/demo/demo.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/dock.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/application.custom.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/pure-min.css')}}">
        <!--[if lte IE 8]>
            <link rel="stylesheet" type="text/css" href="{{asset('css/grids-responsive-old-ie-min.css')}}">
        <![endif]-->
        <!--[if gt IE 8]><!-->
            <link rel="stylesheet" type="text/css" href="{{asset('css/grids-responsive-min.css')}}">
            <!--<![endif]-->
        @endif



</head>

<body>
    {{-- Dashboard Layout --}}
    <div class="easyui-layout" fit="true">

        {{-- South panel of dashboard --}}
        <div data-options="region:'south', border:false, height:'77px'">



        </div>
        {{-- /South panel of dashboard --}} {{-- Center panel of dashboard --}}
        <div data-options="region:'center'" style="border-left:0;border-right:0;border-top:0;">

            <div class="easyui-tabs" id="DashboardMainTab" data-options="fit:true,border:false,showHeader:false">

                @yield('content')

            </div>


        </div>
        {{-- /Center panel of dashboard --}}

    </div>
    {{-- /Dashboard Layout --}}

    {{-- Dock Panel --}}
    <div id="dock-container">
        <div id="dock">
            <ul>

                <li>
                    <span>Home</span>
                    <a href="#" onclick="switchDashboardMainTab('Home', '')">
                        <img src="/img/dock/home.svg" alt="home" />
                    </a>
                </li>
                @can('view_transaction')
                    <li>
                        <span>Transactions</span>
                        <a href="#" onclick="switchDashboardMainTab('Transactions', 'transactions')">
                            <img src="/img/dock/transaction.svg" alt="Transactions" />
                        </a>
                    </li>
                @endcan
                @can('view_batch')
                    <li>
                        <span>Batches</span>
                        <a href="#" onclick="switchDashboardMainTab('Batches', 'batches')">
                            <img src="/img/dock/batch.svg" alt="Batches" />
                        </a>
                    </li>
                @endcan
                {{-- <li> --}}
                {{--     <span>Batches</span> --}}
                {{--     <a href="#" onclick="switchDashboardMainTab('Batches', 'batches')"> --}}
                {{--         <img src="img/dock/batch.svg" alt="Batches" /> --}}
                {{--     </a> --}}
                {{-- </li> --}}
                @can('view_check')
                    <li>
                        <span>Tests</span>
                        <a href="#" onclick="switchDashboardMainTab('Checks', 'checks')">
                            <img src="/img/dock/test.svg" alt="Tests" />
                        </a>
                    </li>
                @endcan

                @can('view_payment')
                    <li>
                        <span>Payments</span>
                        <a href="#" onclick="switchDashboardMainTab('Payments', 'payments')">
                            <img src="/img/dock/payment.svg" alt="Payments" />
                        </a>
                    </li>
                @endcan

                @can('view_balance')
                    <li>
                        <span>Balances</span>
                        <a href="#" onclick="switchDashboardMainTab('Balances', 'balances')">
                            <img src="/img/dock/balance.svg" alt="Balances" />
                        </a>
                    </li>
                @endcan

                @can('view_report')
                    <li>
                        <span>Reports</span>
                        <a href="#" onclick="switchDashboardMainTab('Reports', 'reports')">
                            <img src="/img/dock/report.svg" alt="Reports" />
                        </a>
                    </li>
                @endcan
                @if(user()->canany(['view_certificate', 'view_release']))
                    <li>
                        <span>Certificates</span>
                        <a href="#" onclick="switchDashboardMainTab('Certificates', 'certificates')">
                            <img src="/img/dock/certificate.svg" alt="Certificates" />
                        </a>
                    </li>
                @endif
<!-- bread_dashboard -->
            </ul>
        </div>
    </div>
    {{-- /Dock Panel --}}


    <script>

        function switchDashboardMainTab(title, url) {

            var tabExists = $('#DashboardMainTab').tabs('exists', title);
            if(tabExists){
                $('#DashboardMainTab').tabs('select', title);
            } else {
                // add a new tab panel
                $('#DashboardMainTab').tabs('add',{
                    title: title,
                    href: url,
                });

            }
        }

        // set header for ajax calls
        $(function(){

            const csrf_token = $('meta[name="csrf-token"]').attr('content');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrf_token
                }
            });

            window.CSRF_TOKEN = csrf_token

        });

        $(function(){

            @if(Auth::user()->open_transaction_after_login)
                @can('create_transaction')
                    switchDashboardMainTab('Transactions', 'transactions')
                @endcan
            @endif

        });

    </script>

    <style>
        .tabs-header-noborder{
            padding:0;
        }
    </style>

</body>
</html>
