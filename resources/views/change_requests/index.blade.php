<div id="ChangeRequestDatagridContainer" style="width:100%;height:100%;">
    <table id="ChangeRequestDatagrid"></table>

    <div id="ChangeRequestDatagridToolbar" style="padding:5px;text-align:center;">

      <a href="#" class="easyui-linkbutton"
                  iconCls="icon-search"
                  onclick="showChangeRequestViewDialog()">View</a>

      <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#ChangeRequestDatagrid').edatagrid('reload')">Reload</a>

      @can('create_change_request')
        <a href="#" class="easyui-linkbutton"
                    iconCls="icon-add"
                    onclick="showChangeRequestDialog()">New</a>
      @endcan

    </div>
</div>

<div id="ChangeRequestDialog" class="easyui-dialog" closed="true" modal="true">

<div id="ChangeRequestViewDialog" class="easyui-dialog" closed="true" modal="true">

<style media="screen">

    #ChangeRequestDatagridContainer .datagrid-view .datagrid-body{
        background: url('/img/datagrid/change_request.png') no-repeat center;
    }

</style>


<script type="text/javascript">

  $(function(){
    $('#ChangeRequestDatagrid').edatagrid({
      idField:'id',
      title: 'Change Requests',
      toolbar:'#ChangeRequestDatagridToolbar',
      fit:true,
      border:false,
      fitColumns:true,
      singleSelect:true,
      method:'get',
      rownumbers:true,
      pagination:true,
      remoteFilter:true,
      filterMatchType: 'any',
      url:'change_requests/list',
      // saveUrl: 'change_requests/create',
      // updateUrl: 'change_requests/update',
      // destroyUrl: 'change_requests/destroy',

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
          {field:'title',title:'Title',width:50 },
          {field:'description',title:'Description',width:100 },
          {field:'note',title:'Note',width:100 },
          {field:'created_at',title:'Requested At',width:30 },
          {field:'updated_at',title:'Updated At',width:30 },
        ]]
      @can('update_change_request')
      ,
      onDblClickRow: function(index,row) {
        showChangeRequestDialog(row.id);
      }
      @endcan


    });
  });

  $('#ChangeRequestDatagrid').edatagrid('enableFilter', [ ]);

  function showChangeRequestDialog(id)
  {
    var params = '';
    if (id) {
      params = '?id=' + id;
    }
    $('#ChangeRequestDialog').dialog('setTitle', 'New Change Request')
      .dialog('refresh', 'change_requests/dialog' + params);
  }

  function showChangeRequestViewDialog()
  {
    var row = $('#ChangeRequestDatagrid').datagrid('getSelected');

    if (!row) {
      $.messager.show({ title: 'Error', msg: 'Please select a change request'});
      return;
    }

    $('#ChangeRequestViewDialog').dialog('setTitle', 'Change Request')
      .dialog('refresh', 'change_requests/view_dialog' + '?id=' + row.id);
  }


</script>
