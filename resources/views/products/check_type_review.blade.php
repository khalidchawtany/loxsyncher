<table id="ProductCheckTypesDatagrid"></table>

<div id="ProductCheckTypesDatagridToolbar" style="padding:5px;text-align:center;">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload"
        onclick="javascript:$('#ProductCheckTypesDatagrid').edatagrid('reload')">Reload</a>
    @can('view_product')
        <a href="#" class="easyui-linkbutton" onclick="switchDashboardMainTab('Products', '/products')">Products</a>
    @endcan

    <a href="#" class="easyui-linkbutton" iconCls="icon-export"
        onclick="javascript:downloadProductChecktypesReviews();">Download</a>
</div>

<script type="text/javascript">
    $(function() {
        $('#ProductCheckTypesDatagrid').edatagrid({
            idField: 'id',
            title: 'Product Test Types',
            toolbar: '#ProductCheckTypesDatagridToolbar',
            fit: true,
            border: false,
            fitColumns: true,
            singleSelect: true,
            method: 'get',
            rownumbers: true,
            pagination: true,
            remoteFilter: true,
            filterMatchType: 'any',
            url: 'products/check-type-review/list',
            saveUrl: '#',
            updateUrl: '#',
            destroyUrl: '#',

            columns: [
                [{
                        field: 'disabled',
                        as: 'products.disabled',
                        title: 'Disabled (Product)',
                        sortable: true,
                        width: 30,
                        align: 'center',
                        sortable: true
                    },
                    {
                        field: 'name',
                        as: 'products.name',
                        title: 'P. Name',
                        width: 50,
                        sortable: true
                    },
                    {
                        field: 'kurdish_name',
                        as: 'products.kurdish_name',
                        title: 'P. Kurdish Name',
                        width: 50,
                        sortable: true
                    },
                    {
                        field: 'product_category_name',
                        as: 'categories.name',
                        title: 'P. Category',
                        width: 50,
                        sortable: true
                    },
                    {
                        field: 'category',
                        as: 'check_types.category',
                        title: 'Category',
                        width: 50,
                        sortable: true
                    },
                    {
                        field: 'subcategory',
                        as: 'check_types.subcategory',
                        title: 'Subcategory',
                        width: 50,
                        sortable: true
                    },
                    {
                        field: 'check_methods',
                        as: 'product_check_type.check_methods',
                        title: 'Methods',
                        width: 50,
                        sortable: true
                    },

                    {
                        field: 'active',
                        as: 'product_check_type.active',
                        title: 'Active (P. Test)',
                        sortable: true,
                        width: 30,
                        align: 'center',
                        sortable: true
                    },
                    {
                        field: 'check_limits',
                        as: 'product_check_type.check_limits',
                        title: 'Limits',
                        width: 50,
                        sortable: true
                    },
                    {
                        field: 'check_normal_range',
                        as: 'product_check_type.check_normal_range',
                        title: 'Normal Range',
                        width: 50,
                        sortable: true
                    },
                    {
                        field: 'disabled',
                        as: 'products.disabled',
                        title: 'Disabled (Product)',
                        sortable: true,
                        width: 20,
                        align: 'center',
                        formatter: function(value) {
                            return value == 1 ? 'Yes' : 'No';
                        },
                        sortable: true
                    },
                    {
                        field: 'active',
                        as: 'product_check_type.active',
                        title: 'Active (P. Test)',
                        sortable: true,
                        width: 20,
                        align: 'center',
                        formatter: function(value) {
                            return value == 1 ? 'Yes' : 'No';
                        },
                        sortable: true
                    },

                ]
            ]

        });
    });

    $('#ProductCheckTypesDatagrid').edatagrid('enableFilter', [{
            field: 'disabled',
            type: 'combobox',
            options: {
                panelHeight: 'auto',
                data: [{
                        value: '',
                        text: 'All'
                    },
                    {
                        value: '0',
                        text: 'Enabled'
                    }

                    , {
                        value: '1',
                        text: 'Disabled'
                    }
                ],
                onChange: function(value) {
                    if (value == '') {
                        $('#ProductCheckTypesDatagrid').datagrid('removeFilterRule', 'products.disabled');
                    } else {
                        $('#ProductCheckTypesDatagrid').datagrid('addFilterRule', {
                            field: 'products.disabled',
                            op: 'equal',
                            value: value
                        });
                    }
                    $('#ProductCheckTypesDatagrid').datagrid('doFilter');
                }
            }
        },

        {
            field: 'active',
            type: 'combobox',
            options: {
                panelHeight: 'auto',
                data: [{
                    value: '',
                    text: 'All'
                }, {
                    value: '1',
                    text: 'Active'
                }, {
                    value: '0',
                    text: 'Inactive'
                }],
                onChange: function(value) {
                    if (value == '') {
                        $('#ProductCheckTypesDatagrid').datagrid('removeFilterRule',
                            'product_check_type.active');
                    } else {
                        $('#ProductCheckTypesDatagrid').datagrid('addFilterRule', {
                            field: 'product_check_type.active',
                            op: 'equal',
                            value: value
                        });
                    }
                    $('#ProductCheckTypesDatagrid').datagrid('doFilter');
                }
            }
        },
    ]);



    function downloadProductChecktypesReviews() {
        var url = '<?= \URL::route('download_product_checktypes_reviews') ?>?'
        var reviewProductCheckTypesDatagridOptions = $('#ProductCheckTypesDatagrid').datagrid('options');
        var filterRules = reviewProductCheckTypesDatagridOptions.filterRules;
        var sort = reviewProductCheckTypesDatagridOptions.sortName;
        var order = reviewProductCheckTypesDatagridOptions.sortOrder;

        var col = $('#ProductCheckTypesDatagrid').datagrid('getColumnOption', sort);

        if (col && col.as) {
            sort = col.as;
        }

        var queryStrings = 'filterRules=' + JSON.stringify(filterRules);

        if (sort) {
            queryStrings += '&sort=' + sort + '&order=' + order;
        }

        print(url + queryStrings);

    }
</script>
