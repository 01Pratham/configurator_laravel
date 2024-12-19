@foreach ($Data as $Arr)
    <div class="container-fluid card-box my-2" id="container_{{ $loop->index }}">
        <div class="header row  p-0 m-0" data-target="tableContainer-{{ $loop->index }}">
            <div class="px-2">
                <h2 class="project_name" data-parent="container_{{ $loop->index }}">{{ $Arr['project_name'] }}</h2>
                <div style="color: #777; font-size: 12px;">
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
                'Dat    a' => $Arr['child_body'],
                'searchable' => [
                    'key' => '',
                    'class' => '',
                ],
                'exceptional_keys' => [],
            ])
        </div>
    </div>
@endforeach


<style>
    .card-box {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 16px;
        box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
    }


    /* Header Section */
    .header {
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background-color 0.3s ease-in-out;
    }

    .header h2 {
        margin: 0;
        font-size: 18px;
        color: #333;
        transition: color 0.3s ease-in-out;
    }

    .arrow-icon i {
        transition: transform 0.4s ease-in-out;
        /* Smooth rotation */
    }

    .arrow-icon i.rotate {
        transform: rotate(180deg);
    }

    /* Table Animation */
    .table-container {
        display: none;
        opacity: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.5s ease-in-out, opacity 0.5s ease-in-out;
    }

    .table-container.show {
        display: block;
        max-height: 1000px;
        opacity: 1;
    }
</style>


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
