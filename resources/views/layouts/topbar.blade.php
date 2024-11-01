<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ url('/') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ URL::asset('/images/Endupfav.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ URL::asset('/images/logo.png') }}" alt="" height="17">
                    </span>
                </a>

                <a href="{{ url('/') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ URL::asset('/images/Endupfav.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ URL::asset('/images/logo.png') }}" alt="" height="19">
                    </span>
                </a>
            </div>
            @auth
                <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                    <i class="fa fa-fw fa-bars"></i>
                </button>


                <!-- App Search-->
                <form class="app-search d-none d-lg-block">
                    <div class="position-relative">
                        <input type="text" class="form-control" id="seach-top" placeholder="Search Order Number">
                        <span class="bx bx-search-alt"></span>
                        <ul id="search_list" style="display:none"></ul>
                    </div>
                </form>
            @endauth
            <div class="dropdown dropdown-mega d-none d-lg-block ms-2">
                {{--            <button type="button" class="btn header-item waves-effect" data-bs-toggle="dropdown" aria-haspopup="false" aria-expanded="false"> --}}
                {{--                <span key="t-megamenu">@lang('translation.Mega_Menu')</span> --}}
                {{--                <i class="mdi mdi-chevron-down"></i> --}}
                {{--            </button> --}}
                {{--            <div class="dropdown-menu dropdown-megamenu"> --}}
                {{--                <div class="row"> --}}
                {{--                    <div class="col-sm-8"> --}}

                {{--                        <div class="row"> --}}
                {{--                            <div class="col-md-4"> --}}
                {{--                                <h5 class="font-size-14 mt-0" key="t-ui-components">@lang('translation.UI_Components')</h5> --}}
                {{--                                <ul class="list-unstyled megamenu-list"> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-lightbox">@lang('translation.Lightbox')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-range-slider">@lang('translation.Range_Slider')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-sweet-alert">@lang('translation.Sweet_Alert')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-rating">@lang('translation.Rating')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-forms">@lang('translation.Forms')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-tables">@lang('translation.Tables')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-charts">@lang('translation.Charts')</a> --}}
                {{--                                    </li> --}}
                {{--                                </ul> --}}
                {{--                            </div> --}}

                {{--                            <div class="col-md-4"> --}}
                {{--                                <h5 class="font-size-14 mt-0" key="t-applications">@lang('translation.Applications')</h5> --}}
                {{--                                <ul class="list-unstyled megamenu-list"> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-ecommerce">@lang('translation.Ecommerce')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-calendar">@lang('translation.Calendars')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-email">@lang('translation.Email')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-projects">@lang('translation.Projects')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-tasks">@lang('translation.Tasks')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-contacts">@lang('translation.Contacts')</a> --}}
                {{--                                    </li> --}}
                {{--                                </ul> --}}
                {{--                            </div> --}}

                {{--                            <div class="col-md-4"> --}}
                {{--                                <h5 class="font-size-14 mt-0" key="t-extra-pages">@lang('translation.Extra_Pages')</h5> --}}
                {{--                                <ul class="list-unstyled megamenu-list"> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-light-sidebar">@lang('translation.Light_Sidebar')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-compact-sidebar">@lang('translation.Compact_Sidebar')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-horizontal">@lang('translation.Horizontal_layout')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-maintenance">@lang('translation.Maintenance')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-coming-soon">@lang('translation.Coming_Soon')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-timeline">@lang('translation.Timeline')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-faqs">@lang('translation.FAQs')</a> --}}
                {{--                                    </li> --}}

                {{--                                </ul> --}}
                {{--                            </div> --}}
                {{--                        </div> --}}
                {{--                    </div> --}}
                {{--                    <div class="col-sm-4"> --}}
                {{--                        <div class="row"> --}}
                {{--                            <div class="col-sm-6"> --}}
                {{--                                <h5 class="font-size-14 mt-0" key="t-ui-components">@lang('translation.UI_Components')</h5> --}}
                {{--                                <ul class="list-unstyled megamenu-list"> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-lightbox">@lang('translation.Lightbox')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-range-slider">@lang('translation.Range_Slider')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-sweet-alert">@lang('translation.Sweet_Alert')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-rating">@lang('translation.Rating')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-forms">@lang('translation.Forms')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-tables">@lang('translation.Tables')</a> --}}
                {{--                                    </li> --}}
                {{--                                    <li> --}}
                {{--                                        <a href="javascript:void(0);" key="t-charts">@lang('translation.Charts')</a> --}}
                {{--                                    </li> --}}
                {{--                                </ul> --}}
                {{--                            </div> --}}

                {{--                            <div class="col-sm-5"> --}}
                {{--                                <div> --}}
                {{--                                    <img src="{{ URL::asset ('/assets/images/megamenu-img.png') }}" alt="" class="img-fluid mx-auto d-block"> --}}
                {{--                                </div> --}}
                {{--                            </div> --}}
                {{--                        </div> --}}
                {{--                    </div> --}}
                {{--                </div> --}}

                {{--            </div> --}}
            </div>
        </div>

        <div class="d-flex">

            {{--        <div class="dropdown d-inline-block d-lg-none ms-2"> --}}
            {{--            <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown" --}}
            {{--                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> --}}
            {{--                <i class="mdi mdi-magnify"></i> --}}
            {{--            </button> --}}
            {{--            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" --}}
            {{--                aria-labelledby="page-header-search-dropdown"> --}}

            {{--                <form class="p-3"> --}}
            {{--                    <div class="form-group m-0"> --}}
            {{--                        <div class="input-group"> --}}
            {{--                            <input type="text" class="form-control" placeholder="@lang('translation.Search')" aria-label="Search input"> --}}

            {{--                            <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>s --}}
            {{--                        </div> --}}
            {{--                    </div> --}}
            {{--                </form> --}}
            {{--            </div> --}}
            {{--        </div> --}}

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    {{-- @switch(Session::get('lang'))
                               @case('ru')
                                   <img src="{{ URL::asset('/assets/images/flags/russia.jpg')}}" alt="Header Language" height="16">
                               @break
                               @case('it')
                                   <img src="{{ URL::asset('/assets/images/flags/italy.jpg')}}" alt="Header Language" height="16">
                               @break
                               @case('de')
                                   <img src="{{ URL::asset('/assets/images/flags/germany.jpg')}}" alt="Header Language" height="16">
                               @break
                               @case('es')
                                   <img src="{{ URL::asset('/assets/images/flags/spain.jpg')}}" alt="Header Language" height="16">
                               @break
                               @default --}}
                    <img src="{{ URL::asset('/assets/images/flags/ukFlag.webp') }}" alt="Header Language"
                        height="16">
                    {{-- @endswitch --}}
                </button>
            </div>
            {{-- <div class="dropdown-menu dropdown-menu-end"> --}}

            {{--                <!-- item--> --}}
            {{--                <a href="{{ url('index/en') }}" class="dropdown-item notify-item language" data-lang="eng"> --}}
            {{--                    <img src="{{ URL::asset ('/assets/images/flags/us.jpg') }}" alt="user-image" class="me-1" height="12"> <span class="align-middle">English</span> --}}
            {{--                </a> --}}
            {{--                <!-- item--> --}}
            {{--                <a href="{{ url('index/es') }}" class="dropdown-item notify-item language" data-lang="sp"> --}}
            {{--                    <img src="{{ URL::asset ('/assets/images/flags/spain.jpg') }}" alt="user-image" class="me-1" height="12"> <span class="align-middle">Spanish</span> --}}
            {{--                </a> --}}

            {{--                <!-- item--> --}}
            {{--                <a href="{{ url('index/de') }}" class="dropdown-item notify-item language" data-lang="gr"> --}}
            {{--                    <img src="{{ URL::asset ('/assets/images/flags/germany.jpg') }}" alt="user-image" class="me-1" height="12"> <span class="align-middle">German</span> --}}
            {{--                </a> --}}

            {{--                <!-- item--> --}}
            {{--                <a href="{{ url('index/it') }}" class="dropdown-item notify-item language" data-lang="it"> --}}
            {{--                    <img src="{{ URL::asset ('/assets/images/flags/italy.jpg') }}" alt="user-image" class="me-1" height="12"> <span class="align-middle">Italian</span> --}}
            {{--                </a> --}}

            {{--                <!-- item--> --}}
            {{--                <a href="{{ url('index/ru') }}" class="dropdown-item notify-item language" data-lang="ru"> --}}
            {{--                    <img src="{{ URL::asset ('/assets/images/flags/russia.jpg') }}" alt="user-image" class="me-1" height="12"> <span class="align-middle">Russian</span> --}}
            {{--                </a> --}}
            {{--            </div> --}}
            {{--        </div> --}}

            {{--        <div class="dropdown d-none d-lg-inline-block ms-1"> --}}
            {{--            <button type="button" class="btn header-item noti-icon waves-effect" --}}
            {{--                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> --}}
            {{--                <i class="bx bx-customize"></i> --}}
            {{--            </button> --}}
            {{--            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end"> --}}
            {{--                <div class="px-lg-2"> --}}
            {{--                    <div class="row g-0"> --}}
            {{--                        <div class="col"> --}}
            {{--                            <a class="dropdown-icon-item" href="#"> --}}
            {{--                                <img src="{{ URL::asset ('/assets/images/brands/github.png') }}" alt="Github"> --}}
            {{--                                <span>GitHub</span> --}}
            {{--                            </a> --}}
            {{--                        </div> --}}
            {{--                        <div class="col"> --}}
            {{--                            <a class="dropdown-icon-item" href="#"> --}}
            {{--                                <img src="{{ URL::asset ('/assets/images/brands/bitbucket.png') }}" alt="bitbucket"> --}}
            {{--                                <span>Bitbucket</span> --}}
            {{--                            </a> --}}
            {{--                        </div> --}}
            {{--                        <div class="col"> --}}
            {{--                            <a class="dropdown-icon-item" href="#"> --}}
            {{--                                <img src="{{ URL::asset ('/assets/images/brands/dribbble.png') }}" alt="dribbble"> --}}
            {{--                                <span>Dribbble</span> --}}
            {{--                            </a> --}}
            {{--                        </div> --}}
            {{--                    </div> --}}

            {{--                    <div class="row g-0"> --}}
            {{--                        <div class="col"> --}}
            {{--                            <a class="dropdown-icon-item" href="#"> --}}
            {{--                                <img src="{{ URL::asset ('/assets/images/brands/dropbox.png') }}" alt="dropbox"> --}}
            {{--                                <span>Dropbox</span> --}}
            {{--                            </a> --}}
            {{--                        </div> --}}
            {{--                        <div class="col"> --}}
            {{--                            <a class="dropdown-icon-item" href="#"> --}}
            {{--                                <img src="{{ URL::asset ('/assets/images/brands/mail_chimp.png') }}" alt="mail_chimp"> --}}
            {{--                                <span>Mail Chimp</span> --}}
            {{--                            </a> --}}
            {{--                        </div> --}}
            {{--                        <div class="col"> --}}
            {{--                            <a class="dropdown-icon-item" href="#"> --}}
            {{--                                <img src="{{ URL::asset ('/assets/images/brands/slack.png') }}" alt="slack"> --}}
            {{--                                <span>Slack</span> --}}
            {{--                            </a> --}}
            {{--                        </div> --}}
            {{--                    </div> --}}
            {{--                </div> --}}
            {{--            </div> --}}
            {{--        </div> --}}
          
            <div class="dropdown d-inline-block">
                @auth
                    <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img class="rounded-circle header-profile-user" src="{{ URL::asset('/images/Endupfav.png') }}"
                            alt="Header Avatar">
                        <span class="d-none d-xl-inline-block ms-1"
                            key="t-henry">{{ ucfirst(Auth::user()->name) }}</span>
                        <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                    </button>
                @else
                    <a href="{{ route('login') }}"
                        class="btn btn-sm px-3 font-size-16 header-item waves-effect d-flex align-items-center"
                        id="">
                        Login
                    </a>
                @endauth

                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    {{-- <a class="dropdown-item" href="contacts-profile"><i
                            class="bx bx-user font-size-16 align-middle me-1"></i> <span
                            key="t-profile">@lang('translation.Profile')</span></a>
                    <a class="dropdown-item" href="#"><i class="bx bx-wallet font-size-16 align-middle me-1"></i>
                        <span key="t-my-wallet">@lang('translation.My_Wallet')</span></a>
                    <a class="dropdown-item d-block" href="#" data-bs-toggle="modal"
                        data-bs-target=".change-password"><span class="badge bg-success float-end">11</span><i
                            class="bx bx-wrench font-size-16 align-middle me-1"></i> <span
                            key="t-settings">@lang('translation.Settings')</span></a>
                    <a class="dropdown-item" href="#"><i
                            class="bx bx-lock-open font-size-16 align-middle me-1"></i> <span
                            key="t-lock-screen">@lang('translation.Lock_screen')</span></a>
                    <div class="dropdown-divider"></div> --}}
                    
                    @auth
                    @if (Auth::user()->isRetailer())
                    <a class="dropdown-item" href="{{route('retailer.settings')}}"><i
                            class="bx bx-lock-open font-size-16 align-middle me-1"></i> <span
                            key="t-lock-screen">Settings</span></a>
                    @endif
                    @endauth

                    <a class="dropdown-item text-danger" href="javascript:void();"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                            class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span
                            key="t-logout">Logout</span></a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
           
            </div>

            {{--        <div class="dropdown d-inline-block"> --}}
            {{--            <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect"> --}}
            {{--                <i class="bx bx-cog bx-spin"></i> --}}
            {{--            </button> --}}
            {{--        </div> --}}

        </div>
    </div>
</header>
{{-- @auth
    <div class="topnav">
        <div class="container-fluid">
            <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

                <div class="collapse navbar-collapse active" id="topnav-menu-content">
                    <ul class="navbar-nav active">

                        <li class="nav-item dropdown  d-flex align-items-center">
                            <a class="nav-link dropdown-toggle arrow-none d-flex align-items-center"
                                href="{{ route('home') }}" id="topnav-dashboard" role="button">
                                <i class="bx bx-home-circle me-2"></i><span key="t-dashboards">Dashboard</span>

                            </a>

                        </li>
                        @if (Auth::user()->isAdmin())
                            <li class="nav-item dropdown  d-flex align-items-center">
                                <a class="nav-link dropdown-toggle arrow-none d-flex align-items-center"
                                    href="{{ route('riders') }}" id="topnav-uielement" role="button">
                                    <i class="bx bx-cycling me-2"></i>
                                    <span key="t-ui-elements"> Riders</span>

                                </a>


                            </li>
                        @endif
                        @if (Auth::user()->isAdmin())
                            <li class="nav-item dropdown  d-flex align-items-center">
                                <a class="nav-link dropdown-toggle arrow-none d-flex align-items-center"
                                    href="" id="topnav-pages" role="button">
                                    <i class="bx bx-store me-2"></i><span key="t-apps">Retailers</span>
                                                               <div class="arrow-down"></div>
                                </a>
                                                       <div class="dropdown-menu" aria-labelledby="topnav-pages">
 <a href="{{ route('retailers') }}" class="dropdown-item" key="t-chat">Retailers</a>
 <a href="{{ route('retailer.prices') }}" class="dropdown-item" key="t-chat">Prices</a>

                                                       </div>



                            </li>

                            <li class="nav-item dropdown d-flex align-items-center">
                                <a class="nav-link dropdown-toggle arrow-none d-flex align-items-center"
                                    href="{{ route('blocks') }}" id="topnav-components" role="button">
                                    <i class="bx bx-cart me-2"></i><span key="t-components">Blocks</span>

                                </a>

                            </li>
                            <li class="nav-item dropdown d-flex align-items-center">
                                <a class="nav-link dropdown-toggle arrow-none d-flex align-items-center"
                                    href="{{ route('collections') }}" id="topnav-components" role="button">
                                    <i class="bx bx-cart me-2"></i><span key="t-components">Collections</span>

                                </a>

                            </li>
                        @endif
                        <li class="nav-item dropdown d-flex align-items-center">
                            <a class="nav-link dropdown-toggle arrow-none d-flex align-items-center"
                                href="{{ route('orders') }}" id="topnav-components" role="button">
                                <i class="bx bx-cart me-2"></i><span key="t-components">Orders</span>

                            </a>

                        </li>
                        @if (Auth::user()->isAdmin())


                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle arrow-none d-flex align-items-center"
                                    href="{{ route('currencies') }}" id="topnav-components" role="button">
                                    <i class="bx bx-money me-2"></i><span key="t-components">Currencies</span>

                                </a>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle arrow-none d-flex align-items-center"
                                    href="{{ route('packages') }}" id="topnav-components" role="button">
                                    <i class="bx bx-money me-2"></i><span key="t-components">Packages</span>

                                </a>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle arrow-none d-flex align-items-center"
                                    href="{{ route('discounts') }}" id="topnav-components" role="button">
                                    <i class="bx bx-money me-2"></i><span key="t-components">Discounts</span>

                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle arrow-none d-flex align-items-center"
                                    href="{{ route('charges') }}" id="topnav-components" role="button">
                                    <i class="bx bx-money me-2"></i><span key="t-components">Retailer Shipping Charges</span>

                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle arrow-none d-flex align-items-center"
                                    href="{{ route('warehouse') }}" id="topnav-components" role="button">
                                    <i class="bx bx-money me-2"></i><span key="t-components">Warehouse</span>

                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle arrow-none d-flex align-items-center"
                                    href="{{ route('postal-codes') }}" id="topnav-components" role="button">
                                    <i class="bx bx-money me-2"></i><span key="t-components">Postal Codes</span>

                                </a>
                            </li>
                        @endif
                        @if (Auth::user()->isRetailer())
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle arrow-none d-flex align-items-center"
                                    href="{{ route('promotion.index') }}" id="topnav-components" role="button">
                                    <i class="bx bx-money me-2"></i><span key="t-components">Promotion</span>

                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </nav>
        </div>
    </div>
@endauth --}}
