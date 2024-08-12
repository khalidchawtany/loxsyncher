<div id="PermissionsDescriptionDatagridContainer" style="width:100%;height:100%;">
    <table id="PermissionsDescriptionDatagrid"></table>

    <div id="PermissionsDescriptionDatagridToolbar" style="padding:5px;text-align:center;">
        <a href="#" class="easyui-linkbutton" iconCls="icon-add"  onclick="javascript:$('#PermissionsDescriptionDatagrid').edatagrid('addRow')">New</a>
        <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#PermissionsDescriptionDatagrid').edatagrid('reload')">Reload</a>
    </div>

</div>

<style media="screen">

    #PermissionsDescriptionDatagridContainer .datagrid-view .datagrid-body{
        background: url('/img/datagrid/permissions_description.png') no-repeat center;
    }

</style>


<script type="text/javascript">

  $(function(){
    $('#PermissionsDescriptionDatagrid').edatagrid({
      idField:'id',
      title: 'Permissions Description',
      toolbar:'#PermissionsDescriptionDatagridToolbar',
      fit:true,
      border:false,
      fitColumns:true,
      singleSelect:true,
      method:'get',
      rownumbers:true,
      pagination:true,
      remoteFilter:true,
      filterMatchType: 'any',
      url:'permissions/descriptions/list',
      saveUrl: 'permissions/descriptions/create',
      updateUrl: 'permissions/descriptions/update',
      destroyUrl: 'permissions/descriptions/destroy',

        columns: [[
          {field:'id',title:'id',width:10 },

          {field:'permission_name',title:'Name',width:20,
            editor:{
              type:'validatebox',
              options:{
                required:true
              }
            }
          },
          {field:'description',title:'Description',width:40,
            editor:{
              type:'validatebox',
              options:{
                required:true
              }
            }
          },

          {field:'note',title:'Note',width:30,
            editor:{
              type:'validatebox',
              options:{
                required:false
              }
            }
          },

          {field:'action',title:'Action',width:100,align:'center',
              formatter:function(value,row,index){
                  if (row.editing){
                      var s = '<button onclick="saveRow(\'PermissionsDescriptionDatagrid\', ' + index +')">Save</button> ';
                      var c = '<button onclick="cancelRow(\'PermissionsDescriptionDatagrid\', ' + index +')">Cancel</button>';
                      return s+c;
                  } else {
                      var e = '<button onclick="editRow(\'PermissionsDescriptionDatagrid\', ' + index + ')">Edit</button> ';
                      var d = '<button onclick="deleteRow(\'PermissionsDescriptionDatagrid\', ' + index + ')">Delete</button> ';
                      return e+d;
                  }
              }
          }

        ]],

        onBeforeEdit:function(index,row){
            row.editing = true;
            $(this).edatagrid('refreshRow', index);
        },
        onAfterEdit:function(index,row){
            row.editing = false;
            $(this).edatagrid('refreshRow', index);
        },
        onCancelEdit:function(index,row){
            row.editing = false;
            $(this).edatagrid('refreshRow', index);
        }

    });
  });

  $('#PermissionsDescriptionDatagrid').edatagrid('enableFilter', [
      {
          field: 'action',
          type: 'label'
      }
  ]);


</script>
