@php

  $title = 'Permission Request';
  $dialogWidth = 700;
  $dialogHeight = 450;
  $pr_status = -1;

  if($permission_request->status != null) {
    $pr_status = $permission_request->status;
  }

@endphp

@include('styles')

<div class="easyui-layout" fit="true">

  <div class="pa-2 pt-0" data-options="region:'center', border:false">

      <table class="w-full left medium ttop tpy-5">
        <tr>
          <td colspan="2">
            <div class="mt-2 ftitle">Permission Request Info:</div>
          </td>
        </tr>

        <tr>
          <th class="w-100">Request By</th>
          <td>
              {!! $permission_request->user->kurdish_name !!}
          </td>

        </tr>

        <tr>
          <th class="w-100">For (User)</th>
          <td>
              {!! $permission_request->for_user->kurdish_name !!}
          </td>

        </tr>

        <tr>

          <th>Perm./Role</th>
          <td>
            {!! $permission_request->permission_name !!}
          </td>
        </tr>

        <tr>
          <th>Description</th>
          <td>
              {!! $permission_request->description !!}
          </td>
        </tr>

        <tr>
          <th>Note</th>
          <td >
              {!! $permission_request->note !!}
          </td>
        </tr>

        @can('reject_permission_request')
        <tr>
          <td colspan="2"><hr/></td>
        </tr>

        <tr>
          <th>Reason</th>
          <td >
            <input id="permission_request_reject_reason"
                   name="permission_request_reject_reason"
                   value="{!! $permission_request->reason !!}"
                   class="easyui-textbox"
                   style="width:96%; height:54px; "
                   multiline="true"
                   >
          </td>
        </tr>
        @endcan

      </table>
  </div>

  <div class="panel-buttons" data-options="region:'south', height:'auto'">


        @can('approve_permission_request')
          <a href="#" class="easyui-linkbutton"
                      iconCls="icon-ok"
                      onclick="togglePermissionStatus(1)">Approve</a>
        @endcan

        @can('reject_permission_request')
          <a href="#" class="easyui-linkbutton"
                      iconCls="icon-no"
                      onclick="togglePermissionStatus(0)">Reject</a>
        @endcan

        @can('grant_permission_request')
          <a href="#" class="easyui-linkbutton"
                      iconCls="icon-permission"
                      onclick="togglePermissionStatus(2)">Grant</a>
        @endcan

    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel"
      onclick="$('#PermissionRequestViewDialog').dialog('close');" style="width:90px">Cancel</a>

  </div>

</div>

<script type="text/javascript">
  $(function() {
    $('#PermissionRequestViewDialog')
      .dialog({
        width: {!! $dialogWidth !!},
        height: {!! $dialogHeight !!}
      })
      .dialog('center')
      .dialog('setTitle', '{{ $title }}')
      .dialog('open');


  });

  function togglePermissionStatus(status) {

    if({{ $pr_status }} == 0 && status == 2) {
      $.messager.show({ title: 'Error', msg: 'Rejected requests cannot be granted!'});
      return;
    }

    var url = 'permission_requests/toggle-status?id=' + {{ $permission_request->id }} + '&status=' + status;

    $.post(url,
      @can('reject_permission_request')
      {
        'reason': $('#permission_request_reject_reason').textbox('getValue'),
      },
      @endcan
      function(result){
      if (result.success){
        $('#PermissionRequestViewDialog').dialog('close');
        $('#PermissionRequestDatagrid').datagrid('reload');
      } else {
        $.messager.show({ title: 'Error', msg: 'Could not perform the operation!'});
      }
    },'json');

  }

</script>
