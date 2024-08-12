
<div class="easyui-layout" fit="true">

  <div data-options="region:'center',border:false" >
    <table id="IncinerationsReportDatagrid"></table>
  </div>

  <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

      <div class="easyui-panel"  id="IncinerationsReportDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options="
                                        {{-- footer: '#IncinerationsReportDateFilterPanelFooter', --}}
                                        title: 'Date'
                                    ">

              <label >From:</label>
              <input id="IncinerationsReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

              <label >To:</label>
              <input id="IncinerationsReportDatagridToDate"  type="text" class="easyui-datebox" style="width:123px;">

              <div style="text-align:center; margin-top: 10px;">
                  <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applyIncinerationsReportDatagridDateFilter()">Apply</a>
                  <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearIncinerationsReportDatagridDateFilter()">Clear</a>
              </div>
      </div>
      <div id="IncinerationsReportDateFilterPanelFooter" >
      </div>

    <table id="incinerations_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
    </table>
  </div>

</div>

<div id="IncinerationsReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#IncinerationsReportDatagrid').datagrid('reload')">Reload</a>
    @can('print_incinerations_report')
      <a href="#" class="easyui-linkbutton" iconCls="icon-print"  onclick="javascript:printFromIncinerationsReportDatagrid();">Print</a>
    @endcan
</div>


<script type="text/javascript">

  $(function(){
    $('#IncinerationsReportDatagrid').datagrid({
      idField:'id',
      title: 'Incinerations Report',
      toolbar:'#IncinerationsReportDatagridToolbar',
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
      url:'reports/incinerations/list',

        columns: [[

            { field:'invoice_date', as: 'incineration_payments.date', title:'Invoice Date', sortable: true, width:30, align:'center' },
            { field:'invoice_number', as: 'incineration_payments.id', title:'Invoice Number', sortable: true, width:30, align:'center' },
            { field:'paid_amount', as: 'incineration_payments.amount', title:'Paid Amount', sortable: true, width:30, align:'center' },
            { field:'product_name', as: 'products.kurdish_name', title:'Product', sortable: true, width:30 },
            { field:'product_type', as: 'transactions.product_type', title:'P. Type', sortable: true, width:30, align:'center' },
            { field:'office_name', as: 'offices.name', title:'Office', sortable: true, width:30, align:'center' },
          ]],

        onLoadSuccess: function(data)
        {
            $('#incinerations_report_propertygrid').propertygrid('loadData', data.stat_rows );
        }
    });
  });

  $('#IncinerationsReportDatagrid').datagrid('enableFilter', [
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
          onChangeDatagridFilterConrols('IncinerationsReportDatagrid', 'transaction_checks_view.status', value);
        }
      }
    }
  ]
  );

  function clearIncinerationsReportDatagridDateFilter (value)
  {
      $('#IncinerationsReportDatagridFromDate').datebox('reset');
      $('#IncinerationsReportDatagridToDate').datebox('reset');

      $('#IncinerationsReportDatagrid').datagrid('removeFilterRule', 'incineration_payments.date');

      if (value != '') {
          $('#IncinerationsReportDatagrid').datagrid('addFilterRule', {
              field: 'incineration_payments.date',
              value: value
          });
      }

      $('#IncinerationsReportDatagrid').datagrid('doFilter');
  }

  function applyIncinerationsReportDatagridDateFilter ()
  {
      var dateFromText = $('#IncinerationsReportDatagridFromDate').datebox('getValue');
      dateFrom = $.fn.datebox.defaults.parser(dateFromText);
      dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

      var dateToText = $('#IncinerationsReportDatagridToDate').datebox('getValue');
      dateTo = $.fn.datebox.defaults.parser(dateToText);
      dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

      if (dateFromText == '' &&  dateToText == ''){
          $('#IncinerationsReportDatagrid').datagrid('removeFilterRule', 'incineration_payments.date');
      } else if (dateFromText == '') {
          $('#IncinerationsReportDatagrid').datagrid('addFilterRule', {
              field: 'incineration_payments.date',
              op: 'less',
              value: dateTo
          });
      } else if (dateToText == '') {
          $('#IncinerationsReportDatagrid').datagrid('addFilterRule', {
              field: 'incineration_payments.date',
              op: 'greater',
              value: dateFrom
          });
      } else {
          $('#IncinerationsReportDatagrid').datagrid('addFilterRule', {
              field: 'incineration_payments.date',
              op: 'dateTimeBetween',
              value: dateFrom + ',' + dateTo
          });
      }
      $('#IncinerationsReportDatagrid').datagrid('doFilter');
  }

    function printFromIncinerationsReportDatagrid()
    {
      var incinerationsReportDatagridOptions = $('#IncinerationsReportDatagrid').datagrid('options');
      var filterRules = incinerationsReportDatagridOptions.filterRules;
      var sort = incinerationsReportDatagridOptions.sortName;
      var order = incinerationsReportDatagridOptions.sortOrder;

      var col = $('#IncinerationsReportDatagrid').datagrid('getColumnOption', sort);

      if (col && col.as) {
        sort = col.as;
      }

      var urlToPrint = '{!! \URL::route('print_incinerations_report') !!}?';

      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

      if (sort) {
        queryStrings += '&sort=' + sort + '&order=' + order;
      }

      print(urlToPrint + queryStrings);

      //sendPrintCommandToServer(urlToPrint + queryStrings , 'A4', ' -o Landscape');
    }

</script>
