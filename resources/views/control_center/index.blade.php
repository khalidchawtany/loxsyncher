        <div class="easyui-layout" fit="true">

            <div data-options="region:'north'" style="height:90px;padding:10px;border-left:0;border-right: 0;text-align: center;">
                @can('view_permission_request')

                    <a  class="easyui-linkbutton"
                        data-options="size:'large',width:'100px', iconCls:'icon-large-permission-request', iconAlign:'top'"
                        onclick="javascript:$('#settings-center-panel').panel({href:'permission_requests'});">
                        Perm. R.
                    </a>
										<a  class="easyui-linkbutton" data-options="size:'large',width:'2px', disabled:true" style="margin:0 5px">
										</a>
                @endcan

                @can('view_change_request')

                    <a  class="easyui-linkbutton"
                        data-options="size:'large',width:'100px', iconCls:'icon-large-change-request', iconAlign:'top'"
                        onclick="javascript:$('#settings-center-panel').panel({href:'change_requests'});">
                        Change. R.
                    </a>
										<a  class="easyui-linkbutton" data-options="size:'large',width:'2px', disabled:true" style="margin:0 5px">
										</a>
                @endcan

                @can('view_user')
                    <a  class="easyui-linkbutton"
                        data-options="size:'large',width:'100px', iconCls:'icon-large-user', iconAlign:'top'"
                        onclick="javascript:$('#settings-center-panel').panel({href:'users'});">
                        Users
                    </a>
                @endcan


                @can('view_roles_and_permissions')
										<a  class="easyui-linkbutton" data-options="size:'large',width:'2px', disabled:true" style="margin:0 5px">
										</a>

                    <a  class="easyui-linkbutton"
                        data-options="size:'large',width:'100px', iconCls:'icon-large-role', iconAlign:'top'"
                        onclick="javascript:$('#settings-center-panel').panel({href:'roles'});">
                        Roles
                    </a>
                @endcan

                @can('view_roles_and_permissions')
										<a  class="easyui-linkbutton" data-options="size:'large',width:'2px', disabled:true" style="margin:0 5px">
										</a>

                    <a  class="easyui-linkbutton"
                        data-options="size:'large',width:'100px', iconCls:'icon-large-permission', iconAlign:'top'"
                        onclick="javascript:$('#settings-center-panel').panel({href:'permissions'});">
                        Permissions
                    </a>
                @endcan

                @can('review_roles')
										<a  class="easyui-linkbutton" data-options="size:'large',width:'2px', disabled:true" style="margin:0 5px">
										</a>

                    <a  class="easyui-linkbutton"
                        data-options="size:'large',width:'100px', iconCls:'icon-large-role-review', iconAlign:'top'"
                        onclick="javascript:$('#settings-center-panel').panel({href:'roles/review'});">
                        Reveiw Role.
                    </a>
                @endcan

                @can('review_permissions')
										<a  class="easyui-linkbutton" data-options="size:'large',width:'2px', disabled:true" style="margin:0 5px">
										</a>

                    <a  class="easyui-linkbutton"
                        data-options="size:'large',width:'100px', iconCls:'icon-large-permission-review', iconAlign:'top'"
                        onclick="javascript:$('#settings-center-panel').panel({href:'permissions/review'});">
                        Reveiw Perm.
                    </a>
                @endcan

                @can('view_permissions_description')
										<a  class="easyui-linkbutton" data-options="size:'large',width:'2px', disabled:true" style="margin:0 5px">
										</a>

                    <a  class="easyui-linkbutton"
                        data-options="size:'large',width:'100px', iconCls:'icon-large-permission-description', iconAlign:'top'"
                        onclick="javascript:$('#settings-center-panel').panel({href:'permissions/descriptions'});">
                        Perm. Desc.
                    </a>
                @endcan


                @can('view_activity_log')
										<a  class="easyui-linkbutton" data-options="size:'large',width:'2px', disabled:true" style="margin:0 5px">
										</a>

                    <a  class="easyui-linkbutton"
                        data-options="size:'large',width:'100px', iconCls:'icon-large-camera', iconAlign:'top'"
                        onclick="javascript:$('#settings-center-panel').panel({href:'activities'});">
                        Activities
                    </a>
                @endcan


            </div>

            <div data-options="region:'center',border:false" id="settings-center-panel">
                <div class="screen-centered-text">
                    <img src="/img/datagrid/control_center.png" alt="home">
                </div>
            </div>


        </div>
