        <div class="easyui-layout" fit="true">

            <div data-options="region:'north'" style="height:90px;padding:10px;border-left:0;border-right: 0;text-align: center;">

                <a  class="easyui-linkbutton"
                    data-options="size:'large',width:'100px', iconCls:'icon-large-user', iconAlign:'top'"
                    onclick="javascript:$('#settings-center-panel').panel({href:'users'});">
                    Users
                </a>


                <a  class="easyui-linkbutton" data-options="size:'large',width:'2px', disabled:true" style="margin:0 5px">
                </a>

                <a  class="easyui-linkbutton"
                    data-options="size:'large',width:'140px', iconCls:'icon-large-role', iconAlign:'top'"
                    onclick="javascript:$('#settings-center-panel').panel({href:'roles'});">
                    Roles & Permissions
                </a>

                <a  class="easyui-linkbutton" data-options="size:'large',width:'2px', disabled:true" style="margin:0 5px">
                </a>

                @hasrole('super')
                    <a  class="easyui-linkbutton"
                        data-options="size:'large',width:'100px', iconCls:'icon-large-camera', iconAlign:'top'"
                        onclick="javascript:$('#settings-center-panel').panel({href:'activities'});">
                        Activities
                    </a>
                @endhasrole



            </div>

            <div data-options="region:'center',border:false" id="settings-center-panel">
                <div class="screen-centered-text">
                    <img src="/img/dock/manage_users.svg" style="width:30%;opacity: 0.05" alt="home">
                </div>
            </div>


        </div>
