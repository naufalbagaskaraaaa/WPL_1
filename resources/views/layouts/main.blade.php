<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->

    @include('layouts.header')
    
    <!-- plugins:css -->

    @include('layouts.style-global')
    @yield('style-page')
    
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
  </head>
  <body>
    <div class="container-scroller">
      
      <!-- partial:partials/_navbar.html -->
      
      @include('layouts.navbar')

      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        
        @include('layouts.sidebar')

        <!-- partial -->
        <div class="main-panel">
          
          <div class="content-wrapper">
            @yield('content')
          </div>
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
          
          @include('layouts.footer')

          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
     @include('layouts.javascript-global')
     @yield('script-page')
    
    <!-- End custom js for this page -->
  </body>
</html>