@php

  $url = isset($permission_request)? 'permission_requests/update' : 'permission_requests/create';

  $model = 'PermissionRequest';
  $title = 'New PermissionRequest Dialog';
  $dialogWidth = 900;
  $dialogHeight = 350;

  if (!isset($permission_request) || $permission_request == null) {


    $permission_request = (object) [
      'id' => null,
      "requested_for" => null,
      "permission_name" => null,
      "description" => null,
      "note" => null,
    ];
  }

@endphp

@include('styles')

<div class="easyui-layout" fit="true">

  <div data-options="region:'center', border:false">

    {{--Quick Change Product Form--}}
    <form id="{{ $model }}Form" method="post" novalidate>

      <input type="hidden" name="id" value="{!! $permission_request->id !!}">


      <table class="w-full left medium">
        <tr>
          <td colspan="4">
            <div class="mt-2 ftitle">Permission Request Info:</div>
          </td>
        </tr>

        <tr>
          <td>For (User)</td>
          <td>
            <div class="mt-1">

            <select id="requested_for"
                    name="requested_for"
                    value="{!! $permission_request->requested_for !!}"
                    class="easyui-combogrid w-300"
                    style="width:173;"
                    data-options="
                                  url: 'users/json_list',
                                  mode: 'remote',
                                  method: 'get',
                                  idField: 'id',
                                  textField: 'kurdish_name',
                                  value: '{!! $permission_request->requested_for !!}',
                                  required:true,
                                  columns: [[
                                    {field:'id',title:'Id',width:7},
                                    {field:'kurdish_name',title:'Name',width:10},
                                    {field:'email',title:'Email',width:6}
                                  ]],

{{--
                                  onChange: function(newVal, oldVal) {
                                    $('#person_id').val(null);
                                  },

                                  onSelect: function(index, record) {
                                    if (record) {
                                      $('#person_id').val(record.id);
                                      $('#name').textbox('setValue',record.name);
                                      $('#dob').datebox('setValue', record.dob);
                                      $('#nationality').textbox('setValue', record.nationality);
                                      $('#gender').combobox('setValue', record.gender);
                                      $('#address').textbox('setValue', record.address);
                                      $('#phone').textbox('setValue', record.phone);
                                      $('#note').textbox('setValue', record.note);
                                      $('#last_positive_result').html(record.last_positive_result);
                                    }
                                  },
--}}
                                  fitColumns: true,
                                  panelWidth:650,
                                  panelHeight:'auto'
                      ">
            </select>
            </div>
          </td>

          <td>Perm./Role</td>
          <td>
            <input name="permission_name"
                   value="{!! $permission_request->permission_name !!}"
                   class="easyui-textbox  w-300"
                   data-options="required: true"
                   >
          </td>
        </tr>

        <tr>
          <td>Description</td>
          <td colspan="3">
            <div class="mt-1">
            <input name="description" value="{!! $permission_request->description !!}"
                                  class="easyui-textbox"
                                  style="width:96%; height:54px; "
                                  multiline="true"
                                  data-options="required: true"
                                  >
            </div>
          </td>
        </tr>

        <tr>
          <td>Note</td>
          <td colspan="3">
            <div class="mt-1">
            <input name="note" value="{!! $permission_request->note !!}"
                                  class="easyui-textbox"
                                  style="width:96%; height:54px; "
                                  multiline="true"
                                  >
            </div>
          </td>
        </tr>

      </table>
    </form>
  </div>

  <div class="panel-buttons" data-options="region:'south', height:'auto'">

    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="savePermissionRequest()">Save</a>

    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel"
      onclick="$('#PermissionRequestDialog').dialog('close');$('#PermissionRequestDatagrid').edatagrid('reload');" style="width:90px">Cancel</a>

  </div>

</div>

<script type="text/javascript">
  $(function() {
    $('#PermissionRequestDialog')
      .dialog({
        width: {!! $dialogWidth !!},
        height: {!! $dialogHeight !!}
      })
      .dialog('center')
      .dialog('setTitle', '{{ $title }}')
      .dialog('open');

		// For some reason this form gets posted without my code!
		$('#{{$model}}Form').form({ onSubmit: function() {
			return false;
		}});

  });


  function savePermissionRequest() {

    $('#{{$model}}Form').form('submit', {

      url: '{!! $url !!}',

      onSubmit: function(param) {
        param._token = window.CSRF_TOKEN;

        if ($(this).form('validate')) {
          return true;
        }

        return false;
      },

      success: function(result) {

        var result = eval('(' + result + ')');

        if (result.isError) {
          $.messager.show({ title: 'Error', msg: result.msg });
        } else {
          $('#PermissionRequestDialog').dialog('close');
          $('#PermissionRequestDatagrid').edatagrid('reload');
          $.messager.show({ title: 'Success', msg: 'Operation performed successfully!' });
        }
      }
    });

  }

</script>
