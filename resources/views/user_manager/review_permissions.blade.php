
<table id="PermissionsDatagrid">
</table>

<div id="PermissionsDatagridToolbar" style="padding:5px; text-align:center;">
	<a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="$('#PermissionsDatagrid').datagrid('reload')">Reload</a>

	<a href="#" class="easyui-linkbutton" iconCls="icon-export"  onclick="javascript:downloadFromReviewPermissionsDatagrid();">Download</a>

</div>

<script type="text/javascript">
	$(function(){
		$('#PermissionsDatagrid').datagrid({
			idField:'id',
			toolbar: '#PermissionsDatagridToolbar',
			title:'Review Permission',
		    fit:true,
		    border:false,
		    fitColumns:true,
		    singleSelect:true,
		    method:'get',
		    rownumbers:true,
		    url:'permissions/list-all',
		    pagination:true,
		    remoteFilter:true,
      		filterMatchingType:'any',
		    columns: [[
			{field:'user_id', as:'users.id', title:'User Id',width:10, sortable:true},
			{field:'user_name', as:'users.kurdish_name',title:'User Name',width:20, sortable:true},
			{field:'job_description', as:'users.job_description',title:'Job Desc.',width:20, sortable:true},
			{field:'is_staff', as:'users.is_staff',title:'Staff',width:20, sortable:true,
        formatter:function(value){
          return value==1 ? 'Yes' : 'No';
        },
      },
			{field:'permission_name', as:'permissions.name',title:'Permission',width:30, sortable:true},
			{field:'role_name', as:'all_permissions_view.role_name',title:'Through',width:30, sortable:true}
		    ]]
		});
	});

  $('#PermissionsDatagrid').datagrid('enableFilter', [
    {
      field:'is_staff',
      type:'combobox',
      options:{
        panelHeight:'auto',
        data:[{value:'',text:'All'},{value:'1',text:'Yes'},{value:'0',text:'No'}],
        onChange:function(value){
          if (value == ''){
            $('#PermissionsDatagrid').datagrid('removeFilterRule', 'users.is_staff');
          } else {
            $('#PermissionsDatagrid').datagrid('addFilterRule', {
              field: 'users.is_staff',
              op: 'equal',
              value: value
            });
          }
          $('#PermissionsDatagrid').datagrid('doFilter');
        }
      }
    },
  ]);


	function formatPermissionAgency(val) {
		return (val) ? val : '---';
	}

	function formatModelPermissionName(value, row) {
		if(row.properties)
			return row.properties.model_permission_name;
		return '---'
	}


  function downloadFromReviewPermissionsDatagrid()
  {
    var url  ='{!! \URL::route('download_permissions') !!}?'
    var reviewPermissionsDatagridOptions = $('#PermissionsDatagrid').datagrid('options');
    var filterRules = reviewPermissionsDatagridOptions.filterRules;
    var sort = reviewPermissionsDatagridOptions.sortName;
    var order = reviewPermissionsDatagridOptions.sortOrder;

    var col = $('#PermissionsDatagrid').datagrid('getColumnOption', sort);

    if (col && col.as) {
      sort = col.as;
    }

    var queryStrings =  'filterRules=' + JSON.stringify(filterRules);

    if (sort) {
      queryStrings += '&sort=' + sort + '&order=' + order;
    }

    print(url + queryStrings);

  }

</script>
