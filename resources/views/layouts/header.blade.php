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
  <link rel="icon" type="image/png" href="{{ asset('images/2.png') }}?v=aru">

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

.top_menu .mail-nav-link {
    position: relative;
    justify-content: center;
    min-width: 44px;
}

.mail-nav-link svg {
    width: 21px;
    height: 21px;
}

.mail-nav-count {
    position: absolute;
    top: 1px;
    right: 1px;
    min-width: 17px;
    padding: 0.1rem 0.28rem;
    border: 1px solid #80902d;
    border-radius: 999px;
    background: #d93025;
    color: #fff;
    font-size: 0.65rem;
    font-weight: 700;
    line-height: 1.15;
    text-align: center;
}

.mail-page {
    max-width: 1280px;
    padding-bottom: 2rem;
}

.mail-workspace {
    display: grid;
    grid-template-columns: 220px minmax(0, 1fr);
    gap: 1.5rem;
    align-items: start;
}

.mail-sidebar {
    position: sticky;
    top: 1rem;
    padding: 1rem 0.75rem;
    border: 1px solid #e2e6d9;
    border-radius: 0.7rem;
    background: #fff;
    box-shadow: 0 0.4rem 1.1rem rgba(37, 48, 10, 0.05);
}

.mail-folder-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.65rem 0.8rem;
    border-radius: 0.45rem;
    color: #3a4219;
    font-weight: 500;
    text-decoration: none;
}

.mail-folder-link:hover,
.mail-folder-link.active {
    background: #f0f3e5;
    color: #35410c;
}

.mail-folder-link.active {
    font-weight: 700;
}

.mail-main {
    min-width: 0;
}

.mail-page-heading {
    color: #27310b;
}

.mail-toolbar {
    border-bottom: 2px solid #e8ebdf;
}

.mail-tabs .nav-link {
    color: #59622e;
    font-weight: 600;
}

.mail-tabs .nav-link.active {
    color: #35410c;
    border-color: #e4e8d8 #e4e8d8 #fff;
}

.mail-list {
    overflow: hidden;
    border: 1px solid #e2e6d9;
    border-radius: 0.7rem;
    box-shadow: 0 0.4rem 1.1rem rgba(37, 48, 10, 0.06);
    background: #fff;
}

.mail-list-item {
    display: grid;
    gap: 0.3rem;
    padding: 0.95rem 1.15rem;
    border: 0;
    border-bottom: 1px solid #edf0e8;
    color: #293014;
}

.mail-list-item:last-child {
    border-bottom: 0;
}

.mail-list-item:hover {
    background: #f7f9f2;
}

.mail-list-item-unread {
    background: #f4f7e9;
    box-shadow: inset 4px 0 #80902d;
}

.mail-subject-link {
    color: #303b0d;
    font-weight: 700;
    text-decoration: none;
}

.mail-subject-link:hover {
    color: #64751c;
    text-decoration: underline;
}

.mail-list-meta,
.mail-list-footer {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 0.35rem 0.8rem;
    color: #68704b;
    font-size: 0.84rem;
}

.mail-list-meta time {
    white-space: nowrap;
}

.mail-message-preview {
    display: block;
    overflow: hidden;
    color: #717761;
    text-decoration: none;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.mail-empty-state {
    display: grid;
    min-height: 260px;
    place-content: center;
    gap: 0.4rem;
    padding: 2rem;
    text-align: center;
}

.mail-empty-icon {
    color: #a4ae7f;
    font-size: 2.5rem;
}

.mail-label {
    padding: 0.15rem 0.45rem;
    border-radius: 0.25rem;
    background: #f1f2eb;
    color: #68704b;
    font-size: 0.72rem;
    font-weight: 700;
}

.mail-attachment-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    max-width: 100%;
    padding: 0.35rem 0.6rem;
    border: 1px solid #e2e6d9;
    border-radius: 0.4rem;
    background: #fff;
    color: #39420f;
    overflow-wrap: anywhere;
    flex-wrap: wrap;
    font-size: 0.85rem;
}

.mail-attachment-chip a {
    color: #52671a;
    font-weight: 600;
    white-space: nowrap;
}

.mail-message-card {
    overflow: hidden;
    border: 1px solid #e4e8dc;
    border-radius: 0.65rem;
}

.mail-message-card-outgoing {
    border-left: 4px solid #80902d;
}

.mail-message-card-incoming {
    border-left: 4px solid #bcc4a3;
}

.mail-message-card .card-header {
    background: #f7f8f3;
}

.mail-recipient-details {
    display: grid;
    gap: 0.15rem;
    margin-top: 0.25rem;
    color: #68704b;
    font-size: 0.8rem;
    font-weight: 400;
}

.mail-message-body {
    overflow-wrap: anywhere;
    white-space: pre-wrap;
}

.mail-message-body > :last-child {
    margin-bottom: 0;
}

.mail-copy-fields {
    padding: 0.75rem 0.9rem;
    border: 1px solid #e2e6d9;
    border-radius: 0.5rem;
    background: #fbfcf8;
}

.mail-copy-fields summary {
    color: #52671a;
    cursor: pointer;
    font-weight: 600;
}

