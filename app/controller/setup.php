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