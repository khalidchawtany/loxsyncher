
<div class="easyui-layout" fit="true">

  <div data-options="region:'center',border:false" >
    <table id="InspectionReportDatagrid"></table>
  </div>

  <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

      <div class="easyui-panel"  id="InspectionReportDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options="
                                        {{-- footer: '#InspectionReportDateFilterPanelFooter', --}}
                                        title: 'Date'
                                    ">

              <label >From:</label>
              <input id="InspectionReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

              <label >To:</label>
              <input id="InspectionReportDatagridToDate"  type="text" class="easyui-datebox" style="width:123px;">

              <div style="text-align:center; margin-top: 10px;">
                  <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applyInspectionReportDatagridDateFilter()">Apply</a>
                  <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearInspectionReportDatagridDateFilter()">Clear</a>
              </div>
      </div>
      <div id="InspectionReportDateFilterPanelFooter" >
      </div>

    <table id="inspection_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
    </table>
  </div>

</div>

<div id="InspectionUpdateHistoryDialog"
      class="easyui-dialog"
      style="width:1000px;height:500px;padding:10px 10px"
      title="Test Update History" closed="true" modal="true">


<div id="InspectionReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#InspectionReportDatagrid').datagrid('reload')">Reload</a>

    @can('print_inspections_activity_report')
      <a href="#" class="easyui-linkbutton" iconcls="icon-print"  onclick="javascript:printInspectionActivityReport();">print</a>
    @endcan
</div>


<script type="text/javascript">

  $(function(){
    $('#InspectionReportDatagrid').datagrid({
      idField:'id',
      title: 'Inspections Activities',
      toolbar:'#InspectionReportDatagridToolbar',
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
      url:'/reports/activities/inspections/list',
      onDblClickRow:function(index,row){
        var expander = $(this).datagrid('getExpander', index);
        if (expander.hasClass('datagrid-row-expand')){
          $(this).datagrid('expandRow',index);
        }else{
          $(this).datagrid('collapseRow',index);
        }
      },

      columns: [[
        { field:'update_count' , as:'inspections.update_count'       , title:'Update Count'       , sortable: false , width:10   , align:'center',

          formatter: function(val, row, index) {
            var color = val >= 3? "style=\"color: red;\"" : "";
            return '<button ' + color +' onclick=" $(\'#InspectionReportDatagrid\').datagrid(\'expandRow\', ' + index + '); ">' + val + '</button>';
          },
        } ,
        { field:'inspection_id'     , as:'inspections.id'                 , title:'Inspection Id'                 , sortable: true , width:15 } ,
        { field:'user_name'    , as:'users.kurdish_name'        , title:'User'               , sortable: true , width:30 } ,
        { field:'product_type' , as:'inspections.product_type'      , title:'P. Type'            , sortable: true , width:30   , align:'center' } ,
        { field:'product_name' , as:'products.kurdish_name'     , title:'Product'            , sortable: true , width:30 } ,
        { field:'created_at'   , as:'inspections.created_at'        , title:'Created At' , sortable: true , width:30   , align:'center' }
      ]],

      onLoadSuccess: function(data)
      {
        $('#inspection_report_propertygrid').propertygrid('loadData', data.stat_rows );
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
            $('#InspectionReportDatagrid').datagrid('fixDetailRowHeight',index);
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
        {{--   {{-1- url:'reports/activities/inspections/listActivities'+row.itemid, -1-}} --}}
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
        {{--     $('#InspectionReportDatagrid').datagrid('fixDetailRowHeight',index); --}}
        {{--   }, --}}
        {{--   onLoadSuccess:function(){ --}}
        {{--     setTimeout(function(){ --}}
        {{--       $('#InspectionReportDatagrid').datagrid('fixDetailRowHeight',index); --}}
        {{--     },0); --}}
        {{--   } --}}
        {{-- }); --}}
        {{-- ddv.datagrid('loadData',row.updates); --}}
        $('#InspectionReportDatagrid').datagrid('fixDetailRowHeight',index);
      }
    });
  });

  $('#InspectionReportDatagrid').datagrid('enableFilter', [
    {
      field:'update_count',
      type:'label'
    },
  ]
  );

  function clearInspectionReportDatagridDateFilter (value)
  {
      $('#InspectionReportDatagridFromDate').datebox('reset');
      $('#InspectionReportDatagridToDate').datebox('reset');

      $('#InspectionReportDatagrid').datagrid('removeFilterRule', 'inspections.created_at');

      if (value != '') {
          $('#InspectionReportDatagrid').datagrid('addFilterRule', {
              field: 'inspections.created_at',
              value: value
          });
      }

      $('#InspectionReportDatagrid').datagrid('doFilter');
  }

  function applyInspectionReportDatagridDateFilter ()
  {
      var dateFromText = $('#InspectionReportDatagridFromDate').datebox('getValue');
      dateFrom = $.fn.datebox.defaults.parser(dateFromText);
      dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

      var dateToText = $('#InspectionReportDatagridToDate').datebox('getValue');
      dateTo = $.fn.datebox.defaults.parser(dateToText);
      dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

      if (dateFromText == '' &&  dateToText == ''){
          $('#InspectionReportDatagrid').datagrid('removeFilterRule', 'inspections.created_at');
      } else if (dateFromText == '') {
          $('#InspectionReportDatagrid').datagrid('addFilterRule', {
              field: 'inspections.created_at',
              op: 'less',
              value: dateTo
          });
      } else if (dateToText == '') {
          $('#InspectionReportDatagrid').datagrid('addFilterRule', {
              field: 'inspections.created_at',
              op: 'greater',
              value: dateFrom
          });
      } else {
          $('#InspectionReportDatagrid').datagrid('addFilterRule', {
              field: 'inspections.created_at',
              op: 'dateTimeBetween',
              value: dateFrom + ',' + dateTo
          });
      }
      $('#InspectionReportDatagrid').datagrid('doFilter');
  }

    function printInspectionActivityReport()
    {
      var dg = $('#InspectionReportDatagrid').datagrid('options');
      var filterRules = dg.filterRules;
      var sort = dg.sortName;
      var order = dg.sortOrder;

      var col = $('#InspectionReportDatagrid').datagrid('getColumnOption', sort);

      if (col && col.as) {
        sort = col.as;
      }

      var urlToPrint = '{!! \URL::route('print_inspections_activity_report') !!}?';

      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

      if (sort) {
        queryStrings += '&sort=' + sort + '&order=' + order;
      }

      print(urlToPrint + queryStrings);

      //sendPrintCommandToServer(urlToPrint + queryStrings , 'A4', ' -o Landscape');
    }
</script>
