<form action="" method="post" onsubmit="event.preventDefault(); return false;">
    <div class="input-group bg-transparent">
        <input type="text" name="searchBox" id="searchBox" class="form-control" placeholder="Search..."
            aria-describedby="searchButton">

        <button type="button" class="input-group-text p-0 form-control col-sm-1 bg-light" id="searchButton">
            <i class="fa fa-search Center"></i>
        </button>
    </div>
</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Search Functionality
        $("#searchButton").on("click", function() {
            const searchVal = $("#searchBox").val().toLowerCase();
            $(".{{ $searchableClass }}").each(function() {
                const row = ($(this).data("parent") != undefined) ?
                    $("#" + $(this).data("parent")) :
                    $(this).closest("tr");
                console.log(row)
                const cellContent = $(this).html().toLowerCase();

                if (cellContent.includes(searchVal)) {
                    row.removeAttr("hidden"); // Show the row if it matches
                } else {
                    row.attr("hidden", "true"); // Hide the row if it doesn't match
                }
            });
        });

        // Trigger Search on Enter Key
        $("#searchBox").on("keypress", function(e) {
            if (e.which === 13) { // Enter key code is 13
                e.preventDefault();
                $("#searchButton").click();
            }
        });
    });
</script>
