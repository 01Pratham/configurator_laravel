<table class="tbl_tc tbl_summary except" style="width: 100%; padding: ">
    <tr hidden class='extraLine'></tr>
    <tr hidden class='extraLine'></tr>
    <tr hidden class='extraLine'></tr>
    <tr hidden class='extraLine'></tr>
    <tr hidden class='extraLine'></tr>
    <tr hidden class='extraLine'></tr>

    <tbody hidden>
        <tr>
            <th style="background: rgba(198,224,180,1)">Service Components</th>
            <th style="background: rgba(198,224,180,1)">Monthly Service Pay</th>
            <th style="background: rgba(198,224,180,1)">Months</th>
            <th style="background: rgba(198,224,180,1)">Total Cost</th>
            <th style="background: rgba(198,224,180,1)">One Time Service Pay</th>
        </tr>
        @php
            $FINAL_TOTAL = ['HEADS' => [], 'MONTHLY_TOTAL' => 0, 'TENURE_TOTAL' => 0, 'TOTAL_OTC' => 0];
        @endphp
        @foreach ($Total as $KEY => $VAL)
            @php
                if ($KEY == '_prices') {
                    continue;
                }
            @endphp
            <tr>
                <td>ESDS' eNlight Cloud Hosting Services - {{ $VAL['TITLE'] }} </td>
                <td style="white-space: nowrap;">{{ $VAL['MONTHLY_TOTAL'] }}</td>
                <td>{{ $VAL['TENURE'] }}</td>
                <td style="white-space: nowrap;">{{ $VAL['TENURE_TOTAL'] }}</td>
                <td style="white-space: nowrap;">{{ $VAL['OTC'] }}</td>
            </tr>
            @php
                $FINAL_TOTAL['HEADS'][$KEY] = $VAL['TITLE'];
                $FINAL_TOTAL['MONTHLY_TOTAL'] += $VAL['MONTHLY_TOTAL'];
                $FINAL_TOTAL['TENURE_TOTAL'] += $VAL['TENURE_TOTAL'];
                $FINAL_TOTAL['TOTAL_OTC'] += $VAL['OTC'];
            @endphp
        @endforeach
        <tr>
            <td>Total</td>
            <td></td>
            <td></td>
            <td style="white-space: nowrap;">@INR($FINAL_TOTAL['TOTAL_OTC'])</td>
            <td style="white-space: nowrap;">@INR($FINAL_TOTAL['TOTAL_OTC'])</td>
        </tr>
        <tr>
            <th style="background: rgba(198,224,180,1)" colspan=4>
                Total Cost for {{ implode(' and ', $FINAL_TOTAL['HEADS']) }} ( Exclusive of Taxes ).
            </th>
            <th style="background: rgba(198,224,180,1)" style="white-space: nowrap;">
                {{ $FINAL_TOTAL['TENURE_TOTAL'] + $FINAL_TOTAL['TOTAL_OTC'] }}
            </th>
        </tr>
    </tbody>
    <tbody id="terms">
        <tr>
            <th class="noBorder" colspan=80 style="color : rgba(0, 182, 255,1)">Terms and Conditions</th>
        </tr>
        <tr style=" border: hidden;" class="noExl">
            <td style="background:white" hidden class="noBorder"></td>
            <td contentEditable="true" colspan=80 class="myTextArea noBorder noExl" id="terms_cond">
                @foreach ($Other['terms'] as $term)
                    {{ $term }} <br>
                @endforeach
            </td>
        </tr>
    </tbody>
    <tbody id="assumptions">
        <tr>
            <th class="noBorder" colspan=80 style="color : rgba(0, 182, 255,1)">
                ESDS's Assumptions and Considerations
            </th>
        </tr>
        <tr class="noExl">
            <td colspan=80 contentEditable="true" class="myTextArea noBorder assump" id="asump">
                @foreach ($Other['assumptions'] as $assumption)
                    {{ $assumption }} <br>
                @endforeach
            </td>
        </tr>
    </tbody>
    <tbody id="exculsions">
        <tr>
            <th class="noBorder" colspan=80 style="color : rgba(0, 182, 255,1)">ESDS's Exclusions</th>
        </tr>
        <tr class="noExl">
            <td colspan="80" contentEditable="true" id="text_excl" class="myTextArea noBorder assump noExl">
                @foreach ($Other['exculsions'] as $exculsion)
                    {{ $exculsion }} <br>
                @endforeach
            </td>
        </tr>
    </tbody>
</table>

<script>
    let TC = {};

    function inputLines(textArea, lineID) {
        let text = $(textArea).html()
        let lines = text.split('<br>');

        lines.forEach(function(line) {
            line = line.replace("\n                ", '');
            $(`#${lineID}`).append(
                `<tr hidden class = 'line' style = 'width:100%;'><td class = 'noBorder' style = 'width:100%; white-space: nowrap;'>${line}</td></tr>`
            );
        });
        $(textArea).on({
            "keypress": function(event) {
                if (event.keyCode == 13) {
                    $(this).append("<br>")
                }
            },
            "blur": function() {
                // console.log('h')
                text = $(textArea).html();
                lines = text.split('<br>');

                $(`#${lineID} .line`).remove();
                lines.forEach(function(line) {
                    $(`#${lineID}`).append(
                        "<tr hidden class = 'line' style = 'width:100%;'><td class = 'noBorder' style = 'width:100%; white-space: nowrap;'> " +
                        line + " </td></tr>")
                    if (line != "" && !line.match(/            /g)) {
                        TC[lineID].push(line)
                    }
                })
            }
        })
    }

    inputLines("#terms_cond", 'terms');
    inputLines("#asump", 'assumptions');
    inputLines("#text_excl", 'exculsions');
    $(".tbl_tc tbody").each(function() {
        let tbd = $(this).prop("id");
        if (tbd !== "") {
            TC[tbd] = [];
            $(this).find(".line>td").each(function() {
                let line = $(this).html();
                // console.log(line)
                if (line.trim() != "") {
                    TC[tbd].push(line.trim());
                }
            })
        }
    })
</script>
