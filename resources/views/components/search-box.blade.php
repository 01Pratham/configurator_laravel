<form action="" method="post" onsubmit="event.preventDefault();return (event.keyCode || event.charCode)==13">
    <div class="input-group bg-transparent">
        <input type="text" name="searchBox" id="searchBox" class="form-control" aria-describedby="">
        <button class="input-group-text p-0 form-control col-sm-1 bg-light" id="searchButton"
            onclick="
    const searchVal = $('#searchBox').val().toLowerCase();$('.{{ $searchableClass }}').each(function() {if ($(this).html().toLowerCase().includes(searchVal)) {$(this).closest('td').parent().removeAttr('hidden');} else {$(this).closest('td').parent().attr('hidden', 'true');}})">
            <i class="fa fa-search Center"></i>
        </button>
    </div>
</form>
