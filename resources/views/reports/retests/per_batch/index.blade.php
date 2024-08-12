<div class="easyui-layout" fit="true">

    <div id="RetestBatchInfoDialog" class="easyui-dialog" style="width:500px;height:280px;" closed="true" modal="true">
    </div>

    <div data-options="region:'center',border:false">
        <table id="PerBatchReportDatagrid"></table>
    </div>

    <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">


        <div class="easyui-panel" id="PerBatchReportReportDateFilterPanel"
            style="margin-bottom: 10px; padding: 10px 5px;" data-options=" title: 'Date (Batch)' ">

            <label>From:</label>
            <input id="PerBatchReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

            <label>To:</label>
            <input id="PerBatchReportDatagridToDate" type="text" class="easyui-datebox" style="width:123px;">

            <div style="text-align:center; margin-top: 10px;">
                <a href="#" class="easyui-linkbutton" iconCls="icon-filter"
                    onclick="applyPerBatchReportDatagridDateFilter()">Apply</a>
                <a href="#" class="easyui-linkbutton" iconCls="icon-cancel"
                    onclick="clearPerBatchReportDatagridDateFilter()">Clear</a>
            </div>
        </div>


        <div class="easyui-panel" id="PerBatchReportReportCategoryFilterPanel"
            style="margin-bottom: 10px; padding: 10px 5px;" data-options=" title: 'Category' ">

            <label>Category:</label>
            <input id="PerBatchReportDatagridCategoryFilterCombobox" type="combobox" class="easyui-combobox"
                style="width:223px;"
                data-options="
                    url:'reports/products/categories',
                    method:'get',
                    valueField: 'id',
                    textField: 'name',
                    limitToList: true,
                    hasDownArrow: true,
                    panelHeight:'auto',
                    mode: 'remote',
                    required:false,
                    onSelect: function(category) {
                        applyPerBatchReportDatagridCategoryFilter(category);
                    }
                  ">

        </div>




        <div id="PerBatchReportDateFilterPanelFooter">
        </div>

        <table id="per_batch_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;"
            data-options="showGroup:true, scrollbarSize:0 ">
        </table>
    </div>

</div>

<div id="PerBatchReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"
        onclick="javascript:$('#PerBatchReportDatagrid').datagrid('reload')">Reload</a>


    <a href="#" class="easyui-linkbutton" iconCls="icon-tip" onclick="showRetestBatchInfoDialog()">Info</a>
    @can('print_per_batch_report')
        <a href="#" class="easyui-linkbutton" iconCls="icon-print"
            onclick="javascript:printFromPerBatchReportDatagrid();">Print</a>
    @endcan

    @can('download_per_batch_report')
        <a href="#" class="easyui-linkbutton" iconCls="icon-export"
            onclick="javascript:downloadPerBatchReports();">Download</a>
    @endcan

</div>


