<div id="CategoriesDatagridContainer" style="width:100%;height:100%;">

  <table id="CategoriesDatagrid"></table>

  <div id="CategoriesDatagridToolbar" style="padding:5px;text-align:center;">
      @can('create_category')
        <a href="#" class="easyui-linkbutton" iconCls="icon-add"  onclick="javascript:$('#CategoriesDatagrid').edatagrid('addRow')">New</a>
      @endcan
      <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#CategoriesDatagrid').edatagrid('reload')">Reload</a>

      @can('view_product')
      <a href="#" class="easyui-linkbutton" onclick="switchDashboardMainTab('Products', '/products')">Products</a>
      @endcan
  </div>

</div>

<style media="screen">

  #CategoriesDatagridContainer .datagrid-view .datagrid-body{
      background: url('/img/datagrid/category.png') no-repeat center;
  }

</style>

<script type="text/javascript">

  $(function(){
    $('#CategoriesDatagrid').edatagrid({
      idField:'id',
      title: 'Categories',
      toolbar:'#CategoriesDatagridToolbar',
      fit:true,
      border:false,
      fitColumns:true,
      singleSelect:true,
      method:'get',
      rownumbers:true,
      pagination:true,
      remoteFilter: true,
      filterMatchType: 'any',
      url:'categories/list',
      saveUrl: 'categories/create',
      updateUrl: 'categories/update',
      destroyUrl: 'categories/destroy',

        columns: [[
          {field:'id',title:'id',width:20 },

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
                      var s = '<button onclick="saveRow(\'CategoriesDatagrid\', ' + index +')">Save</button> ';
                      var c = '<button onclick="cancelRow(\'CategoriesDatagrid\', ' + index +')">Cancel</button>';
                      return s+c;
                  } else {
                      var e,d;
                      @can('update_category')
                        e = '<button onclick="editRow(\'CategoriesDatagrid\', ' + index + ')">Edit</button> ';
                      @endcan
                      @can('destroy_category')
                        d = '<button onclick="deleteRow(\'CategoriesDatagrid\', ' + index + ')">Delete</button> ';
                      @endcan
                      return e+d;
                  }
              }
          }

        ]]


    });
  });

  $('#CategoriesDatagrid').edatagrid('enableFilter', [
      {
          field: 'action',
          type: 'label'
      }
  ]);
</script>
