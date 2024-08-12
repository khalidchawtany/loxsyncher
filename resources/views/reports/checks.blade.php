
<div class="easyui-layout" fit="true">

  <div data-options="region:'center',border:false" >
    <table id="CheckReportDatagrid"></table>
  </div>

  <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

      <div class="easyui-panel"  id="CheckReportDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options="
                                        {{-- footer: '#CheckReportDateFilterPanelFooter', --}}
                                        title: 'Date'
                                    ">

              <label >From:</label>
              <input id="CheckReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

              <label >To:</label>
              <input id="CheckReportDatagridToDate"  type="text" class="easyui-datebox" style="width:123px;">

              <div style="text-align:center; margin-top: 10px;">
                  <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applyCheckReportDatagridDateFilter()">Apply</a>
                  <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearCheckReportDatagridDateFilter()">Clear</a>
              </div>
      </div>
      <div id="CheckReportDateFilterPanelFooter" >
      </div>

    <table id="check_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
    </table>
  </div>

</div>

<div id="CheckUpdateHistoryDialog"
      class="easyui-dialog"
      style="width:1000px;height:500px;padding:10px 10px"
      title="Test Update History" closed="true" modal="true">


<div id="CheckReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#CheckReportDatagrid').datagrid('reload')">Reload</a>

    @can('print_cheks_activity_report')
      <a href="#" class="easyui-linkbutton" iconCls="icon-print"  onclick="javascript:printChecksReport();">Print</a>
    @endcan
</div>


<script type="text/javascript">

  $(function(){
    $('#CheckReportDatagrid').datagrid({
      idField:'id',
      title: 'Check Report',
      toolbar:'#CheckReportDatagridToolbar',
      fit:true,
      border:false,
      fitColumns:true,
      nowrap:false,
      singleSelect:true,
      method:'get',
      rownumbers:true,
      pagination:true,
      remoteFilter:true,
      filterMatchType: 'any',
      url:'reports/checks/list',

        columns: [[
            { field:'lab'    , as:'check_types.category'        , title:'Lab'               , sortable: true , width:30 } ,
            { field:'test'    , as:'check_types.subcategory'        , title:'Test'               , sortable: true , width:30 } ,
            { field:'check_id'     , as:'checks.id'                 , title:'Id'                 , sortable: true , width:15 } ,
            { field:'check_status' , as:'checks.status'             , title:'Status'             , sortable: true , width:15 } ,
            { field:'update_count' , as:'checks.update_count'       , title:'Update Count'       , sortable: true , width:30   , align:'center',

                formatter: function(val, row) {
                    return '<button onclick="showCheckUpdateHistory(' + row.check_id + ')">' + val + '</button>';
                },
            } ,
            { field:'user_name'    , as:'users.kurdish_name'        , title:'User'               , sortable: true , width:30 } ,
            { field:'product_type' , as:'transactions.product_type' , title:'P. Type'            , sortable: true , width:30   , align:'center' } ,
            { field:'product_name' , as:'products.kurdish_name'     , title:'Product'            , sortable: true , width:30 } ,
            { field:'check_date'   , as:'checks.created_at'         , title:'Test Creation Date' , sortable: true , width:30   , align:'center' }
          ]],

        onLoadSuccess: function(data)
        {
            $('#check_report_propertygrid').propertygrid('loadData', data.stat_rows );
        }
    });
  });

  $('#CheckReportDatagrid').datagrid('enableFilter', [
    {
      field:'check_date',
      type:'label'
    },
  ]
  );

  function clearCheckReportDatagridDateFilter (value)
  {
      $('#CheckReportDatagridFromDate').datebox('reset');
      $('#CheckReportDatagridToDate').datebox('reset');

      $('#CheckReportDatagrid').datagrid('removeFilterRule', 'checks.created_at');

      if (value != '') {
          $('#CheckReportDatagrid').datagrid('addFilterRule', {
              field: 'checks.created_at',
              value: value
          });
      }

      $('#CheckReportDatagrid').datagrid('doFilter');
  }

  function applyCheckReportDatagridDateFilter ()
  {
      var dateFromText = $('#CheckReportDatagridFromDate').datebox('getValue');
      dateFrom = $.fn.datebox.defaults.parser(dateFromText);
      dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

      var dateToText = $('#CheckReportDatagridToDate').datebox('getValue');
      dateTo = $.fn.datebox.defaults.parser(dateToText);
      dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

      if (dateFromText == '' &&  dateToText == ''){
          $('#CheckReportDatagrid').datagrid('removeFilterRule', 'checks.created_at');
      } else if (dateFromText == '') {
          $('#CheckReportDatagrid').datagrid('addFilterRule', {
              field: 'checks.created_at',
              op: 'less',
              value: dateTo
          });
      } else if (dateToText == '') {
          $('#CheckReportDatagrid').datagrid('addFilterRule', {
              field: 'checks.created_at',
              op: 'greater',
              value: dateFrom
          });
      } else {
          $('#CheckReportDatagrid').datagrid('addFilterRule', {
              field: 'checks.created_at',
              op: 'dateTimeBetween',
              value: dateFrom + ',' + dateTo
          });
      }
      $('#CheckReportDatagrid').datagrid('doFilter');
  }

  function showCheckUpdateHistory(check_id) {
    $('#CheckUpdateHistoryDialog').dialog('open')
      .dialog('refresh', 'reports/checks/activity_log?check_id=' + check_id);
  }

    function printChecksReport()
    {
      var checksReportDatagridOptions = $('#CheckReportDatagrid').datagrid('options');
      var filterRules = checksReportDatagridOptions.filterRules;
      var sort = checksReportDatagridOptions.sortName;
      var order = checksReportDatagridOptions.sortOrder;

      var col = $('#CheckReportDatagrid').datagrid('getColumnOption', sort);

      if (col && col.as) {
        sort = col.as;
      }

      var urlToPrint = '{!! \URL::route('print_checks_report') !!}?';

      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

      if (sort) {
        queryStrings += '&sort=' + sort + '&order=' + order;
      }

      print(urlToPrint + queryStrings);

      //sendPrintCommandToServer(urlToPrint + queryStrings , 'A4', ' -o Landscape');
    }

</script>
