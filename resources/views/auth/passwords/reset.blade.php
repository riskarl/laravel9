<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="{{ asset('AdminLTE') }}/img/logo/logo pnc.png" rel="icon">
    <title>SIPROKER - Reset Password</title>
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
            font-size: 18px;
        }
    </style>
</head>

<body class="bg-gradient-login">
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
                                        <h1 class="h4 text-gray-900 mb-4">Reset Password</h1>
                                    </div>
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <form class="user" action="{{ route('password.update') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="token" value="{{ $token }}">
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control" id="email" placeholder="Email" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirm Password" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
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
    <script src="{{ asset('AdminLTE') }}/vendor/jquery/jquery.min.js"></script>
    <script src="{{ asset('AdminLTE') }}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('AdminLTE') }}/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="{{ asset('AdminLTE') }}/js/ruang-admin.min.js"></script>
</body>

</html>
