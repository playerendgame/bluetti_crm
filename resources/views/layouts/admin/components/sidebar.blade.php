<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link bg-light text-center">
      {{-- <img src="../../dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> --}}
      {{-- <span class="brand-text font-weight-light">Bluetti Philippines</span> --}}
      <img class="img-fluid" src="{{ asset('../../bluetti_logo.png') }}" style="width: 13rem">
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info d-flex">
          
          <a href="#" class="d-block nav-link"><b>Greetings {{ Auth::guard('admins')->user()->first_name }} {{ Auth::guard('admins')->user()->last_name }}!</b></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link @if (Request::is( 'admin/dashboard') || Request::is( 'admin/dashboard/*')) active @endif">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          @if (Auth::guard('admins')->user()->hasPermission('mystats.show'))
          <li class="nav-item">
            <a href="{{ route('admin.stats.mystat')}}" class="nav-link @if(Request::is( 'admin/mystats') || Request::is(' admin/mystats/*')) active @endif">
              <i class="nav-icon fas fa-regular fa-chart-bar"></i>
              <p>
                My Stats
              </p>
            </a>
          </li>
          @endif
          <!--DropDown For Admin Only-->
          @if (Auth::guard('admins')->user()->hasPermission('dropdown.show'))
          <li class="nav-item @if (Request::is('admin/dropdown/*')) menu-open @endif">
              <a href="#" class="nav-link @if (Request::is('admin/dropdown/*')) active @endif">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>
                      Dropdown
                      <i class="right fas fa-angle-left"></i>
                  </p>
              </a>
              <ul class="nav nav-treeview">
                @if (Auth::guard("admins")->user()->hasPermission('incentives.show'))
                <li class="nav-item">
                  <a href="{{ route('admin.dropdown.incentives.index')}}" class="nav-link @if (Request::is('admin/dropdown/incentives') || Request::is('admin/dropdown/incentives/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Incentives</p>
                  </a>
                </li>
                @endif
                @if (Auth::guard('admins')->user()->hasPermission('admins.show'))
                <li class="nav-item">
                    <a href="{{ route('admin.dropdown.admin.index')}}" class="nav-link @if (Request::is('admin/dropdown/admin') || Request::is('admin/dropdown/admin/*')) active @endif">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Admins</p>
                    </a>
                </li>
                @endif
                @if (Auth::guard('admins')->user()->hasPermission('referrals.show'))
                <li class="nav-item">
                  <a href="{{ route('admin.dropdown.referrals.index')}}" class="nav-link @if (Request::is('admin/dropdown/referrals') || Request::is('admin/dropdown/admin/referrals/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Referrals</p>
                  </a>
                </li>
                @endif
                @if(Auth::guard('admins')->user()->hasPermission('roles.show'))
                <li class="nav-item">
                    <a href="{{ route('admin.dropdown.role.index') }}" class="nav-link @if (Request::is('admin/dropdown/role') || Request::is('admin/dropdown/role/*')) active @endif">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Roles</p>
                    </a>
                </li>
                @endif
                @if(Auth::guard('admins')->user()->hasPermission('couriers.show'))
                <li class="nav-item">
                  <a href="{{ route('admin.dropdown.courier.index')}}" class="nav-link @if(Request::is('admin/dropdown/courier') || Request::is('admin/dropdown/courier/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Couriers</p>
                  </a>
                </li>
                @endif
                @if(Auth::guard('admins')->user()->hasPermission('mop.show'))
                <li class="nav-item">
                  <a href="{{ route('admin.dropdown.mode-of-payment.index')}}" class="nav-link @if(Request::is('admin/dropdown/mode-of-payment') || Request::is('admin/dropdown/mode-of-payment/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Mode of Payments</p>
                  </a>
                </li>
                @endif
                @if(Auth::guard('admins')->user()->hasPermission('distribution_channels.show'))
                <li class="nav-item">
                  <a href="{{ route('admin.dropdown.distribution_channels.index')}}" class="nav-link @if (Request::is('admin/dropdown/distribution_channels') || Request::is('admin/dropdown/distribution_channels/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Distribution Channels</p>
                  </a>
                </li>
                @endif
                @if(Auth::guard('admins')->user()->hasPermission('att.show'))
                <li class="nav-item">
                  <a href="{{ route('admin.dropdown.attribution.index')}}" class="nav-link @if (Request::is('admin/dropdown/attribution') || Request::is('admin/dropdown/attribution/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Attributions</p>
                  </a>
                </li>
                @endif
                @if(Auth::guard('admins')->user()->hasPermission('funnels.show'))
                <li class="nav-item">
                  <a href="{{ route('admin.dropdown.funnel.index')}}" class="nav-link @if (Request::is('admin/dropdown/funnel') || Request::is('admin/dropdown/funnel/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Funnels</p>
                  </a>
                </li>
                @endif
                @if(Auth::guard('admins')->user()->hasPermission('targets.show'))
                <li class="nav-item">
                  <a href="{{ route('admin.dropdown.target.index') }}" class="nav-link @if (Request::is('admin/dropdown/target') || Request::is('admin/dropdown/target/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Targets</p>
                  </a>
                </li>
                @endif
                @if(Auth::guard('admins')->user()->hasPermission('regions.show'))
                <li class="nav-item">
                  <a href="{{ route('admin.dropdown.region.index') }}" class="nav-link @if (Request::is('admin/dropdown/region') || Request::is('admin/dropdown/region/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Regions</p>
                  </a>
                </li>
                @endif
                @if (Auth::guard('admins')->user()->hasPermission('provinces.show'))
                <li class="nav-item">
                  <a href="{{ route('admin.dropdown.province.index') }}" class="nav-link @if (Request::is('admin/dropdown/province') || Request::is('admin/dropdown/province/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Provinces</p>
                  </a>
                </li>
                @endif
                @if (Auth::guard('admins')->user()->hasPermission('cities.show'))
                <li class="nav-item">
                  <a href="{{ route('admin.dropdown.city.index') }}" class="nav-link @if (Request::is('admin/dropdown/city') || Request::is('admin/dropdown/city/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Cities</p>
                  </a>
                </li>
                @endif
            </ul>
          </li>
          @endif

          <!--Marketing-->
          @if (Auth::guard('admins')->user()->hasPermission('marketing.show'))
          <li class="nav-item @if (Request::is('admin/marketing/*')) menu-open @endif">
            <a href="#" class="nav-link @if (Request::is('admin/marketing/*')) active @endif">
              <i class="nav-icon fas fa-solid fa-bullhorn"></i>
              <p>
                Marketing
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @if (Auth::guard('admins')->user()->hasPermission('daily-aud-ads.show'))
                <li class="nav-item">
                  {{-- <a href="{{ route('admin.marketing.daily-ads-audit.index') }}" class="nav-link @if (Request::is('admin/marketing/daily-ads-audit') || Request::is('admin/marketing/daily-ads-audit/*')) active @endif"> --}}
                    <a href="{{ route('admin.marketing.daily-ads-audit.index')}}" class="nav-link @if(Request::is('admin/marketing/daily-ads-audit') || Request::is('admin/marketing/daily-ads-audit/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Daily Audit Ads vs Gross Sales</p>
                  </a>
                </li>
              @endif
              @if (Auth::guard('admins')->user()->hasPermission('campaign-spent.show'))
                <li class="nav-item">
                    <a href="{{ route('admin.marketing.campaign-spent-per-attribution.index') }}" class="nav-link @if(Request::is('admin/marketing/campaign-spent-per-attribution') || Request::is('admin/marketing/campaign-spent-per-attribution/*')) active @endif">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Campaign Spent per Attribution</p>
                    </a>
                </li>
              @endif
              @if (Auth::guard('admins')->user()->hasPermission('qr.show'))
              <li class="nav-item">
                  <a href="{{ route('admin.marketing.qr.index') }}" class="nav-link @if(Request::is('admin/marketing/qr-generator') || Request::is('admin/marketing/qr-generator/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>QR Code Generator</p>
                  </a>
              </li>
              @endif
            </ul>
          </li>
          @endif

          <!--Finance-->
          @if (Auth::guard('admins')->user()->hasPermission('finance.show'))
          <li class="nav-item @if (Request::is('admin/finance/*')) menu-open @endif">
            <a href="#" class="nav-link @if (Request::is('admin/finance/*')) active @endif">
              <i class="nav-icon fas fa-solid fa-coins"></i>
              <p>
                Finance
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @if (Auth::guard('admins')->user()->hasPermission('purchase.show'))
                <li class="nav-item">
                  <a href="{{ route('admin.finance.purchase.index')}}" class="nav-link @if(Request::is('admin/finance/purchase-order') || Request::is('admin/finance/purchase-order/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Purchase Order</p>
                  </a>
                </li>
              @endif
            </ul>
          </li>
          @endif

          <!-- Retails -->
          <li class="nav-item @if (Request::is('admin/retails/*')) menu-open @endif">
            <a href="#" class="nav-link @if (Request::is('admin/retails/*')) active @endif">
              <i class="nav-icon fas fa-solid fa-store"></i>
              <p>
                Retail
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item @if (Request::is('admin/retails/dropdown/*')) menu-open @endif">
                <a href="#" class="nav-link @if (Request::is('admin/retails/dropdown/*')) active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Dropdown</p>
                  <i class="right fas fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ route('admin.retails.dropdown.store.index')}}" class="nav-link @if(Request::is('admin/retails/dropdown/stores') || Request::is('admin/retails/dropdown/stores/*')) active @endif">
                      <i class="far fa-regular fa-square nav-icon"></i>
                      <p>Stores</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('admin.retails.dropdown.branch.index')}}" class="nav-link @if(Request::is('admin/retails/dropdown/branches') || Request::is('admin/retails/dropdown/branches/*')) active @endif">
                      <i class="far fa-regular fa-square nav-icon"></i>
                      <p>Branches</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.retails.order.index')}}" class="nav-link @if(Request::is('admin/retails/orders') || Request::is('admin/retails/orders/*')) active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Orders</p>
                </a>
              </li>
              <li class="nav-item @if (Request::is('admin/retails/reports/*')) menu-open @endif">
                <a href="#" class="nav-link @if (Request::is('admin/retails/reports/*')) active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Reports</p>
                  <i class="right fas fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ route('admin.retails.report.summary')}}" class="nav-link @if(Request::is('admin/retails/reports/summary') || Request::is('admin/retails/reports/summary/*')) active @endif">
                      <i class="far fa-regular fa-square nav-icon"></i>
                      <p>Summary</p>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </li>

          <!-- End Retail -->

          <!--Orders-->
          @if (Auth::guard('admins')->user()->hasPermission('orders.show'))
          <li class="nav-item @if (Request::is('admin/orders') || Request::is('admin/orders/my-orders') || Request::is('admin/orders/create') || Route::is('admin.orders.show')) menu-open @endif">
            <a href="#" class="nav-link @if (Request::is('admin/orders') || Request::is('admin/orders/my-orders') || Request::is('admin/orders/create') || Route::is('admin.orders.show')) active @endif">
              <i class="nav-icon fas fa-solid fa-box"></i>
              <p>
                Orders
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ route('admin.orders.all')}}" class="nav-link @if (Request::is('admin/orders') || Request::is('admin/orders/create')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>All</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('admin.orders.my-orders')}}" class="nav-link @if (Request::is('admin/orders/my-orders') || Request::is('admin/orders/my-orders/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>My Orders</p>
                  </a> 
                </li>
            </ul>
          </li>
          @endif
          
          <!--Customers-->
          <li class="nav-item @if (Request::is('admin/customers') || Request::is('admin/customers/*')) menu-open @endif">
            <a href="#" class="nav-link @if (Request::is('admin/customers') || Request::is('admin/customers/*')) active @endif">
              <i class="nav-icon fas fa-solid fa-users"></i>
              <p>
                Customers
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('admin.customers.dashboard')}}" class="nav-link @if (Request::is('admin/customers/dashboard')) active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Dashboard</p>
                </a> 
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.customers.index')}}" class="nav-link @if (Request::is('admin/customers')) active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>All</p>
                </a>
              </li>
            </ul>
          </li>
          @if (Auth::guard('admins')->user()->hasPermission('customers.show'))
          {{-- <li class="nav-item">
            <a href="{{ route('admin.customers.index') }}" class="nav-link @if (Request::is( 'admin/customers') || Request::is( 'admin/customers/*')) active @endif">
              <i class="nav-icon fas fa-solid fa-users"></i>
              <p>
                Customers
              </p>
            </a>
          </li> --}}
          @endif

          <!--Products-->
          {{-- @if (Auth::guard('admins')->user()->hasPermission('products.show'))
          <li class="nav-item">
            <a href="{{ route('admin.products.index')}}" class="nav-link @if (Request::is( 'admin/products') || Request::is( 'admin/products/*')) active @endif">
                <i class="nav-icon fas fa-solid fa-boxes"></i>
                <p>
                    Products
                </p>
            </a>
          </li>
          @endif --}}


          <!--Products-->
          @if (Auth::guard('admins')->user()->hasPermission('products.show'))
          <li class="nav-item @if (Request::is('admin/products/*')) menu-open @endif">
            <a href="#" class="nav-link @if (Request::is('admin/products/*')) active @endif">
              <i class="nav-icon fas fa-solid fa-boxes"></i>
              <p>
                Products
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ route('admin.items.index')}}" class="nav-link @if(Request::is('admin/products/items') || Request::is('admin/products/items/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Items</p>
                  </a>
                </li>
            </ul>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('admin.category.index')}}" class="nav-link @if(Request::is('admin/products/category') || Request::is('admin/products/category/*')) active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Category</p>
                </a>
              </li>
          </ul>
          </li>
          @endif


          <!--Inventory-->
          @if (Auth::guard('admins')->user()->hasPermission('inventory.show'))
          <li class="nav-item">
            <a href="{{ route('admin.inventory.index')}}" class="nav-link @if (Request::is('admin/inventory') || Request::is('admin/inventory/*')) active @endif">
              <i class="nav-icon fas fa-solid fa-box"></i>
              <p>
                  Inventory
              </p>
          </a>
          </li>
          @endif

          <!--Reports-->
          @if (Auth::guard('admins')->user()->hasPermission('reports.show'))
          <li class="nav-item @if (Request::is('admin/report') || Request::is('admin/report/*')) menu-open @endif">
            <a href="#" class="nav-link  @if (Request::is('admin/report') || Request::is('admin/report/*')) active @endif">
              {{-- <i class="nav-icon fas fa-solid fa-bar-chart"></i> --}}
              <i class="nav-icon fas fa-solid fa-database"></i>
              <p>
                Reports
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('admin.report.activity-logs.index')}}" class="nav-link @if (Request::is('admin/report/activity-logs') || Request::is('admin/report/activity-logs/*')) active @endif">                    <i class="far fa-circle nav-icon"></i>
                  <p>Activity Logs</p>
                </a>
              </li>
                <li class="nav-item">
                  <a href="{{ route('admin.report.mode-of-payment-orders.index')}}" class="nav-link @if (Request::is('admin/report/mode-of-payments-order') || Request::is('admin/report/mode-of-payments-order/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>MOP Orders Overview</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('admin.report.orders-overview.index')}}" class="nav-link @if (Request::is('admin/report/orders-overview') || Request::is('admin/report/orders-overview/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Orders Overview</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('admin.report.orders-per-area')}}" class="nav-link @if (Request::is('admin/report/orders-per-area') || Request::is('admin/report/orders-per-area/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Orders per Area</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('admin.report.orders-per-category.index')}}" class="nav-link @if (Request::is('admin/report/orders-per-category') || Request::is('admin/report/orders-per-category/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Orders per Category</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('admin.report.orders.per-distribution-channel.index')}}" class="nav-link @if (Request::is('admin/report/orders-per-distribution-channel') || Request::is('admin/report/orders-per-distribution-channel/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Orders Per Distribution Channels</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('admin.report.orders-per-product.index')}}" class="nav-link @if (Request::is('admin/report/orders-per-product') || Request::is('admin/report/orders-per-product/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Orders per Product</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('admin.report.orders-per-product-category.index')}}" class="nav-link @if (Request::is('admin/report/orders-per-product-category') || Request::is('admin/report/orders-per-product-category/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Orders per Product Category</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('admin.report.summary.index')}}" class="nav-link @if (Request::is( 'admin/report/summary') || Request::is( 'admin/report/summary/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Summary</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('admin.report.pancake.index') }}" class="nav-link @if (Request::is( 'admin/report/pancake') || Request::is( 'admin/report/pancake/*')) active @endif">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Pancake</p>
                  </a>
                </li>
            </ul>
          </li>
          @endif

          <!--Settings-->
          {{-- <li class="nav-item">
            <a href="{{ route('admin.settings.changepassword') }}" class="nav-link @if (Request::is('admin/settings') || Request::is('admin/settings/*')) active @endif">
                <i class="nav-icon fas fa-cog "></i>
                <p>
                    Settings
                </p>
            </a>
          </li> --}}

          <!--Settings-->
          <li class="nav-item @if (Request::is('admin/settings/*')) menu-open @endif">
            <a href="#" class="nav-link @if (Request::is('admin/settings/*')) active @endif">
                <i class="nav-icon fas fa-cog"></i>
                <p>
                    Settings
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('admin.settings.dropdown.changepassword') }}" class="nav-link @if (Request::is('admin/settings/dropdown/changepassword') || Request::is('admin/settings/dropdown/changepassword/*')) active @endif">
                        <i class="far fa-circle nav-icon"></i>
                        <p style="font-size: 13px">Change Password</p>
                    </a>
                </li>
            </ul>
          </li>

          
          <!--Logout-->
          <li class="nav-item">
            <a href="#"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>
                Logout
              </p>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
          </li>
          
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>