<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="{{ asset('AdminLTE') }}/img/logo/logo pnc.png" rel="icon">
  <title>SIPROKER - Login</title>
  <link href="{{ asset('AdminLTE') }}/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="{{ asset('AdminLTE') }}/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="{{ asset('AdminLTE') }}/css/ruang-admin.min.css" rel="stylesheet">
  <style>
    .center {
    display: block;
    margin-left: auto;
    margin-right: auto;
    width: 30%;
    }
    </style>
</head>

<body class="bg-gradient-login">
  <!-- Login Content -->
  <div class="container-login">
    <div class="row justify-content-center">
      <div class="col-xl-5 col-lg-12 col-md-9">
        <div class="card shadow-sm my-5">
        <img src="{{ asset('AdminLTE') }}/img/logo/logo pnc.png" class="center"/>
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="login-form">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Login SIPROKER</h1>
                  </div>
                  @if (session('loginError'))
                  <div class="alert alert-danger">
                  {{ session('loginError') }}
                 </div>
                  @endif
                  <form class="user" method="POST" action="{{ url('login') }}">
                    @csrf 
                    <div class="form-group">
                      <input type="text" name="username" class="form-control" id="username" 
                        placeholder="Masukkan Username">
                    </div>
                    <div class="form-group">
                      <input type="password" name="password" class="form-control" id="password" placeholder="Masukkan Password">
                    </div>
                    <div class="form-group">
                      <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </div>
                    <div class="right-align"><a href="{{ '/forgot-password' }}">Forget Password?</a></div>
                    <hr>
                  </form>
                  <hr>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Login Content -->
  <script src="{{ asset('AdminLTE') }}/vendor/jquery/jquery.min.js"></script>
  <script src="{{ asset('AdminLTE') }}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('AdminLTE') }}/vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="{{ asset('AdminLTE') }}/js/ruang-admin.min.js"></script>
</body>

</html>