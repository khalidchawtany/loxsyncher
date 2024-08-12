
<div class="easyui-layout" fit="true">

  <div data-options="region:'center',border:false" >
    <table id="SectorReportDatagrid"></table>
  </div>

  <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

      <div class="easyui-panel"  id="SectorReportDepartmentFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options=" title: 'Sector' ">

              <label >Sector:</label>
              <input id="SectorReportDatagridDepartment" type="combobox" class="easyui-combobox" style="width:223px;"
                  data-options="
                    url:'reports/departments/list?append={{ \App\Models\Incineration::$DEPARTMENT_NAME}}',
                    method:'get',
                    valueField: 'department_id',
                    textField: 'department_name',
                    limitToList: true,
                    hasDownArrow: true,
                    panelHeight:'auto',
                    required:true,
                    onSelect: function(department) {
                        applySectorReportDatagridDepartmentFilter(department);
                    }
                  "
              >

      </div>

      <div class="easyui-panel"  style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options=" title: 'Show Revoked' ">

              <label >Revoked:</label>

			  <input
					  checked
					  class="easyui-switchbutton"
					  onText="Show"
					  offText="Hide"
					  data-options="
								   onChange: function(checked) {
										applySectorReportDatagridRevokeFilter(checked);
								   }
					"/>
	  </div>

      <div class="easyui-panel"  id="SectorReportDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;"
                                    data-options=" title: 'Inspection Date' ">






              <label >From:</label>
              <input id="SectorReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

              <label >To:</label>
              <input id="SectorReportDatagridToDate"  type="text" class="easyui-datebox" style="width:123px;">

              <div style="text-align:center; margin-top: 10px;">
                  <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applySectorReportDatagridDateFilter()">Apply</a>
                  <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearSectorReportDatagridDateFilter()">Clear</a>
              </div>
      </div>



      <div class="easyui-panel"  style="margin-bottom: 10px;"
                                    data-options=" title: 'Sectors Statics' ">
        <table id="sector_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:false, scrollbarSize:0 ">
          </table>
      </div>

      <div class="easyui-panel"  style="margin-bottom: 10px;"
                                 data-options=" title: 'Incinerations Statics' ">
        <table id="incinerations_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:false, scrollbarSize:0 ">
          </table>
      </div>

      <div class="easyui-panel"  style="margin-bottom: 10px;"
                                 data-options=" title: 'All Invoices Statics' ">
        <table id="total_stat_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:false, scrollbarSize:0 ">
          </table>
      </div>



    </table>
  </div>

</div>

