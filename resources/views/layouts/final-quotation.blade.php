@extends('layouts.main-layout')

@section('main')
    {{-- @PRE($Array)    --}}

    @include('components.content-header', [
        'array' => [
            'Estimate' => route('Estimate'),
            'Final Quotation' => route('FinalQuotation'),
        ],
    ])
    <div class="content Main except ">
        <div class="container-fluid except full" style="zoom:65%">
            <link rel="stylesheet" href="/assets/dist/css/FinalQuotation.css">
            <div class="except" id="tbl_div">
                @php
                    $DISC = null;
                @endphp
                @foreach ($Array as $KEY => $VAL)
                    @include('components.FinalTable.table-component', [
                        'KEY' => $KEY,
                        'VAL' => $VAL,
                        'Total' => $Total,
                    ])
                    @php
                        $sheets[$KEY] = $VAL['estmtname'];

                        if ($_discount_status == 'NA' || $_discount_status == 'Approved') {
                            $DISC = true;
                        } elseif (
                            $_discount_status == 'Rejected' ||
                            $_discount_status == 'Remaining' ||
                            $_discount_status == 'Unchanged'
                        ) {
                            $DISC = false;
                        }
                    @endphp
                @endforeach

                @include('components.FinalTable.summary-table-component', [
                    'Total' => $Total,
                    'Other' => $Other,
                ])
            </div>
            <div class="container except d-flex justify-content-center mt-3 py-3">
                <button class="btn btn-outline-danger btn-lg mx-1 export" id="pdf">
                    <i class="fa fa-file-pdf pr-2"></i>
                    {{ __('Export PDF') }}
                </button>
                <button class="btn btn-outline-success btn-lg mx-1 export" id="export">
                    <i class="fa fa-file-excel pr-2"></i>
                    {{ __('Export') }}
                </button>
                @if (in_array(1, session()->get('user')['permissions']))
                    <button class="btn btn-outline-success btn-lg mx-1 export" id="exportShareable">
                        <i class="fa fa-file-excel pr-2"></i>
                        Export as Shareable
                    </button>
                @endif

                @if ($DISC || is_null($DISC))
                    <button class="btn btn-outline-primary btn-lg mx-1" id="push" onclick="Push()">
                        <i class="fab fa-telegram-plane pr-2" aria-hidden="true"></i>
                        Push
                    </button>
                @else
                    @if (in_array(12, session()->get('user')['permissions']) ||
                            session()->get('user')['applicable_discounting_percentage'] > 0)
                        <button class="btn btn-outline-success btn-lg mx-1" id="push"
                            onclick="updateStatus('Approved',{{ session()->get('user')['crm_user_id'] }})"><i
                                class="fa fa-check pr-2" aria-hidden="true"></i>Approve</button>
                        <button class="btn btn-outline-danger btn-lg mx-1" id="push" data-toggle="modal"
                            data-target="#rejectModel"><i class="fa fa-times pr-2" aria-hidden="true"></i>Reject</button>
                        <div class="except modal fade" id="rejectModel" tabindex="-1" role="dialog"
                            aria-labelledby="rejectModelTitle" aria-hidden="true">
                            <div class="except modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="except modal-content" style="zoom: 140%;">
                                    <div class="except modal-header">
                                        <h5 class="except modal-title" id="rejectModelTitle">Rejection remark</h5>
                                        <button type="button" class="except close" data-dismiss="modal" aria-label="Close"
                                            onclick="$('#rejectModel').removeClass('show')">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="except modal-body">
                                        <form id="reject_form">
                                            <textarea name="reject_remark" id="reject_remark" class="form-control" style="height: 20vh;"
                                                placeholder="Enter your remark"></textarea>
                                            <div class='text-danger Error-Reject'></div>
                                        </form>
                                        <div class="except modal-footer">
                                            <button type="button" class="except btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                            <button type="button" class="except btn btn-primary"
                                                onclick="event.preventDefault();updateStatus('Rejected' , {{ session()->get('user')['crm_user_id'] }})">
                                                Save changes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <button class="btn btn-outline-primary btn-lg mx-1" id="push"
                            onclick="updateStatus('Remaining')">
                            <i class="fab fa-telegram-plane pr-2" aria-hidden="true"></i>
                            {{ __('Send for Approval') }}
                        </button>
                    @endif
                @endif
                @if ($edit_id)
                    <button class="btn btn-outline-danger btn-lg mx-1 save" id="Update"
                        data-edit_id="{{ $edit_id }}">
                        <i class="fa fa-sync pr-2"></i>
                        Update
                    </button>

                    <form action="{{ route('Discounting') }}" method="post">
                        @csrf
                        <input type="hidden" name="edit_id" value="{{ $edit_id }}">
                        <button class="btn btn-outline-info btn-lg mx-1" id="Discount" formtarget="_blank">
                            <i class="fa fa-calculator pr-2" aria-hidden="true"></i>
                            Discounting
                        </button>
                    </form>
                @else
                    <button class="btn btn-outline-danger btn-lg mx-1 save" id="Insert" data-edit_id="">
                        <i class="fas fa-save pr-2"></i>
                        Save
                    </button>
                @endif
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/exceljs/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

    <script>
        function updateStatus(status, approved_by = '') {
            const Data = {
                id: "{{ $edit_id }}",
                status: status,
                approved_by: approved_by
            }

            if (status === "Rejected") {
                if (!$("#reject_remark").val()) {
                    $("#reject_remark").addClass("border-danger")
                    $(".Error-Reject").html("Please Enter Remark")
                    return;
                }

                $("#modal, #modal-backdrop").removeClass("show").hide()
                Data.remarks = escapeHtml($("#reject_remark").val());
            }
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                type: 'POST',
                url: "",
                dataType: "TEXT",
                data: Data,
                success: function(response) {
                    alert(response);
                    window.location.reload()
                }
            })
        }

        // console.log("{{ env('CRM_API') }}");
        function insertBrTags(string) {
            if (string.length > 160) {
                let chunks = [];
                for (let i = 0; i < string.length; i += 160) {
                    chunks.push(string.substr(i, 160));
                }
                return chunks.join("<br>");
            }
            return string;
        }
        $("#pdf").click(function() {
            $("#loader").removeClass("d-none");
            var content = $("#tbl_div").clone();
            content.find('.noExl').remove();
            content.find(".final-tbl").attr("style", "zoom:75%; width:100%")
            content.find(".line td").each(function() {
                let line = $(this).html();
                if (line.length > 160) {
                    let new_line = insertBrTags(line)
                    $(this).html(new_line);
                }
            })
            let TCHtml = $(`<table class="tbl_tc tbl_summary except" style="width: 100%;"></table>`);
            let summaryHtml = $(`<table class="tbl_summary" style="width: 100%;"></table>`);
            content.find('.tbl_summary>tbody').each(function() {
                if ($(this).prop("id") !== '') {
                    TCHtml.append(`<tbody id = '${$(this).prop("id")}'>${$(this).html()}</tbody>`);
                } else {
                    summaryHtml.append(`<tbody>${$(this).html()}</tbody>`);
                }
            }).remove();

            content.append(summaryHtml);
            content.append(TCHtml);

            var firstTable = content.find('table:first');
            content.find('.tbl_summary').insertBefore(firstTable);
            content.find('table').each(function(index) {
                if (index > 2) {
                    $('<div style="page-break-before: always;"></div>').insertBefore($(this))
                }
            });
            content.find("select").each(function() {
                $(this).parent().html($(this).val());
            });
            content.find(".extraLine").remove();
            let htmlContent = content.prop('outerHTML');

            console.log(htmlContent);
            // $.ajax({
            //     headers: {
            //         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            //     },
            //     url: '/Ajax/GeneratePDF',
            //     method: 'POST',
            //     data: {
            //         htmlContent: htmlContent.replace(/₹/g,
            //             "<span style='font-family: DejaVu Sans; sans-serif; background: transparent;'>&#8377;</span>"
            //         )
            //     },
            //     success: function(response) {
            //         $("#loader").addClass("d-none");
            //         window.open(response, '_blank')
            //         setTimeout(function() {
            //             $.ajax({
            //                 headers: {
            //                     "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
            //                         "content"),
            //                 },
            //                 url: '/Ajax/DeletePDF',
            //                 method: 'POST',
            //                 data: {
            //                     deleteFileUrl: response
            //                 },
            //                 success: function(res) {
            //                     return;
            //                 }
            //             });
            //         }, 10000);
            //     },
            //     error: function(xhr, status, error) {
            //         console.error('Error:', error);
            //     }
            // });
        });
        @if (in_array(12, session()->get('user')['permissions']))
            @php
                echo 'let sheetNames = {';
                foreach ($sheets as $key => $val) {
                    // $val = escapeHtml($val);
                    echo "sheet{$key} : '{$val}' ,";
                }
                $key += 1;
                echo "sheet{$key} : 'Summary Sheet' };";
            @endphp

            $(document).ready(function() {
                $("#export").click(function() {
                    var tables = document.querySelectorAll('table');
                    convertTablesToExcel(Array.from(tables), "unShareable", sheetNames,
                        "{{ $Other['PROJECT'] }}");
                });
                $("#exportShareable").click(function() {
                    var tables = document.querySelectorAll('table');
                    convertTablesToExcel(Array.from(tables), "Shareable", sheetNames,
                        "{{ $Other['PROJECT'] }}");
                });
            });
        @endif


        function saveToDb(act = "", ty = "btn") {
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                type: "POST",
                url: `/Save/Estimate/${act}`,
                data: {
                    emp_id: {{ session()->get('user')['crm_user_id'] }},
                    data: "{{ base64_encode(json_encode($Other['sku_data'])) }}",
                    period: {{ $Total[1]['TENURE'] }},
                    tc: Base64Encode(JSON.stringify(TC))
                },
                dataType: "TEXT",
                success: function(response) {
                    const jsonObj = JSON.parse(response)
                    if (ty == "btn" && jsonObj.status == 200) {
                        // console.log(jsonObj)
                        alert(jsonObj.message)
                        location.reload()
                    }
                }
            });
        }
        $('.save').click(function() {
            if ($(this).prop("id") == "save") {
                $("#loader").removeAttr("hidden");
            }
            saveToDb($(this).data("edit_id"));
        })

        @if ($edit_id)
            saveToDb({{ $edit_id }}, "auto");
        @endif


        function Push() {
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                type: 'POST',
                url: "/Ajax/PushToCRM",
                dataType: "TEXT",
                data: {
                    action: 'push',
                    data: "{{ base64_encode($JSON) }}"
                },
                success: function(response) {
                    console.log(response);
                }
            });
        }


        $(".Unit select").on("change", function() {
            let val = $(this).val(); // Get the selected value
            let parentId = "#" + $(this).closest("tr").prop("id"); // Get the closest parent with an ID

            // Update ".MRC, .Otc, .DiscountedMrc" elements
            $(parentId)
                .find(".MRC, .Otc, .DiscountedMrc")
                .each(function() {
                    let value = val === "not_billable" ? 0 : $(this).data(Object.keys($(this).data())[0] || 0);
                    $(this).html(INR(value));
                });

            // Update ".percent" elements
            $(parentId)
                .find(".percent")
                .each(function() {
                    let percent = parseFloat($(this).data("percent"));
                    $(this).html(`${percent} %`); // Single template for all cases
                });
        });
    </script>

@endsection
