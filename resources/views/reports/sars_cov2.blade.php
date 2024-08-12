
<div class="easyui-layout" fit="true">

  <div data-options="region:'center',border:false" >
    <table id="SarsCov2ReportDatagrid"></table>
  </div>

  <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

      <div class="easyui-panel"  id="SarsCov2ReportFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options="
                                        {{-- footer: '#SarsCov2ReportDateFilterPanelFooter', --}}
                                        title: 'Filters'
                                    ">

              <div>
                <label >Result:</label>

                <input value="All"
                        style="width:223px;"
                        class="easyui-combobox"
                        data-options="
                            panelHeight: 'auto',
                            valueField: 'value',
                            textField: 'label',
                            data: [
                                { 'label': 'All', 'value': 'All'},
                                { 'label': 'Pending', 'value': ''},
                                { 'label': 'Positive', 'value': '1'},
                                { 'label': 'Negative', 'value': '0'}
                            ],

                            onSelect: function(result) {
                                applySarsCov2ReportDatagridResultFilter(result);
                            }
                        "
                        />
              </div>

      </div>

      <div class="easyui-panel"  id="SarsCov2ReportPaymentDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options="title: 'Payment Date'">

              <label >From:</label>
              <input id="SarsCov2ReportDatagridFromPaymentDate" type="text" class="easyui-datebox" style="width:123px;">

              <label >To:</label>
              <input id="SarsCov2ReportDatagridToPaymentDate"  type="text" class="easyui-datebox" style="width:123px;">

              <div style="text-align:center; margin-top: 10px;">
                  <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applySarsCov2ReportDatagridPaymentDateFilter()">Apply</a>
                  <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearSarsCov2ReportDatagridPaymentDateFilter()">Clear</a>
              </div>
      </div>

      <div class="easyui-panel"  id="SarsCov2ReportDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options="
                                        {{-- footer: '#SarsCov2ReportDateFilterPanelFooter', --}}
                                        title: 'Result Date'
                                    ">

              <label >From:</label>
              <input id="SarsCov2ReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

              <label >To:</label>
              <input id="SarsCov2ReportDatagridToDate"  type="text" class="easyui-datebox" style="width:123px;">

              <div style="text-align:center; margin-top: 10px;">
                  <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applySarsCov2ReportDatagridDateFilter()">Apply</a>
                  <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearSarsCov2ReportDatagridDateFilter()">Clear</a>
              </div>
      </div>
      <div id="SarsCov2ReportDateFilterPanelFooter" >
      </div>

    <table id="sars_cov2_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
    </table>
  </div>

</div>

<div id="SarsCov2ReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#SarsCov2ReportDatagrid').datagrid('reload')">Reload</a>
    @can('print_sars_cov2_report')
      <a href="#" class="easyui-linkbutton" iconCls="icon-print"  onclick="javascript:printFromSarsCov2ReportDatagrid();">SARS COV-2</a>
      <a href="#" class="easyui-linkbutton" iconCls="icon-print"  onclick="javascript:printSpeedReportFromSarsCov2ReportDatagrid();">SARS COV-2 Speed Report</a>
    @endcan
</div>


