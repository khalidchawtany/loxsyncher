<table id="CustomsDatagrid"></table>

<div id="CustomsDatagridToolbar" style="padding:5px;text-align:center;">

    @can('receive_transaction')
        <a href="#" class="easyui-linkbutton" iconCls="icon-edit" onclick="receiveTransaction()">
            Receive
        </a>
    @endcan

    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"
        onclick="javascript:$('#CustomsDatagrid').edatagrid('reload')">Reload</a>
</div>

<div id="ReceiveTransactionDialog" class="easyui-dialog" style="width:500px;height:200px;"
    closed="true" modal="true">
</div>


<script type="text/javascript">
    $(function() {
        $('#CustomsDatagrid').edatagrid({
            idField: 'id',
            title: 'Customs',
            toolbar: '#CustomsDatagridToolbar',
            fit: true,
            border: false,
            fitColumns: true,
            singleSelect: true,
            method: 'get',
            rownumbers: true,
            pagination: true,
            remoteFilter: true,
            filterMatchType: 'any',
            url: 'customs/list',
            columns: [
                [{
                        field: 'id',
                        title: 'Id',
                        width: 10,
                        align: 'left',
                        sortable: true,

                    },
                    {
                        field: 'kurdish_name',
                        as: 'products.kurdish_name',
                        title: 'product',
                        width: 40,
                        sortable: true,
                    },
                    {
                        field: 'date_time',
                        title: 'Date Time',
                        width: 30,
                        sortable: true,
                    },
                    {
                        field: 'received_on',
                        title: 'Received On',
                        width: 30,
                        sortable: true,
                    },

                ]
            ]

        });
    });

    $('#CustomsDatagrid').edatagrid('enableFilter', [{
        field: 'action',
        type: 'label'
    }]);



    function receiveTransaction() {

        $('#ReceiveTransactionDialog')
            .dialog('open')
            .dialog('setTitle', 'Receive Transaction')
            .dialog('refresh', 'customs/show-receive-transaction-dialog');
    }

</script>
