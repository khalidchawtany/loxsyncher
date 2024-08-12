
<div class="easyui-layout" fit="true">

  <div data-options="region:'center',border:false" >
    <table id="TransactionReportDatagrid"></table>
  </div>

  <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

      <div class="easyui-panel"  id="TransactionReportDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options="
                                        {{-- footer: '#TransactionReportDateFilterPanelFooter', --}}
                                        title: 'Date'
                                    ">

              <label >From:</label>
              <input id="TransactionReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

              <label >To:</label>
              <input id="TransactionReportDatagridToDate"  type="text" class="easyui-datebox" style="width:123px;">

              <div style="text-align:center; margin-top: 10px;">
                  <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applyTransactionReportDatagridDateFilter()">Apply</a>
                  <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearTransactionReportDatagridDateFilter()">Clear</a>
              </div>
      </div>
      <div id="TransactionReportDateFilterPanelFooter" >
      </div>

    <table id="transaction_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
    </table>
  </div>

</div>

<div id="TransactionUpdateHistoryDialog"
      class="easyui-dialog"
      style="width:1000px;height:500px;padding:10px 10px"
      title="Test Update History" closed="true" modal="true">


<div id="TransactionReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#TransactionReportDatagrid').datagrid('reload')">Reload</a>

    @can('print_transactions_activity_report')
      <a href="#" class="easyui-linkbutton" iconcls="icon-print"  onclick="javascript:printTransactionActivityReport();">print</a>
    @endcan
</div>


