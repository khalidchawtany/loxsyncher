
<div class="easyui-layout" fit="true">

  <div data-options="region:'center',border:false" >
    <table id="GoodsReportDatagrid"></table>
  </div>

  <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

      <div class="easyui-panel"  id="GoodsReportFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options="
                                        {{-- footer: '#GoodsReportDateFilterPanelFooter', --}}
                                        title: 'Filters'
                                    ">

              <div style="margin-top: 20px;">
                <label >Sector:</label>
                <input id="GoodsReportDatagridDepartment" type="combobox" class="easyui-combobox" style="width:223px;"
                    data-options="
                        url:'reports/departments/list',
                        method:'get',
                        valueField: 'department_id',
                        textField: 'department_name',
                        limitToList: true,
                        hasDownArrow: true,
                        panelHeight:'auto',
                        required:true,
                        onSelect: function(department) {
                            applyGoodsReportDatagridDepartmentFilter(department);
                        }
                    "
                >
              </div>

              <div style="margin-top: 20px;">
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
                                { 'label': 'Passed', 'value': 'Passed'},
                                { 'label': 'Failed', 'value': 'Failed'}
                            ],

                            onSelect: function(status) {
                                applyGoodsReportDatagridStatusFilter(status);
                            }
                        "
                        />
              </div>

      </div>
      <div id="GoodsReportFilterPanelFooter" >
      </div>

      <div class="easyui-panel"  id="GoodsReportDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options="
                                        {{-- footer: '#GoodsReportDateFilterPanelFooter', --}}
                                        title: 'Inspection Date'
                                    ">

              <label >From:</label>
              <input id="GoodsReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

              <label >To:</label>
              <input id="GoodsReportDatagridToDate"  type="text" class="easyui-datebox" style="width:123px;">

              <div style="text-align:center; margin-top: 10px;">
                  <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applyGoodsReportDatagridDateFilter()">Apply</a>
                  <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearGoodsReportDatagridDateFilter()">Clear</a>
              </div>
      </div>
      <div id="GoodsReportDateFilterPanelFooter" >
      </div>

    <table id="goods_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
    </table>
  </div>

</div>

<div id="GoodsReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#GoodsReportDatagrid').datagrid('reload')">Reload</a>
    @can('print_goods_report')
      <a href="#" class="easyui-linkbutton" iconCls="icon-print"  onclick="javascript:printFromGoodsReportDatagrid();">Products</a>
    @endcan
</div>


<script type="text/javascript">

  $(function(){

    $('#GoodsReportDatagrid').datagrid({
      idField:'id',
      title: 'Products Report',
      toolbar:'#GoodsReportDatagridToolbar',
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
      url:'reports/goods/list',

        columns: [[

            { field:'department_name', title:'Sector', width:20, align:'center' },
            { field:'product_name', title:'Product', width:20, align:'center' },
            { field:'product_count', title:'Count', width:30, align:'center' },
            { field:'amount_sum', title:'Amount', width:20, align:'center' },
            { field:'amount_unit', title:'Amount (Unit)', width:20, align:'center' }
          ]],

        onLoadSuccess: function(data)
        {
            $('#goods_report_propertygrid').propertygrid('loadData', data.stat_rows );
        }
    });
  });

  $('#GoodsReportDatagrid').datagrid('enableFilter', []);
  $('#GoodsReportDatagrid').datagrid('getPanel').find('tr.datagrid-filter-row').hide();
  $('#GoodsReportDatagrid').datagrid('resize');

  function clearGoodsReportDatagridDateFilter (value)
  {
      $('#GoodsReportDatagridFromDate').datebox('reset');
      $('#GoodsReportDatagridToDate').datebox('reset');

      $('#GoodsReportDatagrid').datagrid('removeFilterRule', 'transactions.date_time');

      if (value != '') {
          $('#GoodsReportDatagrid').datagrid('addFilterRule', {
              field: 'transactions.date_time',
              value: value
          });
      }

      $('#GoodsReportDatagrid').datagrid('doFilter');
  }

  function applyGoodsReportDatagridDateFilter ()
  {
      var dateFromText = $('#GoodsReportDatagridFromDate').datebox('getValue');
      dateFrom = $.fn.datebox.defaults.parser(dateFromText);
      dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

      var dateToText = $('#GoodsReportDatagridToDate').datebox('getValue');
      dateTo = $.fn.datebox.defaults.parser(dateToText);
      dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

      if (dateFromText == '' &&  dateToText != ''
          || dateFromText != '' &&  dateToText == '') {

          $.messager.show({ title: 'Error', msg: 'You have to select both dates!'});
          return;
      }

      if (dateFromText == '' &&  dateToText == '') {
          $('#GoodsReportDatagrid').datagrid('removeFilterRule', 'transactions.date_time');

      } else {
          $('#GoodsReportDatagrid').datagrid('addFilterRule', {
              field: 'transactions.date_time',
              op: 'dateBetween',
              value: dateFrom + ',' + dateTo
          });
      }
      $('#GoodsReportDatagrid').datagrid('doFilter');
  }

  function applyGoodsReportDatagridDepartmentFilter(department) {

      var filterRulesBefore = $('#GoodsReportDatagrid').datagrid('options').filterRules;

      if (department.department_id == 0) {
          $('#GoodsReportDatagrid').datagrid('removeFilterRule', 'department_id');
      } else {
          $('#GoodsReportDatagrid').datagrid('addFilterRule', {
              field: 'department_id',
              value: department.department_id
          });
      }

      var filterRules = $('#GoodsReportDatagrid').datagrid('options').filterRules;

      if (! (filterRules.length == 0 && filterRulesBefore.length == 0 ) ) {
          $('#GoodsReportDatagrid').datagrid('doFilter');
      }
  }

  function applyGoodsReportDatagridStatusFilter(result)
  {
      if (result.value == 'All') {
          $('#GoodsReportDatagrid').datagrid('removeFilterRule', 'transaction_checks_view.status');
      } else {
          $('#GoodsReportDatagrid').datagrid('addFilterRule', {
              field: 'transaction_checks_view.status',
              value: result.value
          });
      }

      $('#GoodsReportDatagrid').datagrid('doFilter');
  }


    function printFromGoodsReportDatagrid()
    {
      var goodsReportDatagridOptions = $('#GoodsReportDatagrid').datagrid('options');
      var filterRules = goodsReportDatagridOptions.filterRules;
      var sort = goodsReportDatagridOptions.sortName;
      var order = goodsReportDatagridOptions.sortOrder;

      var col = $('#GoodsReportDatagrid').datagrid('getColumnOption', sort);

      if (col && col.as) {
        sort = col.as;
      }

      var urlToPrint = '{!! \URL::route('print_goods_report') !!}?';

      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

      if (sort) {
        queryStrings += '&sort=' + sort + '&order=' + order;
      }

      print(urlToPrint + queryStrings);

      //sendPrintCommandToServer(urlToPrint + queryStrings , 'A4', ' -o Landscape');
    }

</script>
