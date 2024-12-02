<div class="except content" style="min-height: 277px;">
    <div class="except container-fluid">
        <div class="except">
            <div class="except row">
                <div class="except col-12 mb-3 mb-lg-5">
                    <div class="except table-card">
                        <div class="except table-responsive">
                            <table class="table mb-0">
                                <thead class="small text-uppercase bg-body text-muted">
                                    <tr>
                                        <th>#</th>
                                        @foreach ($Header as $Option)
                                            @if ($Option != 'id')
                                                <th>{{ preg_replace('/_/', ' ', $Option) }}</th>
                                            @endif
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($Data as $model)
                                        <tr class="align-middle">
                                            <td>{{ $model->id }}</td>
                                            @foreach ($model->toArray() as $key => $val)
                                                @if (!preg_match('/action|^id/', $key) && !is_array($val) && !in_array($key, $exceptional_keys ?? []))
                                                    <td
                                                        @if ($searchable['key'] == $key) class="{{ $searchable['class'] }}" @endif>
                                                        @if ($key == 'is_active')
                                                            <input type="checkbox" class="prodStatus ToogleSwitch"
                                                                id="status_{{ $model->id }}"
                                                                {{ $val == false ? '' : 'Checked ' }}>
                                                        @else
                                                            {{ $val }}
                                                        @endif
                                                    </td>
                                                @endif
                                            @endforeach

                                            @if ($model->action)
                                                <td class="text-end">
                                                    <div class="except drodown">
                                                        <a data-bs-toggle="dropdown" href="#" class="btn p-1"
                                                            aria-expanded="false">
                                                            <span class="fa fa-bars" aria-hidden="true"></span>
                                                        </a>
                                                        <div class="except dropdown-menu dropdown-menu-end text-light"
                                                            style="min-width: 8rem; z-index:1 ">
                                                            @foreach ($model->action as $action)
                                                                <a href="#" data-url="{{ $action['path'] }}"
                                                                    class="dropdown-item admin-action-item"
                                                                    data-toggle="modal"
                                                                    data-target="#new-modal"><i>{{ $action['name'] }}</i><i
                                                                        class="{{ $action['icon'] }} float-right pt-1"></i></a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.components.modal')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(".admin-action-item").click(function(event) {
        // event.preventDefault();
        let url = $(this).data("url");
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: url,
            method: "get",
            dataType: "TEXT",
            success: function(res) {
                $(".modal-body").html(res)
            }
        });
    });
</script>


{{--  [
                    "name" => "Create",
                    "path" => "Admin/Table/{$this->table_name}/{$item->id}/",
                    "icon" => "fa fa-share",
                ], --}}
