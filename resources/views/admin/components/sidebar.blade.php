<aside class="main-sidebar sidebar-light-primary elevation-4">
    <a href="" class="brand-link mt-2">
        <img src="{{ asset('assets/dist/img/logo.png') }}" alt="ESDS Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">Configurator</span>
    </a>
    <div class="except sidebar">
        <div class="except user-panel mt-3 pb-2 mb-3 d-flex">
            <div class="except image">
                <img src="{{ asset('assets/dist/img/avatar.png') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="except info">
                <input type="checkbox" id="profileHead" class="head-btn d-none"
                    oninput="if($(this).prop('checked')){ $('#collapseExample').removeClass('hiddenDiv') ; $('#collapseExample').addClass('show')} else {$('#collapseExample').removeClass('show') ; $('#collapseExample').addClass('hiddenDiv')}">
                <label class="text-left btn border-0" for="profileHead" id="profile" style="z-index: 1;">
                    {{ session()->get('user')['name'] }}
                </label>
                <div class="hiddenDiv except" id="collapseExample">
                    <div class=" card-body except">
                        <a onclick="event.preventDefault(); $('#logout-form').submit();" style="cursor: pointer">
                            <span class="fas fa-sign-out-alt mr-2"> </span>{{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('AdminDashboard') }}"
                        class="nav-link @if (Route::is('AdminDashboard')) active @endif except">
                        <i class="nav-icon fas fa-th"></i>
                        <p class="except">
                            {{ __('Dashboard') }}
                        </p>
                    </a>
                </li>

                @php
                    $tables = json_decode(json_encode(DB::select('SHOW TABLES')), true);
                @endphp
                <li class="nav-item">
                    <a href="#" class="nav-link @if (preg_match('/\/Table\//', url()->current())) active @endif">
                        <i class="nav-icon fa fa-list"></i>
                        <p class="except">
                            Tables
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview sidebar-list-c">
                        @foreach ($tables as $table_name)
                            @php
                                if (!preg_match('/tbl_/', $table_name['Tables_in_test-configurator'])) {
                                    continue;
                                }
                                $tbl = preg_replace('/tbl_/', '', $table_name['Tables_in_test-configurator']);
                                $tbl = preg_replace('/_/', ' ', $tbl);
                            @endphp
                            <li class="nav-item">
                                <a href="{{ route('TableData', $table_name['Tables_in_test-configurator']) }}"
                                    class="nav-link @if (url()->current() == route('TableData', $table_name['Tables_in_test-configurator'])) active @endif except">
                                    <i class="nav-icon fas fa-file"></i>
                                    <p class="except text-nowrap">
                                        {{ __(ucwords($tbl)) }}
                                    </p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('Dashboard') }}"
                        class="nav-link @if (Route::is('Dashboard')) active @endif except">
                        <i class="nav-icon fa fa-door-open"></i>
                        <p class="except">
                            {{ __('Back to Dashboard') }}
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
