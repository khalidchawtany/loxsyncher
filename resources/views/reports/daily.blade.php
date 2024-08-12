
<div class="easyui-layout" fit="true">

  <div data-options="region:'center',border:false" >
    <table id="DailyReportDatagrid"></table>
  </div>

  <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

      <div class="easyui-panel"  id="DailyReportDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options="
                                        {{-- footer: '#DailyReportDateFilterPanelFooter', --}}
                                        title: 'Date'
                                    ">

              <label >From:</label>
              <input id="DailyReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

              <label >To:</label>
              <input id="DailyReportDatagridToDate"  type="text" class="easyui-datebox" style="width:123px;">

              <div style="text-align:center; margin-top: 10px;">
                  <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applyDailyReportDatagridDateFilter()">Apply</a>
                  <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearDailyReportDatagridDateFilter()">Clear</a>
              </div>


      <div class="easyui-panel"  id="DailyReportReportCategoryFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options=" title: 'Category' ">

              <label >Category:</label>
              <input id="DailyReportDatagridCategoryFilterCombobox" type="combobox" class="easyui-combobox" style="width:223px;"
                  data-options="
                    url:'reports/products/categories',
                    method:'get',
                    valueField: 'id',
                    textField: 'name',
                    limitToList: true,
                    hasDownArrow: true,
                    panelHeight:'auto',
                    required:false,
                    onSelect: function(category) {
                        applyDailyReportDatagridCategoryFilter(category);
                    }
                  "
              >

      </div>

      </div>



      <div id="DailyReportDateFilterPanelFooter" >
      </div>

    <table id="daily_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
    </table>
  </div>

</div>

<div id="DailyReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#DailyReportDatagrid').datagrid('reload')">Reload</a>
    @can('print_daily_report')
      <a href="#" class="easyui-linkbutton" iconCls="icon-print"  onclick="javascript:printFromDailyReportDatagrid();">Print</a>
    @endcan
    @can('download_daily_report')
      <a href="#" class="easyui-linkbutton" iconCls="icon-export"  onclick="javascript:downloadDailyReports();">Download</a>
    @endcan

</div>


