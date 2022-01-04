@extends('layouts.default')

@section('content')
    <div id="login" class="bg" style="background:url(/img/bg.png) no-repeat;background-size: 100% 100%;">
        <div id="content">
            <div id="login-panel">
                <div id="login-header">
                    <h1 class="text-center"><img id="logo" width="111px" src="/img/192.png"> InfoSchool</h1>
                    <hr>
                    <h4 class="text-center">Chicorycom <span style="font-size:12px;">(Votre Intégrateur Réseau Systéme et Sécurité)</span></h4>
                    <hr>
                    <div id="error" class="hide"> </div>
                </div>
                <div class="flip-container">
                    <div class="flipper">
                        <div class="front panel" id="front_reset">
                            <div class="alert alert-info"  ><marquee>Information School</marquee> </div>
                            <form action="/reset-password/{{ $key }}" id="reset_password_form" method="POST">
                                <h4 id="reset_name">Reset your password</h4>
                                <div class="form-group">
                                    <label class="control-label" for="password">New password
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-key"></i></span>
                                        <input name="password" type="password" id="password" class="form-control"  tabindex="2" placeholder="new password"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="confirm_password">
                                        Confirm new password
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-key"></i></span>
                                        <input name="confirm_password" type="password" id="confirm_password" class="form-control"  tabindex="2" placeholder="Confirm password"/>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <a class="btn btn-default pull-left cancel-reset" href="/reset-password/cancel/{{ $key }}" tabindex="3">
                                        <i class="icon-ok text-success"></i>
                                        Cancel
                                    </a>
                                    <button class="btn btn-primary btn-default pull-right" name="submitLogin" type="submit" tabindex="3">
                                        <i class="icon-ok text-success"></i>
                                        Reset password
                                    </button>
                                </div>
                                <input type="hidden" name="reset_token" id="reset_token" value="{{ $key }}" />
                            </form>
                        </div>
                        <div class="back back_reset" >
                            <h4 id="reset_confirm_name">Your password has been successfully changed.<br/><br/>You will be redirected to the login page in a few seconds.</h4>
                        </div>
                    </div>
                </div>
                <br>
               @include('partials.footer-login')
            </div>
        </div>
    </div>
@endsection
