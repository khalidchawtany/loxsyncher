<div class="easyui-layout" fit="true">

    <div data-options="region:'center',border:false">
        <div class="easyui-panel" style="height:100%;width:100%;"
            data-options=" title: 'Monthly Income Charts' ">
            <canvas id="MonthlyIncomeCharts" style="height:100%; width:100%;"></canvas>


        </div>
    </div>

    <div data-options="region:'east', border: true," style="width:350px; padding: 10px;">

        <div class="easyui-panel" style="margin-bottom: 10px; padding: 10px 5px;"
            data-options=" title: 'Date' ">

            <div>
                <label style="width: 50px; display:inline-block;">From:</label>
                <input id="MonthlyIncomeChartsFromDate" type="text" class="easyui-datebox"
                    style="width:223px;">
            </div>

            <div>
                <label style="width: 50px; display:inline-block;">To:</label>
                <input id="MonthlyIncomeChartsToDate" type="text" class="easyui-datebox"
                    style="width:223px;">
            </div>


            </form>


        </div>
        <div class="easyui-panel" style="margin-bottom: 10px; padding: 10px 5px;"
            data-options=" title: 'Sector' ">
            <label style="width: 50px; display:inline-block;">Sector:</label>
            <input id="MonthlyIncomeChartsDepartment" type="combobox" class="easyui-combobox"
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
                onclick="applyMonthlyIncomeChartFilters()">Apply</a>
            <a href="#" class="easyui-linkbutton" iconCls="icon-cancel"
                onclick="clearMonthlyIncomeChartDateFilter()">Clear</a>
        </div>


    </div>

</div>

<script type="text/javascript">
    const ctx = document.getElementById('MonthlyIncomeCharts').getContext('2d');

    function renderMonthlyIncomeChart(monthly_income_chart_labels, monthly_income_chart_data) {


        MonthlyIncomeChart = new Chart(ctx, {
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                      }
                  }
              },
            data: {
                labels: monthly_income_chart_labels,
                datasets: [{
                    type: 'line',
                    label: 'Total Monthly Income',
                    data: monthly_income_chart_data,
                    borderColor: ['rgba(255, 165, 0, 1)', ],
                    borderWidth: 3,
                }, ]
            }
        });

    }

    $(function() {

        var monthly_income_chart_labels = <?= $monthly_income_chart_labels ?>;

        var monthly_income_chart_data = <?= $monthly_income_chart_data ?>;

        renderMonthlyIncomeChart(monthly_income_chart_labels, monthly_income_chart_data);

    });

    function applyMonthlyIncomeChartFilters() {

        var department_id = $('#MonthlyIncomeChartsDepartment').combobox('getValue');

        var date_from = $('#MonthlyIncomeChartsFromDate').datebox('getValue');
        date_from = date_from != '' ? dateFormatterServer(dateParser(date_from)) : '';

        var date_to = $('#MonthlyIncomeChartsToDate').datebox('getValue');
        date_to = date_to != '' ? dateFormatterServer(dateParser(date_to)) : '';

        $.ajax({
            url: '<?= route('getMonthlyIncomeChartData') ?>',
            data: {
                date_from: date_from,
                date_to: date_to,
                department_id: department_id,
            },
            success: function(data) {

                {{-- console.log(MonthlyIncomeChart.data); --}}
                MonthlyIncomeChart.data = {
                    labels: JSON.parse(data.monthly_income_chart_labels),
                    datasets: [{
                        type: 'line',
                        label: 'Total Monthly Income',
                        data: JSON.parse(data.monthly_income_chart_data),
                        borderColor: ['rgba(255, 165, 0, 1)', ],
                        borderWidth: 3,
                    }, ]
                };
                {{-- console.log(MonthlyIncomeChart.data); --}}

                MonthlyIncomeChart.update();

            },
            dataType: 'json'
        });

    }

    function clearMonthlyIncomeChartDateFilter() {
        $('#MonthlyIncomeChartsFromDate').datebox('clear');
        $('#MonthlyIncomeChartsToDate').datebox('clear');
        applyMonthlyIncomeChartFilters();
    }
</script>
