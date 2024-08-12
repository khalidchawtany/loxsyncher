@php
$isNewRecord = !isset($brand) || $brand == null;

$url = $isNewRecord ? 'brands/create' : 'brands/update';

$model = 'Brand';
$title = $isNewRecord ? 'New Brand' : 'Update Brand';
$dialogWidth = 1000;
$dialogHeight = 350;

if ($isNewRecord) {
    $brand = (object) [
        'id' => null,
        'product_id' => null,
        'company' => null,
        'name' => null,
        'note' => null,
        'user_id' => null,
    ];
}

@endphp

@include('styles')

<div class="easyui-layout" fit="true">

    <div data-options="region:'center', border:false">

        <form id="{{ $model }}Form" method="post" novalidate>

            <input type="hidden" name="id" value="<?= $brand->id ?>">

            <table class="w-full left medium">


                <tr>
                    <td colspan="4">
                        <div class=" ftitle">Brand Info:</div>
                    </td>

                </tr>


                <tr>
                    <td class="pt-1 w-100">Product</td>
                    <td class="pt-1">

                        <input class="easyui-combogrid w-300" name="product_id" value="{{ $brand->product_id }}"
                            data-options="
                            url: 'products/json_list',
                            mode: 'remote',
                            method: 'get',
                            idField:'id',
                            textField: 'kurdish_name',
                            limitToList: true,
                            hasDownArrow: true,
                            panelHeight:300,
                            panelWidth:850,
                            prompt: 'Select a product',
                            required:true,
                            columns: [[
                                {field:'id',title:'Id',width:50},
                                {field:'kurdish_name',title:'Kurdish Name',width:300, align: 'right'},
                                {field:'customs_name',title:'Customs Name',width:300, align: 'right'},
                                {field:'name',title:'Name',width:200, align: 'left'},
                            ]],
							"
                            </td>
                    <td class="pl-2 pt-1"></td>
                    <td class="pt-1"></td>

                </tr>


                <tr>
                    <td class="pt-1">Company</td>
                    <td class="pt-1">
                        <input name="company" value="<?= $brand->company ?>" class="easyui-textbox  w-300">
                    </td>
                    <td class="pt-1 pl-2">Name</td>
                    <td class="pt-1">
                        <input name="name" id="BrandDialogBrandName" value="<?= $brand->name ?>"
                            class="easyui-textbox  w-300" data-options="required:true">
                    </td>

                </tr>




                <tr>
                    <td>Note</td>
                    <td colspan="3">
                        <div class="mt-1">
                            <input name="note" value="<?= $brand->note ?>" class="easyui-textbox" multiline="true"
                                style="width:96%; height:81px; ">
                        </div>
                    </td>
                </tr>

            </table>
        </form>
    </div>

    <div class="panel-buttons" data-options="region:'south', height:'auto'">

        @if ($isNewRecord)
            <a href="javascript:void(0)" class="easyui-linkbutton c1 mr-3 " iconCls="icon-ok"
                onclick="saveBrand(true)">Save & New</a>
        @endif

        <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveBrand()">Save</a>

        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel"
            onclick="$('#BrandDialog').dialog('close');$('#BrandsDatagrid').edatagrid('reload');"
            style="width:90px">Cancel</a>

    </div>

</div>

<script type="text/javascript">
    $(function() {
        $('#BrandDialog')
            .dialog({
                width: <?= $dialogWidth ?>,
                height: <?= $dialogHeight ?>
            })
            .dialog('center')
            .dialog('setTitle', '{{ $title }}')
            .dialog('open');

        // For some reason this form gets posted without my code!
        $('#{{ $model }}Form').form({
            onSubmit: function() {
                return false;
            }
        });
    });

    function saveBrand(createAnother) {

        $('#{{ $model }}Form').form('submit', {

            url: '<?= $url ?>',

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
                    $.messager.show({
                        title: 'Error',
                        msg: result.msg
                    });
                } else {
                    if (createAnother === true) {
                        $('#BrandDialogBrandName').textbox('clear');
                        $('#BrandDialogBrandName').textbox("textbox").focus();
                    } else {
                        $('#BrandDialog').dialog('close');
                        $('#BrandsDatagrid').edatagrid('reload');
                    }
                    $.messager.show({
                        title: 'Success',
                        msg: 'Operation performed successfully!'
                    });
                }
            }
        });

    }
</script>
