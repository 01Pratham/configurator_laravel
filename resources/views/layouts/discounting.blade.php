@extends('layouts.main-layout')

@section('main')
    @include('components.content-header', [
        'array' => [
            'Estimate' => route('Estimate'),
            'Discounting' => route('Discounting'),
        ],
    ])
    {{-- @PRE($Array, true) --}}
    <div class="content Main except ">
        <div class="container-fluid except full" style="zoom:65%">
            <link rel="stylesheet" href="/assets/dist/css/FinalQuotation.css">
            <div class="except" id="tbl_div">
                @foreach ($Array as $KEY => $VAL)
                    @include('components.FinalTable.table-component', [
                        'KEY' => $KEY,
                        'VAL' => $VAL,
                        'Total' => $Total,
                        'Products' => $Products[$KEY],
                    ])
                @endforeach
            </div>
            <div class="container except d-flex justify-content-center mt-3 py-3">
                <button class="btn btn-outline-danger btn-lg mx-1 save" id="Update">
                    <i class="fa fa-sync pr-2"></i>
                    Update
                </button>
            </div>
        </div>
    </div>
    <script>
        const sum = (obj) => Object.values(obj).reduce((a, b) => a + b, 0);

        $(".Unit, .percent").attr("contenteditable", "true");

        let $_Prices = JSON.parse('<?= json_encode($Total['_prices']) ?>');

        function updateTotalHtml(object) {
            let DiscountedInfra = 0,
                DiscountedMng = 0,
                DiscountedMRC = 0,
                DiscountedTotal = 0,
                discountPercentage = 0,
                DiscAmmtInfra = 0,
                DiscAmmtMng = 0,
                j = object.j;
            // console.log(object.Obj)
            Object.keys(object.Obj).forEach(function(key) {
                let $Parent;
                Object.keys(object.Obj[key]).forEach(function(prodKey) {
                    if (key.match(/vm/g)) {
                        $Parent = $(`#${key}_${prodKey}_${j}`);
                    } else {
                        $Parent = $(`#${prodKey}_${j}`);
                    }
                    MRC = $Parent.find(`.mrc_${j}`).data("mrc");
                    discountPercentage = object.Obj[key][prodKey];
                    DiscountedMRC = MRC - (MRC * (discountPercentage / 100));

                    $Parent
                        .find(".percent")
                        .html(`${discountPercentage.toFixed(2)} %`)
                        .data("percentage", discountPercentage);
                    $Parent
                        .find(".discountAmmt")
                        .html(INR(MRC - DiscountedMRC));
                    $Parent
                        .find(".DiscountedMrc")
                        .html(INR(DiscountedMRC))
                        .data("discountedMrc", DiscountedMRC);

                    if ($Parent.find(`.DiscountedMrc`).hasClass(`Infrastructure_${j}`)) {
                        DiscountedInfra += DiscountedMRC;
                        DiscAmmtInfra += (MRC - DiscountedMRC);
                        console.log(DiscountedInfra,
                            DiscAmmtInfra)
                    } else if ($Parent.find(`.DiscountedMrc`).hasClass(`Managed_${j}`)) {
                        DiscountedMng += DiscountedMRC;
                        DiscAmmtMng += (MRC - DiscountedMRC);
                    }
                });
            });
            const monthlyTotal = parseFloat($(`#total_monthly_${j}`).data("value"));
            DiscountedMRC = parseFloat(DiscountedInfra) + parseFloat(DiscountedMng);
            DiscountedTotal = parseFloat(DiscountedMRC) * parseFloat($(`#MonthlyDiscounted_${j}`).data("period"));
            // console.log(monthlyTotal, DiscountedMRC, DiscountedTotal)
            $(`#DiscInfra_${j}`).html(INR(DiscountedInfra));
            $(`#discAmmtInfra_${j}`).html(INR(DiscAmmtInfra));
            $(`#discAmmtMng_${j}`).html(INR(DiscAmmtMng));
            $(`#DiscMng_${j}`).html(INR(DiscountedMng));
            $(`#DiscTotal_${j}`).html(INR(DiscountedMRC));
            $(`#totalDiscAmmt_${j}`).html(INR(monthlyTotal - DiscountedMRC));
            $(`#totalDiscAmmt_${j}`).data("value", monthlyTotal - DiscountedMRC);
            $(`#MonthlyDiscAmmt_${j}`).html(INR(parseFloat($(`#MonthlyTotal_${j}`).data("value")) - DiscountedTotal));
            $(`#MonthlyDiscounted_${j}`).html(INR(DiscountedTotal)).data("value", DiscountedTotal);
            DiscountedData[object.j]["percentage"] = object.DATA.discountVal;
            DiscountedData[object.j]["Data"] = object.Obj;
        }

        <?php
        echo 'let DiscountedData = {';
        foreach ($Array as $KEY => $VAL) {
            if (is_array($VAL)) {
                echo $KEY . " : {'percentage' : '','Data' : { ";
                foreach ($VAL as $K => $V) {
                    if (is_array($V)) {
                        if (preg_match('/vm/', $K)) {
                            foreach ($V as $k => $V) {
                                echo "$k : {CPU : '',RAM : '',Disk : ''},\n";
                            }
                            continue;
                        }
                        echo "$K : {},\n";
                    }
                }
                echo " },\n";
                echo "}, \n";
            }
        }
        echo "}; \n";
        ?>

        function DiscountingAjax(DATA, j) {
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                type: "post",
                url: "/Estimate/AutoDiscount",
                dataType: "TEXT",
                data: DATA,
                success: function(res) {
                    let Obj = JSON.parse(res)
                    if (Obj.error) {
                        alert(Obj.error)
                    } else {
                        updateTotalHtml({
                            Obj: Obj,
                            j: j,
                            DATA: DATA
                        });
                        $(".percent").attr("Contenteditable", "true");
                    }
                }
            })
        }

        function totalInfra(j, type = "total") {
            let Infrastructure = [];
            let Managed = [];
            $(".Managed_" + j + " , .Infrastructure_" + j + "").each(function() {
                let valHTML = $(this).html();
                let val = valHTML.replace(/₹|,|\n| /g, '');
                if (type == "total") {
                    if ($(this).hasClass("Infrastructure_" + j) && val !== '' && $(this).hasClass("MRC")) {
                        Infrastructure.push(parseFloat(val));
                    }
                    if ($(this).hasClass("Managed_" + j) && val !== '' && $(this).hasClass("MRC")) {
                        Managed.push(parseFloat(val));
                    }
                } else if (type == "discountedTotal") {
                    if ($(this).hasClass("Infrastructure_" + j) && val !== '' && $(this).hasClass(
                            "DiscountedMrc")) {
                        Infrastructure.push(parseFloat(val));
                    }
                    if ($(this).hasClass("Managed_" + j) && val !== '' && $(this).hasClass("DiscountedMrc")) {
                        Managed.push(parseFloat(val));
                    }
                }
            })
            infraTotal = Infrastructure.reduce((accumulator, currentValue) => accumulator + currentValue, 0);
            mngTotal = Managed.reduce((accumulator, currentValue) => accumulator + currentValue, 0);

            if (type == "discountedTotal") {
                $("#DiscInfra_" + j).html(INR(infraTotal));
                $("#DiscMng_" + j).html(INR(mngTotal));

                $("#DiscTotal_" + j).html(INR(
                    parseFloat(infraTotal) +
                    parseFloat(mngTotal)
                )).data("value", parseFloat(infraTotal) + parseFloat(mngTotal))
                let period = parseFloat($("#MonthlyTotal_" + j).data("period"))
                $("#MonthlyDiscounted_" + j).html(INR(
                    (parseFloat(infraTotal) +
                        parseFloat(mngTotal)) * period
                )).data("value", ((parseFloat(infraTotal) + parseFloat(mngTotal)) * period));
            } else if (type == "total") {
                $("#infraTotal_" + j).html(INR(infraTotal));
                $("#MngTotal_" + j).html(INR(mngTotal));

                $("#total_monthly_" + j).html(INR(
                    parseFloat(infraTotal) +
                    parseFloat(mngTotal)
                )).data("value", parseFloat(infraTotal) + parseFloat(mngTotal))
                let period = parseFloat($("#MonthlyTotal_" + j).data("period"))
                $("#MonthlyTotal_" + j).html(INR(
                    (parseFloat(infraTotal) +
                        parseFloat(mngTotal)) * period
                ))
            }
            let percentage = 100 - (100 * ($("#DiscTotal_" + j).data("value") / $("#total_monthly_" + j).data("value")))
            if (isNaN(percentage)) {
                percentage = 0;
            }
            $("#DiscountPercetage_" + j).val(percentage.toFixed(2)).data("percentage", percentage);
        }


        let FirstFocused = true;
        $(".percent").on({
            "click focus": function() {
                if (FirstFocused && $(this).prop("contenteditable") == "true") {
                    $(this).text($(this).data("percentage"))
                    FirstFocused = false;
                }
            },
            "blur": function() {
                let percentage = parseFloat($(this).data("percentage"));
                let newPerc = parseFloat($(this).html().replace(/ |%/g, ""));
                FirstFocused = true;
                let $Mrc = $(this).parent().find(".MRC");

                if (isNaN(newPerc) || newPerc > 99) {
                    alert("Please Enter a valid Percentage");
                    $(this).html(percentage.toFixed(2) + " %");
                } else {
                    percentage = newPerc / 100;
                    let Mrc;
                    if ($Mrc.hasClass("hasOTC")) {
                        Mrc = parseFloat($(this).parent().find(".Otc").html().replace(/,|₹| /g, ""));
                    } else {
                        Mrc = parseFloat($Mrc.html().replace(/,|₹| /g, ""));
                    }
                    let discountedMrc = Mrc - (Mrc * percentage);

                    if (discountedMrc <= 0 && percentage > 0) {
                        alert("Please Enter a Valid Percentage");
                    } else {
                        if ($Mrc.hasClass("hasOTC")) {
                            $(this).parent().find(".DiscountedOtc").html(INR(discountedMrc));
                        } else {
                            $(this).parent().find(".DiscountedMrc").html(INR(discountedMrc));
                            $(this).parent().find(".discountAmmt").html(INR(Mrc - discountedMrc));
                        }
                    }

                    let j = $(this).data("key");
                    let discountID = $(this).data("discid");
                    let group = $(this).data("group");

                    if (group.match(/vm/g)) {
                        let product = $(this).parent().data("product");
                        DiscountedData[j]["Data"][group][product] = percentage * 100;
                    } else {
                        try {
                            DiscountedData[j]["Data"][group][discountID] = percentage * 100;
                        } catch (Error) {
                            DiscountedData[j]["Data"][group] = {
                                [discountID]: percentage * 100
                            };
                        }
                    }
                    totalInfra(j, "discountedTotal");
                    $(this).html(newPerc.toFixed(2) + " %").data("percentage", newPerc);
                }
            },
        })


        $(".DiscountedMrc").on({
            "click focus": function() {
                if (FirstFocused && $(this).prop("contenteditable") == "true") {
                    $(this).html($(this).data("discountedMrc"))
                    FirstFocused = false;
                }
            },
            "blur": function() {
                let $this = $(this)
                let $Parent = $(this).parent()
                let val = parseFloat($this.html().replace(/₹|,| /g, ''));
                let Monthly = $Parent.find(".MRC").data("mrc");
                let percentage = 0;
                let j = $this.data("key")

                try {
                    percentage = parseFloat(100 - (100 * (val / Monthly)));
                    if (isNaN(percentage)) {
                        percentage = 0;
                    } else if (percentage > 99 || Monthly <= 0) {
                        $this.html(INR($this.data("discountedMrc")));
                        return;
                    }
                    totalInfra(j, "discountedTotal")
                    let discountID = $this.data("discid")
                    let group = $this.data("group")
                    if (group.match(/vm/g)) {
                        let product = $Parent.data("product");
                        if (product.match(/vcore/g)) {
                            DiscountedData[j]["Data"][group]["vcore"] = percentage
                        }
                        if (product.match(/ram/g)) {
                            DiscountedData[j]["Data"][group]["ram"] = percentage
                        }
                        if (product.match(/storage/g)) {
                            DiscountedData[j]["Data"][group]["storage"] = percentage
                        }
                    } else {
                        try {
                            DiscountedData[j]["Data"][group][discountID] = percentage
                        } catch (Error) {
                            DiscountedData[j]["Data"][group] = {
                                [discountID]: percentage
                            };
                        }
                    }
                    $Parent.find(".percent").html(`${percentage.toFixed(2)} % `).data("percentage", percentage);
                    if (val)
                        $this.html(INR(val));
                } catch (e) {
                    // console.log("Error");
                    $this.html(INR($this.data("discountedMrc")));
                }
                totalInfra(j, "discountedTotal")
            },
        }).keypress(function(e) {
            let key = e.keyCode || e.charCode;
            if (key == 13) { // if enter key is pressed
                $(this).blur();
                $(this).html();
            }
        });

        $(".Unit").keypress(function(e) {
            let key = e.keyCode || e.charCode;
            if (key == 13) { // if enter key is pressed
                $(this).blur();
                $(this).html();
            }
        })

        FirstFocused = true;

        $(".Unit").on({
            "click focus": function() {
                if (FirstFocused) {
                    $(this).text($(this).data("newunit"))
                    FirstFocused = false;
                }
            },
            blur: function() {
                let $this = $(this);
                let $MRC = $this.parent().find(".MRC");
                let $Qty = $this.parent().find(".qty");
                let $Parent = $this.parent();
                let $name = $this.parent().find(".final")
                let val = parseFloat($this.html().replace(/,|₹| /g, ""));
                let qty = parseFloat($Qty.html().replace(/[a-zA-Z]| /g, ""));
                let newMrc, unit;
                if ($this.data('unit') < val) {
                    try {
                        if ($Parent.data("group").match(/vm/g)) {
                            unit = parseFloat($name.html().split(":")[1].replace(/[a-zA-Z]/g, ""));
                            // console.log(unit)
                            $_Prices[$Parent.data("key")][$Parent.data("group")][$Parent.data("cat")][$Parent
                                .data(
                                    "product")
                            ] = val;
                            newMrc = (val * unit) * qty;
                        } else {
                            $_Prices[$Parent.data("key")][$Parent.data("group")][$Parent.data("cat")] = val;
                            newMrc = val * qty;
                        }
                    } catch (er) {
                        console.log($_Prices[$Parent.data("key")]);
                    }

                    $this.data("newunit", val).html(INR(val));
                    $MRC.html(INR(newMrc));
                    $Parent.find(".percent").removeAttr("contenteditable");
                } else {
                    $this.html(INR($this.data('unit')));
                    if ($Parent.data("group").match(/vm/g)) {
                        unit = parseFloat($name.html().split(":")[1].replace(/[a-zA-Z]/g, ""));
                        $MRC.html(INR((parseFloat($this.data('unit')) * unit) * qty));
                    } else {
                        $MRC.html(INR(parseFloat($this.data('unit')) * qty));
                    }
                }

            }
        });

        @if (in_array(3, session()->get('user')['permissions']))
            var mrc = $('#vm-mrc').html();
            $(".percent").keypress(function(e) {
                var key = e.keyCode || e.charCode;
                if (key == 13) {
                    $(this).blur();
                    $(this).html();
                }
            }).each(function() {
                let actual = parseFloat($(this).parent().find(".Unit").data("unit"));
                let changed = parseFloat($(this).parent().find(".Unit").data("changed"));
                if (actual == changed) {
                    $(this).attr('contentEditable', 'true')
                }
            })
        @endif

        $('.save').click(function() {
            let TotalDiscountedMrc = 0;
            $(".MonthlyDiscounted").each(function() {
                TotalDiscountedMrc += $(this).data('value')
            });
            const act = $(this).prop("id");
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                type: "POST",
                url: `/Save/Estimate/${act}`,
                data: {
                    action: "Discount",
                    emp_id: {{ session()->get('user')['crm_user_id'] }},
                    id: '{{ $edit_id }}',
                    discountedData: JSON.stringify(DiscountedData),
                    discounted_upfront: isNaN(TotalDiscountedMrc) ? 0 : TotalDiscountedMrc,
                    prices: JSON.stringify($_Prices),
                },
                dataType: "TEXT",
                success: function(response) {
                    alert(response);
                }
            });
        })
    </script>
@endsection
