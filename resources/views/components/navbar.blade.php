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
            <a id="notification-bell" class="nav-funcs " role="button">
                <i class="fas fa-bell" style="@if (
                    !in_array(12, session()->get('user')['permissions']) ||
                        session()->get('user')['applicable_discounting_percentage'] == 0) opacity: 0 @endif">
                </i>
            </a>
        </li>
        @if (in_array(12, session()->get('user')['permissions']) ||
                session()->get('user')['applicable_discounting_percentage'] < 0)
            @php
                $discountArray = \App\Models\DiscountData::where('approved_status', 'Remaining');
            @endphp
            <div id="notification-box" class="notification-box">
                Discount to be Approved
                @foreach ($discountArray as $arr)
                    <a href="" class="notifications-item">
                        <div class="text">
                            <h4>Project Name</h4>
                            <p>Employee Name</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item d-sm-inline-block">
            <input type="checkbox" name="" id="mode" hidden>
            <a id="modeIcon" class="nav-funcs " role="button" onclick="$('#mode').click()">
                <i class="fa fa-sun"></i>
            </a>
        </li>
    </ul>
</nav>
