<!-- BEGIN: Header-->
<nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-dark navbar-shadow">
  <div class="navbar-container d-flex content">
    <div class="bookmark-wrapper d-flex align-items-center">
      <ul class="nav navbar-nav d-xl-none">
        <li class="nav-item"><a class="nav-link menu-toggle" href="javascript:void(0);"><i class="ficon" data-feather="menu"></i></a></li>
      </ul>
      {{-- <ul class="nav navbar-nav bookmark-icons">
        <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-email.html" data-toggle="tooltip" data-placement="top" title="Email"><i class="ficon" data-feather="mail"></i></a></li>
        <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-chat.html" data-toggle="tooltip" data-placement="top" title="Chat"><i class="ficon" data-feather="message-square"></i></a></li>
        <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-calendar.html" data-toggle="tooltip" data-placement="top" title="Calendar"><i class="ficon" data-feather="calendar"></i></a></li>
        <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-todo.html" data-toggle="tooltip" data-placement="top" title="Todo"><i class="ficon" data-feather="check-square"></i></a></li>
      </ul> --}}
      <ul class="nav navbar-nav">
        {{-- <li class="nav-item d-none d-lg-block"><a class="nav-link bookmark-star"><i class="ficon text-warning" data-feather="star"></i></a>
          <div class="bookmark-input search-input">
            <div class="bookmark-input-icon"><i data-feather="search"></i></div>
            <input class="form-control input" type="text" placeholder="Bookmark" tabindex="0" data-search="search">
            <ul class="search-list search-list-bookmark"></ul>
          </div>
        </li> --}}
      </ul>
    </div>
    <ul class="nav navbar-nav align-items-center ml-auto">
        <li class="nav-item dropdown dropdown-language">
            <a class="nav-link dropdown-toggle" id="dropdown-flag" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="flag-icon flag-icon-{{ app()->getLocale() == 'en' ? 'us' : 'sa' }}"></i><span class="selected-language">{{ LaravelLocalization::getCurrentLocaleName() }}</span></a>

            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                @continue($localeCode == LaravelLocalization::getCurrentLocale())

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-flag">
                    <a class="dropdown-item" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" data-language="{{ $localeCode }}">
                        <i class="flag-icon flag-icon-{{ $localeCode == 'en' ? 'us' : 'sa' }}"></i> {{ $properties['native'] }}
                    </a>
                </div>

            @endforeach
        </li>
      <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-style"><i class="ficon" data-feather="sun"></i></a></li>
      <li class="nav-item nav-search"><a class="nav-link nav-link-search"><i class="ficon" data-feather="search"></i></a>
          <form method="get" action="{!! LaravelLocalization::localizeUrl('dashboard/search') !!}">
          <div class="search-input">
              <div class="search-input-icon"><i data-feather="search"></i></div>
              <input class="form-control input autocomplete_general" name="search" type="text" placeholder="{!! trans('dashboard.general.search') !!}" tabindex="-1" data-search="search" autocomplete="off">
              <div class="search-input-close"><i data-feather="x"></i></div>
              <ul class="search-list search-list-main"></ul>
          </div>
          </form>
      </li>

      <li class="nav-item dropdown dropdown-notification mr-25"><a class="nav-link" href="javascript:void(0);" data-toggle="dropdown"><i class="ficon" data-feather="bell"></i><span class="badge badge-pill badge-danger badge-up notify_count">{{ auth()->user()->unreadnotifications()->count() }}</span></a>
          <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
              <li class="dropdown-menu-header">
                  <div class="dropdown-header d-flex">
                      <h4 class="notification-title mb-0 mr-auto">{!! trans('dashboard.notification.notifications') !!}</h4>
                      <div class="badge badge-pill badge-light-primary notify_count" id="notify_counter">{{ auth()->user()->unreadnotifications()->count() }}</div>
                  </div>
              </li>
              <li class="scrollable-container media-list notification_list">
                  @forelse ($notifications as $notification)
                          @php
                          $title = "";
                          if (isset($notification->data['title'])) {
                              if (!is_array($notification->data['title'])) {
                                  $title = trans($notification->data['title']);
                              }elseif (isset($notification->data['title'][1])) {
                                  $title = trans($notification->data['title'][0],$notification->data['title'][1]);
                              }elseif (!isset($notification->data['title'][1])) {
                                  $title = trans($notification->data['title'][0]);
                              }else{
                                  $title = "";
                              }
                          }
                          $body = "";
                          if (isset($notification->data['body'])) {
                              if (!is_array($notification->data['body'])) {
                                  $body = trans($notification->data['body']);
                              }elseif (isset($notification->data['body'][1])) {
                                  $body = trans($notification->data['body'][0],$notification->data['body'][1]);
                              }elseif (!isset($notification->data['body'][1])) {
                                  $body = trans($notification->data['body'][0]);
                              }else{
                                  $body = "";
                              }
                          }
                          $route = '#';
                          if (isset($notification->data['notify_type'])) {
                              switch ($notification->data['notify_type']) {
                                  case 'order':
                                      $route = route('dashboard.order.show',$notification->data['order_id']);
                                      break;
                                  case 'contact':
                                      $route = route('dashboard.contact.show',$notification->data['contact_id']);
                                      break;
                              }
                          }
                          if (isset($notification->data['route'])) {
                              $route = $notification->data['route'];
                          }
                          @endphp
                      <a class="d-flex" href="{{ $route }}">
                          <div class="media d-flex align-items-start">
                              <div class="media-left">
                                  {{-- <div class="avatar"><img src="../../../app-assets/images/portrait/small/avatar-s-15.jpg" alt="avatar" width="32" height="32"></div> --}}
                                  @isset($notification->data['order_id'])
                                      <i class="feather icon-shopping-cart font-medium-5 primary"></i>
                                  @else
                                      <i class="feather icon-message-square font-medium-5 primary"></i>
                                  @endisset
                              </div>
                              <div class="media-body">
                                  <p class="media-heading"><span class="font-weight-bolder">{{ $title }}</p>
                                      <small class="notification-text"> {{ str_limit($body,100,'...') }}</small>
                              </div>
                          </div>
                      </a>
                  @empty
                  <a class="d-flex justify-content-center no_notifications pt-2 pb-2">
                      <i class="feather icon-bell align-middle font-medium-3 text-white text-bold-700"></i>
                      <span class="align-middle text-bold-700 font-medium-3 ">{!! trans('dashboard.notification.no_notifications') !!}</span>
                  </a>
                  {{-- <li class="empty-cart d-none p-2 no_notifications">{!! trans('dashboard.notification.no_notifications') !!}.</li> --}}
              @endforelse
              </li>
              <li class="dropdown-menu-footer"><a class="btn btn-primary btn-block" href="{!! route('dashboard.notification.index') !!}">{!! trans('dashboard.general.show_all') !!}</a></li>
          </ul>
      </li>

      <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <div class="user-nav d-sm-flex d-none">
                  <span class="user-name font-weight-bolder">{{ auth()->user()->fullname }}</span>
                  <span class="user-status">{{ optional(auth()->user()->role)->name ?? null }}</span>
              </div>
              <span class="avatar">
                  <img class="round" src="{{ auth()->user()->avatar }}" alt="avatar" height="40" width="40">
                  <span class="avatar-status-online"></span>
              </span>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">
              <a class="dropdown-item" href="{{ LaravelLocalization::localizeUrl('dashboard/get_profile') }}">
                  <i class="mr-50" data-feather="user"></i> {!! trans('dashboard.user.profile') !!}
              </a>
              @if (auth()->user()->hasPermissions('setting','store'))
              <a class="dropdown-item" href="{{ route('dashboard.setting.index') }}"><i class="mr-50" data-feather="settings"></i> {!! trans('dashboard.setting.setting') !!}</a>
              @endif
              <div class="dropdown-divider"></div>
              {!! Form::open(['route' => 'logout' , 'method' => 'POST' , 'id' => 'logout_form']) !!}
              <a class="dropdown-item" onclick="document.getElementById('logout_form').submit();"><i class="mr-50" data-feather="power"></i> {!! trans('dashboard.auth.logout') !!}</a>
              {!! Form::close() !!}
          </div>
      </li>
    </ul>
  </div>
