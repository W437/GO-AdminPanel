<!DOCTYPE html>
    <?php
            $site_direction = session()->get('site_direction');
            $country=\App\CentralLogics\Helpers::get_business_settings('country');
            $countryCode= strtolower($country??'auto');
    ?>

<html dir="{{ $site_direction }}" lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $site_direction === 'rtl'?'active':'' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Title -->
    <title>@yield('title')</title>
    <!-- Favicon -->
    @php($logo =\App\CentralLogics\Helpers::get_business_settings('icon'))
    <link rel="shortcut icon" href="">
    <link rel="icon" type="image/x-icon" href="{{ dynamicStorage('storage/app/public/business/' . $logo ?? '') }}">
    <!-- Font -->
    <link href="{{dynamicAsset('public/assets/admin/css/fonts.css')}}" rel="stylesheet">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin/css/vendor.min.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin/vendor/icon-set/style.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin/css/custom.css') }}">
    <!-- CSS Front Template -->
    <link  rel="stylesheet" href="{{dynamicAsset('/public/assets/admin/plugins/lightbox/css/lightbox.css')}}">

    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin/css/owl.min.css')}}">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin/css/emojionearea.min.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin/css/theme.minc619.css?v=1.0') }}">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin/css/style.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin/css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin/intltelinput/css/intlTelInput.css')}}">
    @stack('css_or_js')
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin/css/toastr.css') }}">

    <style>
        /* Preloader styling */
        #pre--loader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Hide preloader when loaded */
        body.loaded #pre--loader {
            display: none !important;
        }

        /* Smooth fade-in animation when page loads */
        @keyframes fadeInPage {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Apply fade-in to correct elements */
        body.loaded #content,
        body.loaded #header,
        body.loaded .navbar-vertical-aside {
            animation: fadeInPage 0.3s ease-in-out;
        }

        /* CSS-only fallback: ensure content is visible */
        #content,
        #header,
        .navbar-vertical-aside {
            opacity: 1 !important; /* Ensure visibility */
        }
    </style>
    <script>
        (function() {
            try {
                if (localStorage.getItem('go-admin-theme') === 'dark') {
                    document.documentElement.classList.add('theme-dark');
                }
            } catch (error) {
                console.warn('Theme initialization failed', error);
            }
        })();
    </script>
</head>

<body class="footer-offset">
<script>
    // Robust loading handler with multiple fallbacks
    (function() {
        'use strict';

        function addLoadedClass() {
            document.body.classList.add('loaded');
        }

        // Method 1: If page is from cache or already loaded
        if (document.readyState === 'complete') {
            addLoadedClass();
        }

        // Method 2: On DOMContentLoaded
        document.addEventListener('DOMContentLoaded', addLoadedClass);

        // Method 3: Fallback timeout (ensure visibility within 2 seconds)
        setTimeout(addLoadedClass, 2000);
    })();
</script>

    <div id="pre--loader" class="pre--loader">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div id="loading" class="initial-hidden">
                    <div class="loading--1">
                        <img width="200" src="{{ dynamicAsset('public/assets/admin/img/loader.gif') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Builder -->
    @include('layouts.admin.partials._front-settings')
    <!-- End Builder -->

    <!-- JS Preview mode only -->
    @include('layouts.admin.partials._header')
    @include('layouts.admin.partials._sidebar')
    <!-- END ONLY DEV -->

    <main id="content" role="main" class="main pointer-event">
        <!-- Content -->
        @yield('content')
        <!-- End Content -->

        <!-- Footer -->
        @include('layouts.admin.partials._footer')
        <!-- End Footer -->

        <div class="modal fade" id="popup-modal">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div  class="text-center">
                                    <h2 class="color-8a8a8a">
                                        <i class="tio-shopping-cart-outlined"></i> {{translate('messages.You_have_new_order_Check_Please.')}}
                                    </h2>
                                    <hr>
                                    <button class="btn btn-primary check-order">{{translate('messages.Ok_let_me_check')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="popup-modal-msg">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center">
                                    <h2 class="color-8a8a8a">
                                        <i class="tio-messages"></i> {{ translate('messages.message_description') }}
                                    </h2>
                                    <hr>
                                    <button
                                        class="btn btn-primary check-message">{{ translate('messages.Ok_let_me_check') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="toggle-modal">
            <div class="modal-dialog status-warning-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true" class="tio-clear"></span>
                        </button>
                    </div>
                    <div class="modal-body pb-5 pt-0">
                        <div class="max-349 mx-auto mb-20">
                            <div>
                                <div class="text-center">
                                    <img id="toggle-image" alt="" class="mb-20">
                                    <h5 class="modal-title font-medium mb-4" id="toggle-title"></h5>
                                </div>
                                <div class="text-center" id="toggle-message">
                                </div>
                            </div>
                            <div class="btn--container justify-content-center">
                                <button type="button" id="toggle-ok-button" class="btn btn--primary min-w-120 confirm-Toggle" data-dismiss="modal">{{translate('Ok')}}</button>
                                <button id="reset_btn" type="reset" class="btn btn--cancel min-w-120" data-dismiss="modal">
                                    {{translate("Cancel")}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="toggle-status-modal">
            <div class="modal-dialog status-warning-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true" class="tio-clear"></span>
                        </button>
                    </div>
                    <div class="modal-body pb-5 pt-0">
                        <div class="max-349 mx-auto mb-20">
                            <div>
                                <div class="text-center">
                                    <img id="toggle-status-image" alt="" class="mb-20">
                                    <h5 class="modal-title" id="toggle-status-title"></h5>
                                </div>
                                <div class="text-center" id="toggle-status-message">
                                </div>
                            </div>
                            <div class="btn--container justify-content-center">
                                <button type="button" id="toggle-status-ok-button" class="btn btn--primary min-w-120 confirm-Status-Toggle" data-dismiss="modal">{{translate('Ok')}}</button>
                                <button id="reset_btn" type="reset" class="btn btn--cancel min-w-120" data-dismiss="modal">
                                    {{translate("Cancel")}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="modal fade" id="new-dynamic-submit-model">
            <div class="modal-dialog modal-dialog-centered status-warning-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true" class="tio-clear"></span>
                        </button>
                    </div>
                    <div class="modal-body pb-5 pt-0">
                        <div class="max-349 mx-auto mb-20">
                            <div>
                                <div class="text-center">
                                    <img id="image-src" class="mb-20">
                                    <h5 class="modal-title" id="toggle-title"></h5>
                                </div>
                                <div class="text-center" id="toggle-message">
                                    <h3 id="modal-title"></h3>
                                    <div id="modal-text"></div>
                                </div>

                                </div>
                                <div class="mb-4 d-none" id="note-data">
                                    <textarea class="form-control" placeholder="{{ translate('your_note_here') }}" id="get-text-note" cols="5" ></textarea>
                                </div>
                            <div class="btn--container justify-content-center">
                                <div id="hide-buttons">
                                    <button data-dismiss="modal" id="cancel_btn_text" class="btn btn-outline-secondary min-w-120" >{{translate("Not_Now")}}</button> &nbsp;
                                    <button type="button" id="new-dynamic-ok-button" class="btn btn-outline-danger confirm-model min-w-120">{{translate('Yes')}}</button>
                                </div>

                                <button data-dismiss="modal"  type="button" id="new-dynamic-ok-button-show" class="btn btn--primary  d-none min-w-120">{{translate('Okay')}}</button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </main>
    <!-- ========== END MAIN CONTENT ========== -->

   <!-- ========== END SECONDARY CONTENTS ========== -->
   <script src="{{ dynamicAsset('public/assets/admin/js/custom.js') }}"></script>
   <!-- JS Implementing Plugins -->
   <!-- The core Firebase JS SDK is always required and must be listed first -->
    <script src="{{dynamicAsset('public/assets/admin/js/jquery.min.js')}}"></script>

    <script>
            "use strict";
    // Suppress jQuery Migrate console messages
    if (typeof jQuery !== 'undefined') {
        jQuery.migrateMute = true;
    }

    setTimeout(hide_loader, 1000);
    function hide_loader(){
        $('body').addClass('loaded');
        $('#pre--loader').hide(); // Properly hide the preloader
    }

    </script>
    <script>
        (function () {
            "use strict";

            var THEME_STORAGE_KEY = 'go-admin-theme';

            function getStoredTheme() {
                try {
                    return localStorage.getItem(THEME_STORAGE_KEY) || 'light';
                } catch (error) {
                    console.warn('Theme preference could not be read', error);
                    return 'light';
                }
            }

            function persistTheme(theme) {
                try {
                    localStorage.setItem(THEME_STORAGE_KEY, theme);
                } catch (error) {
                    console.warn('Theme preference could not be saved', error);
                }
            }

            function updateToggleState(theme) {
                var toggle = document.getElementById('themeToggle');
                if (!toggle) {
                    return;
                }
                var icon = toggle.querySelector('.theme-toggle__icon');
                toggle.setAttribute('aria-pressed', theme === 'dark');
                toggle.setAttribute('data-theme', theme);

                if (icon) {
                    icon.className = 'theme-toggle__icon ' + (theme === 'dark' ? 'tio-brightness-1' : 'tio-moon');
                }
            }

            function applyTheme(theme) {
                var root = document.documentElement;
                if (theme === 'dark') {
                    root.classList.add('theme-dark');
                } else {
                    root.classList.remove('theme-dark');
                }
                updateToggleState(theme);
            }

            document.addEventListener('DOMContentLoaded', function () {
                var currentTheme = getStoredTheme();
                applyTheme(currentTheme);

                var toggle = document.getElementById('themeToggle');
                if (toggle) {
                    toggle.addEventListener('click', function () {
                        var nextTheme = document.documentElement.classList.contains('theme-dark') ? 'light' : 'dark';
                        persistTheme(nextTheme);
                        applyTheme(nextTheme);
                    });
                }
            });
        })();
    </script>
    <script src="{{dynamicAsset('public/assets/admin/js/firebase.min.js')}}"></script>

   @stack('script')
   <!-- JS Front -->
   <script src="{{ dynamicAsset('public/assets/admin/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js') }}"></script>
   <script src="{{ dynamicAsset('public/assets/admin/js/vendor.min.js') }}"></script>
   <script src="{{ dynamicAsset('public/assets/admin/js/theme.min.js') }}"></script>
   <script src="{{ dynamicAsset('public/assets/admin/js/sweet_alert.js') }}"></script>
   <script src="{{ dynamicAsset('public/assets/admin/js/toastr.js') }}"></script>
   <script src="{{dynamicAsset('public/assets/admin/js/owl.min.js')}}"></script>
   <script src="{{dynamicAsset('public/assets/admin/intltelinput/js/intlTelInput.min.js')}}"></script>

    <script src="{{ dynamicAsset('/public/assets/admin/plugins/lightbox/js/lightbox.min.js')}}"></script>

   <script>
    "use strict";

       $('.blinkings').on('mouseover', ()=> $('.blinkings').removeClass('active'))
       $('.blinkings').addClass('open-shadow')
       setTimeout(() => {
           $('.blinkings').removeClass('active')
       }, 10000);
       setTimeout(() => {
           $('.blinkings').removeClass('open-shadow')
       }, 5000);

       $(function(){
           var owl = $('.single-item-slider');
           owl.owlCarousel({
               autoplay: false,
               items:1,
               onInitialized  : counter,
               onTranslated : counter,
               autoHeight: true,
               dots: true,
               rtl: {{ $site_direction == 'rtl'  ?  "true"  : "false"}}
           });

           function counter(event) {
               var element   = event.target;         // DOM element, in this example .owl-carousel
                   var items     = event.item.count;     // Number of items
                   var item      = event.item.index + 1;     // Position of the current item

               // it loop is true then reset counter from 1
               if(item > items) {
                   item = item - items
               }
               $('.slide-counter').html(+item+"/"+items)
           }
       });
   </script>
    {!! Toastr::message() !!}

    @if ($errors->any())
        <script>
            "use strict";
            @foreach ($errors->all() as $error)
                toastr.error('{{ translate($error) }}', Error, {
                    CloseButton: true,
                    ProgressBar: true
                });
            @endforeach
        </script>
    @endif

    <script>
"use strict";
        $(document).on('ready', function(){
            $(".direction-toggle").on("click", function () {
                if($('html').hasClass('active')){
                    $('html').removeClass('active')
                    setDirection(1);
                }else {
                    setDirection(0);
                    $('html').addClass('active')
                }
            });
            if ($('html').attr('dir') == "rtl") {
                $(".direction-toggle").find('span').text('Toggle LTR')
            } else {
                $(".direction-toggle").find('span').text('Toggle RTL')
            }

            function setDirection(status) {
                if (status === 1) {
                    $("html").attr('dir', 'ltr');
                    $(".direction-toggle").find('span').text('Toggle RTL')
                } else {
                    $("html").attr('dir', 'rtl');
                    $(".direction-toggle").find('span').text('Toggle LTR')
                }
                $.get({
                        url: '{{ route('admin.business-settings.site_direction') }}',
                        dataType: 'json',
                        data: {
                            status: status,
                        },
                        success: function() {
                        },

                    });
                }
            });


        $(document).on('ready', function() {

            if (window.localStorage.getItem('hs-builder-popover') === null) {
                $('#builderPopover').popover('show')
                    .on('shown.bs.popover', function() {
                        $('.popover').last().addClass('popover-dark')
                    });

                $(document).on('click', '#closeBuilderPopover', function() {
                    window.localStorage.setItem('hs-builder-popover', true);
                    $('#builderPopover').popover('dispose');
                });
            } else {
                $('#builderPopover').on('show.bs.popover', function() {
                    return false
                });
            }

            // BUILDER TOGGLE INVOKER
            // =======================================================
            $('.js-navbar-vertical-aside-toggle-invoker').click(function() {
                $('.js-navbar-vertical-aside-toggle-invoker i').tooltip('hide');
            });


            // INITIALIZATION OF NAVBAR VERTICAL NAVIGATION
            // =======================================================
            var sidebar = $('.js-navbar-vertical-aside').hsSideNav();


            // INITIALIZATION OF TOOLTIP IN NAVBAR VERTICAL MENU
            // =======================================================
            $('.js-nav-tooltip-link').tooltip({
                boundary: 'window'
            })

            $(".js-nav-tooltip-link").on("show.bs.tooltip", function(e) {
                if (!$("body").hasClass("navbar-vertical-aside-mini-mode")) {
                    return false;
                }
            });


            // INITIALIZATION OF UNFOLD
            // =======================================================
            $('.js-hs-unfold-invoker').each(function() {
                var unfold = new HSUnfold($(this)).init();
            });


            // INITIALIZATION OF FORM SEARCH
            // =======================================================
            $('.js-form-search').each(function() {
                new HSFormSearch($(this)).init()
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function() {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });


            // INITIALIZATION OF DATERANGEPICKER
            // =======================================================
            $('.js-daterangepicker').daterangepicker();

            $('.js-daterangepicker-times').daterangepicker({
                timePicker: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'M/DD hh:mm A'
                }
            });

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#js-daterangepicker-predefined .js-daterangepicker-predefined-preview').html(start.format(
                    'MMM D') + ' - ' + end.format('MMM D, YYYY'));
            }

            $('#js-daterangepicker-predefined').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, cb);

            cb(start, end);


            // INITIALIZATION OF CLIPBOARD
            // =======================================================
            $('.js-clipboard').each(function() {
                var clipboard = $.HSCore.components.HSClipboard.init(this);
            });
        });
    </script>

    @stack('script_2')
    <script>
        "use strict";
        let baseUrl = '{{ url('/') }}';
    </script>
    <script src="{{dynamicAsset('public/assets/admin/js/view-pages/common.js')}}"></script>
    <script src="{{dynamicAsset('public/assets/admin/js/keyword-highlighted.js')}}"></script>
    <audio id="myAudio">
        <source src="{{ dynamicAsset('public/assets/admin/sound/notification.mp3') }}" type="audio/mpeg">
    </audio>

    <script>
        "use strict";
        var audio = document.getElementById("myAudio");

        function playAudio() {
            audio.play();
        }

        function pauseAudio() {
            audio.pause();
        }

        $('.route-alert').on('click',function () {
            let route = $(this).data('url')
            let message = $(this).data('message')
            let title = $(this).data('title')
            let processing = $(this).data('processing')
            route_alert(route, message, title, processing);
        })
        function route_alert(route, message, title = "{{ translate('messages.are_you_sure') }}", processing = false) {
            if (processing) {
                Swal.fire({
                    title: title,
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#FC6A57',
                    cancelButtonText: "{{ translate('messages.Cancel') }}",
                    confirmButtonText: "{{ translate('messages.Submit') }}",
                    inputPlaceholder: "{{ translate('messages.Enter_processing_time') }}",
                    input: 'text',
                    html: message + '<br/>' + '<label>{{ translate('messages.Enter_Processing_time_in_minutes') }}</label>',
                    inputValue: processing,
                    preConfirm: (processing_time) => {
                        location.href = route + '&processing_time=' + processing_time;
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                })
            } else {
                Swal.fire({
                    title: title,
                    text: message,
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#FC6A57',
                    cancelButtonText: '{{ translate('messages.No') }}',
                    confirmButtonText: '{{ translate('messages.Yes') }}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        location.href = route;
                    }
                })

            }

        }

        $('.form-alert').on('click',function (){
            let id = $(this).data('id')
            let message = $(this).data('message')
            Swal.fire({
                title: '{{ translate('messages.Are you sure?') }}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: '{{ translate('messages.no') }}',
                confirmButtonText: '{{ translate('messages.Yes') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#'+id).submit()
                }
            })
        })

        @php($fcm_credentials = \App\CentralLogics\Helpers::get_business_settings('fcm_credentials'))
        var firebaseConfig = {
            apiKey: "{{isset($fcm_credentials['apiKey']) ? $fcm_credentials['apiKey'] : ''}}",
            authDomain: "{{isset($fcm_credentials['authDomain']) ? $fcm_credentials['authDomain'] : ''}}",
            projectId: "{{isset($fcm_credentials['projectId']) ? $fcm_credentials['projectId'] : ''}}",
            storageBucket: "{{isset($fcm_credentials['storageBucket']) ? $fcm_credentials['storageBucket'] : ''}}",
            messagingSenderId: "{{isset($fcm_credentials['messagingSenderId']) ? $fcm_credentials['messagingSenderId'] : ''}}",
            appId: "{{isset($fcm_credentials['appId']) ? $fcm_credentials['appId'] : ''}}",
            measurementId: "{{isset($fcm_credentials['measurementId']) ? $fcm_credentials['measurementId'] : ''}}"
        };
        @if (isset($fcm_credentials['apiKey']) && is_string($fcm_credentials['apiKey']) && strlen($fcm_credentials['apiKey'])  > 3 )
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();
        @endif

        function startFCM() {
            messaging
                .requestPermission()
                .then(function() {
                    return messaging.getToken();
                })
                .then(function(token) {
                    // console.log('FCM Token:', token);
                    // Send the token to your backend to subscribe to topic
                    subscribeTokenToBackend(token, 'admin_message');
                }).catch(function(error) {
                console.error('Error getting permission or token:', error);
            });
        }

        function subscribeTokenToBackend(token, topic) {
            fetch('{{url('/')}}/subscribeToTopic', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ token: token, topic: topic })
            }).then(response => {
                if (response.status < 200 || response.status >= 400) {
                    return response.text().then(text => {
                        throw new Error(`Error subscribing to topic: ${response.status} - ${text}`);
                    });
                }
                console.log(`Subscribed to "${topic}"`);
            }).catch(error => {
                console.error('Subscription error:', error);
            });
        }

        function getUrlParameter(sParam) {
            var sPageURL = window.location.search.substring(1);
            var sURLVariables = sPageURL.split('&');
            for (var i = 0; i < sURLVariables.length; i++) {
                var sParameterName = sURLVariables[i].split('=');
                if (sParameterName[0] == sParam) {
                    return sParameterName[1];
                }
            }
        }

        function conversationList() {
            var tab = getUrlParameter('tab');
            $.ajax({
                    url: "{{ route('admin.message.list') }}"+ '?tab=' + tab,
                    success: function(data) {
                        $('#conversation-list').empty();
                        $("#conversation-list").append(data.html);
                        var user_id = getUrlParameter('user');
                    $('.customer-list').removeClass('conv-active');
                    $('#customer-' + user_id).addClass('conv-active');
                    }
                })
        }

        function conversationView() {
            var conversation_id = getUrlParameter('conversation');
            var user_id = getUrlParameter('user');
            var url= '{{url('/')}}/admin/message/view/'+conversation_id+'/' + user_id;
            $.ajax({
                url: url,
                success: function(data) {
                    $('#view-conversation').html(data.view);
                }
            })
        }

        function vendorConversationView() {
            var conversation_id = getUrlParameter('conversation');
            var user_id = getUrlParameter('user');
            var url= '{{url('/')}}/admin/restaurant/message/'+conversation_id+'/' + user_id;
            $.ajax({
                url: url,
                success: function(data) {
                    $('#vendor-view-conversation').html(data.view);
                }
            })
        }

        function dmConversationView() {
            var conversation_id = getUrlParameter('conversation');
            var user_id = getUrlParameter('user');
            var url= '{{url('/')}}/admin/delivery-man/message/'+conversation_id+'/' + user_id;
            $.ajax({
                url: url,
                success: function(data) {
                    $('#dm-view-conversation').html(data.view);
                }
            })
        }
        @php($order_notification_type =\App\CentralLogics\Helpers::get_business_settings('order_notification_type') ?? 'firebase')
        var new_order_type='restaurant_order';

        @if (isset($fcm_credentials['apiKey']) && is_string($fcm_credentials['apiKey']) && strlen($fcm_credentials['apiKey'])  > 3 )

        messaging.onMessage(function(payload) {
            console.log(payload.data);
            if(payload.data.order_id && payload.data.type == "order_request"){
                @php($admin_order_notification = \App\CentralLogics\Helpers::get_business_settings('admin_order_notification') ?? 0)
                @if (\App\CentralLogics\Helpers::module_permission_check('order') && $admin_order_notification && $order_notification_type == 'firebase')
                new_order_type = payload.data.order_type
                playAudio();
                $('#popup-modal').appendTo("body").modal('show');
                @endif

            }else if(payload.data.type == 'message'){
                var conversation_id = getUrlParameter('conversation');
                var user_id = getUrlParameter('user');
                var url= '{{url('/')}}/admin/message/view/'+conversation_id+'/' + user_id;
                console.log(url);
                $.ajax({
                    url: url,
                    success: function(data) {
                        $('#view-conversation').html(data.view);
                    }
                })
                toastr.success('{{ translate('New_message_arrived') }}', {
                    CloseButton: true,
                    ProgressBar: true
                });

                if($('#conversation-list').scrollTop() == 0){
                    conversationList();
                }
            }
        });
        @endif


        @if(\App\CentralLogics\Helpers::module_permission_check('order') && $order_notification_type == 'manual')
            @php($admin_order_notification=\App\CentralLogics\Helpers::get_business_settings('admin_order_notification')??0)
                @if($admin_order_notification)
                setInterval(function () {
                    $.get({
                        url: '{{route('admin.get-restaurant-data')}}',
                        dataType: 'json',
                        success: function (response) {
                            let data = response.data;
                            new_order_type = data.type;
                            if (data.new_order > 0) {
                                playAudio();
                                $('#popup-modal').appendTo("body").modal('show');
                            }
                        },
                    });
                }, 10000);
                @endif
        @endif

        @if (isset($fcm_credentials['apiKey']) && is_string($fcm_credentials['apiKey']) && strlen($fcm_credentials['apiKey'])  > 3 )
        startFCM();
        @endif
        conversationList();

        if(getUrlParameter('conversation')){
            conversationView();
            vendorConversationView();
            dmConversationView();
        }

        $(document).on('click', '.check-order', function () {
            location.href = '{{ route('admin.order.list', ['status' => 'all']) }}';
        });
        $(document).on('click', '.check-message', function () {
            var tab = getUrlParameter('tab');
            location.href = '{{ route('admin.message.list') }}'+ '?tab=' + tab;
        });

        if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write(
            '<script src="{{ dynamicAsset('public/assets/admin') }}/vendor/babel-polyfill/polyfill.min.js"><\/script>');

        $(window).on('load', ()=> {
            $('body').addClass('loaded');
            $('#pre--loader').fadeOut(600); // Fixed selector
        })

        $('.log-out').on('click',function (){
                Swal.fire({
                title: '{{ translate('Do_You_Want_To_Sign_Out_?')}}',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonColor: '#FC6A57',
                cancelButtonColor: '#363636',
                confirmButtonText: `{{ translate('yes')}}`,
                cancelButtonText: `{{ translate('cancel')}}`,
                }).then((result) => {
                if (result.value) {
                location.href='{{route('logout')}}';
                } else{
                Swal.fire('{{ translate('messages.canceled') }}', '', 'info')
                }
                })
            });


            const inputs = document.querySelectorAll('input[type="tel"]');
            inputs.forEach(input => {
                window.intlTelInput(input, {
                    initialCountry: "{{$countryCode}}",
                    utilsScript: "{{ dynamicAsset('public/assets/admin/intltelinput/js/utils.js') }}",
                    autoInsertDialCode: true,
                    nationalMode: false,
                    formatOnDisplay: false,
                    strictMode: true,
                    // allowDropdown: false,
                    @if (\App\CentralLogics\Helpers::get_business_settings('country_picker_status') != 1)
                    onlyCountries: ["{{$countryCode}}"],
                    @endif
                });
            });


            function keepNumbersAndPlus(inputString) {
                let regex = /[0-9+]/g;
                let filteredString = inputString.match(regex);
            return filteredString ? filteredString.join('') : '';
            }

            $(document).on('keyup', 'input[type="tel"]', function () {
                $(this).val(keepNumbersAndPlus($(this).val()));
                });

        //search option
        $(document).ready(function () {
            $('#searchForm input[name="search"]').keyup(function () {
                var searchKeyword = $(this).val().trim();

                if (searchKeyword.length >= 1) {
                    $.ajax({
                        type: 'POST',
                        url: $('#searchForm').attr('action'),
                        data: {search: searchKeyword, _token: $('input[name="_token"]').val()},
                        success: function (response) {
                            if (response.length === 0) {
                                $('#searchResults').html('<div class="fs-16 fw-500 mb-2">' + @json(translate('Search Result')) + '</div>' +
                                '<div class="search-list h-300 d-flex flex-column gap-2 justify-content-center align-items-center fs-16">' +
                                    '<img width="30" src="' + @json(dynamicAsset('/public/assets/admin/img/modal/no-search-found.png')) + '" alt="">' + ' ' +
                                    @json(translate('No result found')) +
                                '</div>');

                            } else {
                                var resultHtml = '';
                                response.forEach(function (route) {
                                    var fullRouteWithKeyword = route.fullRoute + '?keyword=' + encodeURIComponent(searchKeyword);
                                    resultHtml += '<a href="' + fullRouteWithKeyword + '" class="search-list-item d-flex flex-column" data-route-name="' + route.routeName + '" data-route-uri="' + route.URI + '" data-route-full-url="' + route.fullRoute + '" aria-current="true">';
                                    resultHtml += '<h5>' + route.routeName + '</h5>';
                                    resultHtml += '<p class="text-muted fs-12 mb-0">' + route.URI + '</p>';
                                    resultHtml += '</a>';
                                });
                                $('#searchResults').html('<div class="fs-16 fw-500 mb-2">' + @json(translate('Search Result')) + '</div>' + '<div class="search-list d-flex flex-column">' + resultHtml + '</div>');

                                $('.search-list-item').click(function () {
                                    var routeName = $(this).data('route-name');
                                    var routeUri = $(this).data('route-uri');
                                    var routeFullUrl = $(this).data('route-full-url');

                                    $.ajax({
                                        type: 'POST',
                                        url: '{{ route('admin.store.clicked.route') }}',
                                        data: {
                                            routeName: routeName,
                                            routeUri: routeUri,
                                            routeFullUrl: routeFullUrl,
                                            searchKeyword: searchKeyword,
                                            _token: $('input[name="_token"]').val()
                                        },
                                        success: function (response) {
                                            console.log(response.message);
                                        },
                                        error: function (xhr, status, error) {
                                            console.error(xhr.responseText);
                                        }
                                    });
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                } else {
                    $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('Write a minimum of one characters.')}}.</div>');
                }
            });
        });

        document.addEventListener('keydown', function(event) {
            if (event.ctrlKey && event.key === 'k') {
                event.preventDefault();
                document.getElementById('modalOpener').click();
            }
        });

        $(document).ready(function () {
            $("#staticBackdrop").on("shown.bs.modal", function () {
                $(this).find("#searchForm input[type=search]").val('');
                $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('Loading recent searches')}}...</div>');
                $(this).find("#searchForm input[type=search]").focus();

                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.recent.search') }}',
                    success: function (response) {
                        if (response.length === 0) {
                            $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('It appears that you have not yet searched.')}}.</div>');
                        } else {
                            var resultHtml = '';
                            response.forEach(function (route) {
                                resultHtml += '<a href="' + route.route_full_url + '" class="search-list-item d-flex flex-column" data-route-name="' + route.route_name + '" data-route-uri="' + route.route_uri + '" data-route-full-url="' + route.route_full_url + '" aria-current="true">';
                                resultHtml += '<h5>' + route.route_name + '</h5>';
                                resultHtml += '<p class="text-muted fs-12  mb-0">' + route.route_uri + '</p>';
                                resultHtml += '</a>';
                            });
                            $('#searchResults').html('<div class="recent-search fs-16 fw-500 animate">' +
                                @json(translate('Recent Search')) + '<div class="search-list d-flex flex-column mt-2">' + resultHtml + '</div></div>');

                            $('.search-list-item').click(function () {
                                var routeName = $(this).data('route-name');
                                var routeUri = $(this).data('route-uri');
                                var routeFullUrl = $(this).data('route-full-url');
                                var searchKeyword = $('input[type=search]').val().trim();

                                $.ajax({
                                    type: 'POST',
                                    url: '{{ route('admin.store.clicked.route') }}',
                                    data: {
                                        routeName: routeName,
                                        routeUri: routeUri,
                                        routeFullUrl: routeFullUrl,
                                        searchKeyword: searchKeyword,
                                        _token: $('input[name="_token"]').val()
                                    },
                                    success: function (response) {
                                        console.log(response.message);
                                    },
                                    error: function (xhr, status, error) {
                                        console.error(xhr.responseText);
                                    }
                                });
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('Error loading recent searches')}}.</div>');
                    }
                });
            });
        });

        $("#staticBackdrop").on("hidden.bs.modal", function () {
            $('#searchResults').empty();
        });

        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('search', function() {
            if (!this.value.trim()) {
                $('#searchResults').html('<div class="text-center text-muted py-5"></div>');
            }
        });

        $('#searchForm').submit(function (event) {
            event.preventDefault();
        });
    </script>

</body>

</html>
