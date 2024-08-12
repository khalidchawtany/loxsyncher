<div class="easyui-layout" fit="true">

  <div id="displayUserBar" data-options="region:'north',border:false" style="height:50px;text-align:center;padding:10px;font-size:20px;background:darkseagreen;">
      @if($seleced_user)
        {!! $seleced_user->kurdish_name !!} Permissions
      @else
        ---
      @endif
  </div>

  <div data-options="region:'south',border:false" style="height:50px;border-top:1px solid #95B8E7;text-align:center;padding:10px;">

    @can('assign_permission_to_user')
    <a href="javascript:void(0)" class="easyui-linkbutton" onclick="checkAllPermissions()">Check all</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" onclick="unCheckAllPermissions()">Uncheck all</a>
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" onclick="savePermissions()" style="width:100px;">Save</a>
    @endcan

  </div>

  <div id="PermissionsPage" data-options="region:'center',border:false">


    <form method="post" style="width:100%;height:100%" id="PermissionsForm">

      <table PermissionTable id="PermissionsDatagrid">
					@include('user_manager.permission_rows')
      </table>

    </form>


  </div>

</div>


<script type="text/javascript">

  var oldPermissions = {!! $permissions !!};

  $(function() {

    $('#PermissionsPage :input[type="checkbox"]').change(function(){
        handlePermissionCheck($(this));
    });

    $('form#PermissionsForm :input[type="checkbox"]').each(function(){

      if(oldPermissions.indexOf($(this).prop('name')) >= 0)
        $(this).prop('checked', true);

    });

  });



  function handlePermissionCheck($el) {

    var isAll = $el.is("[all]");
    var isMain = $el.is("[main]");
    var isView = $el.is("[view]");
    var checkboxState = $el.prop('checked');

    var parent = isAll? $el.closest('table') : $el.closest('tr');

    var inputs = [];

    parent.find("td input:checkbox").each(function(index) {

        inputs.push($(this));

    });

    if(isAll || isMain){
      changeAllBoxes(inputs, checkboxState);
    }
    else if (isView) {
      if (checkboxState == false) {
        changeAllBoxes(inputs, checkboxState);
      } else {
        inputs[0].prop('checked', checkboxState);
      }
    } else if (checkboxState == true){ // all other controls
      inputs[0].prop('checked', true);
      inputs[1].prop('checked', true);
    }

  }

  function changeAllBoxes(inputs, checkboxState) {
    for (var i = 0; i < inputs.length; i++) {
      inputs[i].prop('checked', checkboxState);
    }
  }


  @can('assign_permission_to_user')
  function savePermissions(){

    $('#PermissionsForm').form('submit',{
      url: 'users/permissions/{!! $seleced_user->id !!}/save',
      onSubmit: function(param){
        param._token = window.CSRF_TOKEN;
      },
      success: function(result){
        var result = eval('('+result+')');
        if (result.msg){
          $.messager.show({title: 'Error', msg: result.msg});
        } else {
          $.messager.show({title: 'Sucess', msg: 'Operation performed successfully!'})
        }
      }
    });
  }
  @endcan

  function checkAllPermissions() {
      $('#PermissionsForm input:checkbox').prop('checked', true);
  }

  function unCheckAllPermissions() {
      $('#PermissionsForm input:checkbox').prop('checked', false);
  }

</script>

<style media="screen">

  #PermissionsDatagrid{
    width: 100%;
    border-collapse: collapse;
  }
  #PermissionsDatagrid thead tr th:first-child,
  #PermissionsDatagrid tbody tr td:first-child {
    width: 200px;
    min-width: 200px;
    max-width: 200px;
    word-break: break-all;
    text-align:left;
    vertical-align: top;
  }

  .permission-checkbox {
    margin: 10px 10px 10px 20px !important;
  }

  .permission-header th div{
    position: absolute;
    background: #E0ECFF;
    color: black;
    padding: 9px 25px;
    top: 0;
    width:100%;
    margin-left: -25px;
    line-height: normal;
  }

  #PermissionsPage tr{
    border:1px solid #ddd;
  }
  #PermissionsPage td{
    {{--  border-bottom: 1px solid #ddd;  --}}
  }

  #PermissionsPage td:nth-child(1) {
    background:#b76e6e;
    color:white;
    border-bottom: 1px solid #ddd;
  }

  #PermissionsPage td[field="title"]{
    background: red;
    color: white;
  }
</style>
