<?php
/**
 * Created by PhpStorm.
 * User: assane
 * Date: 30/08/16
 * Time: 19:19
 */

namespace App\Http\Controllers;


use App\Repository\RepositoryAnneesScol;
use App\Repository\RepositoryDashboard;
use App\Repository\RepositoryInfos;
use App\Repository\RepositoryInvoice;
use Psr\Http\Message\ResponseInterface;

class DashboardController {



    public function index(): ResponseInterface
    {



        $dashboard = new RepositoryDashboard(new \DB, 'anneescolaire');
        $staticDash = $dashboard;
           // dd($Dashboard->detailMFCount());
            //dd($Dashboard->TotalClasse());


        $secsion = new RepositoryAnneesScol(new \DB, TBL_ANNEESCOL);
        $infos = new RepositoryInfos(new \DB, TBL_INFOS);
        $invoic = new RepositoryInvoice(new \DB, TBL_ENCAISSEMENT);
        $invoic = $invoic->getLastInvoic();
        $dashboard = [
            'tCl'=>$dashboard->TotalClasse(),
            'Inc'=>$dashboard->TotalInscrits(),
            'recolIns'=>$dashboard->InsctRecolte(),
            'recoMens'=>$dashboard->MensuelRecolte(),
            'encaissemnt'=>$dashboard->Totalencaisser(),
            'mf' => $dashboard->detailMFCount()
        ];

        return view( 'dashboard.home', compact('secsion','dashboard','infos','invoic','staticDash'));
    }
}
