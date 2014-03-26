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
        $setups = $app->evefit->getSetups();

        $app->render('dashboard.twig', array('setups' => $setups));
    }

}