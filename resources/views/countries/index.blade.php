<div id="CountryDatagridContainer" style="width:100%;height:100%;">
    <table id="CountryDatagrid"></table>

    <div id="CountryDatagridToolbar" style="padding:5px;text-align:center;">
        @can('create_country')
            <a href="#" class="easyui-linkbutton" iconCls="icon-add"  onclick="javascript:$('#CountryDatagrid').edatagrid('addRow')">New</a>
        @endcan
        <a href="#" class="easyui-linkbutton c5"  onclick="setDefaultCountry()">Change default country</a>
        <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#CountryDatagrid').edatagrid('reload')">Reload</a>
    </div>

</div>

<style media="screen">

    #CountryDatagridContainer .datagrid-view .datagrid-body{
        background: url('img/datagrid/country.png') no-repeat center;
    }

</style>


<script type="text/javascript">

  $(function(){
    $('#CountryDatagrid').edatagrid({
      idField:'id',
      title: 'Countries',
      toolbar:'#CountryDatagridToolbar',
      fit:true,
      border:false,
      fitColumns:true,
      singleSelect:true,
      method:'get',
      rownumbers:true,
      pagination:true,
      remoteFilter:true,
      filterMatchType: 'any',
      url:'countries/list',
      saveUrl: 'countries/create',
      updateUrl: 'countries/update',
      destroyUrl: 'countries/destroy',

        columns: [[
          {field:'name',title:'Country',width:50,
            editor:{
              type:'validatebox',
              options:{
                required:false,
                //validType: '',
              }
            },
          },

          {field:'action',title:'Action',width:100,align:'center',
              formatter:function(value,row,index){
                  if (row.editing){
                      var s = '<button onclick="saveRow(\'CountryDatagrid\', ' + index +')">Save</button> ';
                      var c = '<button onclick="cancelRow(\'CountryDatagrid\', ' + index +')">Cancel</button>';
                      return s+c;
                  } else {
                      var e,d;
                      @can('update_country')
                        e = '<button onclick="editRow(\'CountryDatagrid\', ' + index + ')">Edit</button> ';
                      @endcan
                      @can('destroy_country')
                        d = '<button onclick="deleteRow(\'CountryDatagrid\', ' + index + ')">Delete</button> ';
                      @endcan
                      return e+d;
                  }
              }
          }

        ]],

        rowStyler: function(index, row) {
          if(row.is_default) {
            return 'background-color:Green';
          }
        }

    });
  });

  $('#CountryDatagrid').edatagrid('enableFilter', [
      {
          field: 'action',
          type: 'label'
      }
  ]);

  function setDefaultCountry() {

	    var row = $('#CountryDatagrid').datagrid('getSelected');

	    if (row) {

            $.post('/countries/set-default?id=' + row.id, function(result){

            if (result.success){
                $('#CountryDatagrid').datagrid('reload');
            } else {
                $.messager.show({ title: 'Error', msg: 'Could not perform the operation!'});
            }
            },'json');

	    } else {
	    	$.messager.show({title:'Error', msg:'Please select a country'});
	    }

	}


</script>
