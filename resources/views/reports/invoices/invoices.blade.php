<div class="easyui-layout" fit="true">

    <div data-options="region:'center',border:false">
        <table id="InvoicesReportDatagrid"></table>
    </div>

    <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

        <div class="easyui-panel" id="InvoicesReportDateFilterPanel"
            style="margin-bottom: 10px; padding: 10px 5px;" data-options=" title: 'Date' ">

            <label>From:</label>
            <input id="InvoicesReportDatagridFromDate" type="text" class="easyui-datebox"
                style="width:123px;">

            <label>To:</label>
            <input id="InvoicesReportDatagridToDate" type="text" class="easyui-datebox"
                style="width:123px;">

            <div style="text-align:center; margin-top: 10px;">
                <a href="#" class="easyui-linkbutton" iconCls="icon-filter"
                    onclick="applyInvoicesReportDatagridDateFilter()">Apply</a>
                <a href="#" class="easyui-linkbutton" iconCls="icon-cancel"
                    onclick="clearInvoicesReportDatagridDateFilter()">Clear</a>
            </div>
        </div>
        <div id="InvoicesReportDateFilterPanelFooter">
        </div>

        <table id="invoices_report_propertygrid" class="easyui-propertygrid"
            style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
        </table>
    </div>

</div>

<div id="InvoicesReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"
        onclick="javascript:$('#InvoicesReportDatagrid').datagrid('reload')">Reload</a>
    @can('print_invoices_report')
        <a href="#" class="easyui-linkbutton" iconCls="icon-print"
            onclick="javascript:printFromInvoicesReportDatagrid();">Print</a>
    @endcan

    @can('download_invoices_report')
        <a href="#" class="easyui-linkbutton" iconCls="icon-export"
            onclick="javascript:downloadInvoicesReports();">Download</a>
    @endcan
</div>


<script type="text/javascript">
    $(function() {
        $('#InvoicesReportDatagrid').datagrid({
            idField: 'id',
            title: ' Invoices Report',
            toolbar: '#InvoicesReportDatagridToolbar',
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
            url: 'reports/invoices/list',

            columns: [
                [{
                        field: 'payment_id',
                        title: 'Payment #',
                        width: 50,
                        sortable: true,
                    },
                    {
                        field: 'payment_amount',
                        as: 'payments.amount',
                        title: 'Amount',
                        width: 50,
                        sortable: true,
                    },
                    {
                        field: 'payment_date',
                        as: 'payments.date_time',
                        title: 'Payment Date',
                        width: 50,
                        sortable: true,
                    },
                    {
                        field: 'office_name',
                        as: 'offices.name',
                        title: 'Office',
                        width: 50,
                        sortable: true,
                    },
                    {
                        field: 'received_by_name',
                        as: 'users.kurdish_name',
                        title: 'Received By',
                        width: 50,
                        sortable: true,
                    },
                    {
                        field: 'received_at',
                        as: 'payments.date_time',
                        title: 'Received At',
                        width: 50,
                        sortable: true,
                    },

                ]
            ],

            onLoadSuccess: function(data) {
                $('#invoices_report_propertygrid').propertygrid(
                    'loadData', data.stat_rows);
            }
        });
    });

    $('#InvoicesReportDatagrid').datagrid('enableFilter', [{
        field: 'payment_amount',
        type: 'numberbox',
        op: ['equal', 'contains', 'less', 'lessorequal', 'greater', 'greaterorequal']
    }, ]);


    function clearInvoicesReportDatagridDateFilter(value) {
        $('#InvoicesReportDatagridFromDate').datebox('reset');
        $('#InvoicesReportDatagridToDate').datebox('reset');

        $('#InvoicesReportDatagrid').datagrid('removeFilterRule',
            'payments.date_time');

        if (value != '') {
            $('#InvoicesReportDatagrid').datagrid('addFilterRule', {
                field: 'payments.date_time',
                value: value
            });
        }

        $('#InvoicesReportDatagrid').datagrid('doFilter');
    }

    function applyInvoicesReportDatagridDateFilter() {
        var dateFromText = $('#InvoicesReportDatagridFromDate').datebox('getValue');
        dateFrom = $.fn.datebox.defaults.parser(dateFromText);
        dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

        var dateToText = $('#InvoicesReportDatagridToDate').datebox('getValue');
        dateTo = $.fn.datebox.defaults.parser(dateToText);
        dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

        if (dateFromText == '' && dateToText == '') {
            $('#InvoicesReportDatagrid').datagrid('removeFilterRule',
                'payments.date_time');
        } else if (dateFromText == '') {
            $('#InvoicesReportDatagrid').datagrid('addFilterRule', {
                field: 'payments.date_time',
                op: 'less',
                value: dateTo
            });
        } else if (dateToText == '') {
            $('#InvoicesReportDatagrid').datagrid('addFilterRule', {
                field: 'payments.date_time',
                op: 'greater',
                value: dateFrom
            });
        } else {
            $('#InvoicesReportDatagrid').datagrid('addFilterRule', {
                field: 'payments.date_time',
                op: 'dateTimeBetween',
                value: dateFrom + ',' + dateTo
            });
        }
        $('#InvoicesReportDatagrid').datagrid('doFilter');
    }

    function printFromInvoicesReportDatagrid() {
        var dgo = $('#InvoicesReportDatagrid').datagrid('options');
        var filterRules = dgo.filterRules;
        var sort = dgo.sortName;
        var order = dgo.sortOrder;

        var col = $('#InvoicesReportDatagrid').datagrid('getColumnOption', sort);

        if (col && col.as) {
            sort = col.as;
        }

        var urlToPrint = '{!! \URL::route('print_invoices_report') !!}?';

        var queryStrings = 'filterRules=' + JSON.stringify(filterRules);

        if (sort) {
            queryStrings += '&sort=' + sort + '&order=' + order;
        }

        print(urlToPrint + queryStrings);

    }


    function downloadInvoicesReports() {
        var dgo = $('#InvoicesReportDatagrid').datagrid('options');
        var filterRules = dgo.filterRules;
        var sort = dgo.sortName;
        var order = dgo.sortOrder;

        var col = $('#InvoicesReportDatagrid').datagrid('getColumnOption', sort);

        if (col && col.as) {
            sort = col.as;
        }

        var urlToPrint = '{!! \URL::route('download_invoices_report') !!}?';

        var queryStrings = 'filterRules=' + JSON.stringify(filterRules);

        if (sort) {
            queryStrings += '&sort=' + sort + '&order=' + order;
        }

        print(urlToPrint + queryStrings);
    }
</script>
