<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                {{-- <li class="menu-title" key="t-menu">Users</li> --}}
                <li>
                    <a class="waves-effect" href="{{ route('home') }}">
                        <i class="bx bx-home-circle "></i><span key="t-dashboards">Dashboard</span>

                    </a>

                </li>
                @if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->isAdmin())
                <li>
                    <a href="{{ route('riders') }}" class="waves-effect">
                        <i class='bx bx-cycling'></i><span key="t-dashboards">Riders</span>
                    </a>

                </li>
                <li>
                    <a href="{{ route('retailers') }}" class="waves-effect">
                        <i class='bx bx-store'></i><span key="t-dashboards">Retailers</span>
                    </a>

                </li>
                {{-- <li>
                        <a class="waves-effect"
                            href="{{ route('retailer.prices') }}" id="topnav-components" role="button">
                <i class="bx bx-money"></i><span key="t-components">Retailer Prices</span>

                </a>

                </li> --}}
                <li>
                    <a class="waves-effect" href="{{ route('blocks') }}" id="topnav-components" role="button">
                        <i class="bx bx-store"></i><span key="t-components">Blocks</span>

                    </a>

                </li>
                <li>
                    <a class="waves-effect" href="{{ route('collections') }}" id="topnav-components" role="button">
                        <i class="bx bx-cart"></i><span key="t-components">Collections</span>

                    </a>

                </li>
                <li>
                    <a class="waves-effect" href="{{ route('return-blocks') }}" id="topnav-components" role="button">
                        <i class="bx bx-cart"></i><span key="t-components">Return Blocks</span>

                    </a>

                </li>



                @endif
                <li>
                    <a href="{{ route('orders') }}" class="waves-effect">
                        <i class='bx bx-cart'></i><span key="t-dashboards">Orders</span>
                    </a>

                </li>

                @if(Auth::user()->retailer)
                <li>
                    <a href="{{ route('finances') }}" class="waves-effect">
                        <i class='bx bx-dollar'></i><span key="t-dashboards">Financials</span>
                    </a>

                </li>
                @endif
                <li>
                    <a href="{{ route('messages.index') }}" class="waves-effect">
                        <i class='bx bx-spreadsheet'></i><span key="t-dashboards">Tickets</span>
                    </a>

                </li>

                @if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->isAdmin())
                <li>
                    <a href="{{ route('currencies') }}" class="waves-effect">
                        <i class='bx bx-money'></i><span key="t-dashboards">Currencies</span>
                    </a>

                </li>
                <li>
                    <a href="{{ route('order_types') }}" class="waves-effect">
                        <i class='bx bx-file'></i><span key="t-dashboards">Order Type </span>
                    </a>

                </li>
                <li>
                    <a href="{{ route('discounts') }}" class="waves-effect">
                        <i class='bx bx-money'></i><span key="t-dashboards">Discounts</span>
                    </a>

                </li>
                <li>
                    <a href="{{ route('charges') }}" class="waves-effect">
                        <i class='bx  bx-purchase-tag-alt'></i><span key="t-dashboards">Retailer Shipping Charges</span>
                    </a>

                </li>
                <li>
                    <a href="{{ route('warehouse') }}" class="waves-effect">
                        <i class='bx bxs-store-alt'></i><span key="t-dashboards">Warehouses</span>
                    </a>

                </li>
                <li>
                    <a href="{{ route('zones') }}" class="waves-effect">
                        <i class='bx bx-map'></i><span key="t-dashboards">Zones</span>
                    </a>

                </li>
                <li>
                    <a href="{{ route('postal-codes') }}" class="waves-effect">
                        <i class='bx bx-map'></i><span key="t-dashboards">Postal Codes</span>
                    </a>

                </li>

                <li>
                    <a href="{{ route('index.shopify.package') }}" class="waves-effect">
                        <i class='bx bxs-offer'></i><span key="t-dashboards">Shopify Packages</span>
                    </a>

                </li>

                @endif

                {{-- <li>
                    <a href="{{ route('promotion.index') }}" class="waves-effect">
                <i class='bx bx-purchase-tag-alt'></i><span key="t-dashboards">Promotions</span>
                </a>
                </li> --}}

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
