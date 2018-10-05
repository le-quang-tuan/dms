<!DOCTYPE html>
<html lang="en">
    @include('includes.headprinter')
    <body>
        @include('includes.header')        
        <!-- <div class="container-fluid time-table"> -->
            @yield('content')        
        <!-- </div> -->
        @include('includes.footer')        
    </body>
</html>