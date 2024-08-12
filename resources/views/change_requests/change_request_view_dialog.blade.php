@php

  $title = 'Change Request (View)';
  $dialogWidth = 700;
  $dialogHeight = 450;
  $cr_status = -1;

  if($change_request->status != null) {
    $cr_status = $change_request->status;
  }


@endphp

@include('styles')

<div class="easyui-layout" fit="true">

  <div class="pa-2 pt-0" data-options="region:'center', border:false">

      <table class="w-full left medium ttop tpy-5">
        <tr>
          <td colspan="2">
            <div class="mt-2 ftitle">Change Request Info:</div>
          </td>
        </tr>

        <tr>
          <th class="w-120">Request By</th>
          <td>
              {!! $change_request->user->kurdish_name !!}
          </td>

        </tr>

        <tr>
          <th class="w-100">Requested At</th>
          <td>
              {!! $change_request->created_at !!}
          </td>

        </tr>

        <tr>
          <th class="w-100">Title</th>
          <th>
              {!! $change_request->title !!}
          </th>

        </tr>


        <tr>
          <th>Description</th>
          <td>
              {!! $change_request->description !!}
          </td>
        </tr>

        <tr>
          <th>Note</th>
          <td >
              {!! $change_request->note !!}
          </td>
        </tr>

        <tr>
          <td colspan="2"><hr/></td>
        </tr>

        <tr>
          <th>Reason</th>
          <td >
            <input id="change_request_reject_reason"
                   name="change_request_reject_reason"
                   value="{!! $change_request->reason !!}"
                   class="easyui-textbox"
                   style="width:96%; height:54px; "
                   multiline="true"
                   @cannot('reject_change_request')
                     readonly
                   @endcannot
                   >
          </td>
        </tr>
      </table>
  </div>

  <div class="panel-buttons" data-options="region:'south', height:'auto'">

        @can('approve_change_request')
          <a href="#" class="easyui-linkbutton"
                      iconCls="icon-ok"
                      onclick="toggleChangeStatus(1)">Approve</a>
        @endcan

        @can('reject_change_request')
          <a href="#" class="easyui-linkbutton"
                      iconCls="icon-no"
                      onclick="toggleChangeStatus(0)">Reject</a>
        @endcan

        @can('grant_change_request')
          <a href="#" class="easyui-linkbutton"
                      iconCls="icon-permission"
                      onclick="toggleChangeStatus(2)">Grant</a>
        @endcan

    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel"
      onclick="$('#ChangeRequestViewDialog').dialog('close');" style="width:90px">Cancel</a>

  </div>

</div>

<script type="text/javascript">
  $(function() {
    $('#ChangeRequestViewDialog')
      .dialog({
        width: {!! $dialogWidth !!},
        height: {!! $dialogHeight !!}
      })
      .dialog('center')
      .dialog('setTitle', '{{ $title }}')
      .dialog('open');
  });

  function toggleChangeStatus(status) {

    if({{$cr_status}}== 0 && status == 2) {
      $.messager.show({ title: 'Error', msg: 'Rejected requests cannot be granted!'});
      return;
    }

    var url = 'change_requests/toggle-status?id=' + {{ $change_request->id }} + '&status=' + status;

    $.post(url,
      {
        reason: $('#change_request_reject_reason').textbox('getValue')
      },

      function(result){
      if (result.success){
        $('#ChangeRequestViewDialog').dialog('close');
        $('#ChangeRequestDatagrid').datagrid('reload');
      } else {
        $.messager.show({ title: 'Error', msg: 'Could not perform the operation!'});
      }
    },'json');

  }
</script>
