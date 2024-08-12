<div class="easyui-layout" fit="true">

  <div data-options="region:'center',border:false" >
    <table id="CurrencyConvertionReportDatagrid"></table>
  </div>

  <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

      <div class="easyui-panel"  id="CurrencyConvertionReportDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options=" title: 'Date' ">

              <label >From:</label>
              <input id="CurrencyConvertionReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

              <label >To:</label>
              <input id="CurrencyConvertionReportDatagridToDate"  type="text" class="easyui-datebox" style="width:123px;">

              <div style="text-align:center; margin-top: 10px;">
                  <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applyCurrencyConvertionReportDatagridDateFilter()">Apply</a>
                  <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearCurrencyConvertionReportDatagridDateFilter()">Clear</a>
              </div>
      </div>
      <div id="CurrencyConvertionReportDateFilterPanelFooter" >
      </div>

    <table id="currency_convertion_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
    </table>
  </div>

</div>

<div id="CurrencyConvertionReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#CurrencyConvertionReportDatagrid').datagrid('reload')">Reload</a>
    @can('print_currency_convertion_report')
      <a href="#" class="easyui-linkbutton" iconCls="icon-print"  onclick="javascript:printFromCurrencyConvertionReportDatagrid();">Print</a>
    @endcan

    @can('download_currency_convertion_report')
      <a href="#" class="easyui-linkbutton" iconCls="icon-export"  onclick="javascript:downloadCurrencyConvertionReports();">Download</a>
    @endcan
</div>


<script type="text/javascript">

  $(function(){
    $('#CurrencyConvertionReportDatagrid').datagrid({
      idField:'id',
      title: 'USD/IQD Report',
      toolbar:'#CurrencyConvertionReportDatagridToolbar',
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
      url:'reports/currency_convertion/list',

        columns: [[

            { field:'department_name', as:'departments.name', title:'Department', sortable: true, width:15 },
            { field:'paid_amount', as: 'payments.amount', title:'Paid Amount (IQD)', sortable: true, width:30, align:'center' },
            { field:'paid_amount_usd', as: 'paid_amount_usd', title:'Paid Amount (USD)', sortable: true, width:30, align:'center' },
            { field:'currency_convertion_ratio', as: 'payments.currency_convertion_ratio', title:'Ratio USD/IQD', sortable: true, width:30, align:'center' },
            { field:'diff_usd', title:'Diff USD', sortable: false, width:30, align:'center' },
            { field:'diff_iqd', title:'Diff IQD', sortable: false, width:30, align:'center' },
            { field:'office_name', as: 'offices.name', title:'Office', sortable: true, width:30, align:'center' },
            { field:'transaction_date', as:'transactions.date_time', title:'T. Date', sortable: true, width:30, align:'center' },
            { field:'transaction_id', as:'transactions.id', title:'Transaction #', sortable: true, width:20, align:'center' },
            { field:'payment_id', as:'payments.id', title:'Payment #', sortable: true, width:20, align:'center' },
            { field:'invoice_number', as:'payments.invoice_number', title:'Invoice Number', sortable: true, width:20, align:'center' },
            { field:'payment_date', as:'payments.date_time', title:'Payment Date', sortable: true, width:30, align:'center' }
          ]],

        onLoadSuccess: function(data)
        {
            $('#currency_convertion_report_propertygrid').propertygrid('loadData', data.stat_rows );
        }
    });
  });

  $('#CurrencyConvertionReportDatagrid').datagrid('enableFilter', [
    {
      field:'payment_date',
      type:'label'
    },
    {
      field:'paid_amount',
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
          onChangeDatagridFilterConrols('CurrencyConvertionReportDatagrid', 'transaction_checks_view.status', value);
        }
      }
    }
  ]
  );

  function clearCurrencyConvertionReportDatagridDateFilter (value)
  {
      $('#CurrencyConvertionReportDatagridFromDate').datebox('reset');
      $('#CurrencyConvertionReportDatagridToDate').datebox('reset');

      $('#CurrencyConvertionReportDatagrid').datagrid('removeFilterRule', 'payments.date_time');

      if (value != '') {
          $('#CurrencyConvertionReportDatagrid').datagrid('addFilterRule', {
              field: 'payments.date_time',
              value: value
          });
      }

      $('#CurrencyConvertionReportDatagrid').datagrid('doFilter');
  }

  function applyCurrencyConvertionReportDatagridDateFilter ()
  {
      var dateFromText = $('#CurrencyConvertionReportDatagridFromDate').datebox('getValue');
      dateFrom = $.fn.datebox.defaults.parser(dateFromText);
      dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

      var dateToText = $('#CurrencyConvertionReportDatagridToDate').datebox('getValue');
      dateTo = $.fn.datebox.defaults.parser(dateToText);
      dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

      if (dateFromText == '' &&  dateToText == ''){
          $('#CurrencyConvertionReportDatagrid').datagrid('removeFilterRule', 'payments.date_time');
      } else if (dateFromText == '') {
          $('#CurrencyConvertionReportDatagrid').datagrid('addFilterRule', {
              field: 'payments.date_time',
              op: 'less',
              value: dateTo
          });
      } else if (dateToText == '') {
          $('#CurrencyConvertionReportDatagrid').datagrid('addFilterRule', {
              field: 'payments.date_time',
              op: 'greater',
              value: dateFrom
          });
      } else {
          $('#CurrencyConvertionReportDatagrid').datagrid('addFilterRule', {
              field: 'payments.date_time',
              op: 'dateTimeBetween',
              value: dateFrom + ',' + dateTo
          });
      }
      $('#CurrencyConvertionReportDatagrid').datagrid('doFilter');
  }

    function printFromCurrencyConvertionReportDatagrid()
    {
      var currency_convertionReportDatagrdiOptions = $('#CurrencyConvertionReportDatagrid').datagrid('options');
      var filterRules = currency_convertionReportDatagrdiOptions.filterRules;
      var sort = currency_convertionReportDatagrdiOptions.sortName;
      var order = currency_convertionReportDatagrdiOptions.sortOrder;

      var col = $('#CurrencyConvertionReportDatagrid').datagrid('getColumnOption', sort);

      if (col && col.as) {
        sort = col.as;
      }

      var urlToPrint = '<?= \URL::route('print_currency_convertion_report') ?>?';

      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

      if (sort) {
        queryStrings += '&sort=' + sort + '&order=' + order;
      }

      print(urlToPrint + queryStrings);
      //sendPrintCommandToServer(urlToPrint + queryStrings , 'A4', ' -o Landscape');
    }


    function downloadCurrencyConvertionReports()
    {
      var currency_convertionReportDatagridOptions = $('#CurrencyConvertionReportDatagrid').datagrid('options');
      var filterRules = currency_convertionReportDatagridOptions.filterRules;
      var sort = currency_convertionReportDatagridOptions.sortName;
      var order = currency_convertionReportDatagridOptions.sortOrder;

      var col = $('#CurrencyConvertionReportDatagrid').datagrid('getColumnOption', sort);

      if (col && col.as) {
        sort = col.as;
      }

      var urlToPrint = '<?= \URL::route('download_currency_convertion_report') ?>?';

      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

      if (sort) {
        queryStrings += '&sort=' + sort + '&order=' + order;
      }

      print(urlToPrint + queryStrings);
    }
</script>
