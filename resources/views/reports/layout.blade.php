@include('styles')

<script src="{{ asset('js/chart.min.js') }}" defer></script>
<script src="{{ asset('jeasyui/datagrid-detailview.js') }}" defer></script>
<div class="easyui-layout" fit="true">

  <div id="report-buttons" data-options="region:'north'"
    style="height:65px;background: #eee;padding:10px;border-left:0;border-right: 0;text-align: center;">

    <a href="javascript:void(0)" class="easyui-menubutton" style="height: 42px;"
      data-options="plain:false,menu:'#activities_reports_menu',iconCls:'icon-more'"
      onclick="">Reports</a>
    <div id="activities_reports_menu" style="width:100px;">

      @can('view_daily_report')
        <div onclick="javascript:$('#reports-center-panel').panel({href:'reports/daily'});">
          Daily
        </div>
      @endcan

      @can('view_financial_report')
        <div onclick="javascript:$('#reports-center-panel').panel({href:'reports/financial'});">
          Financial
        </div>
      @endcan

    @can('view_currency_convertion_report')
      <div onclick="javascript:$('#reports-center-panel').panel({href:'reports/currency_convertion'});">
        USD/IQD
      </div>
    @endcan

    @can('view_incinerations_report')
      <div onclick="javascript:$('#reports-center-panel').panel({href:'reports/incinerations'});">
        Incinerations
      </div>
    @endcan

    @can('view_customs_report')
      <div onclick="javascript:$('#reports-center-panel').panel({href:'reports/customs'});">
        Customs
      </div>
    @endcan

    @can('view_coc_report')
      <div onclick="javascript:$('#reports-center-panel').panel({href:'reports/coc'});">
        Coc
      </div>
    @endcan

    @can('view_sector_report')
      <div onclick="javascript:$('#reports-center-panel').panel({href:'reports/sector'});">
        Sectors
      </div>
    @endcan

    @can('view_goods_report')
      <div onclick="javascript:$('#reports-center-panel').panel({href:'reports/goods'});">
        Goods
      </div>
    @endcan


    @can('view_sars_cov2_report')
      <div onclick="javascript:$('#reports-center-panel').panel({href:'reports/sars_cov2'});">
        SARS COV-2
      </div>
    @endcan

      {{-- <div class="menu-sep"></div> --}}

    </div>


    <!-- Vehicles reports -->
    <a href="javascript:void(0)" class="easyui-menubutton" style="height: 42px; width: 100px;"
      data-options="plain:false,menu:'#certificates_reports_menu',iconCls:'icon-coc'"
      onclick="">Vehicles</a>
    <div id="certificates_reports_menu" style="width:140px;">

      @can('view_vehicles_report')
        <div data-options="iconCls:''"
          onclick="javascript:$('#reports-center-panel').panel({href:'reports/vehicles'});">
          Vehicles
        </div>
      @endcan

      @can('view_pledges_report')
        <div data-options="iconCls:''"
          onclick="javascript:$('#reports-center-panel').panel({href:'reports/pledges'});">
          Pledges
        </div>
        {{-- <div class="menu-sep"></div> --}}
      @endcan

      @can('view_pledge_vehicles_report')
        <div data-options="iconCls:''"
          onclick="javascript:$('#reports-center-panel').panel({href:'reports/pledge_vehicles'});">
          Pledge Vehicles
        </div>
        {{-- <div class="menu-sep"></div> --}}
      @endcan

    </div>

    @can('view_activity_reports')
      <a href="javascript:void(0)" class="easyui-menubutton" style="height: 42px;"
        data-options="plain:false,menu:'#operation_reports_menu',iconCls:'icon-search'"
        onclick="">Activities</a>
      <div id="operation_reports_menu" style="width:100px;">
        @can('view_cheks_activity_report')
          <div data-options="iconCls:'icon-add'"
            onclick="javascript:$('#reports-center-panel').panel({href:'reports/checks'});">
            Tests
          </div>
        @endcan
        {{-- <div class="menu-sep"></div> --}}
        @can('view_batches_activity_report')
          <div data-options=""
            onclick="javascript:$('#reports-center-panel').panel({href:'reports/activities/batches'});">
            Batches
          </div>
        @endcan
        @can('view_transactions_activity_report')
          <div data-options=""
            onclick="javascript:$('#reports-center-panel').panel({href:'reports/activities/transactions'});">
            Transactions
          </div>
        @endcan
        @can('view_balances_activity_report')
          <div data-options=""
            onclick="javascript:$('#reports-center-panel').panel({href:'reports/activities/balances'});">
            Balances
          </div>
        @endcan
        @can('view_inspections_activity_report')
          <div data-options=""
            onclick="javascript:$('#reports-center-panel').panel({href:'reports/activities/inspections'});">
            Inspections
          </div>
        @endcan
        @can('view_payments_activity_report')
          <div data-options=""
            onclick="javascript:$('#reports-center-panel').panel({href:'reports/activities/payments'});">
            Payments
          </div>
        @endcan
      </div>
    @endcan

    @can('view_charts')
      <a href="javascript:void(0)" class="easyui-menubutton" style="height: 42px;"
        data-options="plain:false,menu:'#charts_menu',iconCls:'icon-large-chart'"
        onclick="">Charts</a>
      <div id="charts_menu" style="width:180px;">
        @can('view_weekly_income_charts')
          <div
            onclick="javascript:$('#reports-center-panel').panel({href:'reports/charts/weekly_income_charts'});">
            Weekly Income
          </div>
        @endcan
        @can('view_monthly_income_charts')
          <div
            onclick="javascript:$('#reports-center-panel').panel({href:'reports/charts/monthly_income_charts'});">
            Monthly Income
          </div>
        @endcan
        <div class="menu-sep"></div>
        @can('view_weekly_transaction_charts')
          <div
            onclick="javascript:$('#reports-center-panel').panel({href:'reports/charts/weekly_transaction_charts'});">
            Weekly Transaction
          </div>
        @endcan
        @can('view_monthly_transaction_charts')
          <div
            onclick="javascript:$('#reports-center-panel').panel({href:'reports/charts/monthly_transaction_charts'});">
            Monthly Transaction
          </div>
        @endcan
      </div>
    @endcan

    @can('view_retest_reports')
      <a href="javascript:void(0)" class="easyui-menubutton" style="height: 42px;"
        data-options="plain:false,menu:'#retest_reports_menu',iconCls:'icon-reload'"
        onclick="">Retests</a>
      <div id="retest_reports_menu" style="width:180px;">
        @can('view_retest_report_per_batches')
          <div
            onclick="javascript:$('#reports-center-panel').panel({href:'reports/retests/per_batch'});">
            Per Batch
          </div>
        @endcan
      </div>
    @endcan

  </div>

  <div data-options="region:'center',border:false" id="reports-center-panel">
    <div class="screen-centered-text">
      <img src="/img/dock/test.svg" style="width:30%;opacity: 0.05" alt="home">
    </div>
  </div>


</div>

<style media="screen">
  #report-buttons .easyui-linkbutton {
    margin: 0 5 px;
  }
</style>
