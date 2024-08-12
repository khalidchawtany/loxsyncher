
<div class="easyui-layout" fit="true">

  <div data-options="region:'center',border:false" >
    <table id="CocReportDatagrid"></table>
  </div>

  <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

      <div class="easyui-panel"  id="CocReportDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options="
                                        {{-- footer: '#CocReportDateFilterPanelFooter', --}}
                                        title: 'Inspection Date'
                                    ">

              <label >From:</label>
              <input id="CocReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

              <label >To:</label>
              <input id="CocReportDatagridToDate"  type="text" class="easyui-datebox" style="width:123px;">

              <div style="text-align:center; margin-top: 10px;">
                  <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applyCocReportDatagridDateFilter()">Apply</a>
                  <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearCocReportDatagridDateFilter()">Clear</a>
              </div>
      </div>
      <div id="CocReportDateFilterPanelFooter" >
      </div>

    <table id="coc_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
    </table>
  </div>

</div>

<div id="CocReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#CocReportDatagrid').datagrid('reload')">Reload</a>
    @can('print_coc_report')
      <a href="#" class="easyui-linkbutton" iconCls="icon-print"  onclick="javascript:printFromCocReportDatagrid();">COC Statics</a>
      <a href="#" class="easyui-linkbutton" iconCls="icon-print"  onclick="javascript:printCocTransactionsFromCocReportDatagrid();">COC Transactions</a>
    @endcan
    @can('download_coc_report')
      <a href="#" class="easyui-linkbutton" iconCls="icon-export"  onclick="javascript:downloadFromCocReportDatagrid();">Download</a>
    @endcan
</div>


<script type="text/javascript">

  $(function(){
    $('#CocReportDatagrid').datagrid({
      idField:'id',
      title: 'Coc Report',
      toolbar:'#CocReportDatagridToolbar',
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
      url:'reports/coc/list',

        columns: [[

            { field:'transaction_date', as:'transactions.date_time', title:'Inspection Date', sortable: true, width:30, align:'center' },
            { field:'transaction_id', as:'transactions.id', title:'RD #', sortable: true, width:20, align:'center' },
            { field:'truck_plate', as: 'trucks().plate', title:'Truck Plate', sortable: true, width:30 },
            { field:'product_name', as: 'products.name', title:'Goods Description', sortable: true, width:30 },
            { field:'amount', as:'transactions.amount', title:'Quantity', sortable: true, width:20, align:'center' },
            { field:'unit', as: 'transactions.unit', title:'Type of Package', sortable: true, width:20, align:'center' },
            { field:'payment_id', as:'payments.id', title:'Invoice Number', sortable: true, width:20, align:'center' },
            { field:'payment_date_time', as:'payments.date_time', title:'Invoice Date', sortable: true, width:30, align:'center' },
            { field:'paid_amount', as: 'payments.amount', title:'Invoice Amount (IQD)', sortable: true, width:30, align:'center' }
          ]],

        onLoadSuccess: function(data)
        {
            $('#coc_report_propertygrid').propertygrid('loadData', data.stat_rows );
        }
    });
  });

  $('#CocReportDatagrid').datagrid('enableFilter', [
    {
      field:'transaction_date',
      type:'label'
    },
    {
      field:'paid_amount',
      type:'numberbox',
      op: ['equal', 'contains']
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
          onChangeDatagridFilterConrols('CocReportDatagrid', 'transaction_checks_view.status', value);
        }
      }
    }
  ]
  );

  function clearCocReportDatagridDateFilter (value)
  {
      $('#CocReportDatagridFromDate').datebox('reset');
      $('#CocReportDatagridToDate').datebox('reset');

      $('#CocReportDatagrid').datagrid('removeFilterRule', 'transactions.date_time');

      if (value != '') {
          $('#CocReportDatagrid').datagrid('addFilterRule', {
              field: 'transactions.date_time',
              value: value
          });
      }

      $('#CocReportDatagrid').datagrid('doFilter');
  }

  function applyCocReportDatagridDateFilter ()
  {
      var dateFromText = $('#CocReportDatagridFromDate').datebox('getValue');
      dateFrom = $.fn.datebox.defaults.parser(dateFromText);
      dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

      var dateToText = $('#CocReportDatagridToDate').datebox('getValue');
      dateTo = $.fn.datebox.defaults.parser(dateToText);
      dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

      if (dateFromText == '' &&  dateToText == ''){
          $('#CocReportDatagrid').datagrid('removeFilterRule', 'transactions.date_time');
      } else if (dateFromText == '') {
          $('#CocReportDatagrid').datagrid('addFilterRule', {
              field: 'transactions.date_time',
              op: 'less',
              value: dateTo
          });
      } else if (dateToText == '') {
          $('#CocReportDatagrid').datagrid('addFilterRule', {
              field: 'transactions.date_time',
              op: 'greater',
              value: dateFrom
          });
      } else {
          $('#CocReportDatagrid').datagrid('addFilterRule', {
              field: 'transactions.date_time',
              op: 'dateTimeBetween',
              value: dateFrom + ',' + dateTo
          });
      }
      $('#CocReportDatagrid').datagrid('doFilter');
  }

  function printCocTransactionsFromCocReportDatagrid()
  {
    sendCocDataGridFiltersToServer('{!! \URL::route('print_coc_transactions') !!}?')
  }

  function printFromCocReportDatagrid()
  {
    sendCocDataGridFiltersToServer('{!! \URL::route('print_coc_report') !!}?')
  }

  function downloadFromCocReportDatagrid()
  {
    sendCocDataGridFiltersToServer('{!! \URL::route('download_coc_report') !!}?')
  }

  function sendCocDataGridFiltersToServer(url)
  {
    var cocReportDatagridOptions = $('#CocReportDatagrid').datagrid('options');
    var filterRules = cocReportDatagridOptions.filterRules;
    var sort = cocReportDatagridOptions.sortName;
    var order = cocReportDatagridOptions.sortOrder;

    var col = $('#CocReportDatagrid').datagrid('getColumnOption', sort);

    if (col && col.as) {
      sort = col.as;
    }

    var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

    if (sort) {
      queryStrings += '&sort=' + sort + '&order=' + order;
    }

    print(url + queryStrings);

    //sendPrintCommandToServer(urlToPrint + queryStrings , 'A4', ' -o Landscape');
  }

</script>
