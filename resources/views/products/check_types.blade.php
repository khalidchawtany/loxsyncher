<div class="easyui-layout" fit="true">

  <div data-options="region:'north', height:'50px'" style="overflow:hidden;text-align:center;padding:10px;background:#eee;">
    <h2>
      {!! $product->name !!}
    </h2>
  </div>

  <div data-options="region:'center', border:false" style="padding-top:5px;">
    @can('view_product_check_type')
      <div class="easyui-panel" fit="true" style="text-align:center;overflow:hidden;">
        <form id="ProductCheckTypeForm" method="post" novalidate>

          <div class="fitem">
            <input class="easyui-combogrid" name="check_type_id" id="check_type" style="width:200px" data-options="
                            panelWidth:500,
                            url: 'products/check-type/list/check-types?product_id={!! $product->id !!}',
                            idField:'id',
                            textField:'check_type',
                            mode:'remote',
                            method: 'get',
                            required: true,
                            fitColumns:true,
                            selectOnNavigation: false,
                            pagination:true,
                            columns:[[
                              {field:'category',title:'Category',width:60, align:'left'},
                              {field:'subcategory',title:'Subcategory',width:60, align:'left'},
                              {field:'price',title:'Price',width:80},
                            ]],">
              <button type="button" class="easyui-linkbutton c6" iconCls="icon-save" onclick="saveProductCheckType()">Save</button>
          </div>

        </form>
      </div>
    @endcan
  </div>

  <div data-options="region:'south', border:false" style="height:335px;padding-top:5px;">

    <div class="easyui-panel" style="height:275px;width:100%;">

      <table id="ProductCheckTypeDatagrid"></table>

      <div id="ProductCheckTypeDatagridToolbar" style="padding:5px;text-align:center;">
          <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#ProductCheckTypeDatagrid').datagrid('reload')">Reload</a>
      </div>

    </div>

    <div class="easyui-panel" style="margin-top:5px;height:50px;overflow:hidden;text-align:center;padding:10px;background:#eee;">
      <h2 id="ProductCheckTypeSum">
        Test
      </h2>
    </div>

  </div>
</div>



<script type="text/javascript">

  $(function(){
    $('#ProductCheckTypeDatagrid').edatagrid({
      idField:'id',
      toolbar:'#ProductCheckTypeDatagridToolbar',
      fit:true,
      border:false,
      fitColumns:true,
      singleSelect:true,
      method:'get',
      rownumbers:true,
      url:'products/check-type/list?product_id={!! $product->id !!}',
      updateUrl: 'products/check-type/update?product_id={!! $product->id !!}',

        columns: [[
          {field:'id',title:'T. Type Id',width:30, align:'center', },
          {field:'category',title:'Category',width:30},
          {field:'subcategory',title:'Subcategory',width:60},
          {field:'price',title:'Price',width:30},
          {field:'check_methods',title:'Methods S.',width:50,
            editor:{
              type:'validatebox',
              options:{
                required:false
              }
            }
          },
          {field:'check_limits',title:'Limits S.',width:50,
            editor:{
              type:'validatebox',
              options:{
                required:false
              }
            }
          },
          {field:'check_normal_range',title:'Normal Range',width:50,
            editor:{
              type:'validatebox',
              options:{
                required:false
              }
            }
          },
          {field:'note',title:'Note',width:50,
            editor:{
              type:'validatebox',
              options:{
                required:false
              }
            }
          },
          {field:'order',title:'Order',width:30,
            editor:{
              type:'validatebox',
              options:{
                required:false
              }
            }
          },
          {field:'active',title:'Active',width:30,align:'center',

            formatter:function(value){
              if (value == 0)
                return 'No';
              else if (value == 1)
                return 'Yes';
            },
            editor:{
              type:'checkbox',
                    options:{
                        required:false,
                        on:'1',off:'0'
                  }
            }
          },
          {field:'action',title:'Action',width:60,align:'center',
              formatter:function(value,row,index){
                  if (row.editing){
                      var s = '<button onclick="saveRow(\'ProductCheckTypeDatagrid\', ' + index +')">Save</button> ';
                      var c = '<button onclick="cancelRow(\'ProductCheckTypeDatagrid\', ' + index +')">Cancel</button>';
                      return s+c;
                  } else {
                      var e,d;
                      @can('update_product_check_type')
                          e = '<button onclick="editRow(\'ProductCheckTypeDatagrid\', ' + index + ')">Edit</button> ';
                      @endcan
                      @can('destroy_product_check_type')
                          d =  '<button onclick="deleteProductCheckType(' + row.id + ')">Delete</button> ';
                      @endcan
                      return e+d;

                    }
              }
          }

        ]],

      onLoadSuccess:function(data) {
        if(data) {
          if(data.rows) {

            var sum = _.sum(_.map(data.rows, function(row) {
              return Number.parseFloat(row.price || 0);
            }));

            $('#ProductCheckTypeSum').text(sum);
          }
        }
      }

    });
  });

  function saveProductCheckType() {

    $('#ProductCheckTypeForm').form('submit', {

        url: 'products/check-type/create?product_id={!! $product->id !!}',

        onSubmit: function(param) {
        	   param._token = window.CSRF_TOKEN;
             return $(this).form('validate');
        },

        success: function(result) {

            var result = eval('(' + result + ')');

            if (result.isError) {

        		  $.messager.show({ title: 'Error', msg: result.msg});


            } else {

                $('#ProductCheckTypeDatagrid').datagrid('reload')
                $('#ProductCheckTypeForm').form('clear');
                $.messager.show({ title: 'Success', msg: 'Operation performed successfully!'});
                $('#ProductCheckTypeForm #check_type').focus();


            }
        }
    });

  }

  function deleteProductCheckType(id) {

      $.messager.confirm('Confirm', 'Are you sure you want to delete this user?', function(r) {
          if (r) {

              $.post('products/check-type/destroy', {
                  'id': id,
                  'product_id': '{!! $product->id !!}'
              }, function(result) {
                  if (result.success) {

                      $('#ProductCheckTypeDatagrid').datagrid('reload');

                  } else {
                  	$.messager.show({ title: 'Error', msg: result.msg});
                  }
              }, 'json');
          }
      });
  }

</script>
