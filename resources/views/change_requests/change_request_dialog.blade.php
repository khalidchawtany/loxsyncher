@php

  $url = isset($change_request)? 'change_requests/update' : 'change_requests/create';

  $model = 'ChangeRequest';
  $title = 'New Change Request Dialog';
  $dialogWidth = 900;
  $dialogHeight = 350;

  if (!isset($change_request) || $change_request == null) {


    $change_request = (object) [
      'id' => null,
      "title" => null,
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

      <input type="hidden" name="id" value="{!! $change_request->id !!}">


      <table class="w-full left medium">
        <tr>
          <td colspan="4">
            <div class="mt-2 ftitle">Change Request Info:</div>
          </td>
        </tr>

        <tr>
          <td>Title</td>
          <td>
            <input name="title"
                   value="{!! $change_request->title !!}"
                   class="easyui-textbox  w-300"
                   data-options="required: true"
                   >
          </td>
        </tr>

        <tr>
          <td>Description</td>
          <td colspan="3">
            <div class="mt-1">
            <input name="description" value="{!! $change_request->description !!}"
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
            <input name="note" value="{!! $change_request->note !!}"
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

    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveChangeRequest()">Save</a>

    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel"
      onclick="$('#ChangeRequestDialog').dialog('close');$('#ChangeRequestDatagrid').edatagrid('reload');" style="width:90px">Cancel</a>

  </div>

</div>

<script type="text/javascript">
  $(function() {
    $('#ChangeRequestDialog')
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


  function saveChangeRequest() {

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
          $('#ChangeRequestDialog').dialog('close');
          $('#ChangeRequestDatagrid').edatagrid('reload');
          $.messager.show({ title: 'Success', msg: 'Operation performed successfully!' });
        }
      }
    });

  }

</script>
