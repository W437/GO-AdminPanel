<!DOCTYPE html>
    <?php
    $log_email_succ = session()->get('log_email_succ');
    ?>
<html dir="{{ $site_direction }}" lang="{{ $locale }}" class="{{ $site_direction === 'rtl'?'active':'' }}">
<head>
    <!-- Required Meta Tags Always Come First -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @php
        $app_name = \App\CentralLogics\Helpers::get_business_settings('business_name', false);
        $icon = \App\CentralLogics\Helpers::get_business_settings('icon', false);
    @endphp
    <!-- Title -->
    <title>{{ translate('messages.login') }} | {{$app_name??'GO Admin Panel'}}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{asset($icon ? 'storage/app/public/business/'.$icon : 'public/favicon.ico')}}">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/vendor/icon-set/style.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/css/style.css">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/css/toastr.css">
    
    <!-- Custom Minimalistic Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #9463ac 0%, #a97dc4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 16px;
            padding: 48px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        
        .logo-container {
            margin-bottom: 32px;
        }
        
        .logo-container img {
            max-width: 120px;
            height: auto;
        }
        
        .login-title {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }
        
        .login-subtitle {
            color: #64748b;
            margin-bottom: 32px;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.2s ease;
            width: 100%;
        }
        
        .form-control:focus {
            border-color: #9463ac;
            box-shadow: 0 0 0 3px rgba(148, 99, 172, 0.1);
            outline: none;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group-append {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #9463ac 0%, #a97dc4 100%);
            border: none;
            border-radius: 8px;
            padding: 14px 24px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.2s ease;
            margin-top: 8px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(148, 99, 172, 0.4);
            color: white;
        }
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 24px 0;
        }
        
        .custom-checkbox {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #64748b;
        }
        
        .forgot-password {
            color: #9463ac;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .forgot-password:hover {
            color: #a97dc4;
            text-decoration: none;
        }
        
        .captcha-container {
            margin: 20px 0;
        }
        
        .version-badge {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            backdrop-filter: blur(10px);
            font-weight: 500;
            white-space: nowrap;
        }
        
        @media (max-width: 768px) {
            .login-container {
                padding: 32px 24px;
                margin-bottom: 60px;
            }
            
            .version-badge {
                position: fixed;
                bottom: 15px;
                left: 50%;
                right: auto;
                transform: translateX(-50%);
                background: rgba(255, 255, 255, 0.15);
                color: white;
                backdrop-filter: blur(10px);
                font-size: 11px;
                padding: 4px 12px;
            }
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 24px 20px;
                margin-bottom: 50px;
            }
            
            .version-badge {
                font-size: 10px;
                padding: 3px 10px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Logo -->
        <div class="logo-container">
            <img src="{{ dynamicAsset('/public/assets/admin/img/hopa-logo.png') }}"
                 alt="GO Admin Panel"
                 onerror="this.src='{{ dynamicAsset('/public/assets/admin/img/logo.png') }}'">
        </div>
        
        <!-- Title -->
        <h1 class="login-title">Welcome Back</h1>
        <p class="login-subtitle">Sign in to your GO Admin Panel</p>
        
        <!-- Login Form -->
        <form class="login_form" action="{{route('login_post')}}" method="post" id="form-id">
            @csrf
            @php($role = $role ?? null )
            <input type="hidden" name="role" value="{{  $role ?? null }}">

            <!-- Email -->
            <div class="form-group">
                <label class="form-label" for="signinSrEmail">{{translate('messages.your_email')}}</label>
                <input type="email" 
                       class="form-control" 
                       value="{{ $email ?? '' }}" 
                       name="email" 
                       id="signinSrEmail"
                       tabindex="1" 
                       placeholder="Enter your email address"
                       required>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label" for="signupSrPassword">{{translate('messages.password')}}</label>
                <div class="input-group">
                    <input type="password" 
                           class="form-control js-toggle-password"
                           name="password" 
                           id="signupSrPassword" 
                           value="{{ $password ?? '' }}"
                           placeholder="Enter your password"
                           required
                           data-hs-toggle-password-options='{
                                        "target": "#changePassTarget",
                                "defaultClass": "tio-hidden-outlined",
                                "showClass": "tio-visible-outlined",
                                "classChangeTarget": "#changePassIcon"
                                }'>
                    <div id="changePassTarget" class="input-group-append">
                        <a href="javascript:" style="color: #64748b;">
                            <i id="changePassIcon" class="tio-visible-outlined"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form Options -->
            <div class="form-options">
                <div class="custom-checkbox">
                    <input type="checkbox" 
                           id="termsCheckbox" 
                           {{ $password ? 'checked' : '' }}
                           name="remember"
                           style="margin-right: 8px;">
                    <label for="termsCheckbox">{{translate('messages.remember_me')}}</label>
                </div>
                
                <div class="form-group {{ $role == 'admin' ? '' : 'd-none' }}">
                    <a href="javascript:" class="forgot-password" data-toggle="modal" data-target="#forgetPassModal">
                        {{ translate('Forget_Password?') }}
                    </a>
                </div>
                <div class="form-group {{ $role == 'vendor' ? '' : 'd-none' }}">
                    <a href="javascript:" class="forgot-password" data-toggle="modal" data-target="#forgetPassModal1">
                        {{ translate('Forget_Password?') }}
                    </a>
                </div>
            </div>

            <!-- Captcha - Disabled for admin login -->
            {{--
            @php($recaptcha = \App\CentralLogics\Helpers::get_business_settings('recaptcha'))
            @if(isset($recaptcha) && $recaptcha['status'] == 1)
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                <input type="hidden" name="set_default_captcha" id="set_default_captcha_value" value="0">
                <div class="captcha-container d-none" id="reload-captcha">
                    <div class="row">
                        <div class="col-6 pr-0">
                            <input type="text"
                                   class="form-control border-0"
                                   name="custome_recaptcha"
                                   id="custome_recaptcha"
                                   required
                                   placeholder="{{translate('Enter recaptcha value')}}"
                                   autocomplete="off"
                                   value="{{env('APP_MODE')=='dev'? session('six_captcha'):''}}">
                        </div>
                        <div class="col-6 bg-white rounded d-flex">
                            <img src="<?php echo $custome_recaptcha->inline(); ?>" class="rounded w-100" />
                            <div class="p-3 pr-0 capcha-spin reloadCaptcha">
                                <i class="tio-cached"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="captcha-container" id="reload-captcha">
                    <div class="row">
                        <div class="col-6 pr-0">
                            <input type="text"
                                   class="form-control border-0"
                                   name="custome_recaptcha"
                                   id="custome_recaptcha"
                                   required
                                   placeholder="{{translate('Enter recaptcha value')}}"
                                   autocomplete="off"
                                   value="{{env('APP_MODE')=='dev'? session('six_captcha'):''}}">
                        </div>
                        <div class="col-6 bg-white rounded d-flex">
                            <img src="<?php echo $custome_recaptcha->inline(); ?>" class="rounded w-100" />
                            <div class="p-3 pr-0 capcha-spin reloadCaptcha" style="cursor: pointer;">
                                <i class="tio-cached"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            --}}

            <!-- Login Button -->
            <button type="submit" class="btn btn-login" id="signInBtn">
                {{translate('messages.sign_in')}}
            </button>
        </form>
        
        <!-- Demo Credentials (if in demo mode) -->
        @if(env('APP_MODE') =='demo' )
            @if (isset($role) &&  $role == 'admin')
                <div style="margin-top: 24px; padding: 16px; background: #f8f9fa; border-radius: 8px; text-align: left;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div style="font-size: 14px; color: #64748b;">
                            <div><strong>Email:</strong> admin@admin.com</div>
                            <div><strong>Password:</strong> 12345678</div>
                        </div>
                        <button class="btn btn-sm" id="copy_cred" style="background: #9463ac; color: white; border: none; border-radius: 4px; padding: 8px 12px;">
                            <i class="tio-copy"></i>
                        </button>
                    </div>
                </div>
            @endif
            @if (isset($role) &&  $role == 'vendor')
                <div style="margin-top: 24px; padding: 16px; background: #f8f9fa; border-radius: 8px; text-align: left;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div style="font-size: 14px; color: #64748b;">
                            <div><strong>Email:</strong> test.restaurant@gmail.com</div>
                            <div><strong>Password:</strong> 12345678</div>
                        </div>
                        <button class="btn btn-sm" id="copy_cred2" style="background: #9463ac; color: white; border: none; border-radius: 4px; padding: 8px 12px;">
                            <i class="tio-copy"></i>
                        </button>
                    </div>
                </div>
            @endif
        @endif
    </div>
    
    <!-- Version Badge -->
    <div class="version-badge">
        {{translate('messages.software_version')}} {{ env('SOFTWARE_VERSION', '8.3') }}
    </div>

    <!-- Forget Password Modals -->
    <div class="modal fade" id="forgetPassModal">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-header justify-content-end" style="border: none;">
                    <span type="button" class="close-modal-icon" data-dismiss="modal">
                        <i class="tio-clear"></i>
                    </span>
                </div>
                <div class="modal-body" style="padding: 32px;">
                    <div class="forget-pass-content" style="text-align: center;">
                        <img src="{{dynamicAsset('/public/assets/admin/img/send-mail.svg')}}" alt="" style="width: 80px; margin-bottom: 24px;">
                        <h4 style="color: #1e293b; margin-bottom: 16px;">{{ translate('Send_Mail_to_Your_Email_?') }}</h4>
                        <p style="color: #64748b; margin-bottom: 24px;">{{ translate('A_mail_will_be_send_to_your_registered_email_with_a_link_to_change_passowrd') }}</p>
                        <a class="btn btn-login" href="{{route('reset-password')}}">{{ translate('Send_Mail') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="forgetPassModal1">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-header justify-content-end" style="border: none;">
                    <span type="button" class="close-modal-icon" data-dismiss="modal">
                        <i class="tio-clear"></i>
                    </span>
                </div>
                <div class="modal-body" style="padding: 32px;">
                    <div class="forget-pass-content" style="text-align: center;">
                        <img src="{{dynamicAsset('/public/assets/admin/img/send-mail.svg')}}" alt="" style="width: 80px; margin-bottom: 24px;">
                        <h4 style="color: #1e293b; margin-bottom: 16px;">{{ translate('messages.Send_Mail_to_Your_Email_?') }}</h4>
                        <form action="{{ route('vendor-reset-password') }}" method="post">
                            @csrf
                            <input type="email" name="email" class="form-control" required placeholder="Enter your email" style="margin-bottom: 20px;">
                            <button type="submit" class="btn btn-login">{{ translate('messages.Send_Mail') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="successMailModal">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-header justify-content-end" style="border: none;">
                    <span type="button" class="close-modal-icon" data-dismiss="modal">
                        <i class="tio-clear"></i>
                    </span>
                </div>
                <div class="modal-body" style="padding: 32px;">
                    <div class="forget-pass-content" style="text-align: center;">
                        <img src="{{dynamicAsset('/public/assets/admin/img/sent-mail.svg')}}" alt="" style="width: 80px; margin-bottom: 24px;">
                        <h4 style="color: #1e293b; margin-bottom: 16px;">{{ translate('A_mail_has_been_sent_to_your_registered_email') }}!</h4>
                        <p style="color: #64748b;">{{ translate('Click_the_link_in_the_mail_description_to_change_password') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Implementing Plugins -->
    <script src="{{dynamicAsset('public/assets/admin')}}/js/vendor.min.js"></script>
    <script src="{{dynamicAsset('public/assets/admin')}}/js/theme.min.js"></script>
    <script src="{{dynamicAsset('public/assets/admin')}}/js/toastr.js"></script>
    {!! Toastr::message() !!}

    @if ($errors->any())
        <script>
            @foreach($errors->all() as $error)
            toastr.error('{{translate($error)}}', 'Error', {
                CloseButton: true,
                ProgressBar: true
            });
            @endforeach
        </script>
    @endif
    
    @if ($log_email_succ)
    @php(session()->forget('log_email_succ'))
        <script>
            $('#successMailModal').modal('show');
        </script>
    @endif

    <script>
        $(document).on('click','.reloadCaptcha', function(){
            $.ajax({
                url: "{{ route('reload-captcha') }}",
                type: "GET",
                dataType: 'json',
                beforeSend: function () {
                    $('.capcha-spin').addClass('active')
                },
                success: function(data) {
                    $('#reload-captcha').html(data.view);
                },
                complete: function () {
                    $('.capcha-spin').removeClass('active')
                }
            });
        });

        $(document).on('ready', function () {
            $('.js-toggle-password').each(function () {
                new HSTogglePassword(this).init()
            });
            $('.js-validate').each(function () {
                $.HSCore.components.HSValidation.init($(this));
            });
        });

        @if(env('APP_MODE') =='demo')
            $("#copy_cred").click(function() {
                $('#signinSrEmail').val('admin@admin.com');
                $('#signupSrPassword').val('12345678');
                toastr.success('Credentials copied!', 'Success', {
                    CloseButton: true,
                    ProgressBar: true
                });
            })
            $("#copy_cred2").click(function() {
                $('#signinSrEmail').val('test.restaurant@gmail.com');
                $('#signupSrPassword').val('12345678');
                toastr.success('Credentials copied!', 'Success', {
                    CloseButton: true,
                    ProgressBar: true
                });
            })
        @endif
    </script>

    @if(isset($recaptcha) && $recaptcha['status'] == 1)
        <script src="https://www.google.com/recaptcha/api.js?render={{$recaptcha['site_key']}}"></script>
        <script>
            $(document).ready(function() {
                $('#signInBtn').click(function (e) {
                    if( $('#set_default_captcha_value').val() == 1){
                        $('#form-id').submit();
                        return true;
                    }
                    e.preventDefault();
                    if (typeof grecaptcha === 'undefined') {
                        toastr.error('Invalid recaptcha key provided.');
                        $('#reload-captcha').removeClass('d-none');
                        $('#set_default_captcha_value').val('1');
                        return;
                    }
                    grecaptcha.ready(function () {
                        grecaptcha.execute('{{$recaptcha['site_key']}}', {action: 'submit'}).then(function (token) {
                            $('#g-recaptcha-response').value = token;
                            $('#form-id').submit();
                        });
                    });
                });
            });
        </script>
    @endif

</body>
</html>