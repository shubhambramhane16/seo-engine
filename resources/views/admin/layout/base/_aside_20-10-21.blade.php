{{-- Aside --}}

@php
    $kt_logo_image = 'logo-light.png';
@endphp

@if (config('layout.brand.self.theme') === 'light')
    @php $kt_logo_image = 'logo-dark.png' @endphp
@elseif (config('layout.brand.self.theme') === 'dark')
    @php $kt_logo_image = 'seo-engine.png' @endphp
@endif

<div class="aside aside-left {{ Metronic::printClasses('aside', false) }} d-flex flex-column flex-row-auto"
    id="kt_aside">

    {{-- Brand --}}
    <div class="brand flex-column-auto {{ Metronic::printClasses('brand', false) }}" id="kt_brand">
        <div class="brand-logo text-center">
            <a href="{{ url('/') }}">

                <img class="pt-10  w-70" alt="{{ config('app.name') }}" src="{{ asset('media/logos/' . $kt_logo_image) }}" />
            </a>
        </div>

        @if (config('layout.aside.self.minimize.toggle'))
            <button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
                {{ Metronic::getSVG('media/svg/icons/Navigation/Angle-double-left.svg', 'svg-icon-xl') }}
            </button>
        @endif

    </div>

    {{-- Aside menu --}}
    <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">

        @if (config('layout.aside.self.display') === false)
            <div class="header-logo">
                <a href="{{ url('/') }}">


                    <!-- <img alt="{{ config('app.name') }}" src="{{ asset('media/logos/' . $kt_logo_image) }}" /> -->
                </a>
            </div>
        @endif

        <div id="kt_aside_menu" class="aside-menu my-4 {{ Metronic::printClasses('aside_menu', false) }}"
            data-menu-vertical="1" {{ Metronic::printAttrs('aside_menu') }}>
            <ul class="menu-nav {{ Metronic::printClasses('aside_menu_nav', false) }}">
                <li class="menu-section">
                    <h4 class="menu-text">Custom</h4>
                    <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
                </li>
                <li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
                    <a href="#" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <rect fill="#000000" x="4" y="4" width="7" height="7" rx="1.5">
                                    </rect>
                                    <path
                                        d="M5.5,13 L9.5,13 C10.3284271,13 11,13.6715729 11,14.5 L11,18.5 C11,19.3284271 10.3284271,20 9.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M14.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L14.5,11 C13.6715729,11 13,10.3284271 13,9.5 L13,5.5 C13,4.67157288 13.6715729,4 14.5,4 Z M14.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L14.5,20 C13.6715729,20 13,19.3284271 13,18.5 L13,14.5 C13,13.6715729 13.6715729,13 14.5,13 Z"
                                        fill="#000000" opacity="0.3"></path>
                                </g>
                            </svg>
                        </span>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>


                <li class="menu-item menu-item-submenu menu-item-open" aria-haspopup="true" data-menu-toggle="hover">
                    <a href="#" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <path
                                        d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path
                                        d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
                                        fill="#000000" fill-rule="nonzero" />
                                </g>
                            </svg>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-text">Users</span><i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu " kt-hidden-height="320" style=""><span class="menu-arrow"></span>
                        <ul class="menu-subnav">
                            <li class="menu-item  menu-item-parent" aria-haspopup="true"><span class="menu-link"><span
                                        class="menu-text">Admin</span></span></li>
                            <li class="menu-item  @yield('userlist')" aria-haspopup="true" data-menu-toggle="hover">
                                <a href="#" class="menu-link menu-toggle">
                                    <i class="menu-bullet menu-bullet-line"><span></span></i>
                                    <span class="menu-text">Roles</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="menu-submenu ">
                                    <span class="menu-arrow"></span>
                                    <ul class="menu-subnav">
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/users/list') }}" class="menu-link ">
                                                <i class="menu-bullet menu-bullet-dot"><span></span></i><span
                                                    class="menu-text @yield('userlist')">List</span>
                                            </a>
                                        </li>
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/users/add-user') }}" class="menu-link "><i
                                                    class="menu-bullet menu-bullet-dot"><span></span></i><span
                                                    class="menu-text">Add User</span></a>
                                        </li>
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/users/edit-user') }}" class="menu-link "><i
                                                    class="menu-bullet menu-bullet-dot"><span></span></i><span
                                                    class="menu-text">Edit User</span></a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="menu-item  menu-item-submenu  @yield('paymentmaster')  @yield('paymentmaster_add')  @yield('paymentmaster_edit')"
                                aria-haspopup="true" data-menu-toggle="hover">
                                <a href="#" class="menu-link menu-toggle">
                                    <i class="menu-bullet menu-bullet-line"><span></span></i>
                                    <span class="menu-text">Payment Master</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="menu-submenu ">
                                    <span class="menu-arrow"></span>
                                    <ul class="menu-subnav">
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/payment/list') }}" class="menu-link ">
                                                <i class="menu-bullet menu-bullet-dot"><span></span></i><span
                                                    class="menu-text @yield('paymentmaster')">List</span>
                                            </a>
                                        </li>
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/payment/add') }}" class="menu-link ">
                                                <i class="menu-bullet menu-bullet-dot "><span></span></i><span
                                                    class="menu-text @yield('paymentmaster_add')">Add</span></a>
                                        </li>
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/payment/edit') }}" class="menu-link">
                                                <i class="menu-bullet menu-bullet-dot"><span></span></i><span
                                                    class="menu-text  @yield('paymentmaster_edit')">Edit</span></a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="menu-item  menu-item-submenu  @yield('facilitymaster')  @yield('facilitymaster_add')  @yield('facilitymaster_edit')"
                                aria-haspopup="true" data-menu-toggle="hover">
                                <a href="#" class="menu-link menu-toggle">
                                    <i class="menu-bullet menu-bullet-line"><span></span></i>
                                    <span class="menu-text">Facility Master</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="menu-submenu ">
                                    <span class="menu-arrow"></span>
                                    <ul class="menu-subnav">
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/facility/list') }}" class="menu-link ">
                                                <i class="menu-bullet menu-bullet-dot"><span></span></i><span
                                                    class="menu-text @yield('facilitymaster')">List</span>
                                            </a>
                                        </li>
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/facility/add') }}" class="menu-link ">
                                                <i class="menu-bullet menu-bullet-dot "><span></span></i><span
                                                    class="menu-text @yield('facilitymaster_add')">Add</span></a>
                                        </li>
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/facility/edit') }}" class="menu-link">
                                                <i class="menu-bullet menu-bullet-dot"><span></span></i><span
                                                    class="menu-text  @yield('facilitymaster_edit')">Edit</span></a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="menu-item  menu-item-submenu  @yield('centermaster')  @yield('centermaster_add')  @yield('centermaster_edit')"
                                aria-haspopup="true" data-menu-toggle="hover">
                                <a href="#" class="menu-link menu-toggle">
                                    <i class="menu-bullet menu-bullet-line"><span></span></i>
                                    <span class="menu-text">Center Master</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="menu-submenu ">
                                    <span class="menu-arrow"></span>
                                    <ul class="menu-subnav">
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/centers/list') }}" class="menu-link ">
                                                <i class="menu-bullet menu-bullet-dot"><span></span></i><span
                                                    class="menu-text @yield('centermaster')">List</span>
                                            </a>
                                        </li>
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/centers/add') }}" class="menu-link ">
                                                <i class="menu-bullet menu-bullet-dot "><span></span></i><span
                                                    class="menu-text @yield('centermaster_add')">Add</span></a>
                                        </li>
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/centers/edit') }}" class="menu-link">
                                                <i class="menu-bullet menu-bullet-dot"><span></span></i><span
                                                    class="menu-text  @yield('centermaster_edit')">Edit</span></a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="menu-item  menu-item-submenu  @yield('packagemaster')  @yield('packagemaster_add')  @yield('packagemaster_edit')"
                                aria-haspopup="true" data-menu-toggle="hover">
                                <a href="#" class="menu-link menu-toggle">
                                    <i class="menu-bullet menu-bullet-line"><span></span></i>
                                    <span class="menu-text">Packages Master</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="menu-submenu ">
                                    <span class="menu-arrow"></span>
                                    <ul class="menu-subnav">
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/packages/list') }}" class="menu-link ">
                                                <i class="menu-bullet menu-bullet-dot"><span></span></i><span
                                                    class="menu-text @yield('packagemaster')">List</span>
                                            </a>
                                        </li>
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/packages/add') }}" class="menu-link ">
                                                <i class="menu-bullet menu-bullet-dot "><span></span></i><span
                                                    class="menu-text @yield('packagemaster_add')">Add</span></a>
                                        </li>
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/packages/edit') }}" class="menu-link">
                                                <i class="menu-bullet menu-bullet-dot"><span></span></i><span
                                                    class="menu-text  @yield('packagemaster_edit')">Edit</span></a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="menu-item  menu-item-submenu  @yield('lifestylemaster')  @yield('lifestylemaster_add')  @yield('lifestylemaster_edit')"
                                aria-haspopup="true" data-menu-toggle="hover">
                                <a href="#" class="menu-link menu-toggle">
                                    <i class="menu-bullet menu-bullet-line"><span></span></i>
                                    <span class="menu-text">Lifestyle Master</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="menu-submenu ">
                                    <span class="menu-arrow"></span>
                                    <ul class="menu-subnav">
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/lifestyle/list') }}" class="menu-link ">
                                                <i class="menu-bullet menu-bullet-dot"><span></span></i><span
                                                    class="menu-text @yield('lifestylemaster')">List</span>
                                            </a>
                                        </li>
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/lifestyle/add') }}" class="menu-link ">
                                                <i class="menu-bullet menu-bullet-dot "><span></span></i><span
                                                    class="menu-text @yield('lifestylemaster_add')">Add</span></a>
                                        </li>
                                        <li class="menu-item " aria-haspopup="true">
                                            <a href="{{ url('admin/lifestyle/edit') }}" class="menu-link">
                                                <i class="menu-bullet menu-bullet-dot"><span></span></i><span
                                                    class="menu-text  @yield('lifestylemaster_edit')">Edit</span></a>
                                        </li>
                                    </ul>
                                </div>
                            </li>





                        </ul>
                    </div>
                </li>
                {{-- Menu::renderVerMenu(config('menu_aside.items')) --}}
            </ul>
        </div>
    </div>

</div>
