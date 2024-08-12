
<table id="ReviewRolesDatagrid">
</table>

<div id="ReviewRolesDatagridToolbar" style="padding:5px; text-align:center;">
	<a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="$('#ReviewRolesDatagrid').datagrid('reload')">Reload</a>

	<a href="#" class="easyui-linkbutton" iconCls="icon-export"  onclick="javascript:downloadFromReviewReviewRolesDatagrid();">Download</a>

</div>

<script type="text/javascript">
	$(function(){
		$('#ReviewRolesDatagrid').datagrid({
			idField:'id',
			toolbar: '#ReviewRolesDatagridToolbar',
			title:'Review Roles',
		    fit:true,
		    border:false,
		    fitColumns:true,
		    singleSelect:true,
		    method:'get',
		    rownumbers:true,
		    url:'roles/review/list',
		    pagination:true,
		    remoteFilter:true,
      		filterMatchingType:'any',
		    columns: [[
          {field:'name', as:'roles.name',title:'Role',width:10, sortable:true, style:"vertical-align:top;"},
          {field:'permissions', title:'Permission',width:90, sortable:false,
            formatter: function(val) {
              if(val) {
                return  _.map(val, 'name').sort().join('</br>');
              }
              return '<div style="text-align:center;">---</div>'
            }
          },

		    ]]
		});
	});

	$('#ReviewRolesDatagrid').datagrid('enableFilter', [
      {
          field: 'permissions',
          type: 'label'
      }
]);




  function downloadFromReviewReviewRolesDatagrid()
  {
    var url  ='{!! \URL::route('download_roles') !!}?'
    var reviewReviewRolesDatagridOptions = $('#ReviewRolesDatagrid').datagrid('options');
    var filterRules = reviewReviewRolesDatagridOptions.filterRules;
    var sort = reviewReviewRolesDatagridOptions.sortName;
    var order = reviewReviewRolesDatagridOptions.sortOrder;

    var col = $('#ReviewRolesDatagrid').datagrid('getColumnOption', sort);

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
