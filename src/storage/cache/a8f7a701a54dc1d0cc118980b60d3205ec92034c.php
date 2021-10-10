<?php $__env->startSection('content'); ?>
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

                        <div class="front  panel" id="loginn">
                            <div class="alert alert-info"  ><marquee>Information School</marquee> </div>
                            <form action="/login" id="login_form" method="post" >
                                <div class="form-group">
                                    <label>Adresse e-mail</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                        <i class="icon-user"></i></span>
                                        <input name="email" type="text" id="email" class="form-control"  autofocus="autofocus" tabindex="1" placeholder="test@example.com">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label >Mot de passe</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-key"></i></span>
                                        <input name="password" type="password" id="passwd" class="form-control"  tabindex="2" placeholder="Password"/>
                                    </div>
                                </div>
                                <div class="form-group clearfix">
                                    <div id="remind-me" class="checkbox pull-left">
                                        <label for="stay_logged_in">
                                            <input name="stay_logged_in" type="checkbox" id="stay_logged_in" value="1" tabindex="3">
                                            Rester connecté
                                        </label>

                                    </div>
                                    <a href="#" id="boutonrotation" class="show-forgot-password pull-right">
                                        Mot de passe oublié
                                    </a>
                                </div>
                                <div class="panel-footer">
                                    <button name="submitLogin" type="submit" tabindex="4"  class="btn btn-default btn-lg btn-block ladda-button">
                                       <span id="ajax_running_login" style="display: none;">
                                            <i class="icon-refresh icon-spin icon-fw"></i>
                                        </span>
                                        <span class="ladda-label" id="boutonlogin" >
                                            Se connecter
                                        </span>
                                    </button>
                                    <hr>

                                </div>
                                <input type="hidden" name="redirect" id="redirect" value="&amp;token=9d21105683b1ce75b80a4125327963b7">
                            </form>

                        </div>

                        <div class="back  panel" id="back_reset">
                            <form action="/reset-password" id="forgot_password_form" method="POST" novalidate="novalidate">
                                <div class="alert">
                                    <h4>Mot de passe oublié?</h4>
                                    <p>Afin de recevoir votre code d'accès par e-mail, s'il vous plaît entrez l'adresse que vous avez indiquée au cours du processus d'inscription.</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="email_forgot">
                                        Email
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-envelope"></i></span>
                                        <input type="text" name="email" id="email_forgot" class="form-control" autofocus="autofocus" tabindex="5" placeholder="test@example.com">
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <a href="#"  id="boutonreture"  class="btn btn-default show-login-form" name="retour" value="securite_assane_sarr313" tabindex="7">
                                        <i class="icon-caret-left"></i>
                                        Retour login
                                    </a>
                                    <button class="btn btn-default pull-right" name="submitEnvoyer" type="submit" tabindex="6">
                                        <i class="icon-ok text-success"></i>
                                        Envoyer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <br>
                <?php echo $__env->make('partials.footer-login', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/assane/dev-doc/web-apps/framework/src/resources/views/auth/login.blade.php ENDPATH**/ ?>