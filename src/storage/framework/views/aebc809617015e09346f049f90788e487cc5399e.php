<?php $__env->startSection('home'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <i class="icon-spinner"></i> Page Not Found <span class="badge"></span>
                    <span class="panel-heading-action"></span>

                </div>
                <div class="span12">
                    <div class="hero-unit center">
                        <h1>Page Not Found <small><b  style="color:#ff0000" >Error 404</b></small></h1>
                        <br />
                        <p>La page demandée n'a pu être trouvée, soit contacter votre webmaster ou essayer à nouveau.</p>

                        <a href="javascript: window.history.back();" class="btn btn-large btn-info"><i class="icon-home icon-white"></i> Retour</a>
                    </div>
                    <br />

                </div>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/assane/dev-doc/web-apps/framework/src/resources/views/errors/404.blade.php ENDPATH**/ ?>