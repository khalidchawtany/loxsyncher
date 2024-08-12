 <style>
   .datagrid-editable input[type="checkbox"] {
     margin-left: 45%;
   }
 </style>

 <table id="DepartmentsDatagrid"></table>

 <div id="DepartmentsDatagridToolbar" style="padding:5px;text-align:center;">
   @if(user()->HasRole('Super'))
     <a href="#" class="easyui-linkbutton" iconCls="icon-add" onclick="javascript:$('#DepartmentsDatagrid').edatagrid('addRow')">New</a>
   @endif
   <a href="#" class="easyui-linkbutton" iconCls="icon-reload" onclick="javascript:$('#DepartmentsDatagrid').edatagrid('reload')">Reload</a>
 </div>

 <script type="text/javascript">
   $(function() {
     $('#DepartmentsDatagrid').edatagrid({
       idField: 'id',
       title: 'Departments',
       toolbar: '#DepartmentsDatagridToolbar',
       fit: true,
       border: false,
       fitColumns: true,
       singleSelect: true,
       method: 'get',
       rownumbers: true,
       pagination: true,
       remoteFilter: true,
       filterMatchType: 'any',
       url: 'departments/list',
       saveUrl: 'departments/create',
       updateUrl: 'departments/update',
       destroyUrl: 'departments/destroy',

       columns: [
         [{
             field: 'name',
             title: 'Name',
             sortable: true,
             editor: {
               type: 'validatebox',
               options: {
                 required: true
               }
             }
           },
           {
             field: 'is_third_party',
             title: 'Third Party',
             align: 'center',
             sortable: true,
             formatter: function(val) {
               return (val == '1') ? 'Yes' : 'No';
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
             field: 'needs_inspections_approved',
             title: 'Signs Inspections',
             sortable: true,
             align: 'center',
             formatter: function(val) {
               return (val == '1') ? 'Yes' : 'No';
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
             field: 'manager_name',
             title: 'Manager Name',
             sortable: true,
             editor: {
               type: 'validatebox',
               options: {
                 required: false
               }
             }
           },


           @if(user()->HasRole('Super'))
           {
             field: 'props',
             title: '<div title="Bit Order: Destination, Country, Balance, Batch, Merchant, Office. Ex: 15 => 001011">Properties</div>',
             align: 'center',
             editor: {
               type: 'numberbox',
               options: {
                 required: true
               },
             },
           },
           @endif {
             field: 'kurdish_name',
             title: 'Kurdish Name',
             sortable: true,
             editor: {
               type: 'validatebox',
               options: {
                 required: true
               }
             }
           },

           {
             field: 'to',
             title: 'To (Whom)',
             sortable: true,
             editor: {
               type: 'validatebox',
               options: {
                 required: true
               }
             }
           },

           {
             field: 'to_arabic',
             sortable: true,
             title: 'To Arabic (Whom)',
             editor: {
               type: 'validatebox',
               options: {
                 required: true
               }
             }
           },



           {
             field: 'delays_results',
             title: 'Delays Results',
             align: 'center',
             sortable: true,
             formatter: function(val) {
               return (val == '1') ? 'Yes' : 'No';
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
             field: 'sample_count',
             title: 'Sample #',
             align: 'center',
             sortable: true,
             editor: {
               type: 'validatebox',
               options: {
                 required: true
               }
             }
           },

           {
             field: 'permit_copies',
             title: 'Permit #',
             align: 'center',
             sortable: true,
             editor: {
               type: 'numberbox',
               options: {
                 required: true
               }
             }
           },

           {
             field: 'transaction_copies',
             title: 'Transaction #',
             align: 'center',
             sortable: true,
             editor: {
               type: 'numberbox',
               options: {
                 required: true
               }
             }
           },

           {
             field: 'failed_transaction_copies',
             title: 'Failed Transaction #',
             align: 'center',
             sortable: true,
             editor: {
               type: 'numberbox',
               options: {
                 required: true
               }
             }
           },

           {
             field: 'invoice_copies',
             title: 'Invoice #',
             align: 'center',
             sortable: true,
             editor: {
               type: 'numberbox',
               options: {
                 required: true
               }
             }
           },

           {
             field: 'action',
             title: 'Action',
             align: 'center',
             formatter: function(value, row, index) {
               if (row.editing) {
                 var s = '<button onclick="saveRow(\'DepartmentsDatagrid\', ' + index + ')">Save</button> ';
                 var c = '<button onclick="cancelRow(\'DepartmentsDatagrid\', ' + index + ')">Cancel</button>';
                 return s + c;
               } else {
                 var r = "";
                 @can('update_department')
                   r += '<button onclick="editRow(\'DepartmentsDatagrid\', ' + index + ')">Edit</button> ';
                 @endcan
                 @if(user()->HasRole('Super'))
                   r += '<button onclick="deleteRow(\'DepartmentsDatagrid\', ' + index + ')">Delete</button> ';
                 @endif
                 return r;
               }
             }
           }

         ]
       ]

     });
   });

   $('#DepartmentsDatagrid').edatagrid('enableFilter', [{
     field: 'action',
     type: 'label'
   }]);
 </script>
