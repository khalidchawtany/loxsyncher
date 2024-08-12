<div class="easyui-layout" fit="true">

  <div data-options="region:'west', collapsible:true" style="width:400px;border-bottom:0;">
    <table id="RolesDatagrid"></table>

    <div id="RolesDatagridToolbar" style="padding:5px;text-align:center;">
      @can('create_role')
        <a href="#" class="easyui-linkbutton" iconCls="icon-add"  onclick="javascript:$('#RolesDatagrid').edatagrid('addRow')">New</a>
      @endcan
      <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#RolesDatagrid').edatagrid('reload')">Reload</a>
    </div>
  </div>

  <div data-options="region:'center',border:false, title:'Permissions'" id="RolesPermissionPanel">

  </div>

</div>


<script type="text/javascript">

  $(function(){
    $('#RolesDatagrid').edatagrid({
      idField:'id',
      title: 'Roles',
      toolbar:'#RolesDatagridToolbar',
      fit:true,
      border:false,
      fitColumns:true,
      singleSelect:true,
      method:'get',
      rownumbers:true,
      url:'roles/list',
      saveUrl: 'roles/insert',
      updateUrl: 'roles/update',
      destroyUrl: 'roles/destroy',

        columns: [[
          {field:'name',title:'Name',width:50,
            editor:{
              type:'validatebox',
              options:{
                required:true
              }
            }
          },

          {field:'action',title:'Action',width:100,align:'center',
              formatter:function(value,row,index){
                  if (row.editing){
                      var s = '<button onclick="saveRow(\'RolesDatagrid\', ' + index +')">Save</button> ';
                      var c = '<button onclick="cancelRow(\'RolesDatagrid\', ' + index +')">Cancel</button>';
                      return s+c;
                  } else {
                    var r = '';
                    @can('update_role')
                      r += '<button onclick="editRow(\'RolesDatagrid\', ' + index + ')">Edit</button> ';
                    @endcan
                    @can('destroy_role')
                      r += '<button onclick="deleteRow(\'RolesDatagrid\', ' + index + ')">Delete</button> ';
                    @endcan
                      r += '<button onclick="loadPermissions(\'' + row.name + '\')">Permissions</button> ';
                      return r;
                  }
              }
          }

        ]]


    });
  });

  function loadPermissions(roleName) {

    $('#RolesPermissionPanel').panel({
      'href': 'roles/permissions?role_name=' + roleName,
    });

  }

  function resetPermissionCheckboxes() {

    $('#RolesPermissionPanel').panel('clear');

  }


</script>
