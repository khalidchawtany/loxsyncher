<div class="easyui-layout" fit="true">

  <div data-options="region:'center',border:false" >
    <table id="FinancialReportDatagrid"></table>
  </div>

  <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

      <div class="easyui-panel"  id="FinancialReportDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options=" title: 'Date' ">

              <label >From:</label>
              <input id="FinancialReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

              <label >To:</label>
              <input id="FinancialReportDatagridToDate"  type="text" class="easyui-datebox" style="width:123px;">

              <div style="text-align:center; margin-top: 10px;">
                  <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applyFinancialReportDatagridDateFilter()">Apply</a>
                  <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearFinancialReportDatagridDateFilter()">Clear</a>
              </div>
      </div>
      <div id="FinancialReportDateFilterPanelFooter" >
      </div>

    <table id="financial_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
    </table>
  </div>

</div>

<div id="FinancialReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#FinancialReportDatagrid').datagrid('reload')">Reload</a>
    @can('print_financial_report')
      <a href="#" class="easyui-linkbutton" iconCls="icon-print"  onclick="javascript:printFromFinancialReportDatagrid();">Print</a>
    @endcan

    @can('download_financial_report')
      <a href="#" class="easyui-linkbutton" iconCls="icon-export"  onclick="javascript:downloadFinancialReports();">Download</a>
    @endcan
</div>


<script type="text/javascript">

  $(function(){
    $('#FinancialReportDatagrid').datagrid({
      idField:'id',
      title: 'Financial Report',
      toolbar:'#FinancialReportDatagridToolbar',
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
      url:'reports/financial/list',

        columns: [[

            { field:'department_name', as:'departments.name', title:'Department', sortable: true, width:15 },
            { field:'result', as:'transaction_checks_view.status', title:'Result', sortable: true, width:15 },
            { field:'paid_amount', as: 'payments.amount', title:'Paid Amount', sortable: true, width:30, align:'center' },
            { field:'office_name', as: 'offices.name', title:'Office', sortable: true, width:30, align:'center' },
            { field:'product_type', as: 'transactions.product_type', title:'P. Type', sortable: true, width:30, align:'center' },
            { field:'product_name', as: 'products.kurdish_name', title:'Product', sortable: true, width:30 },
            { field:'transaction_date', as:'transactions.date_time', title:'T. Date', sortable: true, width:30, align:'center' },
            { field:'transaction_id', as:'transactions.id', title:'Transaction #', sortable: true, width:20, align:'center' },
            { field:'payment_id', as:'payments.id', title:'Payment #', sortable: true, width:20, align:'center' },
            { field:'invoice_number', as:'payments.invoice_number', title:'Invoice Number', sortable: true, width:20, align:'center' },
            { field:'payment_date', as:'payments.date_time', title:'Payment Date', sortable: true, width:30, align:'center' }
          ]],

        onLoadSuccess: function(data)
        {
            $('#financial_report_propertygrid').propertygrid('loadData', data.stat_rows );
        }
    });
  });

  $('#FinancialReportDatagrid').datagrid('enableFilter', [
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
          onChangeDatagridFilterConrols('FinancialReportDatagrid', 'transaction_checks_view.status', value);
        }
      }
    }
  ]
  );

  function clearFinancialReportDatagridDateFilter (value)
  {
      $('#FinancialReportDatagridFromDate').datebox('reset');
      $('#FinancialReportDatagridToDate').datebox('reset');

      $('#FinancialReportDatagrid').datagrid('removeFilterRule', 'payments.date_time');

      if (value != '') {
          $('#FinancialReportDatagrid').datagrid('addFilterRule', {
              field: 'payments.date_time',
              value: value
          });
      }

      $('#FinancialReportDatagrid').datagrid('doFilter');
  }

  function applyFinancialReportDatagridDateFilter ()
  {
      var dateFromText = $('#FinancialReportDatagridFromDate').datebox('getValue');
      dateFrom = $.fn.datebox.defaults.parser(dateFromText);
      dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

      var dateToText = $('#FinancialReportDatagridToDate').datebox('getValue');
      dateTo = $.fn.datebox.defaults.parser(dateToText);
      dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

      if (dateFromText == '' &&  dateToText == ''){
          $('#FinancialReportDatagrid').datagrid('removeFilterRule', 'payments.date_time');
      } else if (dateFromText == '') {
          $('#FinancialReportDatagrid').datagrid('addFilterRule', {
              field: 'payments.date_time',
              op: 'less',
              value: dateTo
          });
      } else if (dateToText == '') {
          $('#FinancialReportDatagrid').datagrid('addFilterRule', {
              field: 'payments.date_time',
              op: 'greater',
              value: dateFrom
          });
      } else {
          $('#FinancialReportDatagrid').datagrid('addFilterRule', {
              field: 'payments.date_time',
              op: 'dateTimeBetween',
              value: dateFrom + ',' + dateTo
          });
      }
      $('#FinancialReportDatagrid').datagrid('doFilter');
  }

    function printFromFinancialReportDatagrid()
    {
      var financialReportDatagrdiOptions = $('#FinancialReportDatagrid').datagrid('options');
      var filterRules = financialReportDatagrdiOptions.filterRules;
      var sort = financialReportDatagrdiOptions.sortName;
      var order = financialReportDatagrdiOptions.sortOrder;

      var col = $('#FinancialReportDatagrid').datagrid('getColumnOption', sort);

      if (col && col.as) {
        sort = col.as;
      }

      var urlToPrint = '<?= \URL::route('print_financial_report') ?>?';

      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

      if (sort) {
        queryStrings += '&sort=' + sort + '&order=' + order;
      }

      print(urlToPrint + queryStrings);
      //sendPrintCommandToServer(urlToPrint + queryStrings , 'A4', ' -o Landscape');
    }


    function downloadFinancialReports()
    {
      var financialReportDatagridOptions = $('#FinancialReportDatagrid').datagrid('options');
      var filterRules = financialReportDatagridOptions.filterRules;
      var sort = financialReportDatagridOptions.sortName;
      var order = financialReportDatagridOptions.sortOrder;

      var col = $('#FinancialReportDatagrid').datagrid('getColumnOption', sort);

      if (col && col.as) {
        sort = col.as;
      }

      var urlToPrint = '<?= \URL::route('download_financial_report') ?>?';

      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

      if (sort) {
        queryStrings += '&sort=' + sort + '&order=' + order;
      }

      print(urlToPrint + queryStrings);
    }
</script>
