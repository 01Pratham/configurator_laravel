<table class="table mb-0">
    <thead class="small text-uppercase bg-body text-muted">
        <tr>
            <th>#</th>
            @foreach ($Header as $Option)
                <th>{{ $Option }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php
            $count = 1;
        @endphp
        @foreach ($Data as $Arr)
            <tr class="align-middle">
                <td>{{ $count }}</td>
                @foreach ($Arr as $key => $val)
                    @if ($key == 'child_body')
                    @elseif ($key != 'id' && !is_array($val) && !in_array($key, $exceptional_keys ?? []))
                        <td @if ($searchable['key'] == $key) class="{{ $searchable['class'] }}" @endif>
                            @if ($key == 'is_active')
                                <input type="checkbox" class="prodStatus ToogleSwitch" id="status_{{ $Arr['id'] }}"
                                    {{ $val == false ? '' : 'Checked ' }}>
                            @else
                                <?= $val ?>
                            @endif
                        </td>
                    @endif
                @endforeach
                @if (isset($Arr['action']))
                    <td class="text-end">
                        <div class="except drodown">
                            <a data-bs-toggle="dropdown" href="#" class="btn p-1" aria-expanded="false">
                                <span class="fa fa-bars" aria-hidden="true"></span>
                            </a>
                            <div class="except dropdown-menu dropdown-menu-end text-light"
                                style="min-width: 8rem; z-index:1 ">
                                @foreach ($Arr['action'] as $action)
                                    <a href="{{ $action['path'] }}" class="dropdown-item"><i>{{ $action['name'] }}</i><i
                                            class="{{ $action['icon'] }} float-right pt-1"></i></a>
                                @endforeach
                            </div>
                        </div>
                    </td>
                @endif
            </tr>

            @php
                $count += 1;
            @endphp
        @endforeach
    </tbody>
</table>
