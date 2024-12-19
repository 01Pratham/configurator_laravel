<div class="except content" style="min-height: 277px;">
    <div class="except container-fluid">
        <div class="except">
            <div class="except row">
                <div class="except col-12 mb-3 mb-lg-5">
                    <div class="except table-card">
                        <div class="except table-responsive">
                            @if (isset($other['colapssible']))
                                @if ($other['colapssible'])
                                    @include('components.data-table-collapsible-format', [
                                        'Header' => $table_head,
                                        'Data' => $table_body,
                                        'searchable' => $searchable,
                                        'exceptional_keys' => $exceptional_keys ?? null,
                                    ])
                                @endif
                            @else
                                @include('components.data-table-format', [
                                    'Header' => $table_head,
                                    'Data' => $table_body,
                                    'searchable' => $searchable,
                                    'exceptional_keys' => $exceptional_keys ?? null,
                                ])
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
