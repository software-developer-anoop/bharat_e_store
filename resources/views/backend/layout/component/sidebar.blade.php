<!--  BEGIN SIDEBAR  -->
<div class="sidebar-wrapper sidebar-theme">
  <nav id="sidebar">
    <ul class="navbar-nav theme-brand flex-row  text-center">
      <li class="nav-item theme-logo">
        <a href="{{route('admin.dashboard')}}">
        <img src="{{$web->logo?asset('uploads/'.$web->logo):''}}" class="navbar-logo" alt="logo">
        </a>
      </li>
      <li class="nav-item theme-text">
        <a href="{{route('admin.dashboard')}}" class="nav-link"> {{$web->site_name??''}} </a>
      </li>
    </ul>
    <ul class="list-unstyled menu-categories" id="accordionExample">
      <li class="menu{{ request()->is('/admin/dashboard') ? 'active' : '' }}">
        <a href="{{route('admin.dashboard')}}" aria-expanded="true" class="dropdown-toggle">
          <div class="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>Dashboard</span>
          </div>
        </a>
      </li>
      @php
      $menus = [
      '1' => 'Country Master',
      '2' => 'State Master',
      '3' => 'City Master',
      '4' => 'Address Master',
      '5' => 'CMS Pages Master',
      '6' => 'Customer Master',
      '7' => 'User Master',
      '8' => 'Category Master',
      '9' => 'Subcategory Master',
      '10' => 'Product Master',
      '11' => 'Coupon Master',
      '12' => 'Notification Master',
      '13' => 'Orders',
      '14' => 'Settings',
      '15' => 'Assign Menu'
      ];
      $user = Auth::user();
      // Check if user is super admin (adjust to your logic)
      $isSuperAdmin = $user->role === 'super_admin'; // or $user->is_super_admin == 1;
      if ($isSuperAdmin) {
      $assignedMenuIds = array_keys($menus);
      } else {
      // Get assigned_menus string and convert to array
      $assignedMenuString = DB::table('users')->where('id', $user->id)->value('assigned_menus');
      $assignedMenuIds = $assignedMenuString
      ? array_map('trim', explode(',', $assignedMenuString))
      : [];
      }
      @endphp
      @if (
      in_array('1', $assignedMenuIds) ||
      in_array('2', $assignedMenuIds) ||
      in_array('3', $assignedMenuIds) ||
      in_array('4', $assignedMenuIds)
      )
      <li class="menu menu-heading">
        <div class="heading">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
            <circle cx="12" cy="12" r="10"></circle>
          </svg>
          <span>Location Masters</span>
        </div>
      </li>
      @endif
      @if (in_array('1', $assignedMenuIds))
      <li class="menu {{ request()->is('admin/country-list','admin/add-country') ? 'active' : '' }}">
        <a href="#country" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
          <div class="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>Country Master</span>
          </div>
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>
        </a>
        <ul class="collapse submenu list-unstyled" id="country" data-parent="#accordionExample">
          <li>
            <a href="{{route('admin.country-list')}}"> Country List </a>
          </li>
          <li>
            <a href="{{route('admin.add-country')}}"> Add Country </a>
          </li>
        </ul>
      </li>
      @endif
      @if (in_array('2', $assignedMenuIds))
      <li class="menu {{ request()->is('admin/state-list','admin/add-state') ? 'active' : '' }}">
        <a href="#state" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
          <div class="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>State Master</span>
          </div>
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>
        </a>
        <ul class="collapse submenu list-unstyled" id="state" data-parent="#accordionExample">
          <li>
            <a href="{{route('admin.state-list')}}"> State List </a>
          </li>
          <li>
            <a href="{{route('admin.add-state')}}"> Add State </a>
          </li>
        </ul>
      </li>
      @endif
      @if (in_array('3', $assignedMenuIds))
      <li class="menu {{ request()->is('admin/city-list','admin/add-city') ? 'active' : '' }}">
        <a href="#city" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
          <div class="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>City Master</span>
          </div>
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>
        </a>
        <ul class="collapse submenu list-unstyled" id="city" data-parent="#accordionExample">
          <li>
            <a href="{{route('admin.city-list')}}"> City List </a>
          </li>
          <li>
            <a href="{{route('admin.add-city')}}"> Add City </a>
          </li>
        </ul>
      </li>
      @endif
      @if (in_array('4', $assignedMenuIds))
      <li class="menu {{ request()->is('admin/address-list','admin/add-address') ? 'active' : '' }}">
        <a href="#address" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
          <div class="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>Address Master</span>
          </div>
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>
        </a>
        <ul class="collapse submenu list-unstyled" id="address" data-parent="#accordionExample">
          <li>
            <a href="{{route('admin.address-list')}}"> Address List </a>
          </li>
          <li>
            <a href="{{route('admin.add-address')}}"> Add Address </a>
          </li>
        </ul>
      </li>
      @endif
      @if(
      in_array('5', $assignedMenuIds) ||
      in_array('6', $assignedMenuIds) ||
      in_array('7', $assignedMenuIds) ||
      in_array('8', $assignedMenuIds) ||
      in_array('9', $assignedMenuIds) ||
      in_array('10', $assignedMenuIds) ||
      in_array('11', $assignedMenuIds) ||
      in_array('12', $assignedMenuIds) 
      )
      <li class="menu menu-heading">
        <div class="heading">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
            <circle cx="12" cy="12" r="10"></circle>
          </svg>
          <span>Other Masters</span>
        </div>
      </li>
      @endif
      @if (in_array('5', $assignedMenuIds))
      <li class="menu">
        <a href="#cmspage" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
          <div class="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>CMS Pages Master</span>
          </div>
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>
        </a>
        <ul class="collapse submenu list-unstyled" id="cmspage" data-parent="#accordionExample">
          <li>
            <a href="{{route('admin.cms-list')}}"> Cms Page List </a>
          </li>
          <li>
            <a href="{{route('admin.add-cms-page')}}"> Add Cms Page </a>
          </li>
        </ul>
      </li>
      @endif
      @if (in_array('6', $assignedMenuIds))
      <li class="menu {{ request()->is('admin/customer-list','admin/add-customer') ? 'active' : '' }}">
        <a href="#customer" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
          <div class="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>Customer Master</span>
          </div>
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>
        </a>
        <ul class="collapse submenu list-unstyled" id="customer" data-parent="#accordionExample">
          <li>
            <a href="{{route('admin.customer-list')}}"> Customer List </a>
          </li>
          <li>
            <a href="{{route('admin.add-customer')}}"> Add Customer </a>
          </li>
        </ul>
      </li>
      @endif
      @if (in_array('7', $assignedMenuIds))
      <li class="menu {{ request()->is('admin/user-list','admin/add-user') ? 'active' : '' }}">
        <a href="#user" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
          <div class="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>User Master</span>
          </div>
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>
        </a>
        <ul class="collapse submenu list-unstyled" id="user" data-parent="#accordionExample">
          <li>
            <a href="{{route('admin.user-list')}}"> User List </a>
          </li>
          <li>
            <a href="{{route('admin.add-user')}}"> Add User </a>
          </li>
        </ul>
      </li>
      @endif
      @if (in_array('8', $assignedMenuIds))
      <li class="menu {{ request()->is('admin/category-list','admin/add-category') ? 'active' : '' }}">
        <a href="#category" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
          <div class="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>Category Master</span>
          </div>
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>
        </a>
        <ul class="collapse submenu list-unstyled" id="category" data-parent="#accordionExample">
          <li>
            <a href="{{route('admin.category-list')}}"> Category List </a>
          </li>
          <li>
            <a href="{{route('admin.add-category')}}"> Add Category </a>
          </li>
        </ul>
      </li>
      @endif
      @if (in_array('9', $assignedMenuIds))
      <li class="menu {{ request()->is('admin/subcategory-list','admin/add-subcategory') ? 'active' : '' }}">
        <a href="#subcategory" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
          <div class="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>Subcategory Master</span>
          </div>
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>
        </a>
        <ul class="collapse submenu list-unstyled" id="subcategory" data-parent="#accordionExample">
          <li>
            <a href="{{route('admin.subcategory-list')}}"> Subcategory List </a>
          </li>
          <li>
            <a href="{{route('admin.add-subcategory')}}"> Add Subcategory </a>
          </li>
        </ul>
      </li>
      @endif
      @if (in_array('10', $assignedMenuIds))
      <li class="menu {{ request()->is('admin/product-list','admin/add-product') ? 'active' : '' }}">
        <a href="#product" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
          <div class="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>Product Master</span>
          </div>
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>
        </a>
        <ul class="collapse submenu list-unstyled" id="product" data-parent="#accordionExample">
          <li>
            <a href="{{route('admin.product-list')}}"> Product List </a>
          </li>
          <li>
            <a href="{{route('admin.add-product')}}"> Add Product </a>
          </li>
        </ul>
      </li>
      @endif
      @if (in_array('11', $assignedMenuIds))
      <li class="menu {{ request()->is('admin/coupon-list','admin/add-coupon') ? 'active' : '' }}">
        <a href="#coupon" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
          <div class="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>Coupon Master</span>
          </div>
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>
        </a>
        <ul class="collapse submenu list-unstyled" id="coupon" data-parent="#accordionExample">
          <li>
            <a href="{{route('admin.coupon-list')}}"> Coupon List </a>
          </li>
          <li>
            <a href="{{route('admin.add-coupon')}}"> Add Coupon </a>
          </li>
        </ul>
      </li>
      @endif
      @if (in_array('12', $assignedMenuIds))
      <li class="menu {{ request()->is('admin/notification-list','admin/add-notification') ? 'active' : '' }}">
        <a href="#notification" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
          <div class="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>Notification Master</span>
          </div>
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>
        </a>
        <ul class="collapse submenu list-unstyled" id="notification" data-parent="#accordionExample">
          <li>
            <a href="{{route('admin.notification-list')}}"> Notification List </a>
          </li>
          <li>
            <a href="{{route('admin.add-notification')}}"> Add Notification </a>
          </li>
        </ul>
      </li>
      @endif
      @if(
      in_array('13', $assignedMenuIds) ||
      in_array('14', $assignedMenuIds) ||
      in_array('15', $assignedMenuIds) 
      )
      <li class="menu menu-heading">
        <div class="heading">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
            <circle cx="12" cy="12" r="10"></circle>
          </svg>
          <span>Others</span>
        </div>
      </li>
      @endif
      @if (in_array('13', $assignedMenuIds))
      <li class="menu">
        <a href="#orders" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
          <div class="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>Orders</span>
          </div>
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>
        </a>
        <ul class="collapse submenu list-unstyled" id="orders" data-parent="#accordionExample">
          <li>
            <a href="{{route('admin.home-setting')}}"> Total Orders </a>
          </li>
          <li>
            <a href="{{route('admin.web-setting')}}">  Pending Orders </a>
          </li>
          <li>
            <a href="{{route('admin.web-setting')}}">  Delivered Orders </a>
          </li>
        </ul>
      </li>
      @endif
      @if (in_array('14', $assignedMenuIds))
      <li class="menu {{ request()->is('admin/web-setting') ? 'active' : '' }}">
        <a href="#settings" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
          <div class="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>Settings</span>
          </div>
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>
        </a>
        <ul class="collapse submenu list-unstyled" id="settings" data-parent="#accordionExample">
          <!-- <li>
            <a href="{{route('admin.home-setting')}}"> Home Setting </a>
            </li> -->
          <li>
            <a href="{{route('admin.web-setting')}}"> Web Setting </a>
          </li>
        </ul>
      </li>
      @endif
      @if (in_array('15', $assignedMenuIds))
      <li class="menu{{ request()->is('/admin/assign-menu') ? 'active' : '' }}">
        <a href="{{route('admin.assign-menu')}}" aria-expanded="true" class="dropdown-toggle">
          <div class="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>Assign Menu</span>
          </div>
        </a>
      </li>
      @endif
    </ul>
  </nav>
</div>
<!--  END SIDEBAR  -->