<?php


namespace eveATcheck\controller;

use eveATcheck\lib\database\database;
use eveATcheck\lib\rulechecker\rulechecker;

class dashboard
{

    /**
     * Frontpage
     *
     * @param \Slim\Slim $app
     */
    public function action_index(\Slim\Slim $app)
    {
//        $fits = $app->evefit->getFits();
//
//        if (count($fits)!=0)
//        {
//            $rules = new rulechecker($app->db);
//            /** fit $fit */
//            foreach ($fits as &$fit)
//            {
//                $fit = $rules->checkFit($fit);
//            }
//        }

        $setups = $app->evefit->getSetups();

        $app->render('dashboard.twig', array('setups' => $setups ));
    }
}



