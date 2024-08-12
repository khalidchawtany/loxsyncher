<div class="easyui-layout" fit="true">

    <div data-options="region:'center',border:false">
        <table id="ReceivedInvoicesReportDatagrid"></table>
    </div>

    <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

        <div class="easyui-panel" id="ReceivedInvoicesReportDateFilterPanel"
            style="margin-bottom: 10px; padding: 10px 5px;" data-options=" title: 'Date' ">

            <label>From:</label>
            <input id="ReceivedInvoicesReportDatagridFromDate" type="text" class="easyui-datebox"
                style="width:123px;">

            <label>To:</label>
            <input id="ReceivedInvoicesReportDatagridToDate" type="text" class="easyui-datebox"
                style="width:123px;">

            <div style="text-align:center; margin-top: 10px;">
                <a href="#" class="easyui-linkbutton" iconCls="icon-filter"
                    onclick="applyReceivedInvoicesReportDatagridDateFilter()">Apply</a>
                <a href="#" class="easyui-linkbutton" iconCls="icon-cancel"
                    onclick="clearReceivedInvoicesReportDatagridDateFilter()">Clear</a>
            </div>
        </div>
        <div id="ReceivedInvoicesReportDateFilterPanelFooter">
        </div>

        <table id="received_invoices_report_propertygrid" class="easyui-propertygrid"
            style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
        </table>
    </div>

</div>

<div id="ReceivedInvoicesReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"
        onclick="javascript:$('#ReceivedInvoicesReportDatagrid').datagrid('reload')">Reload</a>
    @can('print_received_invoices_report')
        <a href="#" class="easyui-linkbutton" iconCls="icon-print"
            onclick="javascript:printFromReceivedInvoicesReportDatagrid();">Print</a>
    @endcan

    @can('download_received_invoices_report')
        <a href="#" class="easyui-linkbutton" iconCls="icon-export"
            onclick="javascript:downloadReceivedInvoicesReports();">Download</a>
    @endcan
</div>


<script type="text/javascript">
    $(function() {
        $('#ReceivedInvoicesReportDatagrid').datagrid({
            idField: 'id',
            title: 'Received Invoices Report',
            toolbar: '#ReceivedInvoicesReportDatagridToolbar',
            fit: true,
            border: false,
            fitColumns: true,
            nowrap: false,
            singleSelect: true,
            method: 'get',
            rownumbers: true,
            pagination: true,
            remoteFilter: true,
            filterMatchType: 'any',
            url: 'reports/received_invoices/list',

            columns: [
                [{
                        field: 'payment_id',
                        title: 'Payment #',
                        width: 50,
                    },
                    {
                        field: 'payment_amount',
                        as: 'payments.amount',
                        title: 'Amount',
                        width: 50,
                    },
                    {
                        field: 'office_name',
                        as: 'offices.name',
                        title: 'Office',
                        width: 50,
                    },
                    {
                        field: 'received_by_name',
                        as: 'users.kurdish_name',
                        title: 'Received By',
                        width: 50,
                    },
                    {
                        field: 'received_at',
                        as: 'received_invoices.received_at',
                        title: 'Received At',
                        width: 50,
                    },

                ]
            ],

            onLoadSuccess: function(data) {
                $('#received_invoices_report_propertygrid').propertygrid(
                    'loadData', data.stat_rows);
            }
        });
    });

    $('#ReceivedInvoicesReportDatagrid').datagrid('enableFilter', [{
        field: 'payment_amount',
        type: 'numberbox',
        op: ['equal', 'contains', 'less', 'lessorequal', 'greater', 'greaterorequal']
    }, ]);


    function clearReceivedInvoicesReportDatagridDateFilter(value) {
        $('#ReceivedInvoicesReportDatagridFromDate').datebox('reset');
        $('#ReceivedInvoicesReportDatagridToDate').datebox('reset');

        $('#ReceivedInvoicesReportDatagrid').datagrid('removeFilterRule',
            'received_invoices.received_at');

        if (value != '') {
            $('#ReceivedInvoicesReportDatagrid').datagrid('addFilterRule', {
                field: 'received_invoices.received_at',
                value: value
            });
        }

        $('#ReceivedInvoicesReportDatagrid').datagrid('doFilter');
    }

    function applyReceivedInvoicesReportDatagridDateFilter() {
        var dateFromText = $('#ReceivedInvoicesReportDatagridFromDate').datebox('getValue');
        dateFrom = $.fn.datebox.defaults.parser(dateFromText);
        dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

        var dateToText = $('#ReceivedInvoicesReportDatagridToDate').datebox('getValue');
        dateTo = $.fn.datebox.defaults.parser(dateToText);
        dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

        if (dateFromText == '' && dateToText == '') {
            $('#ReceivedInvoicesReportDatagrid').datagrid('removeFilterRule',
                'received_invoices.received_at');
        } else if (dateFromText == '') {
            $('#ReceivedInvoicesReportDatagrid').datagrid('addFilterRule', {
                field: 'received_invoices.received_at',
                op: 'less',
                value: dateTo
            });
        } else if (dateToText == '') {
            $('#ReceivedInvoicesReportDatagrid').datagrid('addFilterRule', {
                field: 'received_invoices.received_at',
                op: 'greater',
                value: dateFrom
            });
        } else {
            $('#ReceivedInvoicesReportDatagrid').datagrid('addFilterRule', {
                field: 'received_invoices.received_at',
                op: 'dateTimeBetween',
                value: dateFrom + ',' + dateTo
            });
        }
        $('#ReceivedInvoicesReportDatagrid').datagrid('doFilter');
    }

    function printFromReceivedInvoicesReportDatagrid() {
        var dgo = $('#ReceivedInvoicesReportDatagrid').datagrid('options');
        var filterRules = dgo.filterRules;
        var sort = dgo.sortName;
        var order = dgo.sortOrder;

        var col = $('#ReceivedInvoicesReportDatagrid').datagrid('getColumnOption', sort);

        if (col && col.as) {
            sort = col.as;
        }

        var urlToPrint = '{!! \URL::route('print_received_invoices_report') !!}?';

        var queryStrings = 'filterRules=' + JSON.stringify(filterRules);

        if (sort) {
            queryStrings += '&sort=' + sort + '&order=' + order;
        }

        print(urlToPrint + queryStrings);

    }


    function downloadReceivedInvoicesReports() {
        var dgo = $('#ReceivedInvoicesReportDatagrid').datagrid('options');
        var filterRules = dgo.filterRules;
        var sort = dgo.sortName;
        var order = dgo.sortOrder;

        var col = $('#ReceivedInvoicesReportDatagrid').datagrid('getColumnOption', sort);

        if (col && col.as) {
            sort = col.as;
        }

        var urlToPrint = '{!! \URL::route('download_received_invoices_report') !!}?';

        var queryStrings = 'filterRules=' + JSON.stringify(filterRules);

        if (sort) {
            queryStrings += '&sort=' + sort + '&order=' + order;
        }

        print(urlToPrint + queryStrings);
    }
</script>
