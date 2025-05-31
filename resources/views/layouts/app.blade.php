<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="assets/images/favicon.svg" type="image/x-icon" />
    <title>NgaturUang</title>


    <!-- ========== All CSS files linkup ========= -->
    @include('partials.head')
    

  </head>
  <body>
    <!-- ======== Preloader =========== -->
    <div id="preloader">
      <div class="spinner"></div>
    </div>
    <!-- ======== Preloader =========== -->

    <!-- ======== Nav =========== -->
     @include('partials.nav')


    <!-- ======== main-wrapper start =========== -->
    <main class="main-wrapper">

      <!-- ========== header ========== -->
        @include('partials.header')

      <!-- ========== section ========== -->
       @yield('content')

      <!-- ========== footer =========== -->
      @include('partials.footer')
    </main>
    <!-- ======== main-wrapper end =========== -->

    <!-- ========= All Javascript files linkup ======== -->
    @include('partials.scripts')
  </body>
</html>
