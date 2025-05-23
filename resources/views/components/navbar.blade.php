<nav class="main-header navbar navbar-expand navbar-light" style="" id="navbar">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="aside-bar nav-funcs " id="" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars aside-bar"></i>
            </a>
        </li>
    </ul>
    <ul class="navbar-nav">
        <li class="nav-item">
            <a id="notification-bell" class="nav-funcs " role="button"
                style="@if (
                    !in_array(12, session()->get('user')['permissions']) ||
                        session()->get('user')['applicable_discounting_percentage'] == 0) cursor:auto; @endif">
                <i class="fas fa-bell" style="@if (
                    !in_array(12, session()->get('user')['permissions']) ||
                        session()->get('user')['applicable_discounting_percentage'] == 0) opacity: 0; cursor:auto; @endif">
                </i>
            </a>
        </li>
        @if (in_array(12, session()->get('user')['permissions']) ||
                session()->get('user')['applicable_discounting_percentage'] < 0)
            @php
                $discountArray = \App\Models\ProjectQuotationMaster::select([
                    'tbl_project_quotation_master.id',
                    'tbl_project_master.project_name',
                    'tbl_project_master.project_pot_id as pot_id',
                    'tbl_login_master.first_name',
                    'tbl_login_master.last_name',
                ])
                    ->join(
                        'tbl_project_master',
                        'tbl_project_master.id',
                        '=',
                        'tbl_project_quotation_master.project_id',
                    )
                    ->join(
                        'tbl_login_master',
                        'tbl_login_master.crm_user_id',
                        '=',
                        'tbl_project_quotation_master.user_id',
                    )
                    ->where('discount_approval_status', 'Pending')
                    ->get()
                    ->toArray();
            @endphp
            <div id="notification-box" class="notification-box">
                Discount to be Approved
                @foreach ($discountArray as $arr)
                    <a href="{{ route('CreateNew', $arr['quot_id']) }}" class="notifications-item">
                        <div class="text">
                            <h4>{{ $arr['project_name'] }} : POT-{{ $arr['pot_id'] }}</h4>
                            <p>{{ $arr['first_name'] }} {{ $arr['last_name'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item d-sm-inline-block">
            <input type="checkbox" name="" id="mode" hidden
                onclick="localStorage.setItem('mode',
                $(this).prop('checked')); mode();">
            <a id="modeIcon" class="nav-funcs " role="button" onclick="$('#mode').click()">
                <i class="fa fa-sun"></i>
            </a>
        </li>
    </ul>
</nav>