.mail-preview-frame {
    display: block;
    width: 100%;
    height: min(70vh, 650px);
    border: 1px solid #e2e6d9;
    border-radius: 0.4rem;
    background: #f7f8f3;
}

.mail-preview-image {
    display: block;
    max-width: 100%;
    max-height: 650px;
    margin-top: 0.75rem;
    border: 1px solid #e2e6d9;
    border-radius: 0.4rem;
    object-fit: contain;
}

.mail-compose-card {
    gap: 0;
    padding: clamp(0.85rem, 1.6vw, 1.25rem);
    border: 1px solid #e2e6d9;
    border-radius: 0.8rem;
    background: linear-gradient(180deg, #fff 0%, #fdfefb 100%);
    box-shadow: 0 0.5rem 1.4rem rgba(37, 48, 10, 0.08);
}

.mail-compose-title-icon {
    float: left;
    display: grid;
    width: 42px;
    height: 42px;
    margin: 0.1rem 0.85rem 0 0;
    place-items: center;
    border-radius: 0.65rem;
    background: #eef2e2;
    color: #64751c;
}

.mail-compose-title-icon svg {
    width: 23px;
    height: 23px;
}

.mail-compose-main .mail-compose-field {
    margin-bottom: 0.75rem;
}

.mail-compose-main .mail-compose-field > .form-label,
.mail-recipient-picker > .form-label,
.mail-recipient-picker .form-label {
    margin-bottom: 0.3rem;
    color: #39421b;
    font-weight: 650;
}

.mail-compose-main .mail-compose-card .form-control,
.mail-compose-main .mail-compose-card .form-select {
    min-height: 38px;
    border-color: #dce2d2;
    border-radius: 0.48rem;
    background-color: #fff;
}

.mail-compose-main .mail-compose-card .form-control:focus,
.mail-compose-main .mail-compose-card .form-select:focus {
    border-color: #9da96f;
    box-shadow: 0 0 0 0.2rem rgba(128, 144, 45, 0.14);
}

.mail-recipient-picker {
    padding: 0.6rem 0.7rem;
    border: 1px solid #e3e8da;
    border-radius: 0.58rem;
    background: #fff;
}

.mail-recipient-count {
    color: #70775a;
    font-size: 0.78rem;
}

.mail-recipient-search-wrap {
    position: relative;
}

.mail-recipient-search {
    min-height: 35px !important;
    font-size: 0.84rem;
}

.mail-recipient-options {
    position: absolute;
    z-index: 20;
    top: calc(100% + 0.25rem);
    right: 0;
    left: 0;
    max-height: 190px;
    overflow-y: auto;
    border: 1px solid #edf0e8;
    border-radius: 0.45rem;
    background: #fcfdf9;
    box-shadow: 0 0.5rem 1rem rgba(37, 48, 10, 0.14);
}

.mail-recipient-options[hidden],
.mail-recipient-option[hidden],
.mail-recipient-no-results[hidden] {
    display: none !important;
}

.mail-recipient-selected {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
    margin-bottom: 0.3rem;
}

.mail-recipient-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    max-width: 100%;
    padding: 0.12rem 0.4rem 0.12rem 0.55rem;
    border: 1px solid #dfe5d2;
    border-radius: 1rem;
    background: #f3f6eb;
    color: #435316;
    font-size: 0.75rem;
}

.mail-recipient-chip > span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.mail-recipient-chip button {
    padding: 0;
    border: 0;
    background: transparent;
    color: #596a19;
    font-size: 1rem;
    line-height: 1;
}

.mail-recipient-option {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    min-width: 0;
    padding: 0.4rem 0.6rem;
    cursor: pointer;
    transition: background-color 120ms ease;
}

.mail-recipient-option + .mail-recipient-option {
    border-top: 1px solid #f0f2ec;
}

.mail-recipient-option:hover {
    background: #f3f6eb;
}

.mail-recipient-option .form-check-input {
    position: static !important;
    flex: 0 0 auto;
    margin: 0;
}

.mail-recipient-identity {
    display: grid;
    min-width: 0;
    line-height: 1.25;
}

