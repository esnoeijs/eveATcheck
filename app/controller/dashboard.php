<?php


namespace eveATcheck\controller;

use eveATcheck\lib\database\database;
use eveATcheck\lib\rulechecker\rulechecker;
use Slim\Slim;

class dashboard
{

    /**
     * Frontpage
     *
     * @param \Slim\Slim $app
     */
    public function action_index(Slim $app)
    {
        if (!$app->user->isLoggedin())
            $setups = array();
        else
            $setups = $app->evefit->getSetups();


        $tour   = $app->rulechecker->getTournament();

        $app->render('dashboard.twig', array('setups' => $setups, 'tournament' => $tour, 'user' => $app->user));
    }

    /**
     * Returns partialHTML of a list of fits.
     *
     * @param \Slim\Slim $app
     */
    public function action_listAll(Slim $app)
    {
        if (!$app->user->isLoggedin()) return false;

        $setups = $app->evefit->getSetups();
        $tour   = $app->rulechecker->getTournament();

        foreach ($setups as $setup)
        {
            $app->render('setup/setup.twig', array('setup' => $setup, 'tournament' => $tour));
        }
    }

    /**
     * Returns partialHTML for a given setup by setupId
     *
     * @param \Slim\Slim $app
     * @param String $setupId
     */
    public function action_list(Slim $app, $setupId)
    {
        if (!$app->user->isLoggedin()) return false;

        $setups = $app->evefit->getSetups();
        $tour   = $app->rulechecker->getTournament();


        foreach ($setups as $setup)
        {
            if ($setup->getId() == $setupId)
                $app->render('setup/setup.twig', array('setup' => $setup, 'tournament' => $tour));
        }
    }

}