<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="Content-Language" content="en-us">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Ardhi University | Chuo Kikuu cha Ardhi">
  <meta name="keywords" content=" , Chuo Kikuu cha Ardhi ">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
  <meta name="theme-color" content="#fff">
  <meta name="apple-mobile-web-app-status-bar-style" content="#ffffff">

  <title>ARDHI | Home</title>
  <style media="screen">
    p img {
      width: 100%;
    }
  </style>
      <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

      <!-- html2canvas and jsPDF -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
{{--    <link rel="stylesheet" href="{{ asset('css/ardhi/master.css') }}">--}}
    <!-- jQuery (necessary for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

      <link rel="stylesheet" href="https://www.aru.ac.tz/site/css/master.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap">
    <!-- Add this in the <head> section for Bootstrap CSS -->
{{--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">--}}

{{--    <!-- Add this before the closing </body> tag for Bootstrap JS -->--}}
{{--    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>--}}

  <style>
    .nav-link {
    font-size: 16px; /* Adjust the font size as needed */
    font-weight: 400; /* Adjust the font weight if necessary */
    text-decoration: none; /* Remove underline from links */
    color: #007bff; /* Default link color, adjust as needed */
}

.nav-link:hover {
    color: #0056b3; /* Color on hover, adjust as needed */
}

.nav-item {
    margin: 0 10px; /* Adjust the spacing between items */
}

.compact-entry-form {
    max-width: 760px;
    margin-inline: auto;
}

.compact-entry-form .mb-3 {
    margin-bottom: 0.75rem !important;
}

.compact-entry-form .form-label {
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
    font-weight: 500;
}

.compact-entry-form .form-control {
    padding: 0.4rem 0.65rem;
}

.attendee-options {
    max-height: 220px;
    overflow-y: auto;
    border: 1px solid #d5dce3;
    border-radius: 0.25rem;
    background: #fff;
}

.attendee-option {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 210px;
    align-items: center;
    gap: 0.75rem;
    padding: 0.4rem 0.6rem;
}

.attendee-option .form-select {
    width: 100%;
    min-width: 0;
}

.attendee-option + .attendee-option {
    border-top: 1px solid #edf0f2;
}

.attendee-person {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    min-width: 0;
    margin: 0;
    cursor: pointer;
}

.attendee-person .form-check-input {
    position: static !important;
    flex: 0 0 auto;
    margin: 0;
}

.attendee-person span {
    display: grid;
    min-width: 0;
}

.attendee-person small {
    overflow: hidden;
    color: #6c757d;
    text-overflow: ellipsis;
    white-space: nowrap;
}

@media (max-width: 575.98px) {
    .attendee-option {
        grid-template-columns: minmax(0, 1fr);
        gap: 0.35rem;
    }

    .attendee-option .form-select {
        margin-left: 1.4rem;
        width: calc(100% - 1.4rem);
    }
}

.top_menu .nav-item > .nav-link,
.top_menu .nav-item > form > .nav-link {
    display: inline-flex;
    align-items: center;
    min-height: 40px;
    padding: 0.55rem 0.75rem;
    border-radius: 0.35rem;
    color: #fff;
    transition: background-color 140ms ease, color 140ms ease;
}

.top_menu .nav-item > .nav-link:hover,
.top_menu .nav-item > form > .nav-link:hover {
    color: #fff;
    background-color: rgba(255, 255, 255, 0.16);
}

.top_menu .nav-item > .nav-link:focus-visible,
.top_menu .nav-item > form > .nav-link:focus-visible {
    color: #fff;
    outline: 2px solid rgba(255, 255, 255, 0.9);
    outline-offset: 2px;
}

.navigation-dropdown .dropdown-menu {
    width: auto;
    min-width: 0;
    max-width: calc(100vw - 1rem);
    margin-top: 0;
    padding: 0.25rem 0.3rem;
    background-color: #80902d;
    border: 1px solid rgba(255, 255, 255, 0.28);
    box-shadow: 0 0.45rem 1rem rgba(24, 35, 8, 0.22);
}

.publication-dropdown > .dropdown-menu,
.publication-submenu > .dropdown-menu,
.settings-dropdown > .dropdown-menu,
.user-dropdown > .dropdown-menu {
    border-radius: 0;
}

.navigation-dropdown .dropdown-menu > li {
    display: block;
    width: auto;
    text-align: left;
}

.navigation-dropdown .dropdown-item {
    display: block;
    width: 100%;
    white-space: nowrap;
    padding: 0.4rem 0.55rem;
    border-radius: 0.2rem;
    color: #fff;
    font-size: 0.9rem;
    font-weight: 500;
    line-height: 1.25;
    text-align: left;
    transition: background-color 140ms ease, color 140ms ease;
}

.navigation-dropdown .dropdown-item:hover,
.navigation-dropdown .dropdown-item:focus {
    color: #fff;
    background-color: rgba(255, 255, 255, 0.16);
}

.navigation-dropdown .dropdown-item:focus-visible {
    outline: 2px solid rgba(255, 255, 255, 0.9);
    outline-offset: -3px;
}

.publication-submenu {
    position: relative;
}

.publication-submenu > .dropdown-menu {
    top: 0;
    left: 100%;
    margin-top: -0.45rem;
    margin-left: 0;
}

.publication-submenu > .dropdown-menu:not([hidden]) {
    display: block;
}

.publication-submenu > .dropdown-toggle::after {
    float: right;
    margin-top: 0.55rem;
}

.btn-link {
    font-size: 16px; /* Ensure button text matches link text */
    color: #007bff; /* Match the color with other links */
    text-decoration: none; /* Remove underline from button text */
}

        .event-main-div {
      border: 1px solid #D55E1C;
    }

    .event-main-title {
      border-bottom: 1px solid #D55E1C;
    }

    .event-main-title a:hover {
      color: #ddd !important;
    }

    .event-div:hover {
      background-color: #f5f5f5;
    }

    .project-main-div {
      border: 1px solid #2752A1;
    }

    .project-main-title {
      border-bottom: 1px solid #2752A1;
    }

    .project-main-title a:hover {
      color: #ddd !important;
    }

    .project-div:hover {
      background-color: #f5f5f5;
    }
    #newsletterTable {
        width: 100%;
        table-layout: fixed; /* Ensures columns have a fixed width and don't resize */
        border-collapse: collapse; /* Prevents borders from affecting the layout */
    }

    #newsletterTable th, #newsletterTable td {
        text-align: center;
        padding: 8px;
        overflow: hidden;
        text-overflow: ellipsis; /* In case the content overflows, it will show '...' */
    }

    #newsletterTable th {
        background-color: #f2f2f2; /* Optional: Makes header stand out */
    }

    .container .card {
      border-radius: 0.85rem;
      box-shadow:
        0 2px 5px rgba(0, 0, 0, 0.06),
        0 10px 24px rgba(0, 0, 0, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.55) !important;
      transition: box-shadow 220ms ease;
    }

    .container .card .card-header {
      padding: 0.9rem 1.1rem;
      font-weight: 600;
    }

    .container .card .card-body {
      padding: 1.25rem;
    }

    .container .card .table th,
    .container .card .table td {
      vertical-align: middle;
    }

    .container .card button,
    .container .card .btn {
      transition: box-shadow 180ms ease;
    }

    .container .card button:hover,
    .container .card .btn:hover {
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.14);
    }

    @media (hover: hover) and (pointer: fine) {
      .container .card:hover {
        box-shadow:
          0 5px 10px rgba(0, 0, 0, 0.07),
          0 20px 38px rgba(0, 0, 0, 0.14),
          inset 0 1px 0 rgba(255, 255, 255, 0.55) !important;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .container .card,
      .container .card button,
      .container .card .btn {
        transition: none;
      }
    }

  </style>
</head>

<body>
  <div class="col-12 px-0">
    <!-- HEADER -->
    <header class="col-12 px-0">

      <!-- top navbar -->
      <div class="col-12 px-0">
        <div class="row top_nav">
          <div class="top_menu d-flex">
            <ul class="list-inline mx-auto justify-content-center">
                @if(Auth::check())
                    <li class='nav-item'>
                        <a class='nav-link'  href='{{ route('visitor_stats') }}'>Report</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' href='{{ route('news_leter') }}'>Newsletters</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' href='{{ route('counter.report') }}'>Visitors</a>
                    </li>
                    <li class="nav-item dropdown publication-dropdown navigation-dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="publicationDropdown" role="button"
                           data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">Publication</a>
                        <ul class="dropdown-menu" aria-labelledby="publicationDropdown">
                            @if(Auth::user()->isAdmin())
                                <li class="publication-submenu">
                                    <button class="dropdown-item dropdown-toggle" type="button"
                                            data-publication-submenu="administrationSubmenu" aria-expanded="false">
                                        Administration
                                    </button>
                                    <ul class="dropdown-menu" id="administrationSubmenu" hidden>
                                        <li><a class="dropdown-item" href="/barcode">Barcode</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.meetings.index') }}">Meeting Management</a></li>
                                    </ul>
                                </li>
                            @endif
                            @if(Auth::user()->isAcademicStaff() || Auth::user()->isAdmin())
                                <li class="publication-submenu">
                                    <button class="dropdown-item dropdown-toggle" type="button"
                                            data-publication-submenu="academicsSubmenu" aria-expanded="false">
                                        Academics
                                    </button>
                                    <ul class="dropdown-menu" id="academicsSubmenu" hidden>
                                        <li><a class="dropdown-item" href="{{ route('proposal') }}">Proposal Management</a></li>
                                        <li><a class="dropdown-item" href="{{ route('academics.meetings.index') }}">Minutes Management</a></li>
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </li>
                    <li class="nav-item dropdown settings-dropdown navigation-dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button"
                           data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">Settings</a>
                        <ul class="dropdown-menu" aria-labelledby="settingsDropdown">
                            <li><a class="dropdown-item" href="{{ route('chatbot.training.index') }}">Train Chatbot</a></li>
                            @if(Auth::user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">Manage Users</a></li>
                            @endif
                        </ul>
                    </li>
                    <li class="nav-item dropdown user-dropdown navigation-dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="userDropdown">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">{{ __('Log Out') }}</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <script>
                        // Redirect to the login page
                        window.location.href = '{{ route('login') }}';
                    </script>
                @endif

            </ul>

          </div>
        </div>
      </div>
      <!-- /top navbar -->

      <!-- top middle -->
      <div class="col-12 px-0 pb-1">
        <div class="container mt-0 top-middle">
          <div class="row ">
            <div class="col-md-2 col-sm-2 col-xs-2 float-left text-left">
              <a href="https://www.aru.ac.tz"><img src="https://www.aru.ac.tz/site/images/emblem.png" alt="emblem" class="emblem mx-auto img-fluid"></a>
            </div>

            <div class="col-md-8 col-sm-8 col-xs-8 text-center pt-3 my-auto pt-xs-0">
              <h1 class="mb-0 title">
                ARDHI UNIVERSITY LIBRARY VCS
              </h1>
            </div>

            <div class="col-md-2 col-sm-2 col-xs-2 text-right client-logo">
              <a href="https://www.aru.ac.tz"><img src="https://www.aru.ac.tz/site/images/logo.jpg" alt="Logo" style="" class="img-fluid"></a>
            </div>
          </div>
        </div>
      </div>

      <div class="container mt-4">
        @yield('content')
      </div>

      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      @stack('scripts')
      <script>
        document.querySelectorAll('.navigation-dropdown').forEach(function (dropdown) {
          var toggle = dropdown.querySelector(':scope > .dropdown-toggle');
          var menu = dropdown.querySelector(':scope > .dropdown-menu');
          var closeTimer;

          dropdown.addEventListener('mouseenter', function () {
            window.clearTimeout(closeTimer);
            menu.classList.add('show');
            toggle.setAttribute('aria-expanded', 'true');
          });

          dropdown.addEventListener('mouseleave', function () {
            closeTimer = window.setTimeout(function () {
              if (dropdown.matches(':hover')) {
                return;
              }

              menu.classList.remove('show');
              toggle.setAttribute('aria-expanded', 'false');
              dropdown.querySelectorAll('.publication-submenu .dropdown-menu').forEach(function (submenu) {
                submenu.hidden = true;
                submenu.previousElementSibling.setAttribute('aria-expanded', 'false');
              });
            }, 200);
          });
        });

        document.querySelectorAll('[data-publication-submenu]').forEach(function (toggle) {
          var submenuItem = toggle.closest('.publication-submenu');
          var submenu = document.getElementById(toggle.dataset.publicationSubmenu);
          var submenuCloseTimer;

          submenuItem.addEventListener('mouseenter', function () {
            window.clearTimeout(submenuCloseTimer);
            document.querySelectorAll('.publication-submenu .dropdown-menu').forEach(function (menu) {
              menu.hidden = true;
              menu.previousElementSibling.setAttribute('aria-expanded', 'false');
            });

            submenu.hidden = false;
            toggle.setAttribute('aria-expanded', 'true');
          });

          submenuItem.addEventListener('mouseleave', function () {
            submenuCloseTimer = window.setTimeout(function () {
              if (submenuItem.matches(':hover')) {
                return;
              }

              submenu.hidden = true;
              toggle.setAttribute('aria-expanded', 'false');
            }, 200);
          });

          toggle.addEventListener('click', function (event) {
            event.stopPropagation();
            var shouldOpen = submenu.hidden || submenuItem.matches(':hover');

            document.querySelectorAll('.publication-submenu .dropdown-menu').forEach(function (menu) {
              menu.hidden = true;
              menu.previousElementSibling.setAttribute('aria-expanded', 'false');
            });

            submenu.hidden = !shouldOpen;
            toggle.setAttribute('aria-expanded', String(shouldOpen));
          });
        });
      </script>
    </header>
  </div>
</body>

</html>
