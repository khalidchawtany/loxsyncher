<div id="SpecificationDatagridContainer" style="width:100%;height:100%;">
    <table id="SpecificationDatagrid"></table>

    <div id="SpecificationDatagridToolbar" style="padding:5px;text-align:center;">
        @can('create_specification')
            <a href="#" class="easyui-linkbutton" iconCls="icon-add"  onclick="javascript:$('#SpecificationDatagrid').edatagrid('addRow')">New</a>
        @endcan
        <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#SpecificationDatagrid').edatagrid('reload')">Reload</a>

        @can('create_specification')
        <a href="#" class="easyui-linkbutton" iconCls="icon-import"  onclick="showAttachDocumentDialog()">Attach</a>
        @endcan
        {{-- <a href="#" class="easyui-linkbutton" iconCls="icon-large-smartart"  onclick="showDocumentDialog()">Document</a> --}}
    </div>

</div>

<div id="AttachDocumentDialog" class="easyui-dialog" style="width:500px;height:230px;"closed="true" modal="true">
</div>

<div id="DocumentDialog" class="easyui-dialog" style="width:100%;height:100%;"closed="true" modal="true">
</div>

<style media="screen">

    #SpecificationDatagridContainer .datagrid-view .datagrid-body{
        background: url('/img/datagrid/specification.png') no-repeat center;
    }

</style>


<script type="text/javascript">

  $(function(){
    $('#SpecificationDatagrid').edatagrid({
      idField:'id',
      title: 'Standards',
      toolbar:'#SpecificationDatagridToolbar',
      fit:true,
      border:false,
      fitColumns:true,
      singleSelect:true,
      method:'get',
      rownumbers:true,
      pagination:true,
      remoteFilter:true,
      filterMatchType: 'any',
      url:'specifications/list',
      saveUrl: 'specifications/create',
      updateUrl: 'specifications/update',
      destroyUrl: 'specifications/destroy',

        columns: [[

          {field:'category',title:'Category',width:40
              @can('update_specification')
              , editor: {
              type: 'combobox',
              options: {
                panelHeight: 'auto',
                hasDownArrow: true,
                limitToList: false,
                valueField: 'category',
                textField: 'category',
                method:'get',
                url:'specifications/category/list',
              }
            }
              @endcan
          },
          {field:'title',title:'Title',width:50
              @can('update_specification')
              , editor:{
              type:'validatebox',
              options:{
                required:true,
                //validType: '',
              }
            }
              @endcan
          },
          {field:'title_eng',title:'Title En',width:50
              @can('update_specification')
              , editor:{
              type:'validatebox',
              options:{
                required:true,
                //validType: '',
              }
            }
              @endcan
          },
          {field:'number',title:'Number',width:20, align:'center'
              @can('update_specification')
              ,
            editor:{
              type:'validatebox',
              options:{
                required:true,
                //validType: '',
              }
            }
              @endcan
          },
          {field:'standard',title:'Standard',width:25, align:'center'
              @can('update_specification')
              ,
            editor:{
              type:'validatebox',
              options:{
                required:true,
                //validType: '',
              }
            }
              @endcan
          },
          {
            field:'status',
            as: 'specifications.status',
            title:'Status',
            sortable: true,
            width:20,
            align:'center'

              @can('update_specification')
              ,
            editor:{
              type:'combobox',
              options:{
                required:true,
                valueField:'value',
                valueField:'text',
                panelHeight:'auto',
                limitToList:true,
                data:[{value:'WIP', text:'WIP'},{value:'Active', text:'Active'},{value:'Suspended',text:'Suspended'}],
              }
            }
              @endcan
          },
          {field:'document_url', title:'Document',width:30, align:'center',
              formatter:function(value,row,index){
                  var s = '<button onclick="showDocumentDialog(' + row.id + ',\'' + row.title + '\')">Show</button> ';
                  if (value == null) {
                      s = 'No Document';
                  }
                  return s;
              }
          },

          {field:'note',title:'Note',width:50

              @can('update_specification')
              ,
            editor:{
              type:'validatebox',
              options:{
                required:false,
                //validType: '',
              }
            }
              @endcan
          }

              @if(user()->canany(['update_specification','destroy_specification']))
            ,

          {field:'action',title:'Action',width:50,align:'center',
              formatter:function(value,row,index){
                  if (row.editing){
                      var r = '';
                      r += '<button onclick="saveRow(\'SpecificationDatagrid\', ' + index +')">Save</button> ';
                      r += '<button onclick="cancelRow(\'SpecificationDatagrid\', ' + index +')">Cancel</button>';
                      return r;
                  } else {
                      var r = '';

                      @can('update_specification')
                          r +='<button onclick="editRow(\'SpecificationDatagrid\', ' + index + ')">Edit</button> ';
                      @endcan

                      @can('destroy_specification')
                          r += '<button onclick="deleteRow(\'SpecificationDatagrid\', ' + index + ')">Delete</button> ';
                      @endcan
                      return r;
                  }
              }
          }
            @endif

        ]],

        onBeforeEdit:function(index,row){
            row.editing = true;
            $(this).edatagrid('refreshRow', index);
        },
        onAfterEdit:function(index,row){
            row.editing = false;
            $(this).edatagrid('refreshRow', index);
        },
        onCancelEdit:function(index,row){
            row.editing = false;
            $(this).edatagrid('refreshRow', index);
        },

        onDestroy: function() {
        }

    });
  });

  $('#SpecificationDatagrid').edatagrid('enableFilter', [
      {
          field: 'action',
          type: 'label'
      },
      {
          field:'status',
          type:'combobox',
          options:{
              value: 'Active',
              panelHeight:'auto',
              data:[{value:'',text:'All'},{value:'WIP', text:'WIP'},{value:'Active', text:'Active'},{value:'Suspended',text:'Suspended'}],
              onChange:function(value){
                  if (value == ''){
                      $('#SpecificationDatagrid').datagrid('removeFilterRule', 'specifications.status');
                  } else {
                      $('#SpecificationDatagrid').datagrid('addFilterRule', {
                          field: 'specifications.status',
                          op: 'equal',
                          value: value
                      });
                  }
                  $('#SpecificationDatagrid').datagrid('doFilter');
              }
          }
      }
  ]);

  function showAttachDocumentDialog() {

    // var row = quotaDataGrid.datagrid('getSelected');
    var row = $('#SpecificationDatagrid').datagrid('getSelected');

    if (!row) {
      $.messager.show({ title: 'Error', msg: 'Please select a specification'});
      return;
    }

    $('#AttachDocumentDialog').dialog('open').dialog('setTitle', 'Attach Document')
      .dialog('refresh', 'specifications/show_attach_document_dialog/'+ row.id);
  }

  function showDocumentDialog(id, title) {
    $('#DocumentDialog').dialog('open').dialog('setTitle', title)
      .dialog('refresh', 'specifications/show_document_dialog/'+ id);
  }

</script>