<script type="text/javascript">
    $(function() {
        $('#PerBatchReportDatagrid').datagrid({
            idField: 'id',
            title: 'Retest Batches',
            toolbar: '#PerBatchReportDatagridToolbar',
            fit: true,
            border: false,
            fitColumns: true,
            nowrap: true,
            singleSelect: true,
            method: 'get',
            rownumbers: true,
            pagination: true,
            remoteFilter: true,
            filterMatchType: 'any',
            url: 'reports/retests/per_batch/list',

            columns: [
                [{
                        title: 'Transaction Information',
                        colspan: 5
                    },
                    {
                        title: 'Batch Information',
                        colspan: 3
                    },
                    {
                        title: 'Retest',
                        colspan: 1
                    },
                ],
                [{
                        field: 'department_name',
                        as: 'departments.name',
                        title: 'Department',
                        width: "100px",
                        sortable: true,
                    },
                    {
                        field: 'transaction_id',
                        as: 'batches.transaction_id',
                        title: 'Id',
                        width: "100px",
                        sortable: true,
                    },
                    {
                        field: 'transaction_date',
                        as: 'transactions.date_time',
                        title: 'Date',
                        width: "140px",
                        sortable: true,
                    },
                    {
                        field: 'product_name',
                        as: 'products.kurdish_name',
                        title: 'Product',
                        sortable: true,
                    },
                    {
                        field: 'category_name',
                        as: 'categories.name',
                        title: 'Category',
                        sortable: true,
                    },
                    {
                        field: 'batch_date',
                        as: 'batches.created_at',
                        title: 'Date',
                        sortable: true,
                        width: "140px",
                        align: 'center'
                    },
                    {
                        field: 'batch_id',
                        as: 'batches.id',
                        title: 'Id',
                        sortable: true,
                        width: "100px",
                        align: 'center'
                    },

                    {
                        field: 'product_type',
                        as: 'batches.product_type',
                        title: 'Product Type',
                        sortable: true,
                        align: 'center'
                    },

                    {
                        field: 'retest_batch_id',
                        as: 'batches.retest_batch_id',
                        title: 'Retest of #',
                        sortable: true,
                        width: "100px",
                        align: 'center'
                    },

                ]
            ],

            onDblClickRow: function(index, row) {
                showRetestBatchInfoDialog(row.batch_id);
            },

            onLoadSuccess: function(data) {
                $('#per_batch_report_propertygrid').propertygrid('loadData', data.stat_rows);
            }
        });
    });

    $('#PerBatchReportDatagrid').datagrid('enableFilter', []);

    function clearPerBatchReportDatagridDateFilter(value) {
        $('#PerBatchReportDatagridFromDate').datebox('reset');
        $('#PerBatchReportDatagridToDate').datebox('reset');

        $('#PerBatchReportDatagrid').datagrid('removeFilterRule', 'batches.created_at');

        if (value != '') {
            $('#PerBatchReportDatagrid').datagrid('addFilterRule', {
                field: 'batches.created_at',
                value: value
            });
        }

        $('#PerBatchReportDatagrid').datagrid('doFilter');
    }

    function applyPerBatchReportDatagridDateFilter() {
        var dateFromText = $('#PerBatchReportDatagridFromDate').datebox('getValue');
        dateFrom = $.fn.datebox.defaults.parser(dateFromText);
        dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

        var dateToText = $('#PerBatchReportDatagridToDate').datebox('getValue');
        dateTo = $.fn.datebox.defaults.parser(dateToText);
        dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

        if (dateFromText == '' && dateToText == '') {
            $('#PerBatchReportDatagrid').datagrid('removeFilterRule', 'batches.created_at');
        } else if (dateFromText == '') {
            $('#PerBatchReportDatagrid').datagrid('addFilterRule', {
                field: 'batches.created_at',
                op: 'less',
                value: dateTo
            });
        } else if (dateToText == '') {
            $('#PerBatchReportDatagrid').datagrid('addFilterRule', {
                field: 'batches.created_at',
                op: 'greater',
                value: dateFrom
            });
        } else {
            $('#PerBatchReportDatagrid').datagrid('addFilterRule', {
                field: 'batches.created_at',
                op: 'dateTimeBetween',
                value: dateFrom + ',' + dateTo
            });
        }
        $('#PerBatchReportDatagrid').datagrid('doFilter');
    }

    function applyPerBatchReportDatagridCategoryFilter(category) {

        var filterRulesBefore = $('#PerBatchReportDatagrid').datagrid('options').filterRules;

        if (category.id == 0) {
            $('#PerBatchReportDatagrid').datagrid('removeFilterRule', 'categories.id');
        } else {
            $('#PerBatchReportDatagrid').datagrid('addFilterRule', {
                field: 'categories.id',
                value: category.id
            });
        }

        var filterRules = $('#PerBatchReportDatagrid').datagrid('options').filterRules;

        $('#PerBatchReportDatagrid').datagrid('doFilter');
    }


    function printFromPerBatchReportDatagrid() {
        var per_batchReportDatagridOptions = $('#PerBatchReportDatagrid').datagrid('options');
        var filterRules = per_batchReportDatagridOptions.filterRules;
        var sort = per_batchReportDatagridOptions.sortName;
        var order = per_batchReportDatagridOptions.sortOrder;

        var col = $('#PerBatchReportDatagrid').datagrid('getColumnOption', sort);

        if (col && col.as) {
            sort = col.as;
        }

        var urlToPrint = '<?= \URL::route('print_per_batch_report') ?>?';

        var queryStrings = 'filterRules=' + JSON.stringify(filterRules);

        if (sort) {
            queryStrings += '&sort=' + sort + '&order=' + order;
        }

        print(urlToPrint + queryStrings);

        //sendPrintCommandToServer(urlToPrint + queryStrings , 'A4', ' -o Landscape');
    }

    function downloadPerBatchReports() {
        var per_batchReportDatagridOptions = $('#PerBatchReportDatagrid').datagrid('options');
        var filterRules = per_batchReportDatagridOptions.filterRules;
        var sort = per_batchReportDatagridOptions.sortName;
        var order = per_batchReportDatagridOptions.sortOrder;

        var col = $('#PerBatchReportDatagrid').datagrid('getColumnOption', sort);

        if (col && col.as) {
            sort = col.as;
        }

        var urlToPrint = '<?= \URL::route('download_per_batch_report') ?>?';
        var queryStrings = 'filterRules=' + JSON.stringify(filterRules);

        if (sort) {
            queryStrings += '&sort=' + sort + '&order=' + order;
        }

        print(urlToPrint + queryStrings);
    }

    function getSelectedRetestBatch(column) {
        var row = $('#PerBatchReportDatagrid').datagrid('getSelected');

        if (!row) {
            $.messager.show({
                title: 'Error',
                msg: 'Please select a batch'
            });
            return;
        }
        if (!column) {
            return row['id'];
        }
        return row[column];
    }

    function showRetestBatchInfoDialog(selected_batch_id) {

        var batch_id = getSelectedRetestBatch('batch_id');
        if (!batch_id) {
            if (selected_batch_id) {
                batch_id = selected_batch_id;
            } else {

                return;
            }
        }

        $('#RetestBatchInfoDialog')
            .dialog({
                width: '900px',
                height: '700px',
            })
            .dialog('center')
            .dialog('setTitle', 'Retest Batch Info')
            .dialog('open')
            .dialog('refresh', 'reports/retests/per_batch/retest_batch_info_dialog?batch_id=' + batch_id);
    }
</script>
