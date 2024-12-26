<link rel="stylesheet" href="{{ asset('assets/dist/css/collapsible-table.css') }}">
@foreach ($Data as $Arr)
    <div class="container-fluid card-box my-2" id="container_{{ $loop->index }}">
        <div class="header row  p-0 m-0" data-target="tableContainer-{{ $loop->index }}">
            <div class="px-2">
                <div class="project_name h5" data-parent="container_{{ $loop->index }}">{{ $Arr['project_name'] }}</div>
                <div style="color: #e5b7b7; font-size: 12px;">
                    {{ $Arr['pot_id'] }}
                </div>
            </div>
            <div class="row float-right px-2">
                <div class="px-2" style="font-size: 12px; ">
                    <div style="color: #777;"> {{ $Arr['created_at'] }} </div>
                    <div style="color: #777;"> {{ $Arr['updated_at'] }} </div>
                </div>
                <div class="arrow-icon px-2">
                    <i class="fa fa-chevron-down" id="arrow-{{ $loop->index }}"></i>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-container mt-3" id="tableContainer-{{ $loop->index }}" style="overflow-x: auto;">
            @include('components.data-table-format', [
                'Header' => $Header['child_body'],
                'Data' => $Arr['child_body'],
                'searchable' => [
                    'key' => '',
                    'class' => '',
                ],
                'exceptional_keys' => [],
            ])
        </div>
    </div>
@endforeach



<script>
    $(document).ready(function() {
        $(".header").on("click", function() {
            // Get the target table container ID
            const $header = $(this)
            const target = $header.data("target");

            // Toggle table visibility with animation
            const $tableContainer = $("#" + target);
            const $arrowIcon = $header.find("i");

            if ($tableContainer.hasClass("show")) {
                $tableContainer.removeClass("show");
                setTimeout(() => {
                    $tableContainer.css("display", "none");
                }, 500); // Wait for animation
            } else {
                $tableContainer.css("display", "block");
                setTimeout(() => {
                    $tableContainer.addClass("show");
                }, 10); // Small delay for smooth animation
            }

            // Toggle arrow rotation
            $arrowIcon.toggleClass("rotate");
        });
    });
</script>
