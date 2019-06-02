@extends('layouts.app')

@section('pageTitle')
    - Login
@endsection

@section('content1')
<section id="wrapper">
    
    <div class="login-register" style="background-image:url(images/background/login-register.jpg);"> 

        <div class="login-box card">
            <div class="card-block">
                <form class="form-horizontal form-material" method="post" id="loginform" action="{{ route('login') }}">
                     {{ csrf_field() }}
                     <h3 class="box-title m-b-20">Sign In</h3>

                     @if (session('status'))
                        <div class="alert alert-success" role="alert">{{ session('status') }}</div>
                     @endif

                     @if (session('warning'))
                        <div class="alert alert-warning" role="alert">{{ session('warning') }}</div>
                     @endif

                     <div class="form-group ">
                        <div class="col-xs-12">
                            <input class="form-control" name="email" type="email" value="{{ old('email') }}" required autofocus placeholder="Email"> 
                            @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control" type="password" name="password" required placeholder="Password"> 
                            @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="checkbox checkbox-primary pull-left p-t-0">
                                <input id="checkbox-signup" type="checkbox">
                                <label for="checkbox-signup"> Remember me </label>
                            </div> <a href="/password/reset" id="to-recover" class="text-dark pull-right"><i class="fa fa-lock m-r-5"></i> Forgot pwd?</a> 
                        </div>
                    </div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <input type="hidden" name="loggedFrom" value="customer">
                            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Log In</button>
                        </div>
                    </div>
                    <div class="form-group m-b-0">
                        <div class="col-sm-12 text-center">
                            <p>Don't have an account? <a href="/register" class="text-info m-l-5"><b>Sign Up</b></a></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
