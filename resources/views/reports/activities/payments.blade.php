
<div class="easyui-layout" fit="true">

  <div data-options="region:'center',border:false" >
    <table id="PaymentReportDatagrid"></table>
  </div>

  <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

      <div class="easyui-panel"  id="PaymentReportDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options="
                                        {{-- footer: '#PaymentReportDateFilterPanelFooter', --}}
                                        title: 'Date'
                                    ">

              <label >From:</label>
              <input id="PaymentReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

              <label >To:</label>
              <input id="PaymentReportDatagridToDate"  type="text" class="easyui-datebox" style="width:123px;">

              <div style="text-align:center; margin-top: 10px;">
                  <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applyPaymentReportDatagridDateFilter()">Apply</a>
                  <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearPaymentReportDatagridDateFilter()">Clear</a>
              </div>
      </div>
      <div id="PaymentReportDateFilterPanelFooter" >
      </div>

    <table id="payment_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
    </table>
  </div>

</div>

<div id="PaymentUpdateHistoryDialog"
      class="easyui-dialog"
      style="width:1000px;height:500px;padding:10px 10px"
      title="Test Update History" closed="true" modal="true">


<div id="PaymentReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#PaymentReportDatagrid').datagrid('reload')">Reload</a>

    @can('print_payments_activity_report')
      <a href="#" class="easyui-linkbutton" iconcls="icon-print"  onclick="javascript:printPaymentActivityReport();">print</a>
    @endcan
</div>


<script type="text/javascript">

  $(function(){
    $('#PaymentReportDatagrid').datagrid({
      idField:'id',
      title: 'Payments Activities',
      toolbar:'#PaymentReportDatagridToolbar',
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
      url:'/reports/activities/payments/list',
      onDblClickRow:function(index,row){
        var expander = $(this).datagrid('getExpander', index);
        if (expander.hasClass('datagrid-row-expand')){
          $(this).datagrid('expandRow',index);
        }else{
          $(this).datagrid('collapseRow',index);
        }
      },

      columns: [[
        { field:'update_count' , as:'payments.update_count'       , title:'Update Count'       , sortable: false , width:10   , align:'center',

          formatter: function(val, row, index) {
            var color = val >= 2? "style=\"color: red;\"" : "";
            return '<button ' + color +' onclick=" $(\'#PaymentReportDatagrid\').datagrid(\'expandRow\', ' + index + '); ">' + val + '</button>';
          },
        } ,
        { field:'payment_id'     , as:'payments.id'                 , title:'Payment Id'                 , sortable: true , width:15 } ,
        { field:'paid_amount'     , as:'payments.amount'                 , title:'Paid Amount'                 , sortable: true , width:15 } ,
        { field:'user_name'    , as:'users.kurdish_name'        , title:'User'               , sortable: true , width:30 } ,
        { field:'transaction_id' , as:'transactions.id'      , title:'Transaction Id'            , sortable: true , width:30   , align:'center' } ,
        { field:'product_type' , as:'transactions.product_type'      , title:'P. Type'            , sortable: true , width:30   , align:'center' } ,
        { field:'product_name' , as:'products.kurdish_name'     , title:'Product'            , sortable: true , width:30 } ,
        { field:'created_at'   , as:'payments.created_at'        , title:'Created At' , sortable: true , width:30   , align:'center' }
      ]],

      onLoadSuccess: function(data)
      {
        $('#payment_report_propertygrid').propertygrid('loadData', data.stat_rows );
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
            $('#PaymentReportDatagrid').datagrid('fixDetailRowHeight',index);
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
        {{--   {{-1- url:'reports/activities/payments/listActivities'+row.itemid, -1-}} --}}
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
        {{--     $('#PaymentReportDatagrid').datagrid('fixDetailRowHeight',index); --}}
        {{--   }, --}}
        {{--   onLoadSuccess:function(){ --}}
        {{--     setTimeout(function(){ --}}
        {{--       $('#PaymentReportDatagrid').datagrid('fixDetailRowHeight',index); --}}
        {{--     },0); --}}
        {{--   } --}}
        {{-- }); --}}
        {{-- ddv.datagrid('loadData',row.updates); --}}
        $('#PaymentReportDatagrid').datagrid('fixDetailRowHeight',index);
      }
    });
  });

  $('#PaymentReportDatagrid').datagrid('enableFilter', [
    {
      field:'update_count',
      type:'label'
    },
  ]
  );

  function clearPaymentReportDatagridDateFilter (value)
  {
      $('#PaymentReportDatagridFromDate').datebox('reset');
      $('#PaymentReportDatagridToDate').datebox('reset');

      $('#PaymentReportDatagrid').datagrid('removeFilterRule', 'payments.created_at');

      if (value != '') {
          $('#PaymentReportDatagrid').datagrid('addFilterRule', {
              field: 'payments.created_at',
              value: value
          });
      }

      $('#PaymentReportDatagrid').datagrid('doFilter');
  }

  function applyPaymentReportDatagridDateFilter ()
  {
      var dateFromText = $('#PaymentReportDatagridFromDate').datebox('getValue');
      dateFrom = $.fn.datebox.defaults.parser(dateFromText);
      dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

      var dateToText = $('#PaymentReportDatagridToDate').datebox('getValue');
      dateTo = $.fn.datebox.defaults.parser(dateToText);
      dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

      if (dateFromText == '' &&  dateToText == ''){
          $('#PaymentReportDatagrid').datagrid('removeFilterRule', 'payments.created_at');
      } else if (dateFromText == '') {
          $('#PaymentReportDatagrid').datagrid('addFilterRule', {
              field: 'payments.created_at',
              op: 'less',
              value: dateTo
          });
      } else if (dateToText == '') {
          $('#PaymentReportDatagrid').datagrid('addFilterRule', {
              field: 'payments.created_at',
              op: 'greater',
              value: dateFrom
          });
      } else {
          $('#PaymentReportDatagrid').datagrid('addFilterRule', {
              field: 'payments.created_at',
              op: 'dateTimeBetween',
              value: dateFrom + ',' + dateTo
          });
      }
      $('#PaymentReportDatagrid').datagrid('doFilter');
  }


    function printPaymentActivityReport()
    {
      var dg = $('#PaymentReportDatagrid').datagrid('options');
      var filterRules = dg.filterRules;
      var sort = dg.sortName;
      var order = dg.sortOrder;

      var col = $('#PaymentReportDatagrid').datagrid('getColumnOption', sort);

      if (col && col.as) {
        sort = col.as;
      }

      var urlToPrint = '{!! \URL::route('print_payments_activity_report') !!}?';

      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

      if (sort) {
        queryStrings += '&sort=' + sort + '&order=' + order;
      }

      print(urlToPrint + queryStrings);

      //sendPrintCommandToServer(urlToPrint + queryStrings , 'A4', ' -o Landscape');
    }
</script>
