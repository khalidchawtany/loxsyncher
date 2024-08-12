@push ('styles')
    table.logo_table {
        margin-top: 10px;
        text-align: left;
        width: auto;
        float: left;
        color: #666666;
        font-size: 12px;
    }
@endpush

@isset($is_test_result)
    <table class="header_table">
        <tr>
            <td style="width:1%; ">
            </td>
            <td style="width:41%; font-weight: bold; font-size:13px; line-height: 24px;">
                <p>كۆمپانیای لۆكس بۆ کۆنترۆڵی جۆری و دەرکردنی بڕوانامە</p>
                <p>نوسینگەی {!! \App\Models\AppSetting::get('site_name_formal_kurdish') !!}</p>
                <p>شركة لوكس للسیطرة النوعیة و اصدار الشهادات</p>
                <p>مکتب {!! \App\Models\AppSetting::get('site_name_formal_arabic') !!}</p>
            </td>
            <td style="width:13%; text-align: center;">
                <img src="{{ asset('img/print_logo.png') }}" >
            </td>
                <td style="width:45%; font-weight: bold; text-align: left; direction: ltr;">
                    <table class="logo_table">
                        <tr>
                            <td>
                                <img style="width: 3.15cm; height: 158.27px;" src="{{ asset('img/IB005.png') }}" >
                            </td>
                                <td>
                                    <img style="width: 3.15cm;" src="{{ asset('img/TL038.png') }}" >
                                </td>
                        </tr>
                    </table>
                </td>
        </tr>
    </table>
    <div class="header_table_seperator"></div>
@else
    @isset($landscape)
        <img style="width:100%;" src="{{ App\Utils\AssetsHelper::img('Lox-Letterhead-landscape.png') }}" >
    @else
        <img style="width:100%;" src="{{ App\Utils\AssetsHelper::img('Lox-Letterhead.png') }}" >
    @endisset
@endisset

