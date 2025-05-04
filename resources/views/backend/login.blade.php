<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>{{env('APP_NAME')}} | Login</title>
    <link rel="icon" type="image/x-icon" href="{{asset('assets/backend/assets/img/bharat_favicon.ico')}}"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="{{asset('assets/backend/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/backend/assets/css/plugins.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/backend/assets/css/authentication/form-1.css')}}" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/backend/assets/css/forms/theme-checkbox-radio.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/backend/assets/css/forms/switches.css')}}">
    <link href="{{asset('assets/backend/plugins/notification/snackbar/snackbar.min.css')}}" rel="stylesheet" type="text/css" />
  </head>
  <body class="form">
    <div class="form-container">
      <div class="form-form">
        <div class="form-form-wrap">
          <div class="form-container">
            <div class="form-content">
              <h1 class="">Log In to <a href="{{route('login')}}"><span class="brand-name">{{env('APP_NAME')}}</span></a></h1>
              <!-- <p class="signup-link">New Here? <a href="auth_register.html">Create an account</a></p> -->
              <form class="text-left">
                <div class="form">
                  <div id="username-field" class="field-wrapper input">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                      <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <input id="userName" name="userName" type="text" class="form-control" placeholder="Username" autocomplete="off">
                  </div>
                  <div id="password-field" class="field-wrapper input mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock">
                      <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                      <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <input id="password" name="userPassword" type="password" class="form-control" placeholder="Password" autocomplete="off">
                  </div>
                  <div class="d-sm-flex justify-content-between">
                    <div class="field-wrapper toggle-pass">
                      <p class="d-inline-block">Show Password</p>
                      <label class="switch s-primary">
                      <input type="checkbox" id="toggle-password" class="d-none">
                      <span class="slider round"></span>
                      </label>
                    </div>
                    <div class="field-wrapper">
                      <button type="button" class="btn btn-primary" id="submit" value="" onclick="return validateUser()">Log In</button>
                    </div>
                  </div>
                 <!--  <div class="field-wrapper text-center keep-logged-in">
                    <div class="n-chk new-checkbox checkbox-outline-primary">
                      <label class="new-control new-checkbox checkbox-outline-primary">
                      <input type="checkbox" class="new-control-input">
                      <span class="new-control-indicator"></span>Keep me logged in
                      </label>
                    </div>
                  </div>
                  <div class="field-wrapper">
                    <a href="auth_pass_recovery.html" class="forgot-pass-link">Forgot Password?</a>
                  </div> -->
                </div>
              </form>
              <p class="terms-conditions">© <?=date('Y')?> All Rights Reserved. 
                <!-- <a href="index.html">CORK</a> is a product of Designreset. <a href="javascript:void(0);">Cookie Preferences</a>, <a href="javascript:void(0);">Privacy</a>, and <a href="javascript:void(0);">Terms</a>. -->
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="form-image">
        <div class="l-image">
        </div>
      </div>
    </div>
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{asset('assets/backend/assets/js/libs/jquery-3.1.1.min.js')}}"></script>
    <script src="{{asset('assets/backend/bootstrap/js/popper.min.js')}}"></script>
    <script src="{{asset('assets/backend/bootstrap/js/bootstrap.min.js')}}"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <script src="{{asset('assets/backend/assets/js/authentication/form-1.js')}}"></script>
    <script src="{{asset('assets/backend/plugins/notification/snackbar/snackbar.min.js')}}"></script>
    <script type="text/javascript">
    function validateUser() {
        const userName = document.getElementById('userName').value;
        const userPassword = document.getElementById('password').value;

        if (!userName) {
            Snackbar.show({
                text: 'Please Enter Username.',
                pos: 'top-right',
                actionTextColor: '#fff',
                backgroundColor: '#e7515a'
            }); 
            return false;
        } else if (!userPassword) {
            Snackbar.show({
                text: 'Please Enter The password.',
                pos: 'top-right',
                actionTextColor: '#fff',
                backgroundColor: '#e7515a'
            });
            return false;
        } else {
            $.ajax({
                url: "{{route('authenticate')}}",
                method: "POST",
                data: {
                    userName: userName,
                    userPassword: userPassword,
                    _token: '{{csrf_token()}}'
                },
                dataType:'json',
                beforeSend: function() {
                    $('#submit').prop('disabled', true);
                    Snackbar.show({
                        text: 'Please Wait...',
                        pos: 'top-right',
                        actionTextColor: '#fff',
                        backgroundColor: '#e2a03f'
                    }); 
                },
                success: function(response) {
                  if(response.status){
                    setTimeout(function() {
                        Snackbar.show({
                          text: response.msg,
                          pos: 'top-right',
                          actionTextColor: '#fff',
                          backgroundColor: '#8dbf42'
                      });
                       window.location.href = response.url;
                    }, 1500);
                  }else{
                    Snackbar.show({
                        text: response.msg,
                        pos: 'top-right',
                        actionTextColor: '#fff',
                        backgroundColor: '#e7515a'
                    });
                    $('#submit').prop('disabled', false);
                  }  
                },
                error: function(xhr) {
                    $('#submit').prop('disabled', false);
                    // console.log("Error:", xhr);
                    Snackbar.show({
                        text: xhr,
                        pos: 'top-right',
                        actionTextColor: '#fff',
                        backgroundColor: '#e7515a'
                    });
                }
            });
        }
    }
    </script>
  </body>
</html>