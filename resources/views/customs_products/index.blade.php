<div id="CustomsProductsDatagridContainer" style="width:100%;height:100%;">

  <table id="CustomsProductsDatagrid"></table>

  <div id="CustomsProductsDatagridToolbar" style="padding:5px;text-align:center;">
      @can('create_customs_product')
        <a href="#" class="easyui-linkbutton" iconCls="icon-add"  onclick="javascript:$('#CustomsProductsDatagrid').edatagrid('addRow')">New</a>
      @endcan
      <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#CustomsProductsDatagrid').edatagrid('reload')">Reload</a>

      @can('view_product')
      <a href="#" class="easyui-linkbutton" onclick="switchDashboardMainTab('Products', '/products')">Products</a>
      @endcan

	  <a href="#" class="easyui-linkbutton c4"  iconCls="icon-edit" onclick="showImportCustomsProductFromExcelFileDialog()">Import</a>
  </div>

</div>

<div id="CustomsProductImportDialog" class="easyui-dialog" style="width:500px;height:300px;"closed="true" modal="true">
</div>

<style media="screen">

  #CustomsProductsDatagridContainer .datagrid-view .datagrid-body{
      background: url('/img/datagrid/customs_product.png') no-repeat center;
  }

</style>

<script type="text/javascript">

  $(function(){
    $('#CustomsProductsDatagrid').edatagrid({
      idField:'id',
      title: 'CustomsProducts',
      toolbar:'#CustomsProductsDatagridToolbar',
      fit:true,
      border:false,
      fitColumns:true,
      singleSelect:true,
      method:'get',
      rownumbers:true,
      pagination:true,
      remoteFilter: true,
      filterMatchType: 'any',
      url:'customs_products/list',
      saveUrl: 'customs_products/create',
      updateUrl: 'customs_products/update',
      destroyUrl: 'customs_products/destroy',

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

          {field:'custom_id',title:'Customs Id',width:50,
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
                      var s = '<button onclick="saveRow(\'CustomsProductsDatagrid\', ' + index +')">Save</button> ';
                      var c = '<button onclick="cancelRow(\'CustomsProductsDatagrid\', ' + index +')">Cancel</button>';
                      return s+c;
                  } else {
                      var e,d;
                      @can('update_customs_product')
                        e = '<button onclick="editRow(\'CustomsProductsDatagrid\', ' + index + ')">Edit</button> ';
                      @endcan
                      @can('destroy_customs_product')
                        d = '<button onclick="deleteRow(\'CustomsProductsDatagrid\', ' + index + ')">Delete</button> ';
                      @endcan
                      return e+d;
                  }
              }
          }

        ]]


    });
  });

  $('#CustomsProductsDatagrid').edatagrid('enableFilter', [
      {
          field: 'action',
          type: 'label'
      }
  ]);

  function showImportCustomsProductFromExcelFileDialog() {
	  $('#CustomsProductImportDialog').dialog('open').dialog('setTitle', 'Import Customs  Products')
		  .dialog('refresh', '{!! route('showImportCustomsProductFromExcelFileDialog') !!}');
  }
</script>
