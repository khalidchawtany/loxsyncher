
<table id="UsersDatagrid"></table>

<div id="UsersDatagridToolbar">

  <p class="hint hint-p icon-tip-p">
    The default control panel password for new created user is his/her email
  </p>
  <div style="padding:5px;">

    @can('create_user')
        <a href="#" class="easyui-linkbutton" iconCls="icon-add"  onclick="addUser()">New</a>
    @endcan
    @can('update_user')
        <a href="#" class="easyui-linkbutton" iconCls="icon-edit"  onclick="editUser('save')">Edit</a>
    @endcan
    @can('destroy_user')
        <a href="#" class="easyui-linkbutton" iconCls="icon-remove"  onclick="removeUser()">Remove</a>
    @endcan

    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"  onclick="javascript:$('#UsersDatagrid').datagrid('reload')">Reload</a>

    @can('toggle_user_status')
        <a href="#" class="easyui-linkbutton c5" onclick="toggleUserStatus()">Toggle User Status</a>
    @endcan

    @can('reset_user_password')
        <a href="#" class="easyui-linkbutton c5" onclick="resetUserPassword()">Reset User PWD</a>
    @endcan

    @can('impersonate_user')
        <a href="#" class="easyui-linkbutton c5" onclick="impersonateUser()">Impersonate</a>
    @endcan

  </div>


</div>

<div id="UsersDialog" class="easyui-dialog" style="width:500px;height:auto;padding:10px 20px" buttons="#UsersDialogButtons" title="User" closed="true" modal="true">

    <form id="UsersForm" method="post" novalidate>

      <div class="ftitle">User Information</div>

      <div class="fitem">
          <input name="name" class="easyui-textbox" required="true" prompt="Name" style="width:100%;">
      </div>

      <div class="fitem">
          <input name="kurdish_name" class="easyui-textbox" required="true" prompt="Kurdish Name" style="width:100%;">
      </div>

      <div class="fitem">
          <input name="job_description" class="easyui-textbox" required="true" prompt="Job Description/Dep" style="width:100%;">
      </div>

      <div class="fitem">
          <input name="email" class="easyui-textbox" required="true" validType="email" prompt="Email" style="width:100%;">
      </div>

      <div class="fitem">
          <input class="easyui-tagbox"
                 id="department"
                 name="department[]"
                 style="width:100%;"
                 data-options="
                    url: 'users/departments/list',
                    method: 'get',
                    valueField: 'name',
                    textField: 'name',
                    limitToList: true,
                    hasDownArrow: true,
                    panelHeight:'auto',
                    prompt: 'Department',
                    required:true
              ">
      </div>

      <div class="fitem">
          <input class="easyui-tagbox" id="role" name="role[]" style="width:100%;" data-options="
              url: 'users/roles/list',
              method: 'get',
              valueField: 'name',
              textField: 'name',
              limitToList: true,
              hasDownArrow: true,
              panelHeight:'auto ',
              prompt: 'Role',
              required:false
              ">
      </div>

      <div class="fitem">
          <input class="easyui-combobox" name="is_staff" style="width:100%;" data-options="
              valueField: 'value',
              textField: 'text',
              limitToList: true,
              hasDownArrow: true,
              panelHeight:'auto ',
              prompt: 'User is company staff',
              required:true,
              data: [
                  {
                    text: 'Yes',
                    value: '1',
                  },
                  {
                    text: 'No',
                    value: '0',
                  },
                ]
              ">
      </div>

      <div class="fitem">
          <input class="easyui-combobox" name="open_transaction_after_login" style="width:100%;" data-options="
              valueField: 'value',
              textField: 'text',
              limitToList: true,
              hasDownArrow: true,
              panelHeight:'auto ',
              prompt: 'Show transaction window after login',
              required:true,
              data: [
                  {
                    text: 'Yes',
                    value: '1',
                  },
                  {
                    text: 'No',
                    value: '0',
                  },
                ]
              ">
      </div>

      <div class="fitem">
				External View
          <input class="easyui-combobox" name="external_view" style="width:100%;" data-options="
              valueField: 'value',
              textField: 'text',
              limitToList: true,
              hasDownArrow: true,
              panelHeight:'auto ',
              prompt: 'Can view from external IP',
              required:true,
              data: [
                  {
                    text: 'Yes',
                    value: '1',
                  },
                  {
                    text: 'No',
                    value: '0',
                  },
                ]
              ">
      </div>
      <div class="fitem">
				External Update
          <input class="easyui-combobox" name="external_update" style="width:100%;" data-options="
              valueField: 'value',
              textField: 'text',
              limitToList: true,
              hasDownArrow: true,
              panelHeight:'auto ',
              prompt: 'Can update from external IP',
              required:true,
              data: [
                  {
                    text: 'Yes',
                    value: '1',
                  },
                  {
                    text: 'No',
                    value: '0',
                  },
                ]
              ">
      </div>

    </form>
