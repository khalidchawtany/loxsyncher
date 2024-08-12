
<div class="easyui-layout" fit="true">

  <div data-options="region:'center',border:false" >
    <table id="BatchReportDatagrid"></table>
  </div>

  <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

      <div class="easyui-panel"  id="BatchReportDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options="
                                        {{-- footer: '#BatchReportDateFilterPanelFooter', --}}
                                        title: 'Date'
                                    ">

              <label >From:</label>
              <input id="BatchReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

              <label >To:</label>
              <input id="BatchReportDatagridToDate"  type="text" class="easyui-datebox" style="width:123px;">

              <div style="text-align:center; margin-top: 10px;">
                  <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applyBatchReportDatagridDateFilter()">Apply</a>
                  <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearBatchReportDatagridDateFilter()">Clear</a>
              </div>
      </div>
      <div id="BatchReportDateFilterPanelFooter" >
      </div>

    <table id="batch_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
    </table>
  </div>

</div>

<div id="BatchUpdateHistoryDialog"
      class="easyui-dialog"
      style="width:1000px;height:500px;padding:10px 10px"
      title="Test Update History" closed="true" modal="true">


<div id="BatchReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#BatchReportDatagrid').datagrid('reload')">Reload</a>

    @can('print_batches_activity_report')
      <a href="#" class="easyui-linkbutton" iconcls="icon-print"  onclick="javascript:printBatchesActivityReport();">print</a>
    @endcan
</div>


<script type="text/javascript">

  $(function(){
    $('#BatchReportDatagrid').datagrid({
      idField:'id',
      title: 'Batches Activities',
      toolbar:'#BatchReportDatagridToolbar',
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
      url:'/reports/activities/batches/list',

      columns: [[
        { field:'activities_count' , title:'Update Count'       , sortable: false , width:10   , align:'center', sortable:true,

          formatter: function(val, row, index) {
            return '<button onclick=" $(\'#BatchReportDatagrid\').datagrid(\'expandRow\', ' + index + '); ">' + val + '</button>';
          },
        } ,
        { field:'batch_id'     , as:'batches.id'                 , title:'Batch Id'                 , sortable: true , width:15 } ,
        { field:'user_name'    , as:'users.kurdish_name'        , title:'User'               , sortable: true , width:30 } ,
        { field:'product_type' , as:'batches.product_type'      , title:'P. Type'            , sortable: true , width:30   , align:'center' } ,
        { field:'product_name' , as:'products.kurdish_name'     , title:'Product'            , sortable: true , width:30 } ,
        { field:'created_at'   , as:'batches.created_at'        , title:'Created At' , sortable: true , width:30   , align:'center' }
      ]],

      onLoadSuccess: function(data)
      {
        $('#batch_report_propertygrid').propertygrid('loadData', data.stat_rows );
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
            $('#BatchReportDatagrid').datagrid('fixDetailRowHeight',index);
          }
        });
        ddv.html("");
        ddv.append('<tr> <th>User</th> <th>Date</th> <th>Old</th> <th>New</th> <th>Diff</th> </tr>')
        row.activities.forEach(function(update){
          var props = update.properties;
          var newData = JSON.stringify(props.attributes, null, 2).replace(/,/g, '</br>').replace(/"/g, "").replace(/}/g, "").replace(/{/,"");
          var oldData = JSON.stringify(props.old, null, 2).replace(/,/g, '</br>').replace(/"/g, "").replace(/}/g, "").replace(/{/,"");
          var diff = diffFilter(props.old, props.attributes);
          diff  =JSON.stringify(diff, null, 2).replace(/,/g, '</br>').replace(/"/g, "").replace(/}/g, "").replace(/{/,"");
          ddv.append('<tr><td style="text-align: center;">' + update.user_name +  '</td>  <td style="text-align: center;">' + update.date +  '</td>  <td>' + oldData +  '</td> <td>' + newData +  '</td><td>' + diff +  '</td></tr>')
        });

        {{-- ddv.datagrid({ --}}
        {{--   {{-1- url:'reports/activities/batches/listActivities'+row.itemid, -1-}} --}}
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
        {{--     $('#BatchReportDatagrid').datagrid('fixDetailRowHeight',index); --}}
        {{--   }, --}}
        {{--   onLoadSuccess:function(){ --}}
        {{--     setTimeout(function(){ --}}
        {{--       $('#BatchReportDatagrid').datagrid('fixDetailRowHeight',index); --}}
        {{--     },0); --}}
        {{--   } --}}
        {{-- }); --}}
        {{-- ddv.datagrid('loadData',row.updates); --}}
        $('#BatchReportDatagrid').datagrid('fixDetailRowHeight',index);
      }
    });
  });

  $('#BatchReportDatagrid').datagrid('enableFilter', [
    {
      field:'activity_count',
      type:'label'
    },
  ]
  );

  function clearBatchReportDatagridDateFilter (value)
  {
      $('#BatchReportDatagridFromDate').datebox('reset');
      $('#BatchReportDatagridToDate').datebox('reset');

      $('#BatchReportDatagrid').datagrid('removeFilterRule', 'batches.created_at');

      if (value != '') {
          $('#BatchReportDatagrid').datagrid('addFilterRule', {
              field: 'batches.created_at',
              value: value
          });
      }

      $('#BatchReportDatagrid').datagrid('doFilter');
  }

  function applyBatchReportDatagridDateFilter ()
  {
      var dateFromText = $('#BatchReportDatagridFromDate').datebox('getValue');
      dateFrom = $.fn.datebox.defaults.parser(dateFromText);
      dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

      var dateToText = $('#BatchReportDatagridToDate').datebox('getValue');
      dateTo = $.fn.datebox.defaults.parser(dateToText);
      dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

      if (dateFromText == '' &&  dateToText == ''){
          $('#BatchReportDatagrid').datagrid('removeFilterRule', 'batches.created_at');
      } else if (dateFromText == '') {
          $('#BatchReportDatagrid').datagrid('addFilterRule', {
              field: 'batches.created_at',
              op: 'less',
              value: dateTo
          });
      } else if (dateToText == '') {
          $('#BatchReportDatagrid').datagrid('addFilterRule', {
              field: 'batches.created_at',
              op: 'greater',
              value: dateFrom
          });
      } else {
          $('#BatchReportDatagrid').datagrid('addFilterRule', {
              field: 'batches.created_at',
              op: 'dateTimeBetween',
              value: dateFrom + ',' + dateTo
          });
      }
      $('#BatchReportDatagrid').datagrid('doFilter');
  }

    function printBatchesActivityReport()
    {
      var dg = $('#BatchReportDatagrid').datagrid('options');
      var filterRules = dg.filterRules;
      var sort = dg.sortName;
      var order = dg.sortOrder;

      var col = $('#BatchReportDatagrid').datagrid('getColumnOption', sort);

      if (col && col.as) {
        sort = col.as;
      }

      var urlToPrint = '{!! \URL::route('print_batches_activity_report') !!}?';

      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

      if (sort) {
        queryStrings += '&sort=' + sort + '&order=' + order;
      }

      print(urlToPrint + queryStrings);

      //sendPrintCommandToServer(urlToPrint + queryStrings , 'A4', ' -o Landscape');
    }

</script>
