<tr>
    <th class='except unshareable' style=' background: rgba(212,212,212,1);'> Sr No . </th>
    <th class='final colspan except unshareable' colspan='3' style=' background: rgba(212,212,212,1);'> Description
    </th>
    <th class='colspan except unshareable' style=' background: rgba(212,212,212,1);' colspan='2'>MRC</th>
    <th class='colspan except unshareable' style=' background: rgba(212,212,212,1);' colspan='2'>Discounted MRC</th>
    @if (Route::is('Discounting'))
        <th class='colspan except unshareable' style=' background: rgba(212,212,212,1);' colspan='2'>
            Discount Ammount
        </th>
    @endif
</tr>

@php
    $i = 0;
@endphp

@if (!empty($INFRASTRUCTURE))
    <tr>
        <td style="" class='unshareable'>{{ $i += 1 }}</td>
        <td style="" class='colspan  final unshareable' colspan='3'> Infrastructure</td>
        <td style="white-space: nowrap;" class='colspan unshareable' colspan='2'id="infraTotal_{{ $KEY }}">
            @INR(array_sum($INFRASTRUCTURE))
        </td>
        <td style="white-space: nowrap;" class='colspan unshareable'
            colspan='2'id="discAmmtInfra_{{ $KEY }}">
            @INR(array_sum($DISCOUNTED_INFRASTRUCTURE) ?? array_sum($INFRASTRUCTURE))
        </td>
        @if (Route::is('Discounting'))
            <td style="white-space: nowrap;" colspan='2'
                class='colspan unshareable'id="DiscInfra_{{ $KEY }}">
                @INR(array_sum($INFRASTRUCTURE) - (array_sum($DISCOUNTED_INFRASTRUCTURE) ?? array_sum($INFRASTRUCTURE)))
            </td>
        @endif
    </tr>
@endif
@if (!empty($MANAGED))
    <tr>
        <td style="" class='unshareable'>{{ $i += 1 }}</td>
        <td style="" class='colspan  final unshareable' colspan='3'> Managed Services</td>
        <td style="white-space: nowrap;" class='colspan unshareable' colspan='2'id="MngTotal_{{ $KEY }}">
            @INR(array_sum($MANAGED))
        </td>
        <td style="white-space: nowrap;" class='colspan unshareable' colspan='2'id="discAmmtMng_{{ $KEY }}">
            @INR(array_sum($DISCOUNTED_MANAGED) ?? array_sum($MANAGED))
        </td>
        @if (Route::is('Discounting'))
            <td style="white-space: nowrap;" colspan='2' class='colspan unshareable'id="DiscMng_{{ $KEY }}">
                @INR(array_sum($MANAGED) - (array_sum($DISCOUNTED_MANAGED) ?? array_sum($MANAGED)))
            </td>
        @endif
    </tr>
@endif
@if ($OTC > 0)
    <tr>
        <td style="" class='unshareable'>{{ $i += 1 }}</td>
        <td style="" class='colspan final unshareable' colspan='3'> One Time Cost </td>
        <td style="white-space: nowrap;" class='colspan unshareable' colspan='2' id="final_otc_{{ $KEY }}">
            @INR($OTC)
        </td>
        <td style="white-space: nowrap;" class='colspan unshareable' colspan='2'
            id="discAmmtOtc_{{ $KEY }}">@INR(0)</td>
        @if (Route::is('Discounting'))
            <td style="white-space: nowrap;" colspan='2' class='colspan unshareable'>@INR(0)</td>
        @endif
    </tr>
@endif
<tr>
    <th class=' final unshareable' style=' background-color: rgb(255, 207, 203);'> </th>
    <th class=' final colspan except unshareable' colspan='3' style=' background-color: rgb(255, 207, 203);'>
        Total [ Monthly ]
    </th>
    <th class=' colspan except unshareable' colspan='2'
        style=' background-color: rgb(255, 207, 203);white-space: nowrap;' id='total_monthly_{{ $KEY }}'
        data-value="{{ $MONTHLY_TOTAL }}">
        @INR($MONTHLY_TOTAL)
    </th>
    <th class=' colspan except unshareable' colspan='2'
        style=' background-color: rgb(255, 207, 203);white-space: nowrap;' id='DiscTotal_{{ $KEY }}'
        data-value="">
        @INR($DISCOUNTED_MONTHLY_TOTAL ?? $MONTHLY_TOTAL)
    </th>
    @if (Route::is('Discounting'))
        <th style=' background-color: rgb(255, 207, 203);white-space: nowrap;' colspan='2'
            class=' colspan except unshareable'id='totalDiscAmmt_{{ $KEY }}' data-value="">
            @INR($MONTHLY_TOTAL - ($DISCOUNTED_MONTHLY_TOTAL ?? $MONTHLY_TOTAL))
        </th>
    @endif
</tr>
<tr>
    <th class=' final unshareable' style=' background-color: rgb(255, 226, 182);'> </th>
    <th class=' final colspan except unshareable' colspan='3' style=' background-color: rgb(255, 226, 182);'>
        Total [ For {{ $TENURE }} Months ]
    </th>
    <th class=' colspan except unshareable' colspan='2'
        style=' background-color: rgb(255, 226, 182);white-space: nowrap;' id="MonthlyTotal_{{ $KEY }}"
        data-period="{{ $TENURE }}" data-value="{{ $TENURE_TOTAL }}">
        @INR($TENURE_TOTAL)
    </th>
    <th class=' colspan except unshareable MonthlyDiscounted' colspan='2'
        style=' background-color: rgb(255, 226, 182);white-space: nowrap;' id="MonthlyDiscounted_{{ $KEY }}"
        data-period="{{ $TENURE }}">
        @INR($DISCOUNTED_TENURE_TOTAL ?? $TENURE_TOTAL)
    </th>
    @if (Route::is('Discounting'))
        <th class=' colspan except unshareable' style=' background-color: rgb(255, 226, 182);white-space: nowrap;'
            id="MonthlyDiscAmmt_{{ $KEY }}" data-period="{{ $TENURE }}" colspan='2'>
            @INR($TENURE_TOTAL - ($DISCOUNTED_TENURE_TOTAL ?? $TENURE_TOTAL))
        </th>
    @endif
</tr>
