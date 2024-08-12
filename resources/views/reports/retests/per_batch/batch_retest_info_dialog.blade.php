<div class="easyui-layout" fit="true">

    <div data-options="region:'center', border:false" style="padding: 10px;">


        @include('reports.retests.per_batch.retest_batch_info', [
            'header' => 'Original Batch' . ' - ' . $batch->created_at,
        ])

        @foreach ($batch->retests as $retest)
            @include('reports.retests.per_batch.retest_batch_info', [
                'header' => 'Retest #' . $loop->iteration . ' - ' . $retest->created_at,
                'batch' => $retest,
            ])
        @endforeach


    </div>
    <div class="panel-buttons" data-options="region:'south', height:'auto'">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel"
            onclick="javascript:$('#RetestBatchInfoDialog').dialog('close');" style="width:90px">Close</a>
    </div>
</div>
