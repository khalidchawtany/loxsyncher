<div class="easyui-layout" fit="true">

    <div data-options="region:'center', border:false">

        {{--Respecification Form--}}
        <form id="AttachDocumentForm" method="post" enctype="multipart/form-data"  novalidate>

            <div class="ftitle">Attach Document:</div>

            <div class="fitem">
                <label>Number:</label>
                <strong>{!! $specification->number !!}</strong>
                <input type="hidden" name="specification_id" value="{!! $specification->id !!}">
            </div>

            <div class="fitem">
                <label>Title:</label>
                <strong>{!! $specification->title !!}</strong>
            </div>

            <div class="fitem">
                <label>Document:</label>
                <input class="easyui-textbox" name="specificationDocument" data-options="required:true" style="width:300px">
            </div>
 {{-- 0B46is8eYJww2WGZVcHpPQ0tKTHc4WGZ3VGlQQWtXT1FhcE84 --}}


        </form>
    </div>
    <div class="panel-buttons" data-options="region:'south', height:'auto'">
        <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="attachDocument()">Save</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#AttachDocumentDialog').dialog('close');" style="width:90px">Cancel</a>
    </div>
</div>

<script type = "text/javascript" >


    function attachDocument() {

        $.messager.progress();

        $('#AttachDocumentForm').form('submit', {

            url: 'specifications/attach_document',

            onSubmit: function(param) {
                param._token = window.CSRF_TOKEN;

                if($(this).form('validate')) {
                    return true;
                }

                $.messager.progress('close');
                return false;

            },

            success: function(result) {

                var result = eval('(' + result + ')');

                if (result.isError) {
                    $.messager.show({ title: 'Error', msg: result.msg});
                } else {
                    $('#AttachDocumentDialog').dialog('close');
                    $('#SpecificationDatagrid').datagrid('reload');
                    $.messager.show({ title: 'Success', msg: 'Operation performed successfully!'});
                }

                $.messager.progress('close');

            }
        });

    }


</script>