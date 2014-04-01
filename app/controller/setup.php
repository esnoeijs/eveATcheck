<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/22/14
 * Time: 2:00 PM
 */

namespace eveATcheck\controller;


use eveATcheck\lib\evefit\evefit;
use Slim\Slim;

class setup
{
    /**
     * Detail page
     *
     * @param \Slim\Slim $app
     */
    public function action_details(\Slim\Slim $app, $setupId)
    {
        if (!$app->user->isLoggedin()) return false;

        $setup  = $app->evefit->getSetup($setupId);
        $tour   = $app->rulechecker->getTournament();

        $app->render('setup/details.twig', array('setup' => $setup, 'tournament' => $tour, 'user' => $app->user));
    }


    /**
     * Returns the form HTML to create a new setup.
     * @param Slim $app
     */
    public function action_addDialog(Slim $app)
    {
        if (!$app->user->isLoggedin()) return false;

        $app->render('setup/addDialog.twig');
    }

    /**
     * Creates a new empty setup with the given name and description and
     * adds it to the user session
     *
     * @param Slim $app
     */
    public function action_add(Slim $app)
    {
        if (!$app->user->isLoggedin()) return false;

        $name = $app->request()->post('name');
        $desc = $app->request()->post('description');

        $app->evefit->addSetup(new \eveATcheck\lib\evefit\lib\setup(null, $name, $desc, $app->user->getId()));

        return;
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

    /**
     * Deletes a setup from the user session.
     *
     * @param Slim $app
     * @param String $setupId
     */
    public function action_delete(Slim $app, $setupId)
    {
        if (!$app->user->isLoggedin()) return false;

        $app->evefit->deleteSetup($setupId);
    }

}