<?php
/*
* 2007-2017 Chicorycom
*
* NOTICE OF LICENSE
*
* Ce fichier source est soumis à la licence Open Software (OSL 3.0)
* Qui est livré avec ce package dans le fichier LICENSE.txt.
* Il est également disponible sur le World Wide Web à cette URL:
* http://opensource.org/licenses/osl-3.0.php
* Si vous n'avez pas reçu une copie de la licence et ne pouvez
* Vous pouvez l'obtenir sur le World Wide Web, veuillez envoyer un courriel
* À license@chicorycom.net afin que nous puissions vous en envoyer une copie immédiatement.
*
* DISCLAIMER
*
* Ne modifiez pas ou ajoutez à ce fichier si vous souhaitez mettre à jour Chicorycom vers une version plus récente
* Versions ultérieures. Si vous souhaitez personnaliser Chicorycom pour vos besoins
* s'il vous plaît se référer à http://www.chicorycom.net pour plus d'informations.
*
*  @author Assane SARR <contact@chicorycom.net>
*  @copyright  2007-2021 Chicorycom
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of Chicorycom
*/
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Boot up application, AKA Turn the lights on.
 */
$app = require_once __DIR__ . '/../src/bootstrap/app.php';

/**
$kernel = $app->resolve(App\Http\HttpKernel::class);


$kernel->bootstrapApplication();

/**
 * Passing our Request through the app

$app->run();
*/



$kernel = $app->make(Kernel::class);


$response = tap($kernel->handle(
    $request = Request::capture()
))->send();

$kernel->terminate($request, $response);








