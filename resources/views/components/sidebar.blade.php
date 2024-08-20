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
                    {{ session()->get('user')['first_name'] }} {{ session()->get('user')['last_name'] }}
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
                    <a href="{{ route('Dashboard') }}"
                        class="nav-link @if (Route::is('Dashboard')) active @endif except">
                        <i class="nav-icon fas fa-th"></i>
                        <p class="except">
                            {{ __('Dashboard') }}
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('SavedEstimates') }}"
                        class="nav-link @if (Route::is('SavedEstimates')) active @endif except" id="allEstms">
                        <i class="nav-icon fa fa-folder-open"></i>
                        <p class="except">
                            {{ __('Saved Quotations') }}
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('CreateNew') }}"
                        class="nav-link @if (Route::is('CreateNew') || preg_match('/\/Estimate/', route(Route::currentRouteName()))) active @endif except" id="create">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p class="except">
                            {{ __('Create New') }}
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('Users') }}"
                        class="nav-link @if (Route::is('Users')) active @endif except" id="teamUsers">
                        <i class="nav-icon fas fa-users"></i>
                        <p class="except">
                            {{ __('Team') }}
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('AllRateCards') }}"
                        class="nav-link @if (Route::is('AllRateCards')) active @endif except" id="rateCard">
                        <i class="nav-icon fas fa-book-open"></i>
                        <p class="except">
                            {{ __('Rate Card') }}
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
{{-- {{ Route::currentRoute Action() }} --}}
