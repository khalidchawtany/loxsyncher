
<div class="easyui-layout" fit="true">

  <div data-options="region:'center',border:false" >
    <table id="VehiclesReportDatagrid"></table>
  </div>

  <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">


      <div class="easyui-panel"  id="VehiclesReportDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options="title: 'Release Date'">

              <label >From:</label>
              <input id="VehiclesReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

              <label >To:</label>
              <input id="VehiclesReportDatagridToDate"  type="text" class="easyui-datebox" style="width:123px;">

              <div style="text-align:center; margin-top: 10px;">
                  <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applyVehiclesReportDatagridDateFilter()">Apply</a>
                  <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearVehiclesReportDatagridDateFilter()">Clear</a>
              </div>
      </div>
      <div id="VehiclesReportDateFilterPanelFooter" >
      </div>

    <table id="vehicles_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
    </table>
  </div>

</div>

<div id="VehiclesReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#VehiclesReportDatagrid').datagrid('reload')">Reload</a>
    @can('download_vehicles_report')
        <a href="#" class="easyui-linkbutton" iconCls="icon-export" onclick="javascript:downloadVehiclesReports();">Download</a>
    @endcan
</div>


<script type="text/javascript">

  $(function(){

    $('#VehiclesReportDatagrid').datagrid({
      idField:'id',
      title: 'Vehicles Report',
      toolbar:'#VehiclesReportDatagridToolbar',
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
      url:'reports/vehicles/list',

        columns: [[

                    {field: 'release_office'         , as: 'releases.office'         , title: 'Office'            , width: 15 , align: 'left', sortable: true} ,
                    {field: 'vehicle_id'             , as: 'vehicles.id'             , title: 'V. Id'             , width: 10 , align: 'left', sortable: true} ,
                    {field: 'vehicle_condition'      , as: 'vehicles.condition'      , title: 'V. Condition'      , width: 15 , align: 'left', sortable: true} ,
                    {field: 'vehicle_vin'            , as: 'vehicles.vin'            , title: 'Vin'               , width: 30 , align: 'left', sortable: true} ,
                    {field: 'vehicle_type'           , as: 'vehicles.type'           , title: 'V. Type'           , width: 10 , align: 'left', sortable: true} ,
                    {field: 'vehicle_color'          , as: 'vehicles.color'          , title: 'Color'             , width: 10 , align: 'left', sortable: true} ,
                    {field: 'vehicle_make_name'      , as: 'vehicles.make_name'      , title: 'Make'              , width: 10 , align: 'left', sortable: true} ,
                    {field: 'vehicle_model_name'     , as: 'vehicles.model_name'     , title: 'Model'             , width: 15 , align: 'left', sortable: true} ,
                    {field: 'vehicle_coc_number'     , as: 'vehicles.coc_number'     , title: 'Coc #'             , width: 30 , align: 'left', sortable: true} ,
                    {field: 'vehicle_coc_date'       , as: 'vehicles.coc_date'       , title: 'Coc Date'          , width: 15 , align: 'left', sortable: true} ,
                    {field: 'vehicle_coc_issuer'     , as: 'vehicles.coc_issuer'     , title: 'Coc Issuer'        , width: 10 , align: 'left', sortable: true} ,
                    {field: 'release_id'             , as: 'releases.id'             , title: 'R. Id'             , width: 10 , align: 'left', sortable: true} ,
                    {field: 'release_date'           , as: 'releases.date'           , title: 'R. Date'           , width: 30 , align: 'left', sortable: true} ,
                    {field: 'release_result'         , as: 'releases.result'         , title: 'R. Result'         , width: 10 , align: 'left', sortable: true} ,
                    {field: 'release_transit_number' , as: 'releases.transit_number' , title: 'Transit#'          , width: 20 , align: 'left', sortable: true} ,
                    {field: 'release_inspector'      , as: 'releases.inspector'      , title: 'R. Inspector'      , width: 20 , align: 'left', sortable: true} ,

          ]],

        onLoadSuccess: function(data)
        {
            $('#vehicles_report_propertygrid').propertygrid('loadData', data.stat_rows );
        }
    });
  });

  $('#VehiclesReportDatagrid').datagrid('enableFilter', [

            {
                field: 'vehicle_type',
                type: 'combobox',
                options: {
                    panelHeight: 'auto',
                    data: <?= json_encode(\App\Helpers\VehicleTypeEnum::filterData()) ?>,
                    onChange: function(value) {
                        if (value == '') {
                            $('#VehiclesReportDatagrid').datagrid('removeFilterRule',
                                'vehicles.type');
                        } else {
                            $('#VehiclesReportDatagrid').datagrid('addFilterRule', {
                                field: 'vehicle_type',
                                op: 'equal',
                                value: value
                            });
                        }
                        $('#VehiclesReportDatagrid').datagrid('doFilter');
                    }
                }
            },

  ]);
  $('#VehiclesReportDatagrid').datagrid('getPanel').find('tr.datagrid-filter-row').hide();
  $('#VehiclesReportDatagrid').datagrid('resize');

  function clearVehiclesReportDatagridDateFilter (value)
  {
      $('#VehiclesReportDatagridFromDate').datebox('reset');
      $('#VehiclesReportDatagridToDate').datebox('reset');

      $('#VehiclesReportDatagrid').datagrid('removeFilterRule', 'releases.date');

      if (value != '') {
          $('#VehiclesReportDatagrid').datagrid('addFilterRule', {
              field: 'releases.date',
              value: value
          });
      }

      $('#VehiclesReportDatagrid').datagrid('doFilter');
  }

  function applyVehiclesReportDatagridDateFilter ()
  {
      var dateFromText = $('#VehiclesReportDatagridFromDate').datebox('getValue');
      dateFrom = $.fn.datebox.defaults.parser(dateFromText);
      dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

      var dateToText = $('#VehiclesReportDatagridToDate').datebox('getValue');
      dateTo = $.fn.datebox.defaults.parser(dateToText);
      dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

      if (dateFromText == '' &&  dateToText != ''
          || dateFromText != '' &&  dateToText == '') {

          $.messager.show({ title: 'Error', msg: 'You have to select both dates!'});
          return;
      }

      if (dateFromText == '' &&  dateToText == '') {
          $('#VehiclesReportDatagrid').datagrid('removeFilterRule', 'releases.date');

      } else {
          $('#VehiclesReportDatagrid').datagrid('addFilterRule', {
              field: 'releases.date',
              op: 'dateBetween',
              value: dateFrom + ',' + dateTo
          });
      }
      $('#VehiclesReportDatagrid').datagrid('doFilter');
  }

    function downloadVehiclesReports() {
        var dgOpts = $('#VehiclesReportDatagrid').datagrid('options');
        var filterRules = dgOpts.filterRules;
        var sort = dgOpts.sortName;
        var order = dgOpts.sortOrder;

        var col = $('#VehiclesReportDatagrid').datagrid('getColumnOption', sort);

        if (col && col.as) {
            sort = col.as;
        }

        var urlToPrint = '<?= \URL::route('download_vehicles_report') ?>?';

        var queryStrings = 'filterRules=' + JSON.stringify(filterRules);

        if (sort) {
            queryStrings += '&sort=' + sort + '&order=' + order;
        }

        print(urlToPrint + queryStrings);
    }

</script>