</nav>
<ul class="main-search-list-defaultlist d-none">
  <li class="d-flex align-items-center"><a href="javascript:void(0);">
      <h6 class="section-label mt-75 mb-0">Files</h6></a></li>
  <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
      <div class="d-flex">
        <div class="mr-75"><img src="../../../app-assets/images/icons/xls.png" alt="png" height="32"></div>
        <div class="search-data">
          <p class="search-data-title mb-0">Two new item submitted</p><small class="text-muted">Marketing Manager</small>
        </div>
      </div><small class="search-data-size mr-50 text-muted">&apos;17kb</small></a></li>
  <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
      <div class="d-flex">
        <div class="mr-75"><img src="../../../app-assets/images/icons/jpg.png" alt="png" height="32"></div>
        <div class="search-data">
          <p class="search-data-title mb-0">52 JPG file Generated</p><small class="text-muted">FontEnd Developer</small>
        </div>
      </div><small class="search-data-size mr-50 text-muted">&apos;11kb</small></a></li>
  <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
      <div class="d-flex">
        <div class="mr-75"><img src="../../../app-assets/images/icons/pdf.png" alt="png" height="32"></div>
        <div class="search-data">
          <p class="search-data-title mb-0">25 PDF File Uploaded</p><small class="text-muted">Digital Marketing Manager</small>
        </div>
      </div><small class="search-data-size mr-50 text-muted">&apos;150kb</small></a></li>
  <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
      <div class="d-flex">
        <div class="mr-75"><img src="../../../app-assets/images/icons/doc.png" alt="png" height="32"></div>
        <div class="search-data">
          <p class="search-data-title mb-0">Anna_Strong.doc</p><small class="text-muted">Web Designer</small>
        </div>
      </div><small class="search-data-size mr-50 text-muted">&apos;256kb</small></a></li>
  <li class="d-flex align-items-center"><a href="javascript:void(0);">
      <h6 class="section-label mt-75 mb-0">Members</h6></a></li>
  <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view.html">
      <div class="d-flex align-items-center">
        <div class="avatar mr-75"><img src="../../../app-assets/images/portrait/small/avatar-s-8.jpg" alt="png" height="32"></div>
        <div class="search-data">
          <p class="search-data-title mb-0">John Doe</p><small class="text-muted">UI designer</small>
        </div>
      </div></a></li>
  <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view.html">
      <div class="d-flex align-items-center">
        <div class="avatar mr-75"><img src="../../../app-assets/images/portrait/small/avatar-s-1.jpg" alt="png" height="32"></div>
        <div class="search-data">
          <p class="search-data-title mb-0">Michal Clark</p><small class="text-muted">FontEnd Developer</small>
        </div>
      </div></a></li>
  <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view.html">
      <div class="d-flex align-items-center">
        <div class="avatar mr-75"><img src="../../../app-assets/images/portrait/small/avatar-s-14.jpg" alt="png" height="32"></div>
        <div class="search-data">
          <p class="search-data-title mb-0">Milena Gibson</p><small class="text-muted">Digital Marketing Manager</small>
        </div>
      </div></a></li>
  <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view.html">
      <div class="d-flex align-items-center">
        <div class="avatar mr-75"><img src="../../../app-assets/images/portrait/small/avatar-s-6.jpg" alt="png" height="32"></div>
        <div class="search-data">
          <p class="search-data-title mb-0">Anna Strong</p><small class="text-muted">Web Designer</small>
        </div>
      </div></a></li>
</ul>
<ul class="main-search-list-defaultlist-other-list d-none">
  <li class="auto-suggestion justify-content-between"><a class="d-flex align-items-center justify-content-between w-100 py-50">
      <div class="d-flex justify-content-start"><span class="mr-75" data-feather="alert-circle"></span><span>No results found.</span></div></a></li>
</ul>
<!-- END: Header-->
