@php
    try {
        $group = preg_match('/vm/', $keys['_K']) ? $keys['_k'] : $keys['_K'];
        $MRC = $arr['mrc'];
        $Discount = floatval($arr['discount']) / 100;
        $DiscountedAmmt = $MRC - $MRC * $Discount;
    } catch (\Throwable $th) {
        $group = preg_match('/vm/', $keys['_K']) ? $keys['_k'] : $keys['_K'];
        $MRC = $arr['mrc'];
        $Discount = 0;
        $DiscountedAmmt = $MRC - $MRC * $Discount;
    }
@endphp
<tr id="{{ $keys['_k'] . '_' . (isset($keys['__k']) ? $keys['__k'] . '_' : '') . $keys['KEY'] }}"
    data-key="{{ $keys['KEY'] }}" data-group="{{ $keys['_K'] }}" data-cat="{{ $keys['_k'] }}">

    <td {{ $rowspan ?? '' }}>
        {{ $arr['service'] }}
    </td>

    <td class="text-left final">
        {{ ucwords($arr['product']) }}
    </td>

    <td class="qty" {{ $rowspan ?? '' }}>
        {{ $arr['qty'] }} {{ !empty($arr['prod_unit']) ? $arr['prod_unit'] : 'NO' }}
    </td>

    <td class="Unit unshareable" data-unit={{ $arr['unit_price'] }}>
        @INR($arr['unit_price'])
    </td>

    <td class="MRC text-nowrap unshareable mrc_{{ "{$KEY} {$Class}" }} {{ floatval($arr['otc']) > 0 ? 'hasOTC' : '' }}"
        data-MRC="{{ $MRC }}">
        @INR($MRC)
    </td>

    <td class="text-nowrap Otc">
        @INR($arr['otc'])
    </td>

    <td class="percent" data-percent="{{ floatval($arr['discount']) }}" data-discId="{{ $keys['_k'] }}"
        data-key="{{ $KEY }}" data-group="{{ $group }}">
        {{ number_format(floatval($arr['discount']), 2, '.', '') . ' %' }}
    </td>

    <td class="DiscountedMrc text-nowrap {{ $Class }}">
        @INR($DiscountedAmmt)
    </td>

    @if (Route::is('Discounting'))
        <td class="text-nowrap DiscountedOtc">@INR(0)</td>
        <td class="discountAmmt text-nowrap">@INR($MRC - $DiscountedAmmt)</td>
    @endif
</tr>
