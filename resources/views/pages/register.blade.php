<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
</head>

<body class="hold-transition register-page">
    <div class="register-box">
        @if(Session::has('error'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
           {{ Session::get('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
           {{ Session::get('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="{{asset('register')}}" class="h1"><b>Register</b>Form</a>
            </div> 
            <div class="card-body">
                <p class="login-box-msg">Register a new membership</p>

                <form action="{{route('postRegister')}}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name='fname' class="form-control" value='{{ old('fname') }}' id='fname'
                            placeholder="Enter first name">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>


                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name='lname' id='lname' value='{{ old('lname') }}'
                            placeholder="Enter last name">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>

                    @error('lname')
                    <p class='text-danger'>*{{ $message }}</p>
                    @enderror

                    <div class="input-group mb-3">
                        <input type="email" class="form-control" name='email' id='email' value='{{ old('email') }}'
                            placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>

                    @error('email')
                    <p class='text-danger'>*{{ $message }}</p>
                    @enderror

                    <div class="input-group mb-3">
                        <input type="password" class="form-control" name='password' id='password'
                            placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    @error('password')
                    <p class='text-danger'>*{{ $message }}</p>
                    @enderror

                    <div class="input-group mb-3">
                        <input type="password" class="form-control" name='cpassword' id='cpassword'
                            placeholder="Retype password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    @error('cpassword')
                    <p class='text-danger'>*{{ $message }}</p>
                    @enderror

                    <div class="input-group mb-3">
                        <input type="number" class="form-control" name='phone' id='phone' placeholder="Enter Phone"
                            value='{{ old('phone') }}'>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-phone"></span>
                            </div>
                        </div>
                    </div>

                    @error('phone')
                    <p class='text-danger'>*{{ $message }}</p>
                    @enderror

                    <div class="row">

                        <!-- /.col -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Send OTP</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>



                <a href="{{route('login')}}" class="text-center">I already have a membership</a>
            </div>
            <!-- /.form-box -->
        </div><!-- /.card -->
    </div>
    <!-- /.register-box -->

    <!-- jQuery -->
    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <!-- AdminLTE App -->
    <script src="{{asset('dist/js/adminlte.min.js')}}"></script>
</body>

</html>