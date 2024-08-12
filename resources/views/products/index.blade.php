 <style>
   .datagrid-editable input[type="checkbox"] {
     margin-left: 45%;
   }
 </style>

 <table id="ProductsDatagrid"></table>

 <div id="ProductsDatagridToolbar" style="padding:5px;text-align:center;">
   @can('create_product')
   <a href="#" class="easyui-linkbutton" iconCls="icon-add" onclick="javascript:$('#ProductsDatagrid').edatagrid('addRow')">New</a>
   @endcan
   <a href="#" class="easyui-linkbutton" iconCls="icon-reload" onclick="javascript:$('#ProductsDatagrid').edatagrid('reload')">Reload</a>


   @can('view_category')
   <a href="#" class="easyui-linkbutton" iconCls="icon-reload" onclick="switchDashboardMainTab('Categories', '/categories')">Categories</a>
   @endcan

   @can('view_customs_product')
   <a href="#" class="easyui-linkbutton" iconCls="icon-large-smartart" onclick="switchDashboardMainTab('Customs Products', '/customs_products')">Customs Products</a>
   @endcan

   @can('view_brand')
   <a href="#" class="easyui-linkbutton" iconCls="icon-large-clipart" onclick="switchDashboardMainTab('Brands', '/brands')">Brands</a>
   @endcan

   @can('view_product_check_type')
   <a href="#" class="easyui-linkbutton" iconCls="icon-review-product-checktype" onclick="switchDashboardMainTab('ProductCheckTypes', '/products/check-type-review')">Review Tests</a>
   @endcan

   <a href="javascript:void(0)"
	  class="easyui-menubutton"
	  style="width: 150px;"
	  data-options="plain:false,menu:'#ProductDatagridToggleColumnsMenu',iconCls:'icon-more'">
	   Toggle Columns
   </a>
   <div id="ProductDatagridToggleColumnsMenu" class="jeasyui-menu">
   </div>

 </div>

 <div id="ProductCheckTypeDialog" class="easyui-dialog" data-options="onClose:function() {
        $('#ProductsDatagrid').datagrid('reload');
      }" style="width:1400px;height:500px;padding:10px 20px" title="Product check types" closed="true" modal="true">

   <script type="text/javascript">
     $(function() {
       $('#ProductsDatagrid').edatagrid({
         idField: 'id',
         title: "Products",
         toolbar: '#ProductsDatagridToolbar',
         fit: true,
         border: false,
         width: '100%',
         fitColumns: true,
         singleSelect: true,
         method: 'get',
         rownumbers: true,
         pagination: true,
         remoteFilter: true,
         filterMatchType: 'any',
         url: 'products/list',
         saveUrl: 'products/create',
         updateUrl: 'products/update',
         destroyUrl: 'products/destroy',

         columns: [
           [{
               field: 'id',
               as: 'products.id',
               title: 'Id',
               sortable: true,
               width: 40,
               priority: 1,
             },
             {
               field: 'hide_regapedan',
               title: 'Hide Regapedan',
			   hidden: true,
               sortable: true,
               width: 30,
               align: 'center',
               formatter: function(value) {
                 return value == 1 ? 'Yes' : 'No';
               }

               @can('hide_product_regapedan'),
               editor: {
                 type: 'checkbox',
                 style: 'text-align:center;',
                 options: {
                   required: false,
                   on: '1',
                   off: '0'
                 }
               }
               @endcan
             },
             {
               field: 'disabled',
               title: 'Disabled',
               as: 'products.disabled',
               sortable: true,
               width: 30,
               align: 'center',
               formatter: function(value) {
                 return value == 1 ? 'Yes' : 'No';
               }

               @can('disable_or_enable_product'),
               editor: {
                 type: 'checkbox',
                 style: 'text-align:center;',
                 options: {
                   required: false,
                   on: '1',
                   off: '0'
                 }
               }
               @endcan
             },

             {
               field: 'blended',
               title: 'Blended',
			   hidden: true,
               as: 'products.blended',
               sortable: true,
               width: 30,
               align: 'center',
               formatter: function(value) {
                 return value == 1 ? 'Yes' : 'No';
               }
               @can('toggle_product_blended')
               , editor: {
                 type: 'checkbox',
                 style: 'text-align:center;',
                 options: {
                   required: false,
                   on: '1',
                   off: '0'
                 }
               }
               @endcan
             },

             {
               field: 'skip_payment',
               title: 'Skip $',
			   hidden: true,
               as: 'products.skip_payment',
               sortable: true,
               width: 30,
               align: 'center',
               formatter: function(value) {
                 return value == 1 ? 'Yes' : 'No';
               },
               editor: {
                 type: 'checkbox',
                 style: 'text-align:center;',
                 options: {
                   required: false,
                   on: '1',
                   off: '0'
                 }
               }
             },
             {
               field: 'delay_results',
               title: 'Delays results',
			   hidden: true,
               as: 'products.delay_results',
               sortable: true,
               width: 30,
               align: 'center',
               formatter: function(value) {
                 return value == 1 ? 'Yes' : 'No';
               },
               editor: {
                 type: 'checkbox',
                 style: 'text-align:center;',
                 options: {
                   required: false,
                   on: '1',
                   off: '0'
                 }
               }
             },


             {
               field: 'departments.name',
               title: 'Department',
               sortable: true,
               width: 40,
               editor: {
                 type: 'combobox',
                 options: {
                   url: 'products/departments/list',
                   method: 'get',
                   valueField: 'name',
                   textField: 'name',
                   limitToList: true,
                   hasDownArrow: true,
                   panelHeight: 'auto',
                   required: true
                 }
               }
             },

             {
               field: 'categories.name',
               title: 'Category',
               sortable: true,
               width: 40,
               editor: {
                 type: 'combobox',
                 options: {
                   url: 'products/categories/list',
                   method: 'get',
                   valueField: 'name',
                   textField: 'name',
                   limitToList: true,
                   hasDownArrow: true,
                   panelHeight: 'auto',
                   required: false
                 }
               }
             },

             {
               field: 'name',
               title: 'Name',
               sortable: true,
               width: 50,
               as: 'products.name',
               editor: {
                 type: 'validatebox',
                 options: {
                   required: true
                 }
               }
             },

             {
               field: 'kurdish_name',
               title: 'Kurdish name',
               sortable: true,
               width: 50,
               as: 'products.kurdish_name',
               align: 'right',
               editor: {
                 type: 'validatebox',
                 options: {
                   required: true
                 }
               }
             },

             {
               field: 'alternative_names',
               title: 'Alternative',
			   hidden: true,
               sortable: true,
               width: 50,
               as: 'products.alternative_names',
               align: 'center',
               editor: {
                 type: 'validatebox',
                 options: {
                   required: false
                 }
               }
             },

             {
               field: 'arabic_name',
               title: 'Arabic name',
               sortable: true,
               width: 50,
               as: 'products.arabic_name',
               align: 'right',
               editor: {
                 type: 'validatebox',
                 options: {
                   required: false
                 }
               }
             },

             {
               field: 'customs_name',
               title: 'Customs name',
               sortable: true,
               width: 50,
               as: 'products.customs_name',
               align: 'right',
               editor: {
                 type: 'combobox',
                 options: {
					 url: 'customs_products/json_list',
					 mode: 'remote',
					 method: 'get',
					 valueField: 'customs_product_name',
					 textField: 'customs_product_name',
					 limitToList: true,
					 hasDownArrow: true,
					 panelHeight: 'auto',
					 prompt: 'Select a customs name',
					 required:true
                 }
               }
             },

             {
               field: 'coc',
               title: 'COC',
			   hidden: true,
               sortable: true,
               width: 30,
               align: 'center',
               formatter: function(value) {
                 return value == 1 ? 'Yes' : 'No';
               },
               editor: {
                 type: 'checkbox',
                 style: 'text-align:center;',
                 options: {
                   required: false,
                   on: '1',
                   off: '0'
                 }
               }
             },

             {
               field: 'date_limit',
               title: 'Day Limit #',
				 hidden: true,
               sortable: true,
               width: 30,
               align: 'center',
               editor: {
                 type: 'numberbox',
                 options: {
                   required: false
                 }
               }
             },
             {
               field: 'amount_limit',
               title: 'Amount',
               sortable: true,
               width: 30,
               align: 'center',
               editor: {
                 type: 'numberbox',
                 options: {
                   required: false
                 }
               }
             },
             {
               field: 'requires_truck_limit',
               title: 'Limit Truck #',
               sortable: true,
               width: 30,
               align: 'center',
               formatter: function(value) {
                 return value == 1 ? 'Yes' : 'No';
               },
               editor: {
                 type: 'checkbox',
                 style: 'text-align:center;',
                 options: {
                   required: false,
                   on: '1',
                   off: '0'
                 }
               }
             },

             {
               field: 'fee_if_less',
               title: 'Fee <',
               sortable: true,
               width: 30,
               align: 'center',
               editor: {
                 type: 'numberbox',
                 options: {
                   required: false
                 }
               }
             },

             {
               field: 'fee_limit',
               title: 'Limit',
               sortable: true,
               width: 10,
               align: 'center',
               editor: {
                 type: 'numberbox',
                 options: {
                   required: false
                 }
               }
             },

             {
               field: 'fee_if_more',
               title: '> Fee',
               sortable: true,
               width: 30,
               align: 'center',
               editor: {
                 type: 'numberbox',
                 options: {
                   required: false
                 }
               }
             },

             {
               field: 'is_paid_individually',
               title: 'Paid individually',
               sortable: true,
               width: 40,
               as: 'products.is_paid_individually',
               sortable: true,
               align: 'center',
               formatter: function(value) {
                 return value == 1 ? 'Yes' : 'No';
               },
               editor: {
                 type: 'checkbox',
                 style: 'text-align:center;',
                 options: {
                   required: false,
                   on: '1',
                   off: '0'
                 }
               }
             },

             {
               field: 'check_types',
               title: 'Tests',
               width: 20,
               align: 'center',
               formatter: function(val, row) {
                 val = (val || []).length;
                 @can('view_product_check_type')
                 if (!row.editing) {
                   return '<button onclick="showProductCheckTypes(' + row.id + ')">' + val + '</button>';
                 }
                 @endcan
                 return val;
               },
             },

             {
               field: 'note',
               title: 'Note',
               width: 50,
               as: 'products.note',
               align: 'left',
               editor: {
                 type: 'validatebox',
                 options: {
                   required: false
                 }
               }
             },

             {
               field: 'action',
               title: 'Action',
               width: 60,
               align: 'center',
               formatter: function(value, row, index) {
                 if (row.editing) {
                   var s = '<button onclick="saveRow(\'ProductsDatagrid\', ' + index + ')">Save</button> ';
                   var c = '<button onclick="cancelRow(\'ProductsDatagrid\', ' + index + ')">Cancel</button>';
                   return s + c;
                 } else {
                   var e, d;
                   @can('update_product')
                   e = '<button onclick="editRow(\'ProductsDatagrid\', ' + index + ')">Edit</button> ';
                   @endcan
                   @can('destroy_product')
                   d = '<button onclick="deleteRow(\'ProductsDatagrid\', ' + index + ')">Delete</button> ';
                   @endcan
                   return e + d;
                 }
               }
             }

           ]
         ],
         onBeforeEdit: function(index, row) {
           row.isNew = row.isNewRecord;
           row.editing = true;
           $(this).edatagrid('refreshRow', index);

           var col = $(this).datagrid('getColumnOption', 'departments.name'); // Here your column name
           if (row.id) {
             col.editor = null;
           } else {
             col.editor = {
               type: 'combobox',
               options: {
                 url: 'products/departments/list',
                 method: 'get',
                 valueField: 'name',
                 textField: 'name',
                 limitToList: true,
                 hasDownArrow: true,
                 panelHeight: 'auto',
                 required: true
               }
             }
           }
         }
       });
     });

	 {{-- $('#ProductsDatagrid').datagrid('columnToggle'); --}}
	 $('#ProductsDatagrid').datagrid('generateToggleColumn');

     $('#ProductsDatagrid').edatagrid('enableFilter', [{
         field: 'action',
         type: 'label'
       },
       {
         field: 'check_types',
         type: 'label'
       },
       {
         field: 'coc',
         type: 'combobox',
         options: {
           panelHeight: 'auto',
           data: [{
             value: '',
             text: 'All'
           }, {
             value: '1',
             text: 'Yes'
           }, {
             value: '0',
             text: 'No'
           }],
           onChange: function(value) {
             if (value == '') {
               $('#ProductsDatagrid').datagrid('removeFilterRule', 'coc');
             } else {
               $('#ProductsDatagrid').datagrid('addFilterRule', {
                 field: 'coc',
                 op: 'equal',
                 value: value
               });
             }
             $('#ProductsDatagrid').datagrid('doFilter');
           }
         }
       },
       {
         field: 'requires_truck_limit',
         type: 'combobox',
         options: {
           panelHeight: 'auto',
           data: [{
             value: '',
             text: 'All'
           }, {
             value: '1',
             text: 'Yes'
           }, {
             value: '0',
             text: 'No'
           }],
           onChange: function(value) {
             if (value == '') {
               $('#ProductsDatagrid').datagrid('removeFilterRule', 'requires_truck_limit');
             } else {
               $('#ProductsDatagrid').datagrid('addFilterRule', {
                 field: 'requires_truck_limit',
                 op: 'equal',
                 value: value
               });
             }
             $('#ProductsDatagrid').datagrid('doFilter');
           }
         }
       },
       {
         field: 'hide_regapedan',
         type: 'combobox',
         options: {
           panelHeight: 'auto',
           data: [{
             value: '',
             text: 'All'
           }, {
             value: '1',
             text: 'Yes'
           }, {
             value: '0',
             text: 'No'
           }],
           onChange: function(value) {
             if (value == '') {
               $('#ProductsDatagrid').datagrid('removeFilterRule', 'products.hide_regapedan');
             } else {
               $('#ProductsDatagrid').datagrid('addFilterRule', {
                 field: 'products.hide_regapedan',
                 op: 'equal',
                 value: value
               });
             }
             $('#ProductsDatagrid').datagrid('doFilter');
           }
         }
       },
       {
         field: 'disabled',
         type: 'combobox',
         options: {
           panelHeight: 'auto',
           data: [{
             value: '',
             text: 'All'
           }, {
             value: '1',
             text: 'Yes'
           }, {
             value: '0',
             text: 'No'
           }],
           onChange: function(value) {
             if (value == '') {
               $('#ProductsDatagrid').datagrid('removeFilterRule', 'products.disabled');
             } else {
               $('#ProductsDatagrid').datagrid('addFilterRule', {
                 field: 'products.disabled',
                 op: 'equal',
                 value: value
               });
             }
             $('#ProductsDatagrid').datagrid('doFilter');
           }
         }
       },
       {
         field: 'skip_payment',
         type: 'combobox',
         options: {
           panelHeight: 'auto',
           data: [{
             value: '',
             text: 'All'
           }, {
             value: '1',
             text: 'Yes'
           }, {
             value: '0',
             text: 'No'
           }],
           onChange: function(value) {
             if (value == '') {
               $('#ProductsDatagrid').datagrid('removeFilterRule', 'products.skip_payment');
             } else {
               $('#ProductsDatagrid').datagrid('addFilterRule', {
                 field: 'products.skip_payment',
                 op: 'equal',
                 value: value
               });
             }
             $('#ProductsDatagrid').datagrid('doFilter');
           }
         }
       },

       {
         field: 'delay_results',
         type: 'combobox',
         options: {
           panelHeight: 'auto',
           data: [{
             value: '',
             text: 'All'
           }, {
             value: '1',
             text: 'Yes'
           }, {
             value: '0',
             text: 'No'
           }],
           onChange: function(value) {
             if (value == '') {
               $('#ProductsDatagrid').datagrid('removeFilterRule', 'products.delay_results');
             } else {
               $('#ProductsDatagrid').datagrid('addFilterRule', {
                 field: 'products.delay_results',
                 op: 'equal',
                 value: value
               });
             }
             $('#ProductsDatagrid').datagrid('doFilter');
           }
         }
       }
     ]);


     function showProductCheckTypes(productId) {
       $('#ProductCheckTypeDialog').dialog('open')
         .dialog('refresh', 'products/check-type?product_id=' + productId);
     }
   </script>