<script type="text/javascript">

  $(function(){
    $('#TransactionReportDatagrid').datagrid({
      idField:'id',
      title: 'Transactions Activities',
      toolbar:'#TransactionReportDatagridToolbar',
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
      view: detailview,
      url:'/reports/activities/transactions/list',
      onDblClickRow:function(index,row){
        var expander = $(this).datagrid('getExpander', index);
        if (expander.hasClass('datagrid-row-expand')){
          $(this).datagrid('expandRow',index);
        }else{
          $(this).datagrid('collapseRow',index);
        }
      },

      columns: [[
        { field:'update_count' , as:'transactions.update_count'       , title:'Update Count'       , sortable: false , width:10   , align:'center',

          formatter: function(val, row, index) {
            var color = val >= 3? "style=\"color: red;\"" : "";
            return '<button ' + color +' onclick=" $(\'#TransactionReportDatagrid\').datagrid(\'expandRow\', ' + index + '); ">' + val + '</button>';
          },
        } ,
        { field:'transaction_id'     , as:'transactions.id'                 , title:'Transaction Id'                 , sortable: true , width:15 } ,
        { field:'user_name'    , as:'users.kurdish_name'        , title:'User'               , sortable: true , width:30 } ,
        { field:'product_type' , as:'transactions.product_type'      , title:'P. Type'            , sortable: true , width:30   , align:'center' } ,
        { field:'product_name' , as:'products.kurdish_name'     , title:'Product'            , sortable: true , width:30 } ,
        { field:'created_at'   , as:'transactions.created_at'        , title:'Created At' , sortable: true , width:30   , align:'center' }
      ]],

      onLoadSuccess: function(data)
      {
        $('#transaction_report_propertygrid').propertygrid('loadData', data.stat_rows );
      },
      detailFormatter:function(index,row){
        return '<div style="padding:2px;position:relative;"><table class="ddv has_border" style="font-size: 14px;"></table></div>';
      },
      onExpandRow: function(index,row){
        var ddv = $(this).datagrid('getRowDetail',index).find('table.ddv');
        ddv.panel({
          border:true,
          cache:false,
          onLoad:function(){
            $('#TransactionReportDatagrid').datagrid('fixDetailRowHeight',index);
          }
        });
        ddv.html("");
        ddv.append('<tr> <th>User</th> <th>Date</th> <th>Old</th> <th>New</th> <th>Diff</th> </tr>')
        row.updates.forEach(function(update){
          var props = JSON.parse(update.props);
          var newData = "";
          var oldData = "";
          if (props.attributes)
            newData = JSON.stringify(props.attributes, null, 2).replace(/,/g, '</br>').replace(/"/g, "").replace(/}/g, "").replace(/{/,"");
          if (props.old)
            oldData = JSON.stringify(props.old, null, 2).replace(/,/g, '</br>').replace(/"/g, "").replace(/}/g, "").replace(/{/,"");

          var diff=newData

          if (props.attributes && props.old) {
            diff = diffFilter(props.old, props.attributes);
            diff  =JSON.stringify(diff, null, 2).replace(/,/g, '</br>').replace(/"/g, "").replace(/}/g, "").replace(/{/,"");
          }
          ddv.append('<tr><td style="text-align: center;">' + update.user_name +  '</td>  <td style="text-align: center;">' + update.date +  '</td>  <td>' + oldData +  '</td> <td>' + newData +  '</td><td>' + diff +  '</td></tr>')
        });

        {{-- ddv.datagrid({ --}}
        {{--   {{-1- url:'reports/activities/transactions/listActivities'+row.itemid, -1-}} --}}
        {{--   fitColumns:true, --}}
        {{--   singleSelect:true, --}}
        {{--   rownumbers:true, --}}
        {{--   loadMsg:'', --}}
        {{--   height:'auto', --}}
        {{--   columns:[[ --}}
        {{--     {field:'date',title:'Date',width:10}, --}}
        {{--     {field:'user_name',title:'User',width:10,align:'center'}, --}}
        {{--     {field:'old',title:'Old',width:100,align:'left', --}}
        {{--       formatter: function(val, row) { --}}
        {{--         return JSON.stringify(val, null, 4); --}}
        {{--       }, --}}
        {{--     }, --}}
        {{--     {field:'new',title:'New',width:100,align:'left', --}}
        {{--       formatter: function(val, row) { --}}
        {{--         return JSON.stringify(val, null, 4); --}}
        {{--       }, --}}
        {{--     } --}}
        {{--   ]], --}}
        {{--   onResize:function(){ --}}
        {{--     $('#TransactionReportDatagrid').datagrid('fixDetailRowHeight',index); --}}
        {{--   }, --}}
        {{--   onLoadSuccess:function(){ --}}
        {{--     setTimeout(function(){ --}}
        {{--       $('#TransactionReportDatagrid').datagrid('fixDetailRowHeight',index); --}}
        {{--     },0); --}}
        {{--   } --}}
        {{-- }); --}}
        {{-- ddv.datagrid('loadData',row.updates); --}}
        $('#TransactionReportDatagrid').datagrid('fixDetailRowHeight',index);
      }
    });
  });

  $('#TransactionReportDatagrid').datagrid('enableFilter', [
    {
      field:'update_count',
      type:'label'
    },
  ]
  );

  function clearTransactionReportDatagridDateFilter (value)
  {
      $('#TransactionReportDatagridFromDate').datebox('reset');
      $('#TransactionReportDatagridToDate').datebox('reset');

      $('#TransactionReportDatagrid').datagrid('removeFilterRule', 'transactions.created_at');

      if (value != '') {
          $('#TransactionReportDatagrid').datagrid('addFilterRule', {
              field: 'transactions.created_at',
              value: value
          });
      }

      $('#TransactionReportDatagrid').datagrid('doFilter');
  }

  function applyTransactionReportDatagridDateFilter ()
  {
      var dateFromText = $('#TransactionReportDatagridFromDate').datebox('getValue');
      dateFrom = $.fn.datebox.defaults.parser(dateFromText);
      dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

      var dateToText = $('#TransactionReportDatagridToDate').datebox('getValue');
      dateTo = $.fn.datebox.defaults.parser(dateToText);
      dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

      if (dateFromText == '' &&  dateToText == ''){
          $('#TransactionReportDatagrid').datagrid('removeFilterRule', 'transactions.created_at');
      } else if (dateFromText == '') {
          $('#TransactionReportDatagrid').datagrid('addFilterRule', {
              field: 'transactions.created_at',
              op: 'less',
              value: dateTo
          });
      } else if (dateToText == '') {
          $('#TransactionReportDatagrid').datagrid('addFilterRule', {
              field: 'transactions.created_at',
              op: 'greater',
              value: dateFrom
          });
      } else {
          $('#TransactionReportDatagrid').datagrid('addFilterRule', {
              field: 'transactions.created_at',
              op: 'dateTimeBetween',
              value: dateFrom + ',' + dateTo
          });
      }
      $('#TransactionReportDatagrid').datagrid('doFilter');
  }

    function printTransactionActivityReport()
    {
      var dg = $('#TransactionReportDatagrid').datagrid('options');
      var filterRules = dg.filterRules;
      var sort = dg.sortName;
      var order = dg.sortOrder;

      var col = $('#TransactionReportDatagrid').datagrid('getColumnOption', sort);

      if (col && col.as) {
        sort = col.as;
      }

      var urlToPrint = '{!! \URL::route('print_transactions_activity_report') !!}?';

      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

      if (sort) {
        queryStrings += '&sort=' + sort + '&order=' + order;
      }

      print(urlToPrint + queryStrings);

      //sendPrintCommandToServer(urlToPrint + queryStrings , 'A4', ' -o Landscape');
    }

</script>
