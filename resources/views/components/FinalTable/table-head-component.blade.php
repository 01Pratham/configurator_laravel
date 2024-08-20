<tr>
    <th class='Head except' style="background-color: rgb(199, 239, 255) ">
        {{ 'A.' . $NO }}
    </th>
    <th class='Head except' style="background-color: rgb(199, 239, 255) ">
        {{ strlen($Service) < 4 ? strtoupper($Service) : ucfirst($Service) }} Services
    </th>
    <th class='Head except' style="background-color: rgb(199, 239, 255) ">
        Unit
    </th>
    <th class='Head unshareable except' style="background-color: rgb(199, 239, 255) ">
        Cost/Unit
    </th>
    <th class='Head unshareable except' style="background-color: rgb(199, 239, 255) ">
        Monthly Cost
    </th>
    <th class='Head unshareable except' style="background-color: rgb(199, 239, 255) ">
        OTC
    </th>
    <th class='Head unshareable except' style="background-color: rgb(199, 239, 255) ">
        Discount %
    </th>
    <th class='Head unshareable except' style="background-color: rgb(199, 239, 255) ">
        Discounted Price
    </th>
    @if (Route::is('Discounting'))
        <th class='Head unshareable except'>Discounted OTC</th>
        <th class='Head unshareable except'>Discount Ammount</th>
    @endif
</tr>
