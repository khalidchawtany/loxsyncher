<div class="easyui-layout" fit="true">

    <div data-options="region:'center', border:false">

        <form id="ReceiveTransactionForm" method="post" novalidate>

            <div class="ftitle">Scan a QrCode to receive it</div>

            <div class="fitem">
                <label>QrCode</label>
                <input id="_receive_transaction_dialog_qrcode" name="qrcode"
                    class="easyui-textbox" style="width:200px;" data-options="
                        required:true,
                        onEnterPress : function (obj, e) {
                          saveReceiveTransaction();
                        }
                    ">
            </div>

        </form>

        <div id="ReceiveTransactionInfo" style="display:none; background-color:lightgreen;">
            <form id="ReceiveTransactionInfoForm" novalidate>

                <div class="ftitle">You just receive the following</div>

                <div class="fitem">
                    <label style="width: 100px;">Transaction Number</label>
                    <input name="transaction_id" class="easyui-textbox"
                        style="width:300px; font-size: 20px; font-weight:bold; text-align: center;"
                        data-options="readonly:true" />
                </div>

                <div class="fitem">
                    <label style="width: 100px;">Date</label>
                    <input id="TransactionDate" name="transaction_date" class="easyui-textbox"
                        style="width:300px; background: #F4F4F4; padding: 10px; text-align: center; border: 1px solid #ddd; font-size: 50px; height: 65px; "
                        data-options="readonly:true, min:0,precision:0" />
                </div>

                <div class="fitem">
                    <label style="width: 100px;">Product</label>
                    <input id="ProductName" name="product_name" class="easyui-textbox"
                        style="width:300px; background: #F4F4F4; padding: 10px; text-align: center; border: 1px solid #ddd; font-size: 50px; height: 65px; " />
                </div>

            </form>
        </div>
    </div>
    <div class="panel-buttons" data-options="region:'south', height:'auto'">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel"
            onclick="javascript:$('#ReceiveTransactionDialog').dialog('close');$('#CustomsDatagrid').datagrid('reload');"
            style="width:90px">Close</a>
    </div>
</div>

<script type="text/javascript">
    function saveReceiveTransaction() {

        $('#ReceiveTransactionForm').form('submit', {

            url: 'customs/receive-transaction',

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

                    $("#ReceiveTransactionInfo").css('display', 'none');

                    $('#ReceiveTransactionDialog')
                        .dialog({
                            width: 500,
                            height: 200
                        })
                        .dialog('setTitle', 'Receive Transaction')
                        .dialog('center')
                        .dialog('open');

                    $.messager.alert({
                        width: 500,
                        title: 'Error',
                        msg: '<div style="color:red; font-size: 30px;">' +
                            '<div style="text-align:center; font-size: 50px;" > ❌</div>' +
                            '<div>' + result.msg + '</div>' +
                            '</div>',
                        showType: 'show',
                        timeout: 5000,
                        style: {
                            right: '',
                            top: document.body.scrollTop + document
                                .documentElement.scrollTop,
                            bottom: ''
                        },
                        fn: function() {
                            setTimeout(function() {
                                $('#_receive_transaction_dialog_qrcode')
                                    .numberbox('textbox')
                                    .focus();
                            });
                        }
                    });
                } else {
                    $.messager.show({
                        title: 'Success',
                        msg: result.success
                    });

                    $("#ReceiveTransactionInfo").css('display', 'block');

                    $('#ReceiveTransactionInfoForm #TransactionDate').textbox('textbox').css(
                        'font-size', '30px');

                    $('#ReceiveTransactionInfoForm #ProductName').textbox('textbox').css(
                        'font-size', '30px');

                    $('#ReceiveTransactionInfoForm').form('load', result.obj);

                    $('#ReceiveTransactionDialog')
                        .dialog({
                            width: 500,
                            height: 450
                        })
                        .dialog('setTitle', 'Receive Transaction')
                        .dialog('center')
                        .dialog('open');
                }

                $('#ReceiveTransactionForm').form('clear'); 

            }
        });

    }

    $(function() {
        setTimeout(function() {
            $('#_receive_transaction_dialog_qrcode').textbox('textbox').focus();
        });
    });

</script>