</div>

<div id="UsersDialogButtons">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveUser()">Save</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#UsersDialog').dialog('close')" style="width:90px">Cancel</a>
</div>


<script>


  $('#UsersDatagrid').datagrid({
    title:'Users',
    fit:true,
    border:false,
    toolbar:'#UsersDatagridToolbar',
    nowrap:false,
    fitColumns:true,
    singleSelect:true,
    method:'get',
    rownumbers:true,
    remoteFilter: true,
    pagination: true,
    filterMatchType: 'any',
    url:'users/list',

    columns: [
        [
            {
                field: 'id',
                title: 'Id',
                width: 40,
            },
            {
                field: 'name',
                title: 'Name',
                width: 120,
            },
            {
                field: 'kurdish_name',
                title: 'Kurdish Name',
                width: 120,
            },
            {
                field: 'email',
                title: 'Email',
                width: 120,
            },
            {
                field: 'job_description',
                title: 'Job Title/Description',
                width: 120,
            },
            {
                field: 'roles',
                title: 'Role/s',
                width: 120,
                formatter: function(val) {
                    if(val) {
                      return  _.map(val, 'name').sort().join('</br>');
                    }
                    return '<div style="text-align:center;">---</div>'
                }
            },
            {
                field: 'permissions',
                title: 'Permission/s',
                width: 120,
                formatter: function(val) {
                    if(val) {
                      return  _.map(val, 'name').sort().join('</br>');
                    }
                    return '<div style="text-align:center;">---</div>'
                }
            },
            {
                field: 'is_staff',
                title: 'Staff',
                width: 120,
                formatter: function(val) {
                    return (val == '1') ? 'Yes': 'No';
                }
            },
            {
                field: 'status',
                title: 'Status',
                width: 120,
                formatter: function(val) {
                    return (val == '1') ? 'Enabled': 'Disabled';
                }
            },

            {
                field: 'external_view',
                title: 'External View',
                width: 120,
                formatter: function(val) {
                    return (val == '1') ? 'Allow': 'Deny';
                }
            },

            {
                field: 'external_update',
                title: 'External Update',
                width: 120,
                formatter: function(val) {
                    return (val == '1') ? 'Allow': 'Deny';
                }
            },
        ]
    ]
  });

  $('#UsersDatagrid').datagrid('enableFilter', [
    {
      field: 'status',
      type: 'combobox',
      options: {
        panelHeight: 'auto',
        hasDownArrow: false,
        limitToList: true,
        data: [{
          'text': 'Enabled',
          'value': '1',
        },
          {
            'text': 'Disabled',
            'value': '0',
          }],
        onChange: function (value) {
          onChangeDatagridFilterConrols('UsersDatagrid', 'status', value);
        }
      }
    },
    {
      field: 'external_view',
      type: 'combobox',
      options: {
        panelHeight: 'auto',
        hasDownArrow: false,
        limitToList: true,
        data: [{
          'text': 'Allow',
          'value': '1',
        },
          {
            'text': 'Deny',
            'value': '0',
          }],
        onChange: function (value) {
          onChangeDatagridFilterConrols('UsersDatagrid', 'external_view', value);
        }
      }
    },
    {
      field: 'external_update',
      type: 'combobox',
      options: {
        panelHeight: 'auto',
        hasDownArrow: false,
        limitToList: true,
        data: [{
          'text': 'Allow',
          'value': '1',
        },
          {
            'text': 'Deny',
            'value': '0',
          }],
        onChange: function (value) {
          onChangeDatagridFilterConrols('UsersDatagrid', 'external_update', value);
        }
      }
    },
		{
			field: 'permissions',
			type: 'label'
		},

       {
			field: 'role',
            type: 'combobox',
			options: {
				panelHeight: 'auto',
                hasDownArrow: false,
                limitToList: true,
                method: 'get',
                textField: 'name',
                valueField: 'name',
                url: 'roles/list',
				onChange: function (value) {
					onChangeDatagridFilterConrols('UsersDatagrid', 'roles.name', value);
				}
			}
        }
    ]);

  var url;

  function addUser() {

      $('#UsersDialog').dialog('open');
      $('#UsersForm').form('clear');

      url = 'users/create';
  }

  function editUser() {

      var row = $('#UsersDatagrid').datagrid('getSelected');

      if (!row) {

      	$.messager.show({ title: 'Error', msg: 'Please select a user'});
        return;
      }

      url = 'users/update?id=' + row.id;

      $('#UsersForm').form('clear');
      $('#UsersForm').form('load', row);
      $('#UsersForm #department').tagbox('setValues', row.departments);
      $('#UsersForm #role').tagbox('setValues', row.roles);

      $('#UsersDialog').dialog('open');

  }

  function saveUser() {

    $('#UsersForm').form('submit', {

        url: url,

        onSubmit: function(param) {
        	   param._token = window.CSRF_TOKEN;
            return $(this).form('validate');

        },
        success: function(result) {

            var result = eval('(' + result + ')');

            if (result.isError) {

        		  $.messager.show({ title: 'Error', msg: result.msg});


            } else {

                $('#UsersDatagrid').datagrid('reload')
                $('#UsersDialog').dialog('close');
                $('#UsersForm').form('clear');
                $.messager.show({ title: 'Success', msg: 'Operation performed successfully!'});


            }
        }
    });

  }
  function removeUser() {

      // var row = quotaDataGrid.datagrid('getSelected');
      var row = $('#UsersDatagrid').datagrid('getSelected');

      if (!row) {
      	$.messager.show({ title: 'Error', msg: 'Please select a user'});
        return;
      }

      $.messager.confirm('Confirm', 'Are you sure you want to delete this user?', function(r) {
          if (r) {

              $.post('users/destroy', {
                  'id': row.id,
              }, function(result) {
                  if (result.success) {

                      $('#UsersDatagrid').datagrid('reload');

                  } else {
                  	$.messager.show({ title: 'Error', msg: result.msg});
                  }
              }, 'json');
          }
      });
  }


  	function formatUserStatus(val) {
		return (val == '1') ? 'Enabled': 'Disabled';
	}

	function toggleUserStatus() {

	    var row = $('#UsersDatagrid').datagrid('getSelected');

	    if (row) {

	      var url = 'users/toggle-status?id=' + row.id;

	      $.messager.confirm('Confirm','Are you sure you want perform this operation?',function(r){
	        if (r){

	          $.post(url, function(result){

	            if (result.success){
	              $.messager.alert('Success','The user status changed');
	              $('#UsersDatagrid').datagrid('reload');
	            } else {
	              $.messager.show({ title: 'Error', msg: 'Could not perform the operation!'});
	            }
	          },'json');

	        }
	      });

	    } else {
	    	$.messager.show({title:'Error', msg:'Please select a user'});
	    }

	}

	function resetUserPassword() {

	    var row = $('#UsersDatagrid').datagrid('getSelected');

	    if (row) {

	      var url = 'users/reset-password?id=' + row.id;

	      $.messager.confirm('Confirm','Are you sure you want perform this operation?',function(r){
	        if (r){

	          $.post(url, function(result){

	            if (result.success){
	              $.messager.alert('Success','The user password reseted');
	              $('#UsersDatagrid').datagrid('reload');
	            } else {
	              $.messager.show({ title: 'Error', msg: 'Could not perform the operation!'});
	            }
	          },'json');

	        }
	      });

	    } else {
	    	$.messager.show({title:'Error', msg:'Please select a user'});
	    }

	}

	function impersonateUser() {

	    var row = $('#UsersDatagrid').datagrid('getSelected');

	    if (row) {

        var url = 'users/impersonate?id=' + row.id;

        $.post(url, function(result){

          if (result.success){
            window.location.href="<?= route("home") ?>";
          } else {
            $.messager.show({ title: 'Error', msg: 'Could not perform the operation!'});
          }
        },'json');


      } else {
        $.messager.show({title:'Error', msg:'Please select a user'});
      }

	}


</script>
