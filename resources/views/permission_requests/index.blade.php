<div id="PermissionRequestDatagridContainer" style="width:100%;height:100%;">
    <table id="PermissionRequestDatagrid"></table>

    <div id="PermissionRequestDatagridToolbar" style="padding:5px;text-align:center;">

      <a href="#" class="easyui-linkbutton"
                  iconCls="icon-search"
                  onclick="showPermissionRequestViewDialog()">View</a>

        @can('create_permission_request')
          <a href="#" class="easyui-linkbutton"
                      iconCls="icon-add"
                      onclick="showPermissionRequestDialog()">New</a>
        @endcan

        <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#PermissionRequestDatagrid').edatagrid('reload')">Reload</a>

    </div>

</div>
<div id="PermissionRequestDialog" class="easyui-dialog" closed="true" modal="true">

<div id="PermissionRequestViewDialog" class="easyui-dialog" closed="true" modal="true">

<style media="screen">

    #PermissionRequestDatagridContainer .datagrid-view .datagrid-body{
        background: url('/img/datagrid/permission_request.png') no-repeat center;
    }

</style>


<script type="text/javascript">

  $(function(){
    $('#PermissionRequestDatagrid').edatagrid({
      idField:'id',
      title: 'Permission Requests',
      toolbar:'#PermissionRequestDatagridToolbar',
      fit:true,
      border:false,
      fitColumns:true,
      singleSelect:true,
      method:'get',
      rownumbers:true,
      pagination:true,
      remoteFilter:true,
      filterMatchType: 'any',
      url:'permission_requests/list',
      // saveUrl: 'permission_requests/create',
      // updateUrl: 'permission_requests/update',
      // destroyUrl: 'permission_requests/destroy',

        columns: [[
          {field:'status',title:'Status',width:20,
            formatter:function(value){
              if (value == 0)
                return '<span style="color: red; font-weight: bold;">Rejected</span>';
              else if (value == 1)
                return '<span style="color: green; font-weight: bold;">Approved</span>';
              else if (value == 2)
                return '<span style="color: blue;">Granted</span>';

              return '<span style="color: orange; font-weight: bold;">Pending</span>';
            }
          },
          {field:'reason',title:'Reason',width:100 },
          {field:'requested_by',title:'Requested By',width:50 },
          {field:'requested_for_name',title:'Requested For',width:50 },
          {field:'permission_name',title:'Permission',width:50 },
          {field:'description',title:'Description',width:100 },
          {field:'note',title:'Note',width:100 },
          {field:'created_at',title:'Requested At',width:30 },
          {field:'updated_at',title:'Updated At',width:30 },
        ]]
      @can('update_permission_request')
      ,
      onDblClickRow: function(index,row) {
        showPermissionRequestDialog(row.id);
      }
      @endcan


    });
  });

  $('#PermissionRequestDatagrid').edatagrid('enableFilter', [ ]);

  function showPermissionRequestDialog(id)
  {
    var params = '';
    if (id) {
      params = '?id=' + id;
    }
    $('#PermissionRequestDialog').dialog('setTitle', 'New Permission Request')
      .dialog('refresh', 'permission_requests/dialog' + params);
  }

  function showPermissionRequestViewDialog()
  {
    var row = $('#PermissionRequestDatagrid').datagrid('getSelected');

    if (!row) {
      $.messager.show({ title: 'Error', msg: 'Please select a PR'});
      return;
    }

    $('#PermissionRequestViewDialog').dialog('setTitle', 'Permission Request')
      .dialog('refresh', 'permission_requests/view_dialog' + '?id=' + row.id);
  }



</script>