<script type="text/javascript">

  $(function(){
    $('#DailyReportDatagrid').datagrid({
      idField:'id',
      title: 'Daily Report',
      toolbar:'#DailyReportDatagridToolbar',
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
      url:'reports/daily/list',

        columns: [[

            { field:'department_name', as:'departments.name', title:'Department', sortable: true, width:15 },
            { field:'result', as:'transaction_checks_view.status', title:'Result', sortable: true, width:15 },
            { field:'unit', as: 'transactions.unit', title:'Unit', sortable: true, width:10, align:'center' },
            { field:'amount', as:'transactions.amount', title:'Amount', sortable: true, width:20, align:'center' },
            { field:'paid_amount', as: 'payments.amount', title:'Paid Amount', sortable: true, width:30, align:'center' },
            { field:'plate', as: 'trucks().plate', title:'Plate', sortable: true, width:30, align:'center' },
            { field:'office_name', as: 'offices.name', title:'Office', sortable: true, width:30, align:'center' },
            { field:'product_type', as: 'transactions.product_type', title:'P. Type', sortable: true, width:30, align:'center' },
            { field:'product_name', as: 'products.kurdish_name', title:'Product', sortable: true, width:30 },
            { field:'batch_count', as:'transactions.batch_count', title:'Batch #', sortable: true, width:20, align:'center' },
            { field:'transaction_date', as:'transactions.date_time', title:'T. Date', sortable: true, width:30, align:'center' },
            { field:'transaction_id', as:'transactions.id', title:'Transaction #', sortable: true, width:20, align:'center' },
            { field:'payment_id', as:'payments.id', title:'Payment #', sortable: true, width:20, align:'center' }
          ]],

        onLoadSuccess: function(data)
        {
            $('#daily_report_propertygrid').propertygrid('loadData', data.stat_rows );
        }
    });
  });

  $('#DailyReportDatagrid').datagrid('enableFilter', [
    {
      field:'transaction_date',
      type:'label'
    },
    {
      field:'paid_amount',
      type:'numberbox',
      op: ['equal', 'contains', 'less', 'lessorequal', 'greater', 'greaterorequal']
    },
    {
      field:'batch_count',
      type:'numberbox',
      op: ['equal', 'contains', 'less', 'lessorequal', 'greater', 'greaterorequal']
    },
    {
      field: 'result',
      type: 'combobox',
      options: {
        panelHeight: 'auto',
        hasDownArrow: false,
        limitToList: true,
        data: [{
          'text': 'Failed',
          'value': 'Failed',
        },
          {
            'text': 'Passed',
            'value': 'Passed',
          }],
        onChange: function (value) {
          onChangeDatagridFilterConrols('DailyReportDatagrid', 'transaction_checks_view.status', value);
        }
      }
    }
  ]
  );

  function clearDailyReportDatagridDateFilter (value)
  {
      $('#DailyReportDatagridFromDate').datebox('reset');
      $('#DailyReportDatagridToDate').datebox('reset');

      $('#DailyReportDatagrid').datagrid('removeFilterRule', 'transactions.date_time');

      if (value != '') {
          $('#DailyReportDatagrid').datagrid('addFilterRule', {
              field: 'transactions.date_time',
              value: value
          });
      }

      $('#DailyReportDatagrid').datagrid('doFilter');
  }

  function applyDailyReportDatagridDateFilter ()
  {
      var dateFromText = $('#DailyReportDatagridFromDate').datebox('getValue');
      dateFrom = $.fn.datebox.defaults.parser(dateFromText);
      dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

      var dateToText = $('#DailyReportDatagridToDate').datebox('getValue');
      dateTo = $.fn.datebox.defaults.parser(dateToText);
      dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

      if (dateFromText == '' &&  dateToText == ''){
          $('#DailyReportDatagrid').datagrid('removeFilterRule', 'transactions.date_time');
      } else if (dateFromText == '') {
          $('#DailyReportDatagrid').datagrid('addFilterRule', {
              field: 'transactions.date_time',
              op: 'less',
              value: dateTo
          });
      } else if (dateToText == '') {
          $('#DailyReportDatagrid').datagrid('addFilterRule', {
              field: 'transactions.date_time',
              op: 'greater',
              value: dateFrom
          });
      } else {
          $('#DailyReportDatagrid').datagrid('addFilterRule', {
              field: 'transactions.date_time',
              op: 'dateTimeBetween',
              value: dateFrom + ',' + dateTo
          });
      }
      $('#DailyReportDatagrid').datagrid('doFilter');
  }

  function applyDailyReportDatagridCategoryFilter(category) {

    var filterRulesBefore = $('#DailyReportDatagrid').datagrid('options').filterRules;

    if (category.id == 0) {
      $('#DailyReportDatagrid').datagrid('removeFilterRule', 'category_id');
    } else {
          $('#DailyReportDatagrid').datagrid('addFilterRule', {
              field: 'category_id',
              value: category.id
          });
    }

    var filterRules = $('#DailyReportDatagrid').datagrid('options').filterRules;

    if (! (filterRules.length == 0 && filterRulesBefore.length == 0 ) ) {
      $('#DailyReportDatagrid').datagrid('doFilter');
    }
  }


    function printFromDailyReportDatagrid()
    {
      var dailyReportDatagridOptions = $('#DailyReportDatagrid').datagrid('options');
      var filterRules = dailyReportDatagridOptions.filterRules;
      var sort = dailyReportDatagridOptions.sortName;
      var order = dailyReportDatagridOptions.sortOrder;

      var col = $('#DailyReportDatagrid').datagrid('getColumnOption', sort);

      if (col && col.as) {
        sort = col.as;
      }

      var urlToPrint = '{!! \URL::route('print_daily_report') !!}?';

      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

      if (sort) {
        queryStrings += '&sort=' + sort + '&order=' + order;
      }

      print(urlToPrint + queryStrings);

      //sendPrintCommandToServer(urlToPrint + queryStrings , 'A4', ' -o Landscape');
    }

    function downloadDailyReports()
    {
      var dailyReportDatagridOptions = $('#DailyReportDatagrid').datagrid('options');
      var filterRules = dailyReportDatagridOptions.filterRules;
      var sort = dailyReportDatagridOptions.sortName;
      var order = dailyReportDatagridOptions.sortOrder;

      var col = $('#DailyReportDatagrid').datagrid('getColumnOption', sort);

      if (col && col.as) {
        sort = col.as;
      }

      var urlToPrint = '{!! \URL::route('download_daily_report') !!}?';

      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

      if (sort) {
        queryStrings += '&sort=' + sort + '&order=' + order;
      }

      print(urlToPrint + queryStrings);
    }

</script>