<script type="text/javascript">

  $(function(){

    $('#SarsCov2ReportDatagrid').datagrid({
      idField:'id',
      title: 'SARS COV-2 Report',
      toolbar:'#SarsCov2ReportDatagridToolbar',
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
      url:'reports/sars_cov2/list',

      columns: [[
        { field:'id', as:'swabs.id', title:'Id', width:10, align:'center' },
        { field:'person_name', as: 'people.name' , title:'Name', width:20, align:'left' },
        { field:'passport_number', as: 'people.passport_number' , title:'Passport', width:10, align:'center' },
        { field:'payment_date', as: 'swabs.created_at' , title:'Payment Date', width:10, align:'center' },
        { field:'result_date', as: 'swabs.date' , title:'Result Date', width:10, align:'center' },
        { field:'result', as: 'swabs.result' , title:'Result', width:10, align:'center',
          formatter: function(val,row,index){
            if(val === 0) {
              return 'Negative';
            } else if (val === 1) {
              return 'Positive';
            }
            return 'Pending';
          }
        },
        { field:'paid_amount', as: 'swabs.paid_amount' , title:'Paid Amount', width:10, align:'center' }
      ]],

        onLoadSuccess: function(data)
        {
            $('#sars_cov2_report_propertygrid').propertygrid('loadData', data.stat_rows );
        }
    });
  });

  $('#SarsCov2ReportDatagrid').datagrid('enableFilter', []);
  $('#SarsCov2ReportDatagrid').datagrid('getPanel').find('tr.datagrid-filter-row').hide();
  $('#SarsCov2ReportDatagrid').datagrid('resize');


  function clearSarsCov2ReportDatagridPaymentDateFilter (value)
  {
      $('#SarsCov2ReportDatagridFromPaymentDate').datebox('reset');
      $('#SarsCov2ReportDatagridToPaymentDate').datebox('reset');

      $('#SarsCov2ReportDatagrid').datagrid('removeFilterRule', 'swabs.created_at');

      if (value != '') {
          $('#SarsCov2ReportDatagrid').datagrid('addFilterRule', {
              field: 'swabs.created_at',
              value: value
          });
      }

      $('#SarsCov2ReportDatagrid').datagrid('doFilter');
  }

  function applySarsCov2ReportDatagridPaymentDateFilter ()
  {
      var dateFromText = $('#SarsCov2ReportDatagridFromPaymentDate').datebox('getValue');
      dateFrom = $.fn.datebox.defaults.parser(dateFromText);
      dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

      var dateToText = $('#SarsCov2ReportDatagridToPaymentDate').datebox('getValue');
      dateTo = $.fn.datebox.defaults.parser(dateToText);
      dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

      if (dateFromText == '' &&  dateToText != ''
          || dateFromText != '' &&  dateToText == '') {

          $.messager.show({ title: 'Error', msg: 'You have to select both payment dates!'});
          return;
      }

      if (dateFromText == '' &&  dateToText == '') {
          $('#SarsCov2ReportDatagrid').datagrid('removeFilterRule', 'swabs.created_at');

      } else {
          $('#SarsCov2ReportDatagrid').datagrid('addFilterRule', {
              field: 'swabs.created_at',
              op: 'dateBetween',
              value: dateFrom + ',' + dateTo
          });
      }
      $('#SarsCov2ReportDatagrid').datagrid('doFilter');
  }

  function clearSarsCov2ReportDatagridDateFilter (value)
  {
      $('#SarsCov2ReportDatagridFromDate').datebox('reset');
      $('#SarsCov2ReportDatagridToDate').datebox('reset');

      $('#SarsCov2ReportDatagrid').datagrid('removeFilterRule', 'swabs.date');

      if (value != '') {
          $('#SarsCov2ReportDatagrid').datagrid('addFilterRule', {
              field: 'swabs.date',
              value: value
          });
      }

      $('#SarsCov2ReportDatagrid').datagrid('doFilter');
  }

  function applySarsCov2ReportDatagridDateFilter ()
  {
      var dateFromText = $('#SarsCov2ReportDatagridFromDate').datebox('getValue');
      dateFrom = $.fn.datebox.defaults.parser(dateFromText);
      dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

      var dateToText = $('#SarsCov2ReportDatagridToDate').datebox('getValue');
      dateTo = $.fn.datebox.defaults.parser(dateToText);
      dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

      if (dateFromText == '' &&  dateToText != ''
          || dateFromText != '' &&  dateToText == '') {

          $.messager.show({ title: 'Error', msg: 'You have to select both dates!'});
          return;
      }

      if (dateFromText == '' &&  dateToText == '') {
          $('#SarsCov2ReportDatagrid').datagrid('removeFilterRule', 'swabs.date');

      } else {
          $('#SarsCov2ReportDatagrid').datagrid('addFilterRule', {
              field: 'swabs.date',
              op: 'dateBetween',
              value: dateFrom + ',' + dateTo
          });
      }
      $('#SarsCov2ReportDatagrid').datagrid('doFilter');
  }

  function applySarsCov2ReportDatagridResultFilter(result)
  {
      if (result.value == 'All') {
          $('#SarsCov2ReportDatagrid').datagrid('removeFilterRule', 'swabs.result');
      } else {
          $('#SarsCov2ReportDatagrid').datagrid('addFilterRule', {
              field: 'swabs.result',
              value: result.value
          });
      }

      $('#SarsCov2ReportDatagrid').datagrid('doFilter');
  }


    function printFromSarsCov2ReportDatagrid()
    {
      var sars_cov2ReportDatagridOptions = $('#SarsCov2ReportDatagrid').datagrid('options');
      var filterRules = sars_cov2ReportDatagridOptions.filterRules;
      var sort = sars_cov2ReportDatagridOptions.sortName;
      var order = sars_cov2ReportDatagridOptions.sortOrder;

      var col = $('#SarsCov2ReportDatagrid').datagrid('getColumnOption', sort);

      if (col && col.as) {
        sort = col.as;
      }

      var urlToPrint = '{!! \URL::route('print_sars_cov2_report') !!}?';

      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

      if (sort) {
        queryStrings += '&sort=' + sort + '&order=' + order;
      }

      print(urlToPrint + queryStrings);

      //sendPrintCommandToServer(urlToPrint + queryStrings , 'A4', ' -o Landscape');
    }

    function printSpeedReportFromSarsCov2ReportDatagrid()
    {
      var sarsCov2ReportDatagridOptions = $('#SarsCov2ReportDatagrid').datagrid('options');
      var filterRules = sarsCov2ReportDatagridOptions.filterRules;
      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

        var urlToPrint = '{!! \URL::route('print_sars_cov2_speed_report') !!}?';
        print(urlToPrint + queryStrings);
    }

</script>
