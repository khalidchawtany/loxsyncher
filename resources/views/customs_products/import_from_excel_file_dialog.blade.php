<div class="easyui-layout" fit="true">

    <div data-options="region:'center', border:false">

	<form id="CustomsProductImportForm" method="post" novalidate enctype="multipart/form-data">

	    <div class="ftitle">Import customs products</div>

      <div class="fitem">
        <label>File:</label>
        <input name="excel_file"
               style="width:230px;"
               class="easyui-filebox"
               data-options="accept: '.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel', required: false">
      </div>

	</form>
    </div>
    <div class="panel-buttons" data-options="region:'south', height:'auto'">
	<a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="importCustomsProductFromFile()">Import</a>
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#CustomsProductImportDialog').dialog('close');$('#CustomsProductsDatagrid').datagrid('reload'); " style="width:90px">Cancel</a>
    </div>
</div>

<script type = "text/javascript" >


    function importCustomsProductFromFile() {

	$('#CustomsProductImportForm').form('submit', {

	    url: '{!! route('importCustomsProductFromFile') !!}',

      onSubmit: function(param) {
          param._token = window.CSRF_TOKEN;

          if($(this).form('validate')) {
              return true;
          }

          return false;
      },

      success: function(result) {

          var result = eval('(' + result + ')');

          if (result.isError) {
              $.messager.show({ title: 'Error', msg: result.msg});
          } else {
              $('#CustomsProductImportDialog').dialog('close');
              $('#CustomsProductsDatagrid').datagrid('reload');
              $.messager.show({ title: 'Success', msg: result.success});
          }
      }
  });

    }


</script>
