<?php


namespace eveATcheck\controller;

use eveATcheck\lib\database\database;
use eveATcheck\lib\evefit\lib\setup;
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

    public function action_heartbeat(Slim $app)
    {
        if (!$app->user->isLoggedin()) return false;

        $lastModified = new \DateTime('00-00-0000');

        $setups = $app->evefit->getSetups();
        /** @var setup $setup */
        foreach ($setups as $setup)
        {
            foreach ($setup->getFits() as $fit)
            {
                $lastModTmp = $fit->getUpdateDate();

                if ($lastModTmp > $lastModified) $lastModified = $lastModTmp;
            }
        }

        echo $lastModified->format('U');
    }
}