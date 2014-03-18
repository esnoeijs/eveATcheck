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
        $fits = array();
        if (isset($_SESSION['fits']))
        {
            $fits = $_SESSION['fits'];

            if (count($_SESSION['fits'])!=0)
            {
                $rules = new rulechecker($app->db);
                /** fit $fit */
                foreach ($_SESSION['fits'] as &$fit)
                {
                    $rules->checkFit($fit);
                }
            }
        }

        $app->render('dashboard.twig', array('fits' => $fits,'flash' => $app->flash['messageGood']  ));
    }
}



