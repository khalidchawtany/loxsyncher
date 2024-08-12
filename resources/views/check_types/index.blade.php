<table id="CheckTypesDatagrid"></table>

<div id="CheckTypesDatagridToolbar" style="padding:5px;text-align:center;">
    @can('create_check_type')
      <a href="#" class="easyui-linkbutton" iconCls="icon-add"  onclick="javascript:$('#CheckTypesDatagrid').edatagrid('addRow')">New</a>
    @endcan
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#CheckTypesDatagrid').edatagrid('reload')">Reload</a>


   @can('view_food_analyser_check_type')
    <a href="#" class="easyui-linkbutton" iconCls="icon-more" onclick="switchDashboardMainTab('Categories', '/food_analyser_check_types')">Food Analyser</a>
   @endcan
</div>

<script type="text/javascript">

  $(function(){
    $('#CheckTypesDatagrid').edatagrid({
      idField:'id',
      title: 'Test Types',
      toolbar:'#CheckTypesDatagridToolbar',
      fit:true,
      border:false,
      fitColumns:true,
      singleSelect:true,
      method:'get',
      rownumbers:true,
      pagination:true,
      remoteFilter:true,
      filterMatchType: 'any',
      url:'check_types/list',
      saveUrl: 'check_types/create',
      updateUrl: 'check_types/update',
      destroyUrl: 'check_types/destroy',

        columns: [[
          {field:'id',title:'Id',width:10},
          {field:'category',title:'Category',width:50,
            editor: {
              type: 'combobox',
              options: {
                panelHeight: 'auto',
                hasDownArrow: true,
                limitToList: false,
                valueField: 'category',
                textField: 'category',
                method:'get',
                url:'check-types/category/list',
              }
            }
          },

          {field:'subcategory',title:'Subcategory',width:50,
            editor:{
              type:'validatebox',
              options:{ }
            }
          },

          {field:'acronym',title:'Acronym',width:50,
            editor:{
              type:'validatebox',
              options:{ }
            }
          },

            {
                field:'disabled',
                title:'Disabled',
                sortable:true,
                width:20,
                align:'center',
                formatter:function(value){
                    return value==1 ? 'Yes' : 'No';
                }

                @can('disable_or_enable_check_types')
                    ,
                editor:{
                    type:'checkbox',
                    style: 'text-align:center;',
                    options:{
                        required:false,
                        on:'1',off:'0'
                    }
                }
            @endcan
            },







          {field:'price',title:'Price',width:50,
            editor:{
              type:'validatebox',
              options:{
                required:true
              }
            }
          },

          {field:'note', title:'Note',width:100,
            editor:{
              type:'validatebox',
              options:{
                required:false
              }
            }
          },

          {field:'reason', title:'Reason',width:100,
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
                      var s = '<button onclick="saveRow(\'CheckTypesDatagrid\', ' + index +')">Save</button> ';
                      var c = '<button onclick="cancelRow(\'CheckTypesDatagrid\', ' + index +')">Cancel</button>';
                      return s+c;
                  } else {
                      var e,d;
                      @can('update_check_type')
                        e = '<button onclick="editRow(\'CheckTypesDatagrid\', ' + index + ')">Edit</button> ';
                      @endcan
                      @can('destroy_check_type')
                        d = '<button onclick="deleteRow(\'CheckTypesDatagrid\', ' + index + ')">Delete</button> ';
                      @endcan
                      return e+d;
                  }
              }
          }

        ]]

    });
  });

  $('#CheckTypesDatagrid').edatagrid('enableFilter', [
      {
          field: 'action',
          type: 'label'
      },
      {
          field:'disabled',
          type:'combobox',
          options:{
              panelHeight:'auto',
              data:[{value:'',text:'All'},{value:'1',text:'Yes'},{value:'0',text:'No'}],
              onChange:function(value){
                  if (value == ''){
                      $('#CheckTypesDatagrid').datagrid('removeFilterRule', 'disabled');
                  } else {
                      $('#CheckTypesDatagrid').datagrid('addFilterRule', {
                          field: 'disabled',
                          op: 'equal',
                          value: value
                      });
                  }
                  $('#CheckTypesDatagrid').datagrid('doFilter');
              }
          }
      },
  ]);


</script>
