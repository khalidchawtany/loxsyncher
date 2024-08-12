<h3 class="center blue-600 mt-1 mb-half bg-blue-200"> {{ $header }} </h3>

<table class="tpx-5 mt-1 w-full left medium has_border inline_horizontal_border border border-blue-200 leading-tight">

    <tr>
        <th class="w-150"> Batch # </th>
        <td class="left"> {{ $batch->id }} </td>
        <th class="w-150"> Date </th>
        <td class="left"> {{ $batch->created_at }} </td>
    </tr>

    <tr>
        <th> Product </th>
        <td class="left"> {{ $batch->batchProduct->kurdish_name }} </td>
        <th> Product Type </th>
        <td class="left"> {{ $batch->product_type }} </td>
    </tr>

    <tr>
        <th> Department </th>
        <td class="left"> {{ $batch->batchProduct->department->name }} </td>
        <th> Product </th>
        <td class="left"> {{ $batch->batchProduct->category->name }} </td>
    </tr>
</table>

<table class="tpx-5 mt-1 w-full left medium has_border inline_horizontal_border border border-blue-200 leading-tight">
    <tr class="bg-blue-100" >
        <th> Lab </th>
        <th> Test </th>
        <th class="center"> Result </th>
        <th class="center"> Status </th>
        <th class="center"> Updated by </th>
    </tr>

    @foreach ($batch->checks as $check)
        <tr>
            <td> {{ $check->checkType->category }} </td>
            <td> {{ $check->checkType->subcategory }} </td>
            <td class="center"> {{ $check->result }} </td>
            <td class="center"> {{ $check->status }} </td>
            <td class="center"> {{ optional($check->updatedBy)->kurdish_name }} </td>
        </tr>
    @endforeach
</table>
