<?php

namespace eveATcheck\controller;

use eveATcheck\lib\database\database;
use eveATcheck\lib\evefit\evefit;
use Slim\Slim;

class fit
{
    public function action_addDialog(Slim $app, $setupId)
    {
        if (!$app->user->isLoggedin()) return false;

        $app->render('fit/addDialog.twig', array('setupId' => $setupId));
    }

    public function action_editDialog(Slim $app, $setupId, $fitId)
    {
        if (!$app->user->isLoggedin()) return false;

        $setup = $app->evefit->getSetup($setupId);
        $fit   = $setup->getFit($fitId);

        $app->render('fit/editDialog.twig', array('setupId' => $setupId, 'fit' => $fit));
    }

    public function action_exportDialog(Slim $app, $setupId, $fitId)
    {
        if (!$app->user->isLoggedin()) return false;

        $setup = $app->evefit->getSetup($setupId);
        $fit   = $setup->getFit($fitId);

        $app->render('fit/exportDialog.twig', array('setupId' => $setupId, 'fit' => $fit));
    }

    /**
     * Add a fit to the user session
     *
     * @method POST
     * @param \Slim\Slim $app
     */
    public function action_add(Slim $app, $setupId)
    {
        if (!$app->user->isLoggedin()) return false;

        $fit  = $app->request()->post('fit');
        $desc = $app->request()->post('description');
        $quantity = $app->request()->post('quantity');

        $app->evefit->addFit($fit, $desc, $quantity, $setupId);
    }

    public function action_update(Slim $app, $setupId, $fitId)
    {
        if (!$app->user->isLoggedin()) return false;

        $newFit      = $app->request()->post('fit');
        $newDesc     = $app->request()->post('description');
        $newQuantity = $app->request()->post('quantity');

        $app->evefit->updateFit($newFit, $newDesc, $newQuantity, $setupId, $fitId);
    }

    /**
     * Deletes a fit from the user session.
     *
     * @param Slim $app
     * @param String $setupId
     */
    public function action_delete(Slim $app, $setupId, $fitId)
    {
        if (!$app->user->isLoggedin()) return false;

        $app->evefit->getSetup($setupId)->deleteFit($fitId);
        $app->evefit->save();
    }


} 