.mail-recipient-identity strong,
.mail-recipient-identity small {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.mail-recipient-identity strong {
    color: #31391b;
    font-size: 0.84rem;
}

.mail-recipient-identity small {
    color: #7a806b;
    font-size: 0.74rem;
}

.mail-recipient-option-disabled {
    opacity: 0.42;
    cursor: not-allowed;
}

.mail-recipient-no-results {
    margin: 0;
    padding: 0.75rem;
    color: #777e64;
    font-size: 0.82rem;
    text-align: center;
}

.mail-copy-fields {
    margin-bottom: 0.7rem;
}

.mail-copy-fields summary {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.8rem;
    padding: 0.55rem 0.7rem;
}

.mail-copy-fields summary small {
    color: #858b75;
    font-weight: 400;
}

.mail-copy-fields[open] {
    padding-bottom: 0.85rem;
}

.mail-copy-fields[open] > summary {
    border-bottom: 1px solid #e7ebdf;
}

.mail-compose-card .ck.ck-editor {
    width: 100%;
}

.mail-editor-validation-error {
    display: block;
}

.mail-editor-validation-error[hidden] {
    display: none !important;
}

.mail-compose-card .ck.ck-toolbar {
    border-color: #dce2d2;
    border-radius: 0.48rem 0.48rem 0 0;
    background: #f7f9f3;
}

.mail-compose-card .ck.ck-editor__main > .ck-editor__editable {
    min-height: 155px;
    border-color: #dce2d2;
    border-radius: 0 0 0.48rem 0.48rem;
    box-shadow: none;
}

.mail-attachment-dropzone {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0.55rem 0.7rem;
    border: 1px dashed #c9d1b3;
    border-radius: 0.55rem;
    background: #fbfcf8;
    color: #5e6646;
    cursor: pointer;
    transition: border-color 140ms ease, background-color 140ms ease;
}

.mail-attachment-dropzone:hover {
    border-color: #80902d;
    background: #f5f7ed;
}

.mail-attachment-icon {
    display: grid;
    width: 32px;
    height: 32px;
    flex: 0 0 auto;
    place-items: center;
    border-radius: 0.5rem;
    background: #edf1e2;
    color: #64751c;
}

.mail-attachment-icon svg {
    width: 22px;
    height: 22px;
}

.mail-attachment-dropzone strong {
    color: #52671a;
}

.mail-attachment-dropzone small {
    display: block;
    margin-top: 0.16rem;
    color: #858b75;
}

.mail-attachment-dropzone input {
    position: absolute;
    width: 1px;
    height: 1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    clip-path: inset(50%);
}

.mail-attachment-dropzone:focus-within {
    outline: 2px solid #80902d;
    outline-offset: 2px;
}

.mail-attachment-filename {
    display: block;
    margin-top: 0.5rem;
    color: #52671a;
    font-size: 0.84rem;
    overflow-wrap: anywhere;
}

.mail-compose-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    padding-top: 0.65rem;
    border-top: 1px solid #edf0e8;
}

.mail-send-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding-inline: 1rem;
}

.mail-send-button svg {
    width: 17px;
    height: 17px;
}

@media (max-width: 575.98px) {
    .mail-workspace {
        grid-template-columns: minmax(0, 1fr);
        gap: 1rem;
    }

    .mail-sidebar {
        position: static;
        padding: 0.75rem;
    }

    .mail-sidebar .nav {
        flex-direction: row !important;
        gap: 0.3rem;
    }

    .mail-folder-link {
        flex: 1;
    }

    .mail-copy-fields summary {
        align-items: flex-start;
        flex-direction: column;
        gap: 0.15rem;
    }

    .mail-compose-card .ck.ck-toolbar {
        overflow-x: auto;
    }

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
  @stack('styles')
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
                    @php($unreadMailCount = Auth::user()->mailRecipientCopies()->whereNull('read_at')->count())
                    <li class='nav-item'>
                        <a class='nav-link mail-nav-link' href='{{ route('staff-mail.inbox') }}'
                           aria-label="Mail{{ $unreadMailCount ? ', '.$unreadMailCount.' unread' : '' }}"
                           title="Mail{{ $unreadMailCount ? ' ('.$unreadMailCount.' unread)' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3.5 6.5h17v12h-17z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            @if ($unreadMailCount)
                                <span class="mail-nav-count">{{ $unreadMailCount > 99 ? '99+' : $unreadMailCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item dropdown publication-dropdown navigation-dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="publicationDropdown" role="button"
                           data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">Publication</a>
                        <ul class="dropdown-menu" aria-labelledby="publicationDropdown">
                            <li><a class="dropdown-item" href="{{ route('calendar.index') }}">Library Calendar</a></li>
                            <li><a class="dropdown-item" href="{{ route('proposal') }}">Proposal Management</a></li>
                            @if(Auth::user()->isAdmin()
                                || Auth::user()->staff_category === 'administrative'
                                || Auth::user()->canManageMinutesFor('administrative'))
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
                            @if(Auth::user()->staff_category === 'academic'
                                || Auth::user()->isAdmin()
                                || Auth::user()->canManageMinutesFor('academic'))
                                <li class="publication-submenu">
                                    <button class="dropdown-item dropdown-toggle" type="button"
                                            data-publication-submenu="academicsSubmenu" aria-expanded="false">
                                        Academics
                                    </button>
                                    <ul class="dropdown-menu" id="academicsSubmenu" hidden>
                                        <li><a class="dropdown-item" href="{{ route('academics.meetings.index') }}">Meeting Minutes Management</a></li>
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </li>
                    @if(Auth::user()->canAccessSettingsNavigation())
                        <li class="nav-item dropdown settings-dropdown navigation-dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button"
                               data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">Settings</a>
                            <ul class="dropdown-menu" aria-labelledby="settingsDropdown">
                                @if(Auth::user()->canTrainChatbot())
                                    <li><a class="dropdown-item" href="{{ route('chatbot.training.index') }}">Train Chatbot</a></li>
                                @endif
                                @if(Auth::user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">Manage Users</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
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
