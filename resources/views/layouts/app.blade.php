<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CBP') }} @yield('pageTitle')</title>

    <!-- Styles -->
    <link href="{{ asset('plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- chartist CSS -->
    <link href="{{ asset('plugins/chartist-js/dist/chartist.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/chartist-js/dist/chartist-init.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/css-chart/css-chart.css') }}" rel="stylesheet">
    <!--This page css - Morris CSS -->
    <link href="{{ asset('plugins/c3-master/c3.min.css') }}" rel="stylesheet">
    <!-- Vector CSS -->
    <link href="{{ asset('plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet" />
    <!-- dropify -->
    <link rel="stylesheet" href="{{ asset('plugins/dropify/dist/css/dropify.min.css') }}">


    <link href="{{ asset('plugins/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
    
    <link href="{{ asset('plugins/bootstrap-switch/bootstrap-switch.min.css') }}" rel="stylesheet">

    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">

    <link href="{{ asset('css/fixed-table.css') }}" rel="stylesheet">

    <!-- <link href="{{ asset('css/breakingNews.css') }}" rel="stylesheet"> -->

    <!-- <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.dataTables.min.css" rel="stylesheet"> -->
    <!-- toast CSS -->



    <link href="{{ asset('modern-ticker/css/modern-ticker.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('modern-ticker/themes/theme1.css') }}" type="text/css" rel="stylesheet">
    <!-- <script src="modern-ticker/js/jquery-3.2.1.min.js" type="text/javascript"></script> -->

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">


    <!-- this is temporary, need to do it proper -->
    <!-- <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:400,500,700,400italic|Material+Icons"> -->

    <!--    
    <link rel="stylesheet" href="https://unpkg.com/vue-material@beta/dist/vue-material.min.css">
    <link rel="stylesheet" href="https://unpkg.com/vue-material@beta/dist/theme/default.css"> 
    -->
    <!-- end: this is temporary, need to do it proper -->

    <script>
   
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
            'apiToken' => auth()->check() ? auth()->user()->api_token : '',
            'stripeKey' => env('STRIPE_KEY', ''),
            'accountSetupStatus' => @Auth::User()->account_setup,
            'newlyRegistered' => session()->has('newlyRegistered') ? true : false
            ]) !!};
    
    </script>
    <script>
        window.abilities = <?php echo json_encode(['user_access' => empty($access)? null:$access]) ?>;
    </script>    
    <style>

    </style>
    <script src="https://js.stripe.com/v3/"></script>
    <!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->
</head>
<body class="fix-header fix-sidebar card-no-border">

    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> 
        </svg>
    </div>

    <div id="app">
        @yield('pre_content')
        
        <?php if (Auth::User() && @Auth::User()->email_verified_at == null): ?>
            @yield('verification')
        <?php elseif (Auth::User()): ?>

            <?php if(Auth::User() && @Auth::User()->sender_id == null): ?>
                <profileregistration ></profileregistration>
            <?php else: ?>

                <topnav></topnav>   
                <leftnav></leftnav>   
                <contentbody></contentbody> 
            <?php endif ?>
        <?php else: ?>

            @yield('content1')
            @yield('content')
            
        <?php endif ?>
           
    </div>
    

    <!-- Google Address -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCKTWOfQ7YI1K8ldPmt6ynRXiwI8qSvQbY&libraries=places"></script>
    
    <!-- All Jquery -->
    <script src="{{ asset('js/app.js') }}"></script>

    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
            
    <!-- ============================================================== -->
    <script src="{{ asset('js/moment.js') }}"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="{{ asset('plugins/bootstrap/js/tether.min.js') }} "></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.min.js') }} "></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
    <!--Wave Effects -->
    <script src="{{ asset('js/waves.js') }}"></script>
    <!--Menu sidebar -->
    <script src="{{ asset('js/sidebarmenu.js') }}"></script>
    <!--stickey kit -->
    <script src="{{ asset('plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
    <script src="{{ asset('plugins/sparkline/jquery.sparkline.min.js') }}"></script>

    <!-- sweet alert -->
    <script src="{{ asset('plugins/sweetalert/sweetalert.min.js') }}"></script>

    <!-- DROPIFY -->
    <script src="{{ asset('plugins/dropify/dist/js/dropify.min.js') }}"></script>

    <script src="{{ asset('js/fixed-table.js') }}"></script>

    <script src="{{ asset('js/jQuery.blockUI.js') }}"></script>

    <!-- <script src="{{ asset('js/breakingNews.js') }}"></script> -->

    <script src="{{ asset('modern-ticker/js/jquery.modern-ticker.min.js') }}" type="text/javascript"></script>

    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->
    <!-- chartist chart -->
    {{--<script src="{{ asset('plugins/chartist-js/dist/chartist.min.js') }}"></script>--}}
    {{--<script src="{{ asset('plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js') }}"></script>--}}
    {{--<!--c3 JavaScript -->--}}
    {{--<script src="{{ asset('plugins/d3/d3.min.js') }}"></script>--}}
    {{--<script src="{{ asset('plugins/c3-master/c3.min.js') }}"></script>--}}

    <!-- switch -->
    <!-- <script src="{{ asset('plugins/bootstrap-switch/bootstrap-switch.min.js') }}"></script> -->
    

    <!-- Vector map JavaScript -->

    {{--<script src="{{ asset('js/dashboard2.js') }}"></script>--}}

    <!-- ============================================================== -->
    <!-- Style switcher -->
    <!-- ============================================================== -->
    <script src="{{ asset('plugins/toast-master/js/jquery.toast.js') }}"></script>
    <!--Custom JavaScript -->
    <script src="{{ asset('js/custom.min.js') }}"></script>
    
    <script src="{{ asset('js/tabletojson.min.js') }}"></script>

    <!-- temporary -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.9/vue.min.js" type="text/javascript"></script>
    <script src="https://unpkg.com/gijgo@1.9.11/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.11/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <!-- end: temporary -->

<!--     <script>
        $(".bt-switch input[type='checkbox'], .bt-switch input[type='radio']").bootstrapSwitch();
    </script> -->

    <!-- <script>
        $(".bt-switch input[type='checkbox']").bootstrapSwitch();
    </script> -->


    <script>
    $(document).ready(function() {
        // Basic
        $('.dropify').dropify();

        // Used events
        // var drEvent = $('#input-file-events').dropify();

        // drEvent.on('dropify.beforeClear', function(event, element) {
        //     return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
        // });

        // drEvent.on('dropify.afterClear', function(event, element) {
        //     alert('File deleted');
        // });

        // drEvent.on('dropify.errors', function(event, element) {
        //     console.log('Has Errors');
        // });

        // var drDestroy = $('#input-file-to-destroy').dropify();
        // drDestroy = drDestroy.data('dropify')
        // $('#toggleDropify').on('click', function(e) {
        //     e.preventDefault();
        //     if (drDestroy.isDropified()) {
        //         drDestroy.destroy();
        //     } else {
        //         drDestroy.init();
        //     }
        // })

    
    });
 

    </script>

</body>
</html>
