<div class="easyui-layout" fit="true">

    <div data-options="region:'center',border:false">
        <div class="easyui-panel" style="height:100%;width:100%;"
            data-options=" title: 'Weekly Transaction Charts' ">
            <canvas id="WeeklyTransactionCharts" style="height:100%; width:100%;"></canvas>


        </div>
    </div>

    <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

        <div class="easyui-panel" style="margin-bottom: 10px; padding: 10px 5px;"
            data-options=" title: 'Date' ">

            <div>
                <label style="width: 50px; display:inline-block;">From:</label>
                <input id="WeeklyTransactionChartsFromDate" type="text" class="easyui-datebox"
                    style="width:223px;">
            </div>

            <div>
                <label style="width: 50px; display:inline-block;">To:</label>
                <input id="WeeklyTransactionChartsToDate" type="text" class="easyui-datebox"
                    style="width:223px;">
            </div>


            </form>


        </div>
        <div class="easyui-panel" style="margin-bottom: 10px; padding: 10px 5px;"
            data-options=" title: 'Sector' ">
            <label style="width: 50px; display:inline-block;">Sector:</label>
            <input id="WeeklyTransactionChartsDepartment" type="combobox" class="easyui-combobox"
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
                onclick="applyWeeklyTransactionChartFilters()">Apply</a>
            <a href="#" class="easyui-linkbutton" iconCls="icon-cancel"
                onclick="clearWeeklyTransactionChartDateFilter()">Clear</a>
        </div>


    </div>

</div>

<script type="text/javascript">
    const ctx = document.getElementById('WeeklyTransactionCharts').getContext('2d');

    function renderWeeklyTransactionChart(weekly_transaction_chart_labels, weekly_transaction_chart_data) {


        WeeklyTransactionChart = new Chart(ctx, {
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                      }
                  }
              },
            data: {
                labels: weekly_transaction_chart_labels,
                datasets: [{
                    type: 'line',
                    label: 'Total Weekly Transaction',
                    data: weekly_transaction_chart_data,
                    borderColor: ['rgba(255, 165, 0, 1)', ],
                    borderWidth: 3,
                }, ]
            }
        });

    }

    $(function() {

        var weekly_transaction_chart_labels = <?= $weekly_transaction_chart_labels ?>;

        var weekly_transaction_chart_data = <?= $weekly_transaction_chart_data ?>;

        renderWeeklyTransactionChart(weekly_transaction_chart_labels, weekly_transaction_chart_data);

    });

    function applyWeeklyTransactionChartFilters() {

        var department_id = $('#WeeklyTransactionChartsDepartment').combobox('getValue');

        var date_from = $('#WeeklyTransactionChartsFromDate').datebox('getValue');
        date_from = date_from != '' ? dateFormatterServer(dateParser(date_from)) : '';

        var date_to = $('#WeeklyTransactionChartsToDate').datebox('getValue');
        date_to = date_to != '' ? dateFormatterServer(dateParser(date_to)) : '';

        $.ajax({
            url: '<?= route('getWeeklyTransactionChartData') ?>',
            data: {
                date_from: date_from,
                date_to: date_to,
                department_id: department_id,
            },
            success: function(data) {

                WeeklyTransactionChart.data = {
                    labels: JSON.parse(data.weekly_transaction_chart_labels),
                    datasets: [{
                        type: 'line',
                        label: 'Total Weekly Transaction',
                        data: JSON.parse(data.weekly_transaction_chart_data),
                        borderColor: ['rgba(255, 165, 0, 1)', ],
                        borderWidth: 3,
                    }, ]
                };

                WeeklyTransactionChart.update();

            },
            dataType: 'json'
        });

    }

    function clearWeeklyTransactionChartDateFilter() {
        $('#WeeklyTransactionChartsFromDate').datebox('clear');
        $('#WeeklyTransactionChartsToDate').datebox('clear');
        applyWeeklyTransactionChartFilters();
    }
</script>
