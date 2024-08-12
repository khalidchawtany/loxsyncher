<div class="easyui-layout" fit="true">

    <div data-options="region:'center',border:false">
        <div class="easyui-panel" style="height:100%;width:100%;"
            data-options=" title: 'Monthly Transaction Charts' ">
            <canvas id="MonthlyTransactionCharts" style="height:100%; width:100%;"></canvas>


        </div>
    </div>

    <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

        <div class="easyui-panel" style="margin-bottom: 10px; padding: 10px 5px;"
            data-options=" title: 'Date' ">

            <div>
                <label style="width: 50px; display:inline-block;">From:</label>
                <input id="MonthlyTransactionChartsFromDate" type="text" class="easyui-datebox"
                    style="width:223px;">
            </div>

            <div>
                <label style="width: 50px; display:inline-block;">To:</label>
                <input id="MonthlyTransactionChartsToDate" type="text" class="easyui-datebox"
                    style="width:223px;">
            </div>


            </form>


        </div>
        <div class="easyui-panel" style="margin-bottom: 10px; padding: 10px 5px;"
            data-options=" title: 'Sector' ">
            <label style="width: 50px; display:inline-block;">Sector:</label>
            <input id="MonthlyTransactionChartsDepartment" type="combobox" class="easyui-combobox"
                style="width:223px;" data-options="
                    url:'reports/departments/list?append={{ \App\Models\Incineration::$DEPARTMENT_NAME }}',
                    method:'get',
                    valueField: 'department_id',
                    textField: 'department_name',
                    limitToList: true,
                    hasDownArrow: true,
                    panelHeight:'auto',
                  ">
        </div>

        <div style="text-align:center; margin-top: 10px;">
            <a href="#" class="easyui-linkbutton" iconCls="icon-filter"
                onclick="applyMonthlyTransactionChartFilters()">Apply</a>
            <a href="#" class="easyui-linkbutton" iconCls="icon-cancel"
                onclick="clearMonthlyTransactionChartDateFilter()">Clear</a>
        </div>


    </div>

</div>

<script type="text/javascript">
    const ctx = document.getElementById('MonthlyTransactionCharts').getContext('2d');

    function renderMonthlyTransactionChart(monthly_transaction_chart_labels, monthly_transaction_chart_data) {


        MonthlyTransactionChart = new Chart(ctx, {
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                      }
                  }
              },
            data: {
                labels: monthly_transaction_chart_labels,
                datasets: [{
                    type: 'line',
                    label: 'Total Monthly Transaction',
                    data: monthly_transaction_chart_data,
                    borderColor: ['rgba(255, 165, 0, 1)', ],
                    borderWidth: 3,
                }, ]
            }
        });

    }

    $(function() {

        var monthly_transaction_chart_labels = <?= $monthly_transaction_chart_labels ?>;

        var monthly_transaction_chart_data = <?= $monthly_transaction_chart_data ?>;

        renderMonthlyTransactionChart(monthly_transaction_chart_labels, monthly_transaction_chart_data);

    });

    function applyMonthlyTransactionChartFilters() {

        var department_id = $('#MonthlyTransactionChartsDepartment').combobox('getValue');

        var date_from = $('#MonthlyTransactionChartsFromDate').datebox('getValue');
        date_from = date_from != '' ? dateFormatterServer(dateParser(date_from)) : '';

        var date_to = $('#MonthlyTransactionChartsToDate').datebox('getValue');
        date_to = date_to != '' ? dateFormatterServer(dateParser(date_to)) : '';

        $.ajax({
            url: '<?= route('getMonthlyTransactionChartData') ?>',
            data: {
                date_from: date_from,
                date_to: date_to,
                department_id: department_id,
            },
            success: function(data) {

                {{-- console.log(MonthlyTransactionChart.data); --}}
                MonthlyTransactionChart.data = {
                    labels: JSON.parse(data.monthly_transaction_chart_labels),
                    datasets: [{
                        type: 'line',
                        label: 'Total Monthly Transaction',
                        data: JSON.parse(data.monthly_transaction_chart_data),
                        borderColor: ['rgba(255, 165, 0, 1)', ],
                        borderWidth: 3,
                    }, ]
                };
                {{-- console.log(MonthlyTransactionChart.data); --}}

                MonthlyTransactionChart.update();

            },
            dataType: 'json'
        });

    }

    function clearMonthlyTransactionChartDateFilter() {
        $('#MonthlyTransactionChartsFromDate').datebox('clear');
        $('#MonthlyTransactionChartsToDate').datebox('clear');
        applyMonthlyTransactionChartFilters();
    }
</script>
