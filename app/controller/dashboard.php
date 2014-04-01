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
        if (!$app->user->isLoggedin())
            $setups = array();
        else
            $setups = $app->evefit->getSetups();


        $tour   = $app->rulechecker->getTournament();

        $app->render('dashboard.twig', array('setups' => $setups, 'tournament' => $tour, 'user' => $app->user));
    }

}