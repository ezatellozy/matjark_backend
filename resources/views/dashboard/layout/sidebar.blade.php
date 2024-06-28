<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto">
                <a class="navbar-brand" href="{!! route('dashboard.home') !!}">
                    <span class="brand-logo">
                        <img src= "@if(setting('logo')) {{setting('logo')}} @else{{ asset('dashboardAssets') }}/images/icons/logo_sm.png @endif" alt="">
                    </span>
                    <h2 class="brand-text">{{ setting('project_name') }}</h2>
                </a>
            </li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                <i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i></a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="{{ request()->route()->getName() == 'dashboard.home' ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{!! route('dashboard.home') !!}">
                    <i data-feather='home'></i>
                    <span class="menu-title text-truncate" data-i18n="{!! trans('dashboard.general.home') !!}">
                        {!! trans('dashboard.general.home') !!}
                    </span>
                </a>
            </li>

            @if (auth()->user()->hasPermissions('setting','store'))
               <li class="{{ request()->route()->getName() == 'dashboard.setting.index' ? 'active' : '' }} nav-item">
                   <a class="d-flex align-items-center" href="{{ route('dashboard.setting.index') }}">
                       <i data-feather='settings'></i>
                       <span class="menu-title text-truncate" data-i18n="{!! trans('dashboard.setting.setting') !!}">
                           {!! trans('dashboard.setting.setting') !!}
                       </span>
                   </a>
               </li>
             @endif


            {{-- Admins --}}
            @if (auth()->user()->hasPermissions('manager'))
            <li class=" nav-item">
                <a class="d-flex align-items-center" href="#">
                    <i data-feather='users'></i>
                    <span class="menu-title text-truncate" data-i18n="{!! trans('dashboard.manager.managers') !!}">
                        {!! trans('dashboard.manager.managers') !!}
                    </span>
                </a>
                <ul class="menu-content">
                    <li class="{{ request()->route()->getName() == 'dashboard.manager.index' || request()->route()->getName() == 'dashboard.manager.show' ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{!! route('dashboard.manager.index') !!}">
                            <i data-feather="circle"></i>
                            <span class="menu-item" data-i18n="{!! trans('dashboard.manager.managers') !!}">
                                {!! trans('dashboard.general.show_all') !!}
                            </span>
                        </a>
                    </li>
                    @if (auth()->user()->hasPermissions('manager','store'))
                    <li class="{{ request()->route()->getName() == 'dashboard.manager.create' || request()->route()->getName() == 'dashboard.manager.edit' ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{!! route('dashboard.manager.create') !!}">
                            <i data-feather="circle"></i>
                            <span class="menu-item" data-i18n="{!! trans('dashboard.manager.add_manager') !!}">
                                {!! trans('dashboard.general.add_new') !!}
                            </span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            {{-- Roles --}}
            @if (auth()->user()->hasPermissions('role'))
            <li class=" nav-item">
                <a class="d-flex align-items-center" href="#">
                    <i data-feather='package'></i>
                    <span class="menu-title text-truncate" data-i18n="{!! trans('dashboard.role.roles') !!}">
                        {!! trans('dashboard.role.roles') !!}
                    </span>
                </a>
                <ul class="menu-content">
                    <li class="{{ request()->route()->getName() == 'dashboard.role.index' || request()->route()->getName() == 'dashboard.role.show' ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{!! route('dashboard.role.index') !!}">
                            <i data-feather="circle"></i>
                            <span class="menu-item" data-i18n="{!! trans('dashboard.role.roles') !!}">
                                {!! trans('dashboard.general.show_all') !!}
                            </span>
                        </a>
                    </li>
                    @if (auth()->user()->hasPermissions('role','store'))
                    <li class="{{ request()->route()->getName() == 'dashboard.role.create' || request()->route()->getName() == 'dashboard.role.edit' ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{!! route('dashboard.role.create') !!}">
                            <i data-feather="circle"></i>
                            <span class="menu-item" data-i18n="{!! trans('dashboard.role.add_role') !!}">
                                {!! trans('dashboard.general.add_new') !!}
                            </span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif



            {{-- District --}}
            {{-- @if (auth()->user()->hasPermissions('district'))
            <li class=" nav-item">
                <a class="d-flex align-items-center" href="#">
                    <i data-feather='package'></i>
                    <span class="menu-title text-truncate" data-i18n="{!! trans('dashboard.district.districts') !!}">
                        {!! trans('dashboard.district.districts') !!}
                    </span>
                </a>
                <ul class="menu-content">
                    <li class="{{ request()->route()->getName() == 'dashboard.district.index' || request()->route()->getName() == 'dashboard.district.show' ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{!! route('dashboard.district.index') !!}">
                            <i data-feather="circle"></i>
                            <span class="menu-item" data-i18n="{!! trans('dashboard.district.districts') !!}">
                                {!! trans('dashboard.general.show_all') !!}
                            </span>
                        </a>
                    </li>
                    @if (auth()->user()->hasPermissions('district','store'))
                    <li class="{{ request()->route()->getName() == 'dashboard.district.create' || request()->route()->getName() == 'dashboard.district.edit' ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{!! route('dashboard.district.create') !!}">
                            <i data-feather="circle"></i>
                            <span class="menu-item" data-i18n="{!! trans('dashboard.district.add_district') !!}">
                                {!! trans('dashboard.general.add_new') !!}
                            </span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif --}}


              {{-- Selender --}}
              {{-- @if (auth()->user()->hasPermissions('selender'))
              <li class=" nav-item">
                  <a class="d-flex align-items-center" href="#">
                      <i data-feather='package'></i>
                      <span class="menu-title text-truncate" data-i18n="{!! trans('dashboard.selender.selenders') !!}">
                          {!! trans('dashboard.selender.selenders') !!}
                      </span>
                  </a>
                  <ul class="menu-content">
                      <li class="{{ request()->route()->getName() == 'dashboard.selender.index' || request()->route()->getName() == 'dashboard.selender.show' ? 'active' : '' }}">
                          <a class="d-flex align-items-center" href="{!! route('dashboard.selender.index') !!}">
                              <i data-feather="circle"></i>
                              <span class="menu-item" data-i18n="{!! trans('dashboard.selender.selenders') !!}">
                                  {!! trans('dashboard.general.show_all') !!}
                              </span>
                          </a>
                      </li>
                      @if (auth()->user()->hasPermissions('selender','store'))
                      <li class="{{ request()->route()->getName() == 'dashboard.selender.create' || request()->route()->getName() == 'dashboard.selender.edit' ? 'active' : '' }}">
                          <a class="d-flex align-items-center" href="{!! route('dashboard.selender.create') !!}">
                              <i data-feather="circle"></i>
                              <span class="menu-item" data-i18n="{!! trans('dashboard.selender.add_selender') !!}">
                                  {!! trans('dashboard.general.add_new') !!}
                              </span>
                          </a>
                      </li>
                      @endif
                  </ul>
              </li>
              @endif --}}


                {{-- Available Days --}}
                {{-- @if (auth()->user()->hasPermissions('available_day'))
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="#">
                        <i data-feather='package'></i>
                        <span class="menu-title text-truncate" data-i18n="{!! trans('dashboard.available_day.available_days') !!}">
                            {!! trans('dashboard.available_day.available_days') !!}
                        </span>
                    </a>
                    <ul class="menu-content">
                        <li class="{{ request()->route()->getName() == 'dashboard.available_day.index' || request()->route()->getName() == 'dashboard.available_day.show' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{!! route('dashboard.available_day.index') !!}">
                                <i data-feather="circle"></i>
                                <span class="menu-item" data-i18n="{!! trans('dashboard.available_day.available_days') !!}">
                                    {!! trans('dashboard.general.show_all') !!}
                                </span>
                            </a>
                        </li>
                        @if (auth()->user()->hasPermissions('available_day','store'))
                        <li class="{{ request()->route()->getName() == 'dashboard.available_day.create' || request()->route()->getName() == 'dashboard.available_day.edit' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{!! route('dashboard.available_day.create') !!}">
                                <i data-feather="circle"></i>
                                <span class="menu-item" data-i18n="{!! trans('dashboard.available_day.add_available_day') !!}">
                                    {!! trans('dashboard.general.add_new') !!}
                                </span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif --}}


                 {{-- Favorite Times --}}
                 {{-- @if (auth()->user()->hasPermissions('favorite_time'))
                 <li class=" nav-item">
                     <a class="d-flex align-items-center" href="#">
                         <i data-feather='package'></i>
                         <span class="menu-title text-truncate" data-i18n="{!! trans('dashboard.favorite_time.favorite_times') !!}">
                             {!! trans('dashboard.favorite_time.favorite_times') !!}
                         </span>
                     </a>
                     <ul class="menu-content">
                         <li class="{{ request()->route()->getName() == 'dashboard.favorite_time.index' || request()->route()->getName() == 'dashboard.favorite_time.show' ? 'active' : '' }}">
                             <a class="d-flex align-items-center" href="{!! route('dashboard.favorite_time.index') !!}">
                                 <i data-feather="circle"></i>
                                 <span class="menu-item" data-i18n="{!! trans('dashboard.favorite_time.favorite_times') !!}">
                                     {!! trans('dashboard.general.show_all') !!}
                                 </span>
                             </a>
                         </li>
                         @if (auth()->user()->hasPermissions('favorite_time','store'))
                         <li class="{{ request()->route()->getName() == 'dashboard.favorite_time.create' || request()->route()->getName() == 'dashboard.favorite_time.edit' ? 'active' : '' }}">
                             <a class="d-flex align-items-center" href="{!! route('dashboard.favorite_time.create') !!}">
                                 <i data-feather="circle"></i>
                                 <span class="menu-item" data-i18n="{!! trans('dashboard.favorite_time.add_favorite_time') !!}">
                                     {!! trans('dashboard.general.add_new') !!}
                                 </span>
                             </a>
                         </li>
                         @endif
                     </ul>
                 </li>
                 @endif --}}



                                    {{-- Car Type --}}
                  {{-- @if (auth()->user()->hasPermissions('car_type'))
                  <li class=" nav-item">
                      <a class="d-flex align-items-center" href="#">
                          <i data-feather='package'></i>
                          <span class="menu-title text-truncate" data-i18n="{!! trans('dashboard.car_type.car_types') !!}">
                              {!! trans('dashboard.car_type.car_types') !!}
                          </span>
                      </a>
                      <ul class="menu-content">
                          <li class="{{ request()->route()->getName() == 'dashboard.car_type.index' || request()->route()->getName() == 'dashboard.car_type.show' ? 'active' : '' }}">
                              <a class="d-flex align-items-center" href="{!! route('dashboard.car_type.index') !!}">
                                  <i data-feather="circle"></i>
                                  <span class="menu-item" data-i18n="{!! trans('dashboard.car_type.car_types') !!}">
                                      {!! trans('dashboard.general.show_all') !!}
                                  </span>
                              </a>
                          </li>
                          @if (auth()->user()->hasPermissions('car_type','store'))
                          <li class="{{ request()->route()->getName() == 'dashboard.car_type.create' || request()->route()->getName() == 'dashboard.car_type.edit' ? 'active' : '' }}">
                              <a class="d-flex align-items-center" href="{!! route('dashboard.car_type.create') !!}">
                                  <i data-feather="circle"></i>
                                  <span class="menu-item" data-i18n="{!! trans('dashboard.car_type.add_car_type') !!}">
                                      {!! trans('dashboard.general.add_new') !!}
                                  </span>
                              </a>
                          </li>
                          @endif
                      </ul>
                  </li>
                  @endif --}}


                    {{-- Sliders --}}
              {{-- @if (auth()->user()->hasPermissions('slider'))
              <li class=" nav-item">
                  <a class="d-flex align-items-center" href="#">
                    <i class="fas fa-sliders-h"></i>
                      <span class="menu-title text-truncate" data-i18n="{!! trans('dashboard.slider.sliders') !!}">
                          {!! trans('dashboard.slider.sliders') !!}
                      </span>
                  </a>
                  <ul class="menu-content">
                      <li class="{{ request()->route()->getName() == 'dashboard.slider.index' || request()->route()->getName() == 'dashboard.slider.show' ? 'active' : '' }}">
                          <a class="d-flex align-items-center" href="{!! route('dashboard.slider.index') !!}">
                              <i data-feather="circle"></i>
                              <span class="menu-item" data-i18n="{!! trans('dashboard.slider.sliders') !!}">
                                  {!! trans('dashboard.general.show_all') !!}
                              </span>
                          </a>
                      </li>
                      @if (auth()->user()->hasPermissions('slider','store'))
                      <li class="{{ request()->route()->getName() == 'dashboard.slider.create' || request()->route()->getName() == 'dashboard.slider.edit' ? 'active' : '' }}">
                          <a class="d-flex align-items-center" href="{!! route('dashboard.slider.create') !!}">
                              <i data-feather="circle"></i>
                              <span class="menu-item" data-i18n="{!! trans('dashboard.slider.add_type') !!}">
                                  {!! trans('dashboard.general.add_new') !!}
                              </span>
                          </a>
                      </li>
                      @endif
                  </ul>
              </li>
              @endif --}}


                  {{-- Main Category --}}
                  {{-- @if (auth()->user()->hasPermissions('main_category'))
                  <li class=" nav-item">
                      <a class="d-flex align-items-center" href="#">
                          <i data-feather='package'></i>
                          <span class="menu-title text-truncate" data-i18n="{!! trans('dashboard.main_category.main_categories') !!}">
                              {!! trans('dashboard.main_category.main_categories') !!}
                          </span>
                      </a>
                      <ul class="menu-content">
                          <li class="{{ request()->route()->getName() == 'dashboard.main_category.index' || request()->route()->getName() == 'dashboard.main_category.show' ? 'active' : '' }}">
                              <a class="d-flex align-items-center" href="{!! route('dashboard.main_category.index') !!}">
                                  <i data-feather="circle"></i>
                                  <span class="menu-item" data-i18n="{!! trans('dashboard.main_category.main_categories') !!}">
                                      {!! trans('dashboard.general.show_all') !!}
                                  </span>
                              </a>
                          </li>
                          @if (auth()->user()->hasPermissions('main_category','store'))
                          <li class="{{ request()->route()->getName() == 'dashboard.main_category.create' || request()->route()->getName() == 'dashboard.main_category.edit' ? 'active' : '' }}">
                              <a class="d-flex align-items-center" href="{!! route('dashboard.main_category.create') !!}">
                                  <i data-feather="circle"></i>
                                  <span class="menu-item" data-i18n="{!! trans('dashboard.main_category.add_main_category') !!}">
                                      {!! trans('dashboard.general.add_new') !!}
                                  </span>
                              </a>
                          </li>
                          @endif
                      </ul>
                  </li>
                  @endif --}}


                    {{-- First Sub Category --}}
                    {{-- @if (auth()->user()->hasPermissions('first_sub_category'))
                    <li class=" nav-item">
                        <a class="d-flex align-items-center" href="#">
                            <i data-feather='package'></i>
                            <span class="menu-title text-truncate" data-i18n="{!! trans('dashboard.first_sub_category.first_sub_categories') !!}">
                                {!! trans('dashboard.first_sub_category.first_sub_categories') !!}
                            </span>
                        </a>
                        <ul class="menu-content">
                            <li class="{{ request()->route()->getName() == 'dashboard.first_sub_category.index' || request()->route()->getName() == 'dashboard.first_sub_category.show' ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{!! route('dashboard.first_sub_category.index') !!}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item" data-i18n="{!! trans('dashboard.first_sub_category.main_categories') !!}">
                                        {!! trans('dashboard.general.show_all') !!}
                                    </span>
                                </a>
                            </li>
                            @if (auth()->user()->hasPermissions('first_sub_category','store'))
                            <li class="{{ request()->route()->getName() == 'dashboard.first_sub_category.create' || request()->route()->getName() == 'dashboard.first_sub_category.edit' ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{!! route('dashboard.first_sub_category.create') !!}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item" data-i18n="{!! trans('dashboard.first_sub_category.add_first_sub_category') !!}">
                                        {!! trans('dashboard.general.add_new') !!}
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif --}}

 {{-- Second Sub Category --}}
 {{-- @if (auth()->user()->hasPermissions('second_sub_category'))
 <li class=" nav-item">
     <a class="d-flex align-items-center" href="#">
         <i data-feather='package'></i>
         <span class="menu-title text-truncate" data-i18n="{!! trans('dashboard.second_sub_category.second_sub_categories') !!}">
             {!! trans('dashboard.second_sub_category.second_sub_categories') !!}
         </span>
     </a>
     <ul class="menu-content">
         <li class="{{ request()->route()->getName() == 'dashboard.second_sub_category.index' || request()->route()->getName() == 'dashboard.second_sub_category.show' ? 'active' : '' }}">
             <a class="d-flex align-items-center" href="{!! route('dashboard.second_sub_category.index') !!}">
                 <i data-feather="circle"></i>
                 <span class="menu-item" data-i18n="{!! trans('dashboard.second_sub_category.main_categories') !!}">
                     {!! trans('dashboard.general.show_all') !!}
                 </span>
             </a>
         </li>
         @if (auth()->user()->hasPermissions('second_sub_category','store'))
         <li class="{{ request()->route()->getName() == 'dashboard.second_sub_category.create' || request()->route()->getName() == 'dashboard.second_sub_category.edit' ? 'active' : '' }}">
             <a class="d-flex align-items-center" href="{!! route('dashboard.second_sub_category.create') !!}">
                 <i data-feather="circle"></i>
                 <span class="menu-item" data-i18n="{!! trans('dashboard.second_sub_category.add_second_sub_category') !!}">
                     {!! trans('dashboard.general.add_new') !!}
                 </span>
             </a>
         </li>
         @endif
     </ul>
 </li>
 @endif --}}


        </ul>
    </div>
</div>
<!-- END: Main Menu-->
