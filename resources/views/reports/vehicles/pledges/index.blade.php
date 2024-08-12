<div class="easyui-layout" fit="true">

    <div data-options="region:'center',border:false">
        <table id="PledgesReportDatagrid"></table>
    </div>

    <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">


        <div class="easyui-panel" id="PledgesReportDateFilterPanel" style="margin-bottom: 10px; padding: 10px 5px;" data-options="title: 'Pledge Date'">

            <label>From:</label>
            <input id="PledgesReportDatagridFromDate" type="text" class="easyui-datebox" style="width:123px;">

            <label>To:</label>
            <input id="PledgesReportDatagridToDate" type="text" class="easyui-datebox" style="width:123px;">

            <div style="text-align:center; margin-top: 10px;">
                <a href="#" class="easyui-linkbutton" iconCls="icon-filter" onclick="applyPledgesReportDatagridDateFilter()">Apply</a>
                <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="clearPledgesReportDatagridDateFilter()">Clear</a>
            </div>
        </div>
        <div id="PledgesReportDateFilterPanelFooter">
        </div>

        <table id="pledges_report_propertygrid" class="easyui-propertygrid" style="width:100%; height:auto;" data-options="showGroup:true, scrollbarSize:0 ">
        </table>
    </div>

</div>

<div id="PledgesReportDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload" onclick="javascript:$('#PledgesReportDatagrid').datagrid('reload')">Reload</a>

    @can('download_pledges_report')
    <a href="#" class="easyui-linkbutton" iconCls="icon-export" onclick="javascript:downloadPledgesReports();">Download</a>
    @endcan

    <a href="javascript:void(0)"
       class="easyui-menubutton"
       style="width: 150px;"
       data-options="plain:false,menu:'#PledgesReportDatagridToggleColumnsMenu',iconCls:'icon-more'">
        Toggle Columns
    </a>
    <div id="PledgesReportDatagridToggleColumnsMenu" class="jeasyui-menu">
    </div>
</div>


