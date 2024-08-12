
<div class="easyui-layout" fit="true">

  <div data-options="region:'center',border:false" >
    <table id="CustomsReportDatagrid"></table>
  </div>

  <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

      <div class="easyui-panel"  id="CustomsReportDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options="
                                        {{-- footer: '#CustomsReportDateFilterPanelFooter', --}}
                                        title: 'Date'
                                    ">

              <label >From:</label>
              <input id="CustomsReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

              <label >To:</label>
              <input id="CustomsReportDatagridToDate"  type="text" class="easyui-datebox" style="width:123px;">

              <div style="text-align:center; margin-top: 10px;">
                  <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applyCustomsReportDatagridDateFilter()">Apply</a>
                  <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearCustomsReportDatagridDateFilter()">Clear</a>
              </div>
      </div>
      <div id="CustomsReportDateFilterPanelFooter" >
      </div>

    <table id="customs_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
    </table>
  </div>

</div>

<div id="CustomsReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#CustomsReportDatagrid').datagrid('reload')">Reload</a>
    @can('print_customs_report')
      <a href="#" class="easyui-linkbutton" iconCls="icon-print"  onclick="javascript:printFromCustomsReportDatagrid();">Print</a>
    @endcan
</div>


<script type="text/javascript">

  $(function(){
    $('#CustomsReportDatagrid').datagrid({
      idField:'id',
      title: 'Customs Report',
      toolbar:'#CustomsReportDatagridToolbar',
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
      url:'reports/customs/list',

        columns: [[

            { field:'invoice_number', as: 'payments.invoice_number', title:'Invoice Number', sortable: true, width:30, align:'center' },
            { field:'paid_amount', as: 'payments.amount', title:'Paid Amount', sortable: true, width:30, align:'center' },
            { field:'plate', as: 'trucks().plate', title:'Plate', sortable: true, width:30, align:'center' },
            { field:'product_name', as: 'products.kurdish_name', title:'Product', sortable: true, width:30 },
            { field:'product_type', as: 'transactions.product_type', title:'P. Type', sortable: true, width:30, align:'center' },
            { field:'office_name', as: 'offices.name', title:'Office', sortable: true, width:30, align:'center' },
          ]],

        onLoadSuccess: function(data)
        {
            $('#customs_report_propertygrid').propertygrid('loadData', data.stat_rows );
        }
    });
  });

  $('#CustomsReportDatagrid').datagrid('enableFilter', [
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
          onChangeDatagridFilterConrols('CustomsReportDatagrid', 'transaction_checks_view.status', value);
        }
      }
    }
  ]
  );

  function clearCustomsReportDatagridDateFilter (value)
  {
      $('#CustomsReportDatagridFromDate').datebox('reset');
      $('#CustomsReportDatagridToDate').datebox('reset');

      $('#CustomsReportDatagrid').datagrid('removeFilterRule', 'payments.date_time');

      if (value != '') {
          $('#CustomsReportDatagrid').datagrid('addFilterRule', {
              field: 'payments.date_time',
              value: value
          });
      }

      $('#CustomsReportDatagrid').datagrid('doFilter');
  }

  function applyCustomsReportDatagridDateFilter ()
  {
      var dateFromText = $('#CustomsReportDatagridFromDate').datebox('getValue');
      dateFrom = $.fn.datebox.defaults.parser(dateFromText);
      dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

      var dateToText = $('#CustomsReportDatagridToDate').datebox('getValue');
      dateTo = $.fn.datebox.defaults.parser(dateToText);
      dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

      if (dateFromText == '' &&  dateToText == ''){
          $('#CustomsReportDatagrid').datagrid('removeFilterRule', 'payments.date_time');
      } else if (dateFromText == '') {
          $('#CustomsReportDatagrid').datagrid('addFilterRule', {
              field: 'payments.date_time',
              op: 'less',
              value: dateTo
          });
      } else if (dateToText == '') {
          $('#CustomsReportDatagrid').datagrid('addFilterRule', {
              field: 'payments.date_time',
              op: 'greater',
              value: dateFrom
          });
      } else {
          $('#CustomsReportDatagrid').datagrid('addFilterRule', {
              field: 'payments.date_time',
              op: 'dateTimeBetween',
              value: dateFrom + ',' + dateTo
          });
      }
      $('#CustomsReportDatagrid').datagrid('doFilter');
  }

    function printFromCustomsReportDatagrid()
    {
      var customsReportDatagridOptions = $('#CustomsReportDatagrid').datagrid('options');
      var filterRules = customsReportDatagridOptions.filterRules;
      var sort = customsReportDatagridOptions.sortName;
      var order = customsReportDatagridOptions.sortOrder;

      var col = $('#CustomsReportDatagrid').datagrid('getColumnOption', sort);

      if (col && col.as) {
        sort = col.as;
      }

      var urlToPrint = '{!! \URL::route('print_customs_report') !!}?';

      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

      if (sort) {
        queryStrings += '&sort=' + sort + '&order=' + order;
      }

      print(urlToPrint + queryStrings);

      //sendPrintCommandToServer(urlToPrint + queryStrings , 'A4', ' -o Landscape');
    }

</script>
