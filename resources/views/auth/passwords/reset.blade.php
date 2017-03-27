            <!DOCTYPE html>
            <html lang="{{ config('app.locale') }}">
            <head>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="robots" content="none" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
                <meta name="description" content="admin login">
                <title>Admin - {{ Voyager::setting("title") }}</title>
                <link rel="stylesheet" href="{{ config('voyager.assets_path') }}/lib/css/bootstrap.min.css">
                <link rel="stylesheet" href="{{ config('voyager.assets_path') }}/css/animate.min.css">
                <link rel="stylesheet" href="{{ config('voyager.assets_path') }}/css/login.css">
                <style>
                    body {
                        background-image:url('{{ Voyager::image( Voyager::setting("admin_bg_image"), config('voyager.assets_path') . "/images/bg.jpg" ) }}');
                    }
                    .login-sidebar:after {
                        background: linear-gradient(-135deg, {{config('voyager.login.gradient_a','#ffffff')}}, {{config('voyager.login.gradient_b','#ffffff')}});
                        background: -webkit-linear-gradient(-135deg, {{config('voyager.login.gradient_a','#ffffff')}}, {{config('voyager.login.gradient_b','#ffffff')}});
                    }
                    .login-button, .bar:before, .bar:after{
                        background:{{ config('voyager.primary_color','#22A7F0') }};
                    }

                </style>

                <link href='https://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'>
            </head>
            <body>
                <!-- Designed with ♥ by Frondor -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="faded-bg animated"></div>
                        <div class="hidden-xs col-sm-8 col-md-9">
                            <div class="clearfix">
                                <div class="col-sm-12 col-md-10 col-md-offset-2">
                                    <div class="logo-title-container">
                                        <?php $admin_logo_img = Voyager::setting('admin_icon_image', ''); ?>
                                        @if($admin_logo_img == '')
                                        <img class="img-responsive pull-left logo hidden-xs animated fadeIn" src="{{ config('voyager.assets_path') }}/images/logo-icon-light.png" alt="Logo Icon">
                                        @else
                                        <img class="img-responsive pull-left logo hidden-xs animated fadeIn" src="{{ Voyager::image($admin_logo_img) }}" alt="Logo Icon">
                                        @endif
                                        <div class="copy animated fadeIn">
                                            <h1>{{ Voyager::setting('admin_title', 'Voyager') }}</h1>
                                            <p>{{ Voyager::setting('admin_description', 'Welcome to Voyager. The Missing Admin for Laravel') }}</p>
                                        </div>
                                    </div> <!-- .logo-title-container -->
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-4 col-md-3 login-sidebar   fadeInRightBig">

                            <div class="login-container   fadeInRightBig">
                                <h2>Reset Password</h2>

                                @if (session('status'))
                                <div class="alert alert-success fade in alert-dismissable">
                                    {{ session('status') }}
                                </div>
                                @endif

                                <form class="form-horizontal" role="form" method="POST" action="{{ route('password.request') }}">
                                    {{ csrf_field() }}

                                    <input type="hidden" name="token" value="{{ $token }}">
                                    <div class="group form-group{{ $errors->has('email') ? ' has-error' : '' }}">      
                                      <input type="text" name="email" value="{{ $email or old('email') }}" required autofocus>
                                      <span class="highlight"></span>
                                      <span class="bar"></span>
                                      <label><i class="glyphicon glyphicon-user"></i><span class="span-input"> E-mail</span></label>
                                      @if ($errors->has('email'))
                                      <br/>
                                      <span class="alert alert-warning fade in alert-dismissable">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>


                                <div class="group form-group{{ $errors->has('password') ? ' has-error' : '' }}">      
                                  <input type="password" name="password" required>
                                  <span class="highlight"></span>
                                  <span class="bar"></span>
                                  <label><i class="glyphicon glyphicon-lock"></i><span class="span-input"> Password</span></label>

                                  @if ($errors->has('password'))
                                  <br/>
                                  <span class="alert alert-warning fade in alert-dismissable">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                            </div>


                            <div class="group form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">      
                                <input  type="password"   name="password_confirmation" required>
                                <span class="highlight"></span>
                                <span class="bar"></span>
                                <label><i class="glyphicon glyphicon-lock"></i><span class="span-input">Confirm Password</span></label>
                                @if ($errors->has('password_confirmation'))
                                <br/>
                                <span class="alert alert-warning fade in alert-dismissable">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                                @endif  
                            </div>




                            <button type="submit" class="btn btn-block login-button">
                             <span class="signingin hidden"><span class="glyphicon glyphicon-refresh"></span>Resetting Password...</span>
                             <span class="signin">Reset</span>
                         </button>

                     </form> 
 
                 </div>
             </div> <!-- .login-sidebar -->
         </div> <!-- .row -->
     </div> <!-- .container-fluid -->
     <script>
        var btn = document.querySelector('button[type="submit"]');
        var form = document.forms[0];
        btn.addEventListener('click', function(ev){
            if (form.checkValidity()) {
                btn.querySelector('.signingin').className = 'signingin';
                btn.querySelector('.signin').className = 'signin hidden';
            } else {
                ev.preventDefault();
            }
        });
    </script>
</body>
</html>