<script type="text/javascript">
    $(function() {

        $('#PledgesReportDatagrid').datagrid({
            idField: 'id',
            title: 'Pledges Report',
            toolbar: '#PledgesReportDatagridToolbar',
            fit: true,
            border: false,
            nowrap: true,
            singleSelect: true,
            method: 'get',
            rownumbers: true,
            pagination: true,
            remoteFilter: true,
            filterMatchType: 'any',
            url: 'reports/pledges/list',

            columns: [
                [

                    {
                        field: 'id',
                        as: 'pledges.id',
                        sortable: true,
                        title: 'Pledge Id',
                        align: 'center'
                    },
                    {
                        field: 'pledge_date',
                        as: 'pledges.pledge_date',
                        sortable: true,
                        title: 'Pledge Date',
                        align: 'center'
                    },
                    {
                        field: 'remarks',
                        as: 'pledges.remarks',
                        sortable: true,
                        title: 'Remarks',
                        align: 'center'
                    },
                    {
                        field: 'amount',
                        as: 'pledges.amount',
                        sortable: true,
                        title: 'Amount',
                        align: 'center'
                    },
                    {
                        field: 'amount_status',
                        as: 'pledges.amount_status',
                        sortable: true,
                        title: 'Amount status',
                        align: 'center'
                    },


                    {
                        field: 'pledge_payment_amount',
                        as: 'pledge_payments.pledge_payment_amount',
                        sortable: true,
                        title: 'Payment Amount',
                        align: 'center'
                    },

                    {
                        field: 'pledge_payment_received_by_user',
                        as: 'pledge_payments.received_by',
                        sortable: true,
                        title: 'Payment received by',
                        align: 'center'
                    },

                    {
                        field: 'pledge_payment_received_at',
                        as: 'pledge_payments.received_at',
                        sortable: true,
                        title: 'Payment received at',
                        align: 'center'
                    },

                    {
                        field: 'pledge_payment_revoked_on',
                        as: 'pledge_payments.revoked_on',
                        sortable: true,
                        title: 'Payment revoked on',
                        align: 'center'
                    },


                    {
                        field: 'deposited_by_user',
                        as: 'depositUser.name',
                        sortable: true,
                        title: 'Deposited by',
                        align: 'center'
                    },
                    {
                        field: 'deposited_at',
                        as: 'pledges.deposited_at',
                        sortable: true,
                        title: 'Deposited at',
                        align: 'center'
                    },
                    {
                        field: 'release_approved_by_user',
                        as: 'releaseApproverUser.name',
                        sortable: true,
                        title: 'Release approved by',
                        align: 'center'
                    },
                    {
                        field: 'release_approved_at',
                        as: 'pledges.release_approved_at',
                        sortable: true,
                        title: 'Release approved at',
                        align: 'center'
                    },
                    {
                        field: 'refund_approved_by_user',
                        as: 'refundApproverUser.name',
                        sortable: true,
                        title: 'Refund approved by',
                        align: 'center'
                    },
                    {
                        field: 'refund_approved_at',
                        as: 'pledges.refund_approved_at',
                        sortable: true,
                        title: 'Refund approved at',
                        align: 'center'
                    },
                    {
                        field: 'refunded_by_user',
                        as: 'refundedUser.name',
                        sortable: true,
                        title: 'Refunded by',
                        align: 'center'
                    },
                    {
                        field: 'refunded_at',
                        as: 'pledges.refunded_at',
                        title: 'Refunded at',
                        sortable: true,
                        align: 'center'
                    },
                    {
                        field: 'issuer',
                        as: 'pledges.issuer',
                        hidden: true,
                        sortable: true,
                        title: 'Issuer',
                        align: 'center'
                    },
                    {
                        field: 'coc',
                        as: 'pledges.coc',
                        hidden: true,
                        sortable: true,
                        title: 'COC',
                        align: 'center'
                    },
                    {
                        field: 'flawed',
                        as: 'pledges.flawed',
                        hidden: true,
                        sortable: true,
                        title: 'Flawed',
                        align: 'center'
                    },

                    {
                        field: 'day_limit',
                        as: 'pledges.day_limit',
                        hidden: true,
                        sortable: true,
                        title: 'Day limit',
                        align: 'center'
                    },
                    {
                        field: 'vin',
                        as: 'pledges.vin',
                        hidden: true,
                        sortable: true,
                        title: 'VIN',
                        align: 'center'
                    },
                    {
                        field: 'vehicle_condition',
                        as: 'vehicles.condition',
                        hidden: true,
                        sortable: true,
                        title: 'Vehicle condition',
                        align: 'center'
                    },
                    {
                        field: 'office',
                        as: 'pledges.office',
                        hidden: true,
                        sortable: true,
                        title: 'Office',
                        align: 'center'
                    },
                    {
                        field: 'merchant',
                        as: 'pledges.merchant',
                        hidden: true,
                        sortable: true,
                        title: 'Merchant',
                        align: 'center'
                    },
                    {
                        field: 'vehicle_type',
                        as: 'vehicles.type',
                        hidden: true,
                        sortable: true,
                        title: 'Vehicle type',
                        align: 'center'
                    },
                    {
                        field: 'vehicle_color',
                        as: 'vehicles.color',
                        hidden: true,
                        sortable: true,
                        title: 'Vehicle color',
                        align: 'center'
                    },
                    {
                        field: 'vehicle_make',
                        as: 'vehicle_makes.name',
                        hidden: true,
                        sortable: true,
                        title: 'Vehicle make',
                        align: 'center'
                    },
                    {
                        field: 'vehicle_model',
                        as: 'vehicle_models.name',
                        hidden: true,
                        sortable: true,
                        title: 'Vehicle model',
                        align: 'center'
                    },
                    {
                        field: 'amount_status_change_date',
                        as: 'pledges.amount_status_change_date',
                        hidden: true,
                        sortable: true,
                        title: 'Status at',
                        align: 'center'
                    },


                ]
            ],

            onLoadSuccess: function(data) {
                $('#pledges_report_propertygrid').propertygrid('loadData', data
                    .stat_rows);
            }
        });
    });


    $('#PledgesReportDatagrid').datagrid('generateToggleColumn');

    setTimeout(function() {
        $('#PledgesReportDatagrid').datagrid('enableFilter', [{
                field: 'day_limit',
                type: 'numberbox',
                op: ['equal', 'contains', 'less', 'lessorequal', 'greater', 'greaterorequal']
            },
            {
                field: 'amount',
                type: 'numberbox',
                op: ['equal', 'contains', 'less', 'lessorequal', 'greater', 'greaterorequal']
            },


            {

                field: 'flawed',
                type: 'combobox',
                options: {
                    panelHeight: 'auto',
                    data: [{
                        value: '',
                        text: 'All'
                    }, {
                        value: '1',
                        text: 'Yes'
                    }, {
                        value: '0',
                        text: 'No'
                    }],
                    onChange: function(value) {
                        if (value == '') {
                            $('#PledgesReportDatagrid').datagrid('removeFilterRule',
                                'pledges.flawed');
                        } else {
                            $('#PledgesReportDatagrid').datagrid('addFilterRule', {
                                field: 'pledges.flawed',
                                op: 'equal',
                                value: value
                            });
                        }
                        $('#PledgesReportDatagrid').datagrid('doFilter');
                    }
                }
            },

            {
                field: 'vehicle_type',
                type: 'combobox',
                options: {
                    panelHeight: 'auto',
                    data: <?= json_encode(\App\Helpers\VehicleTypeEnum::filterData()) ?>,
                    onChange: function(value) {
                        if (value == '') {
                            $('#PledgesReportDatagrid').datagrid('removeFilterRule',
                                'vehicles.type');
                        } else {
                            $('#PledgesReportDatagrid').datagrid('addFilterRule', {
                                field: 'vehicles.type',
                                op: 'equal',
                                value: value
                            });
                        }
                        $('#PledgesReportDatagrid').datagrid('doFilter');
                    }
                }
            },


            {
                field: 'coc',
                type: 'combobox',
                options: {
                    panelHeight: 'auto',
                    data: <?= json_encode(\App\Helpers\COCStatusEnum::filterData()) ?>,
                    onChange: function(value) {
                        if (value == '') {
                            $('#PledgesReportDatagrid').datagrid('removeFilterRule',
                                'pledges.coc');
                        } else {
                            $('#PledgesReportDatagrid').datagrid('addFilterRule', {
                                field: 'pledges.coc',
                                op: 'equal',
                                value: value
                            });
                        }
                        $('#PledgesReportDatagrid').datagrid('doFilter');
                    }
                }
            },

            {
                field: 'amount_status',
                type: 'combobox',
                options: {
                    panelHeight: 'auto',
                    data: <?= json_encode(\App\Helpers\PledgeAmountStatusEnum::filterData()) ?>,
                    onChange: function(value) {
                        if (value == '') {
                            $('#PledgesReportDatagrid').datagrid('removeFilterRule',
                                'pledges.amount_status');
                        } else {
                            $('#PledgesReportDatagrid').datagrid('addFilterRule', {
                                field: 'pledges.amount_status',
                                op: 'equal',
                                value: value
                            });
                        }
                        $('#PledgesReportDatagrid').datagrid('doFilter');
                    }
                }
            },

            {
                field: 'issuer',
                type: 'combobox',
                options: {
                    panelHeight: 'auto',
                    data: <?= json_encode(\App\Helpers\COCIssuerEnum::filterData()) ?>,
                    onChange: function(value) {
                        if (value == '') {
                            $('#PledgesReportDatagrid').datagrid('removeFilterRule',
                                'pledges.issuer');
                        } else {
                            $('#PledgesReportDatagrid').datagrid('addFilterRule', {
                                field: 'pledges.issuer',
                                op: 'equal',
                                value: value
                            });
                        }
                        $('#PledgesReportDatagrid').datagrid('doFilter');
                    }
                }
            },


        ]);
    }, 0);

    $('#PledgesReportDatagrid').datagrid('getPanel').find('tr.datagrid-filter-row').hide();
    $('#PledgesReportDatagrid').datagrid('resize');

    function clearPledgesReportDatagridDateFilter(value) {
        $('#PledgesReportDatagridFromDate').datebox('reset');
        $('#PledgesReportDatagridToDate').datebox('reset');

        $('#PledgesReportDatagrid').datagrid('removeFilterRule', 'pledges.pledge_date');

        if (value != '') {
            $('#PledgesReportDatagrid').datagrid('addFilterRule', {
                field: 'pledges.pledge_date',
                value: value
            });
        }

        $('#PledgesReportDatagrid').datagrid('doFilter');
    }

    function applyPledgesReportDatagridDateFilter() {
        var dateFromText = $('#PledgesReportDatagridFromDate').datebox('getValue');
        dateFrom = $.fn.datebox.defaults.parser(dateFromText);
        dateFrom = $.fn.datebox.defaults.formatterServer(dateFrom);

        var dateToText = $('#PledgesReportDatagridToDate').datebox('getValue');
        dateTo = $.fn.datebox.defaults.parser(dateToText);
        dateTo = $.fn.datebox.defaults.formatterServer(dateTo);

        if (dateFromText == '' && dateToText != '' ||
            dateFromText != '' && dateToText == '') {

            $.messager.show({
                title: 'Error',
                msg: 'You have to select both dates!'
            });
            return;
        }

        if (dateFromText == '' && dateToText == '') {
            $('#PledgesReportDatagrid').datagrid('removeFilterRule', 'pledges.pledge_date');

        } else {
            $('#PledgesReportDatagrid').datagrid('addFilterRule', {
                field: 'pledges.pledge_date',
                op: 'dateBetween',
                value: dateFrom + ',' + dateTo
            });
        }
        $('#PledgesReportDatagrid').datagrid('doFilter');
    }

    function downloadPledgesReports() {
        var dgOpts = $('#PledgesReportDatagrid').datagrid('options');
        var filterRules = dgOpts.filterRules;
        var sort = dgOpts.sortName;
        var order = dgOpts.sortOrder;

        var col = $('#PledgesReportDatagrid').datagrid('getColumnOption', sort);

        if (col && col.as) {
            sort = col.as;
        }

        var urlToPrint = '<?= \URL::route('download_pledges_report') ?>?';

        var queryStrings = 'filterRules=' + JSON.stringify(filterRules);

        if (sort) {
            queryStrings += '&sort=' + sort + '&order=' + order;
        }

        print(urlToPrint + queryStrings);
    }
</script>
