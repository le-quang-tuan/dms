<!DOCTYPE html>
<html lang="en">
    @include('include.head')
    <body>    	
        <!-- @include('includes.header') -->
        @include('include.sidebar')
        <!-- <div class="container-fluid time-table"> -->
        @yield('content')        
        <!-- </div> -->
        @include('include.footer') 
   
    </body>
</html>