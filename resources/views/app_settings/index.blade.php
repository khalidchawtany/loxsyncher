<div id="AppSettingDatagridContainer" style="width:100%;height:100%;">
  <table id="AppSettingDatagrid"></table>

  <div id="AppSettingDatagridToolbar" style="padding:5px;text-align:center;">

   @if(user()->HasRole('Super'))
    @can('create_app_setting')
    <a href="#" class="easyui-linkbutton" iconCls="icon-add" onclick="javascript:$('#AppSettingDatagrid').edatagrid('addRow')">New</a>
    @endcan
    @endif
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload" onclick="javascript:$('#AppSettingDatagrid').edatagrid('reload')">Reload</a>
  </div>

</div>

<style media="screen">
  #AppSettingDatagridContainer .datagrid-view .datagrid-body {
    background: url('img/datagrid/app_setting.png') no-repeat center;
  }
</style>


<script type="text/javascript">
  $(function() {
    $('#AppSettingDatagrid').edatagrid({
      idField: 'id',
      title: 'AppSetting',
      toolbar: '#AppSettingDatagridToolbar',
      fit: true,
      border: false,
      fitColumns: true,
      singleSelect: true,
      method: 'get',
      rownumbers: true,
      pagination: true,
      remoteFilter: true,
      filterMatchType: 'any',
      url: 'app_settings/list',
      saveUrl: 'app_settings/create',
      updateUrl: 'app_settings/update',
      destroyUrl: 'app_settings/destroy',

      columns: [
        [{
            field: 'name',
            title: 'Name',
            width: 50
          @if(user()->HasRole('Super')),
            editor: {
              type: 'validatebox',
              options: {
                required: false,
                //validType: '',
              }
            }
            @endif
          },
          {
            field: 'value',
            title: 'Value',
            width: 50,
            editor: {
              type: 'validatebox',
              options: {
                required: false,
                //validType: '',
              }
            }
          },

          {
            field: 'action',
            title: 'Action',
            width: 100,
            align: 'center',
            formatter: function(value, row, index) {
              if (row.editing) {
                var s = '<button onclick="saveRow(\'AppSettingDatagrid\', ' + index + ')">Save</button> ';
                var c = '<button onclick="cancelRow(\'AppSettingDatagrid\', ' + index + ')">Cancel</button>';
                return s + c;
              } else {
                var e, d;
                @can('update_app_setting')
                e = '<button onclick="editRow(\'AppSettingDatagrid\', ' + index + ')">Edit</button> ';
                @endcan
                @if(user()->HasRole('Super'))
                  @can('destroy_app_setting')
                    d = '<button onclick="deleteRow(\'AppSettingDatagrid\', ' + index + ')">Delete</button> ';
                  @endcan
                return e + d;
              @endif
                return e;
              }
            }
          }

        ]
      ]

    });
  });

  $('#AppSettingDatagrid').edatagrid('enableFilter', [{
    field: 'action',
    type: 'label'
  }]);
</script>
