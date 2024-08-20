<div class="product-container-fluid container-fluid">
    <div class="input-group p-2">
        <input type="text" id="search_product" class="form-control" placeholder="Search Product...">
    </div>
    <div class="row">
        <div class="category-container my-2 col-md-3" role="presentation">
            <button class=" mt-1" hidden id="SearchedProducts" role="tab" type="submit" onclick="return">
                Searched Products
            </button>
            @foreach ($Categories as $category)
                <button class="product-tab-featured mt-1 @if ($loop->first) active-category @endif"
                    id="{{ $category['primary_category'] }}" role="tab" type="submit"
                    onclick="$('.product-tab-featured').removeClass('active-category'); $(this).addClass('active-category'); showProdsOnCategory($(this))">
                    {{ ucwords(preg_replace('/_/', ' ', $category['primary_category'])) }}
                </button>
            @endforeach
        </div>
        <div class="tabbed-product-container col-lg-9 mt-1" id="product-tab-featured-content" role="tabpanel">
            <div class="row my-2">
                @php
                    $first = true;
                @endphp
                @foreach ($Products as $prod)
                    @php
                        if ($prod['primary_category'] == 'virtual_machine' && !$first) {
                            continue;
                        }
                        if ($prod['primary_category'] == 'virtual_machine') {
                            $prod['default_name'] = 'Virtual Machine';
                        }
                    @endphp
                    <div class="product p-3" data-category="{{ $prod['primary_category'] }}" hidden="hidden"
                        data-prod=@if ($first && $prod['default_int'] == 'virtual_machine') "vm" @else "{{ $prod['default_int'] }}" @endif
                        data-name='{{ $prod['default_name'] }}'>
                        <div class="name except">
                            <i class="except fa fa-box"></i>
                            <strong class="mx-2">
                                {{ $prod['default_name'] }}
                            </strong>
                        </div>
                        <div class="dropdown bg-transparent">
                            <button class="service-info-picker-button dropdown-toggle" type="button"
                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Add to estimate
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"></div>
                        </div>
                    </div>
                    @php
                        if ($prod['primary_category'] == 'virtual_machine') {
                            $first = false;
                        }
                    @endphp
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        addLineItemsToDropdownMenu({{ $prod_list }})
        showProdsOnCategory($(".active-category"))

        let List;
        $("#search_product").on({
            "input": function() {
                const search = $(this).val().toLowerCase();
                if (search !== "") {
                    $(".product-tab-featured").removeClass("active-category")
                    $("#SearchedProducts").removeAttr("hidden").addClass("active-category")
                }
                List = {
                    [search]: []
                }
                $(".product").attr("hidden", "true")
                $(".product").each(function() {
                    let name = $(this).data("name").toLowerCase()
                    if (name.match(search)) {
                        $(this).removeAttr("hidden")
                    }
                })
            },
            blur: function() {
                if ($(this).val() === "") {
                    $("#SearchedProducts").removeClass("active-category").attr("hidden", "true");
                    let first = true
                    $(".product-tab-featured").each(function() {
                        if (first) {
                            $(this).addClass("active-category");
                            showProdsOnCategory($(this));
                            first = false;
                        }
                    });
                }
            }
        })
    })
</script>
