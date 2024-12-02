<form action="#" method="post" id="submit-form">
    <div class="container">
        @if ($action == 'Render')
            <div class="row">
                @foreach ($structure as $field)
                    @if (!preg_match('/id|created_at|updated_at/', $field->Field))
                        @php
                            $fieldType = $field->Type;
                            $fieldName = htmlspecialchars($field->Field, ENT_QUOTES, 'UTF-8');
                        @endphp

                        <div class="form-group m-2">
                            <input type="hidden" name="act" value="Update">
                            <label for="{{ $fieldName }}">{{ ucfirst(preg_replace('/_/', ' ', $fieldName)) }}</label>
                            @if (preg_match('/^varchar/', $fieldType))
                                <input type="text" id="{{ $fieldName }}" name="{{ $fieldName }}"
                                    value="{{ $data[$fieldName] }}" class="form-control">
                            @elseif (preg_match('/^text/', $fieldType))
                                <textarea name="{{ $fieldName }}" id="{{ $fieldName }}" class="form-control col-md-12">{{ $data[$fieldName] }}</textarea>
                            @elseif (preg_match('/^tinyint/', $fieldType))
                                <select name="{{ $fieldName }}" id="{{ $fieldName }}" class="form-control">
                                    <option value="1" {{ intval($data[$fieldName]) == 1 ? 'selected' : '' }}>
                                        True</option>
                                    <option value="0" {{ intval($data[$fieldName]) == 0 ? 'selected' : '' }}>
                                        False</option>
                                </select>
                            @elseif (preg_match('/^int|bigint/', $fieldType))
                                <input type="number" id="{{ $fieldName }}" name="{{ $fieldName }}"
                                    value="{{ $data[$fieldName] }}" class="form-control">
                            @elseif (preg_match('/^enum/', $fieldType))
                                @php
                                    $s = preg_replace("/\(|\)|'|enum/", '', $fieldType);
                                    $a = explode(',', $s);
                                @endphp
                                <select name="{{ $fieldName }}" id="{{ $fieldName }}" class="form-control">
                                    @foreach ($a as $opt)
                                        <option value="{{ $opt }}"
                                            {{ $opt == $data[$fieldName] ? 'selected' : '' }}>
                                            {{ $opt }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary store" id="Update" onclick="st()">Save changes</button>
        @elseif ($action == 'Delete')
            <input type="hidden" name="act" value="Delete">
            <button type="button" class="btn btn-danger store" id="Delete" onclick="st()">Delete</button>
        @endif
    </div>
</form>

<script>
    function st() {
        event.preventDefault();

        let form = document.getElementById("submit-form");
        let formData = new FormData(form);
        let action = $(".store").prop("id");

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: `{{ $url }}${action}`, // Use the dynamic URL and action (Update/Delete)
            method: "POST",
            data: formData,
            processData: false, // Prevent jQuery from processing the data
            contentType: false, // Prevent jQuery from setting content type
            success: function(res) {
                // console.log("Success:", res);
                alert("Action successful");
                window.location.reload();
            },
            error: function(err) {
                console.error("Error:", err);
                alert("Action failed");
            }
        });
    }
</script>