<div id="SectorReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#SectorReportDatagrid').datagrid('reload')">Reload</a>
    @can('print_sector_report')
      <a href="#" class="easyui-linkbutton" iconCls="icon-print"  onclick="javascript:printFromSectorReportDatagrid();">Sector(s)</a>
    @endcan

    @can('print_sector_speed_report')
      <a href="#" class="easyui-linkbutton" iconCls="icon-print"  onclick="javascript:printSpeedReportFromSectorReportDatagrid();">Speed Report</a>
      <input id="printSpeedReportCurrencyType" class="easyui-switchbutton"
                onText="USD" offText="IQD" style="width:60px;">
    @endcan

      {{-- <input value="1" --}}
      {{--        style="width:230px;" --}}
      {{--        id="speed_report_date_box" --}}
      {{--        class="easyui-datebox" --}}
      {{--        data-options=" --}}
      {{--        onClickButton:printSpeedReportFromSectorReportDatagrid, --}}
      {{--        buttonText:'Speed Report', --}}
      {{--        buttonIcon:'icon-print', --}}
      {{--        id: 'xyx', --}}
      {{--        onShowPanel:function() { --}}
      {{--           var options = $(this).datebox('options'); --}}
      {{--           var $this = this; --}}

      {{--           var p = $(this).datebox('panel'); --}}
      {{--           p.find('span.calendar-text').trigger('click'); --}}
      {{--           p.find('.datebox-button-a').eq(0).hide(); --}}

      {{--           var span = p.find('span.calendar-text'); --}}
      {{--           // Block the choice of today's button --}}
      {{--           p.find('.calendar-text').hide(); --}}

      {{--           // Input box can be filled in, will trigger the event, mask out --}}
      {{--           p.find('.calendar-menu-year').attr('readonly','readonly'); --}}

      {{--           // initialization only need to bundle an event is enough --}}
      {{--           if (options.event_handler_has_been_set == undefined) { --}}
      {{--               setTimeout(function () { --}}
      {{--                   options.event_handler_has_been_set =p.find('div.calendar-menu-month-inner td'); --}}
      {{--                   options.event_handler_has_been_set.click(function (e) { --}}
      {{--                       e.stopPropagation(); //Disallow bubbling to perform events that easyui binds to the month --}}
      {{--                       var year = /\d{4}/.exec(span.html())[0]//get the year --}}
      {{--                       var month = parseInt($(this).attr('abbr'), 10); //month --}}
      {{--                       $($this).datebox('hidePanel').datebox('setValue', '01/' + month + '/' + year ); //Set the date value --}}
      {{--                   }); --}}
      {{--               }); --}}
      {{--           } --}}
      {{--        }, --}}
      {{--        formatter: function (d) {// format --}}
      {{--           return  (d.getMonth()<9?'0'+(d.getMonth()+1):(d.getMonth()+1)) + '/' + d.getFullYear(); --}}
      {{--        }, --}}
      {{--        prompt:'' --}}
      {{--        " --}}
      {{--        /> --}}
</div>


<script type="text/javascript">

  $(function(){

    $('#SectorReportDatagrid').datagrid({
      idField:'id',
      title: 'Sector Report',
      toolbar:'#SectorReportDatagridToolbar',
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
      url:'reports/sector/list',

        columns: [[

          { field:'department_name', as:'department_name', title:'Sector', width:20, align:'center' },
          { field:'payment_date', as:'payment_date', title:'Invoice Date', width:30, align:'center' },
          { field:'paid_amount', as:'paid_amount', title:'Invoice Amount (IQD)', width:20, align:'center' },
          { field:'payment_count', as:'payment_count', title:'Invoice Count', width:20, align:'center' },
          { field:'amount_sum', as:'amount_sum', title:'Amount (Ton)', width:20, align:'center' },
          { field:'batch_count', as:'batch_count', title:'Sample Count', width:20, align:'center' },
        ]],

        onLoadSuccess: function(data)
        {
            $('#sector_report_propertygrid').propertygrid('loadData', data.stat_rows );
            $('#incinerations_report_propertygrid').propertygrid('loadData', data.incineration_stats );
            $('#total_stat_report_propertygrid').propertygrid('loadData', data.total_stats );

        }
    });
  });

  $('#SectorReportDatagrid').datagrid('enableFilter', []);
  $('#SectorReportDatagrid').datagrid('getPanel').find('tr.datagrid-filter-row').hide();
  $('#SectorReportDatagrid').datagrid('resize');

  function clearSectorReportDatagridDateFilter (value)
  {
      $('#SectorReportDatagridFromDate').datebox('reset');
      $('#SectorReportDatagridToDate').datebox('reset');

      $('#SectorReportDatagrid').datagrid('removeFilterRule', 'payment_date');

      if (value != '') {
          $('#SectorReportDatagrid').datagrid('addFilterRule', {
              field: 'payment_date',
              value: value
          });
      }

      $('#SectorReportDatagrid').datagrid('doFilter');
  }

  function applySectorReportDatagridDateFilter ()
  {
      var dateFromText = $('#SectorReportDatagridFromDate').datebox('getValue');
      dateFrom = $.fn.datebox.defaults.parser(dateFromText);
      dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

      var dateToText = $('#SectorReportDatagridToDate').datebox('getValue');
      dateTo = $.fn.datebox.defaults.parser(dateToText);
      dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

      if (dateFromText == '' &&  dateToText != ''
          || dateFromText != '' &&  dateToText == '') {

          $.messager.show({ title: 'Error', msg: 'You have to select both dates!'});
          return;
      }

      if (dateFromText == '' &&  dateToText == '') {
          $('#SectorReportDatagrid').datagrid('removeFilterRule', 'payment_date');

      } else {
          $('#SectorReportDatagrid').datagrid('addFilterRule', {
              field: 'payment_date',
              op: 'dateTimeBetween',
              value: dateFrom + ',' + dateTo
          });
      }
      $('#SectorReportDatagrid').datagrid('doFilter');
  }

  function applySectorReportDatagridRevokeFilter(hideRevokedPayments) {

	  var filterRulesBefore = $('#SectorReportDatagrid').datagrid('options').filterRules;

	  if (hideRevokedPayments) {
		  $('#SectorReportDatagrid').datagrid('removeFilterRule', 'hideRevokedPayments');
	  } else {
		  $('#SectorReportDatagrid').datagrid('addFilterRule', {
			  field: 'hideRevokedPayments',
			  value: true
		  });
	  }

	  var filterRules = $('#SectorReportDatagrid').datagrid('options').filterRules;

	  if (! (filterRules.length == 0 && filterRulesBefore.length == 0 ) ) {
		  $('#SectorReportDatagrid').datagrid('doFilter');
	  }
  }

  function applySectorReportDatagridDepartmentFilter(department) {

    var filterRulesBefore = $('#SectorReportDatagrid').datagrid('options').filterRules;

    if (department.department_id == 0) {
      $('#SectorReportDatagrid').datagrid('removeFilterRule', 'department_id');
    } else {
          $('#SectorReportDatagrid').datagrid('addFilterRule', {
              field: 'department_id',
              value: department.department_id
          });
    }

    var filterRules = $('#SectorReportDatagrid').datagrid('options').filterRules;

    if (! (filterRules.length == 0 && filterRulesBefore.length == 0 ) ) {
      $('#SectorReportDatagrid').datagrid('doFilter');
    }
  }

    function printSpeedReportFromSectorReportDatagrid()
    {
        {{-- var date  = $('#speed_report_date_box').datebox('getValue'); --}}

      var isUsd = $('#printSpeedReportCurrencyType').switchbutton('options').checked == true;

      var sectorReportDatagridOptions = $('#SectorReportDatagrid').datagrid('options');
      var filterRules = sectorReportDatagridOptions.filterRules;
      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);
      queryStrings += '&isUsd=' + isUsd;

      var urlToPrint = '{!! \URL::route('print_sector_speed_report') !!}?';
      print(urlToPrint + queryStrings);
    }

    function printFromSectorReportDatagrid()
    {
      var sectorReportDatagridOptions = $('#SectorReportDatagrid').datagrid('options');
      var filterRules = sectorReportDatagridOptions.filterRules;
      var sort = sectorReportDatagridOptions.sortName;
      var order = sectorReportDatagridOptions.sortOrder;

      var col = $('#SectorReportDatagrid').datagrid('getColumnOption', sort);

      if (col && col.as) {
        sort = col.as;
      }

      var urlToPrint = '{!! \URL::route('print_sector_report') !!}?';

      var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

      if (sort) {
        queryStrings += '&sort=' + sort + '&order=' + order;
      }

      print(urlToPrint + queryStrings);

      //sendPrintCommandToServer(urlToPrint + queryStrings , 'A4', ' -o Landscape');
    }

</script>
