<!DOCTYPE html>
<html lang="en">
    @include('includes.head')
    <body>    	
        @include('includes.header')
        @include('includes.sidebar')
        <!-- <div class="container-fluid time-table"> -->
            @yield('content')        
        <!-- </div> -->
        @include('includes.footer')    
   
    </body>
</html>