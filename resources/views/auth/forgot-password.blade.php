<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="{{ asset('AdminLTE') }}/img/logo/logo pnc.png" rel="icon">
  <title>SIPROKER - Forgot Password</title>
  <link href="{{ asset('AdminLTE') }}/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="{{ asset('AdminLTE') }}/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="{{ asset('AdminLTE') }}/css/ruang-admin.min.css" rel="stylesheet">
  <style>
    .center {
      display: block;
      margin-top: 20px;
      margin-left: auto;
      margin-right: auto;
      width: 30%;
    }

    .login-form .form-group {
      position: relative;
    }

    .right-align {
      position: absolute;
      right: 0;
      bottom: -20px;
      font-size: 18px; /* Sesuaikan ukuran font jika diperlukan */
    }
  </style>
</head>

<body class="bg-gradient-login">
  <!-- Forgot Password Content -->
  <div class="container-login">
    <div class="row justify-content-center">
      <div class="col-xl-5 col-lg-13 col-md-9">
        <div class="card shadow-sm my-5">
          <img src="{{ asset('AdminLTE') }}/img/logo/logo pnc.png" class="center" />
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="login-form">
                  <div class="text-center">
                    <p class ="text-left">
                        Lupa password? Tidak masalah. Cukup beri tahu kami alamat email Anda, dan kami akan mengirimkan tautan reset password ke email Anda agar Anda dapat membuat password yang baru.
                    </p>
                  </div>
                  @if (session('status'))
                  <div class="alert alert-success">
                    {{ session('status') }}
                  </div>
                  @endif
                  <form class="user" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group">
                      <input type="email" id="email" name="email" class="form-control" placeholder="Email Address" required autofocus>
                    </div>
                    <div class="form-group">
                      <button type="submit" class="btn btn-primary btn-block">Send Password Reset Link</button>
                    </div>
                    <div class="text-right">
                        <a class="large" href="{{ url('/') }}">Back to Login</a>
                      </div>
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
  <!-- Forgot Password Content -->
  <script src="{{ asset('AdminLTE') }}/vendor/jquery/jquery.min.js"></script>
  <script src="{{ asset('AdminLTE') }}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('AdminLTE') }}/vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="{{ asset('AdminLTE') }}/js/ruang-admin.min.js"></script>
</body>

</html>
