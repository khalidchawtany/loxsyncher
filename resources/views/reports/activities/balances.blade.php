
<div class="easyui-layout" fit="true">

  <div data-options="region:'center',border:false" >
    <table id="BalanceReportDatagrid"></table>
  </div>

  <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

      <div class="easyui-panel"  id="BalanceReportDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options="
                                        {{-- footer: '#BalanceReportDateFilterPanelFooter', --}}
                                        title: 'Date'
                                    ">

              <label >From:</label>
              <input id="BalanceReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

              <label >To:</label>
              <input id="BalanceReportDatagridToDate"  type="text" class="easyui-datebox" style="width:123px;">

              <div style="text-align:center; margin-top: 10px;">
                  <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applyBalanceReportDatagridDateFilter()">Apply</a>
                  <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearBalanceReportDatagridDateFilter()">Clear</a>
              </div>
      </div>
      <div id="BalanceReportDateFilterPanelFooter" >
      </div>

    <table id="balance_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
    </table>
  </div>

</div>

<div id="BalanceUpdateHistoryDialog"
      class="easyui-dialog"
      style="width:1000px;height:500px;padding:10px 10px"
      title="Test Update History" closed="true" modal="true">


<div id="BalanceReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#BalanceReportDatagrid').datagrid('reload')">Reload</a>
</div>


<script type="text/javascript">

  $(function(){
    $('#BalanceReportDatagrid').datagrid({
      idField:'id',
      title: 'Balances Activities',
      toolbar:'#BalanceReportDatagridToolbar',
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
      url:'/reports/activities/balances/list',
      onDblClickRow:function(index,row){
        var expander = $(this).datagrid('getExpander', index);
        if (expander.hasClass('datagrid-row-expand')){
          $(this).datagrid('expandRow',index);
        }else{
          $(this).datagrid('collapseRow',index);
        }
      },

      columns: [[
        { field:'update_count' , as:'balances.update_count'       , title:'Update Count'       , sortable: false , width:10   , align:'center',

          formatter: function(val, row, index) {
            var color = val >= 3? "style=\"color: red;\"" : "";
            return '<button ' + color +' onclick=" $(\'#BalanceReportDatagrid\').datagrid(\'expandRow\', ' + index + '); ">' + val + '</button>';
          },
        } ,
        { field:'balance_id'     , as:'balances.id'                 , title:'Balance Id'                 , sortable: true , width:15 } ,
        { field:'user_name'    , as:'users.kurdish_name'        , title:'User'               , sortable: true , width:30 } ,
        { field:'product_type' , as:'balances.product_type'      , title:'P. Type'            , sortable: true , width:30   , align:'center' } ,
        { field:'product_name' , as:'products.kurdish_name'     , title:'Product'            , sortable: true , width:30 } ,
        { field:'created_at'   , as:'balances.created_at'        , title:'Created At' , sortable: true , width:30   , align:'center' }
      ]],

      onLoadSuccess: function(data)
      {
        $('#balance_report_propertygrid').propertygrid('loadData', data.stat_rows );
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
            $('#BalanceReportDatagrid').datagrid('fixDetailRowHeight',index);
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
        {{--   {{-1- url:'reports/activities/balances/listActivities'+row.itemid, -1-}} --}}
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
        {{--     $('#BalanceReportDatagrid').datagrid('fixDetailRowHeight',index); --}}
        {{--   }, --}}
        {{--   onLoadSuccess:function(){ --}}
        {{--     setTimeout(function(){ --}}
        {{--       $('#BalanceReportDatagrid').datagrid('fixDetailRowHeight',index); --}}
        {{--     },0); --}}
        {{--   } --}}
        {{-- }); --}}
        {{-- ddv.datagrid('loadData',row.updates); --}}
        $('#BalanceReportDatagrid').datagrid('fixDetailRowHeight',index);
      }
    });
  });

  $('#BalanceReportDatagrid').datagrid('enableFilter', [
    {
      field:'update_count',
      type:'label'
    },
  ]
  );

  function clearBalanceReportDatagridDateFilter (value)
  {
      $('#BalanceReportDatagridFromDate').datebox('reset');
      $('#BalanceReportDatagridToDate').datebox('reset');

      $('#BalanceReportDatagrid').datagrid('removeFilterRule', 'balances.created_at');

      if (value != '') {
          $('#BalanceReportDatagrid').datagrid('addFilterRule', {
              field: 'balances.created_at',
              value: value
          });
      }

      $('#BalanceReportDatagrid').datagrid('doFilter');
  }

  function applyBalanceReportDatagridDateFilter ()
  {
      var dateFromText = $('#BalanceReportDatagridFromDate').datebox('getValue');
      dateFrom = $.fn.datebox.defaults.parser(dateFromText);
      dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

      var dateToText = $('#BalanceReportDatagridToDate').datebox('getValue');
      dateTo = $.fn.datebox.defaults.parser(dateToText);
      dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

      if (dateFromText == '' &&  dateToText == ''){
          $('#BalanceReportDatagrid').datagrid('removeFilterRule', 'balances.created_at');
      } else if (dateFromText == '') {
          $('#BalanceReportDatagrid').datagrid('addFilterRule', {
              field: 'balances.created_at',
              op: 'less',
              value: dateTo
          });
      } else if (dateToText == '') {
          $('#BalanceReportDatagrid').datagrid('addFilterRule', {
              field: 'balances.created_at',
              op: 'greater',
              value: dateFrom
          });
      } else {
          $('#BalanceReportDatagrid').datagrid('addFilterRule', {
              field: 'balances.created_at',
              op: 'dateTimeBetween',
              value: dateFrom + ',' + dateTo
          });
      }
      $('#BalanceReportDatagrid').datagrid('doFilter');
  }

</script>
