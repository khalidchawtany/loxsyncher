<div class="easyui-layout" fit="true">

  <div data-options="region:'west', collapsible:true" style="width:400px;border-bottom:0;">
    <table id="usersDatagrid"></table>

    <div id="usersDatagridToolbar" style="padding:5px;text-align:center;">
      <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#usersDatagrid').edatagrid('reload')">Reload</a>
    </div>
  </div>

  <div data-options="region:'center',border:false, title:'Permissions'" id="usersPermissionPanel">

  </div>

</div>


<script type="text/javascript">

  $(function(){
    var permissionsUserDatagrid  = $('#usersDatagrid').edatagrid({
      idField:'id',
      title: 'users',
      toolbar:'#usersDatagridToolbar',
      fit:true,
      border:false,
      fitColumns:true,
      singleSelect:true,
      method:'get',
      rownumbers:true,
      url:'users/permissions/list-users',

        columns: [[
						{field:'kurdish_name',title:'Name',width:50 },
						{field:'action',title:'Action',width:100,align:'center',
								formatter:function(value,row,index){
										return '<button onclick="loadPermissions(\'' + row.id + '\')">Permissions</button> ';
								}
						}
        ]]

    });


    permissionsUserDatagrid.datagrid('enableFilter').datagrid('loadData', permissionsUserDatagrid.edatagrid('getData'));

  });

  function loadPermissions(userId) {

    $('#usersPermissionPanel').panel({
      'href': 'users/permissions/list-user-permissions?userId=' + userId,
    });

  }

  function resetPermissionCheckboxes() {

    $('#usersPermissionPanel').panel('clear');

  }


</script>
