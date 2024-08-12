
<table id="ActivitiesDatagrid">
	<thead>
		<tr>
			<th field="users.name" width="100">Username</th>
			<th field="properties" width="100" formatter="formatModelActivityName">Target</th>
			<th field="subject_id" width="100">Target ID</th>
			<th field="description" width="100">Description</th>
			<th field="created_at" width="100">Time</th>
		</tr>
	</thead>
</table>

<div id="ActivitiesDatagridToolbar" style="padding:5px; text-align:center;">
	<a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="$('#ActivitiesDatagrid').datagrid('reload')">Reload</a>
	{{-- <span id="ActivitiesDateFilter"> --}}
      {{-- <span style="margin-left:5px;">Include data in filter from:</span> --}}
      {{-- <input class="easyui-datebox" id="filter_from" > --}}
      {{-- To: --}}
      {{-- <input class="easyui-datebox" id="filter_to"> --}}
      {{-- <a href="#" class="easyui-linkbutton" iconCls="icon-search"  onclick="datagridFilterByDate('ActivitiesDateFilter', 'ActivitiesDatagrid', 'date')">Apply date filter</a> --}}
      {{-- <a href="#" class="easyui-linkbutton" iconCls="icon-reset"  onclick="clearDatagridFilterByDate('ActivitiesDateFilter', 'ActivitiesDatagrid', 'date')">Clear date filter</a> --}}
    {{-- </span> --}}
</div>

<script type="text/javascript">
	$(function(){
		$('#ActivitiesDatagrid').datagrid({
			idField:'id',
			toolbar: '#ActivitiesDatagridToolbar',
			title:'Ativities',
		    fit:true,
		    border:false,
		    fitColumns:true,
		    singleSelect:true,
		    method:'get',
		    rownumbers:true,
		    url:'activities/list',
		    pagination:true,
		    remoteFilter:true,
      		filterMatchingType:'any',
		    columns: [[
			{field:'id', as:'activity_log.id', title:'Activity Id',width:20, sortable:true},
			{field:'user_name', as:'users.kurdish_name',title:'User',width:50, sortable:true},
			{field:'subject_type', as:'activity_log.subject_type',title:'Subject',width:50, sortable:true},
			{field:'subject_id', as:'activity_log.subject_id',title:'Subject Id',width:50, sortable:true},
			{field:'description', as:'activity_log.description',title:'Description',width:150, sortable:true},
			{{-- {field:'properties', as:'activity_log.properties',title:'Properties',width:50, sortable:true}, --}}
			{field:'created_at', as:'activity_log.created_at',title:'Date Time',width:50, sortable:true},
		    ]]
		});
	});

	$('#ActivitiesDatagrid').datagrid('enableFilter',
	  	[{
	      field:'properties',
	      type:'combobox',
	      options: {
	        panelHeight:'auto',
	        method:'get',
	        valueField: 'model_activity_name',
	        textField: 'model_activity_name',
	        url:'activities/list/model-activity-name',
	        onChange:function(value){
	            if (value == ''){
	                $('#ActivitiesDatagrid').datagrid('removeFilterRule', 'activity_log.properties');
	            } else {
	                $('#ActivitiesDatagrid').datagrid('addFilterRule', {
	                    field: 'activity_log.properties',
	                    value: value
	                });
	            }
	            $('#ActivitiesDatagrid').datagrid('doFilter');
	        }
	      }
	    }]
	 );


	function formatActivityAgency(val) {
		return (val) ? val : '---';
	}

	function formatModelActivityName(value, row) {
		if(row.properties)
			return row.properties.model_activity_name;
		return '---'
	}

</script>
