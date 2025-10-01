           <div class="left-side-menu">

               <div class="h-100" data-simplebar>

                   <!-- User box -->
                   <div class="user-box text-center">
                       <img src="assets/images/users/user-1.jpg" alt="user-img" title="Mat Helme"
                           class="rounded-circle avatar-md">
                       <div class="dropdown">
                           <a href="javascript: void(0);" class="text-dark dropdown-toggle h5 mt-2 mb-1 d-block"
                               data-bs-toggle="dropdown">Geneva Kennedy</a>
                           <div class="dropdown-menu user-pro-dropdown">

                               <!-- item-->
                               <a href="javascript:void(0);" class="dropdown-item notify-item">
                                   <i class="fe-user me-1"></i>
                                   <span>My Account</span>
                               </a>

                               <!-- item-->
                               <a href="javascript:void(0);" class="dropdown-item notify-item">
                                   <i class="fe-settings me-1"></i>
                                   <span>Settings</span>
                               </a>

                               <!-- item-->
                               <a href="javascript:void(0);" class="dropdown-item notify-item">
                                   <i class="fe-lock me-1"></i>
                                   <span>Lock Screen</span>
                               </a>

                               <!-- item-->
                               <a href="javascript:void(0);" class="dropdown-item notify-item">
                                   <i class="fe-log-out me-1"></i>
                                   <span>Logout</span>
                               </a>

                           </div>
                       </div>
                       <p class="text-muted">Admin Head</p>
                   </div>

                   <!--- Sidemenu -->
                   <div id="sidebar-menu">

                       <ul id="side-menu">

                           <li class="menu-title">Navigation</li>

                           <li>
                               <a href="{{ url('admin/dashboard') }}">
                                <i class="mdi mdi-view-dashboard-outline"></i>
                                   <span class="badge bg-success rounded-pill float-end">4</span>
                                   <span> Dashboards </span>
                               </a>
                           </li>

                           <li class="menu-title mt-2">Category</li>

                           <li>
                               <a href="#sidebarEcommerce" data-bs-toggle="collapse">
                                   <i class="mdi mdi-cart-outline"></i>
                                   <span> Employees </span>
                                   <span class="menu-arrow"></span>
                               </a>
                               <div class="collapse" id="sidebarEcommerce">
                                   <ul class="nav-second-level">
                                       <li>
                                           <a href="{{ route('admin_manage_employees') }}">Manage Employees</a>
                                       </li>
                                       <li>
                                           <a href="{{ route('admin_manage_timesheet') }}">Timesheet</a>
                                       </li>
                                   </ul>
                               </div>
                           </li>

                           <li>
                               <a href="#sidebarCrm" data-bs-toggle="collapse">
                                   <i class="mdi mdi-account-multiple-outline"></i>
                                   <span> Settings </span>
                                   <span class="menu-arrow"></span>
                               </a>
                               <div class="collapse" id="sidebarCrm">
                                   <ul class="nav-second-level">
                                       <li>
                                           <a href="{{ route('admin_location_settings') }}">Location Settings</a>
                                       </li>
                                       <li>
                                           <a href="{{ route('admin_settings') }}">Network Settings</a>
                                       </li>
                                   </ul>
                               </div>
                           </li>
                      
                       </ul>

                   </div>
                   <!-- End Sidebar -->

                   <div class="clearfix"></div>

               </div>
               <!-- Sidebar -left -->

           </div>
