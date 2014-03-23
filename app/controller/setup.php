<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/22/14
 * Time: 2:00 PM
 */

namespace eveATcheck\controller;


use Slim\Slim;

class setup
{
    /**
     * Returns the form HTML to create a new setup.
     * @param Slim $app
     */
    public function action_addDialog(Slim $app)
    {
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
        $name = $app->request()->post('name');
        $desc = $app->request()->post('description');

        $app->evefit->addSetup(new \eveATcheck\lib\evefit\lib\setup($name, $desc));

        return;
    }

    /**
     * Returns partialHTML of a list of fits.
     *
     * @param \Slim\Slim $app
     */
    public function action_listAll(Slim $app)
    {
        $setups = $app->evefit->getSetups();

        foreach ($setups as $setup)
        {
            $app->render('setup/setup.twig', array('setup' => $setup ));
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
        $setups = $app->evefit->getSetups();

        foreach ($setups as $setup)
        {
            if ($setup->getId() == $setupId)
            $app->render('setup/setup.twig', array('setup' => $setup ));
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
        $app->evefit->deleteSetup($setupId);
    }



} 