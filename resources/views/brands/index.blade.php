<div id="BrandsDatagridContainer" style="width:100%;height:100%;">

    <table id="BrandsDatagrid"></table>

    <div id="BrandsDatagridToolbar" style="padding:5px;text-align:center;">
        @can('create_brand')
            <a href="#" class="easyui-linkbutton" iconCls="icon-add" onclick="showBrandDialog()">New</a>
        @endcan
        <a href="#" class="easyui-linkbutton" iconCls="icon-reload"
            onclick="javascript:$('#BrandsDatagrid').edatagrid('reload')">Reload</a>

        @can('view_product')
            <a href="#" class="easyui-linkbutton"
                onclick="switchDashboardMainTab('Products', '/products')">Products</a>
        @endcan
    </div>

</div>

<div id="BrandDialog" class="easyui-dialog" closed="true" modal="true"></div>

<style media="screen">
    #BrandsDatagridContainer .datagrid-view .datagrid-body {
        background: url('/img/datagrid/brand.png') no-repeat center;
    }
</style>

<script type="text/javascript">
    $(function() {
        $('#BrandsDatagrid').edatagrid({
            idField: 'id',
            title: 'Brands',
            toolbar: '#BrandsDatagridToolbar',
            fit: true,
            border: false,
            fitColumns: true,
            singleSelect: true,
            method: 'get',
            rownumbers: true,
            pagination: true,
            remoteFilter: true,
            filterMatchType: 'any',
            url: 'brands/list',
            destroyUrl: 'brands/destroy',

            columns: [
                [{
                        field: 'id',
                        title: 'id',
                        width: 20
                    },
                    {
                        field: 'brand_product',
                        as: 'products.kurdish_name',
                        title: 'Product',
                        width: 50,
                    },

                    {
                        field: 'name',
                        title: 'Name',
                        width: 50,
                    },

                    {
                        field: 'company',
                        title: 'Company',
                        width: 50,
                    }

                    @can('destroy_brand')
                        , {
                            field: 'action',
                            title: 'Action',
                            width: 100,
                            align: 'center',
                            formatter: function(value, row, index) {
                                return '<button onclick="deleteRow(\'BrandsDatagrid\', ' +
                                    index + ')">Delete</button>';
                            }
                        }
                    @endcan
                ]
            ],

            onDblClickRow: function(index, row) {
                showBrandDialog(row.id);
            }


        });
    });

    $('#BrandsDatagrid').edatagrid('enableFilter', [{
        field: 'action',
        type: 'label'
    }]);


    function getSelectedBrand(column) {
        var row = $('#BrandsDatagrid').datagrid('getSelected');

        if (!row) {
            $.messager.show({
                title: 'Error',
                msg: 'Please select a brand'
            });
            return;
        }
        if (!column) {
            return row['id'];
        }
        return row[column];
    }

    function showBrandDialog(id) {
        var params = '';
        if (id) {
            params = '?id=' + id;
        }
        $('#BrandDialog').dialog('setTitle', 'New Brand')
            .dialog('refresh', 'brands/dialog' + params);
    }
</script>
