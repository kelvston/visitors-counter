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

  <!-- favicons -->
  <link rel="apple-touch-icon" sizes="57x57" href="https://www.aru.ac.tz/site/images/icon/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="https://www.aru.ac.tz/site/images/icon/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="https://www.aru.ac.tz/site/images/icon/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="https://www.aru.ac.tz/site/images/icon/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="https://www.aru.ac.tz/site/images/icon/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="https://www.aru.ac.tz/site/images/icon/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="https://www.aru.ac.tz/site/images/icon/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="https://www.aru.ac.tz/site/images/icon/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.aru.ac.tz/site/images/icon/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192" href="https://www.aru.ac.tz/site/images/icon/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.aru.ac.tz/site/images/icon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="https://www.aru.ac.tz/site/images/icon/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.aru.ac.tz/site/images/icon/favicon-16x16.png">

  <style media="screen">
    p img {
      width: 100%;
    }
  </style>
  <link rel="stylesheet" href="https://www.aru.ac.tz/site/css/master.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap">

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

.btn-link {
    font-size: 16px; /* Ensure button text matches link text */
    color: #007bff; /* Match the color with other links */
    text-decoration: none; /* Remove underline from button text */
}

    .relative {
      position: relative;
    }

    .absolute {
      position: absolute;
      top: 100%;
      right: 0;
      z-index: 10;
      display: none;
    }

    .relative:hover .absolute {
      display: block;
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
                <li class='nav-item'>
                    <a class='nav-link' target='_blank' href='<?php echo e(route('login')); ?>'>Report</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link' href='<?php echo e(route('counter.report')); ?>'>Home</a>
                </li>
                <li class='nav-item relative'>
                    <a class='nav-link'><?php echo e(Auth::user()->name); ?></a>
                </li>
                <li class='nav-item'>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-link nav-link"><?php echo e(__('Log Out')); ?></button>
                    </form>
                </li>
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
        <?php echo $__env->yieldContent('content'); ?>
      </div>

      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <?php echo $__env->yieldPushContent('scripts'); ?>
      <script>
        document.querySelector('.relative').addEventListener('click', function () {
          var dropdown = this.querySelector('.absolute');
          dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });
      </script>
    </header>
  </div>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\visitor-counter\resources\views/layouts/header.blade.php ENDPATH**/ ?>