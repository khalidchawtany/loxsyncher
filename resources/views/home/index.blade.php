@extends('layouts.dashboard')
@section('content')
    <div title="Home">

        <div class="easyui-layout" fit="true">

            <div data-options="region:'west'" style="width:200px;padding:20px;border-top:0;border-left:0;border-bottom: 0;">




                    <label class="username-label" style="font-weight:bold;">
                             {{ config('app.site_name') . ' ' . config('app.year') }}
                    </label>

                    <label class="username-label">
                             {{ucfirst(Auth::user()->name)}}
                    </label>

                    <a href="{{ url('logout') }}"
                        class="easyui-linkbutton c6"
                        style="margin-bottom:10px;"
                        data-options="size:'large',width:'100%'">
                        Logout
                    </a>

                    <a href="{{ url('chgpwd') }}"
                        class="easyui-linkbutton c5"
                        style="margin-bottom:10px;"
                        data-options="size:'large',width:'100%'">
                        Change Password
                    </a>

                    <?php

                      $manager = app('impersonate');

                      if ($manager->isImpersonating()) {
                    ?>
                    <a href="/users/leave-impersonation"
                       class="easyui-linkbutton c5"
                       data-options="size:'large',width:'100%'" >
                      Leave Impersonation
                    </a>
                    <?php } ?>

            </div>

            <div data-options="region:'center',border:false" id="home-center-panel">

                <div class="easyui-layout" fit="true">
                    <div data-options="region:'north', border: false" style="height: 110px;">
                        <h1 style="font-size:50px;color:#9e91b9;text-align: center;">{{ config('app.site_name') . ' ' . config('app.year') }}</h1>
                    </div>
                    <div class="home-buttons-container" data-options="region:'center', border: false">



                        @can('view_check_type')
                            <a  class="easyui-linkbutton"
                                data-options="size:'large',width:'150px', height: '100px;', iconCls:'icon-large-testtype', iconAlign:'left'"
                                onclick="switchDashboardMainTab('Check Types', 'check_types')">
                                Test Types
                            </a>
                        @endcan

                        @can('view_product')
                            <a  class="easyui-linkbutton"
                                data-options="size:'large',width:'150px', height: '100px', iconCls:'icon-large-product', iconAlign:'left'"
                                onclick="switchDashboardMainTab('Products', 'products')">
                                Products
                            </a>
                        @endcan

                        @can('view_department')
                            <a  class="easyui-linkbutton"
                                data-options="size:'large',width:'150px', height: '100px', iconCls:'icon-large-department', iconAlign:'left'"
                                onclick="switchDashboardMainTab('Departments', 'departments')">
                                Departments
                            </a>
                        @endcan


                        @can('view_specification')
                            <a  class="easyui-linkbutton"
                                data-options="size:'large',width:'150px', height: '100px', iconCls:'icon-large-specification', iconAlign:'left'"
                                onclick="switchDashboardMainTab('Specifications', 'specifications')">
                                Standards
                            </a>
                        @endcan



<!-- bread_home_icon -->
                        @can('view_control_center')
                            <a  class="easyui-linkbutton"
                                data-options="size:'large',width:'150px', height: '100px', iconCls:'icon-large-control-center', iconAlign:'left'"
                                onclick="switchDashboardMainTab('Control Center', 'control-center')">
                                Control Center
                            </a>
                        @endcan

                        @can('view_app_setting')
                            <a  class="easyui-linkbutton"
                                data-options="size:'large',width:'150px', height: '100px', iconCls:'icon-large-appsetting', iconAlign:'left'"
                                onclick="switchDashboardMainTab('App Settings', 'app_settings')">
                                App Settings
                            </a>
                        @endcan


                    </div>


                </div>
            </div>


        </div>


    </div>
@endsection

<style>
    .username-label {
        font-weight:normal;
        padding: 5px;
        border: 1px solid #eee;
        font-size:12px;
        margin-bottom:10px;
        display:block;
        text-align:center;
    }
</style>

