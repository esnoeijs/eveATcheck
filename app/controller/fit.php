<?php

namespace eveATcheck\controller;

use eveATcheck\lib\database\database;
use eveATcheck\lib\evefit\evefit;

class fit {

    /**
     * Add a fit to the user session
     *
     * @method POST
     * @param \Slim\Slim $app
     */
    public function action_add(\Slim\Slim $app)
    {
        $fit = $app->request()->post('fit');
        $db  = $app->db;

        $app->evefit->addFit($fit);

        if ($app->request()->isAjax())
        {
            return;
        }
        else
        {
            $app->flash('messageGood','Fits successfully added');
            $app->redirect('/');
        }
    }

    /**
     * Returns partialHTML of a list of fits.
     *
     * @param \Slim\Slim $app
     */
    public function action_list(\Slim\Slim $app)
    {
        $fits = $app->evefit->getFits();

        foreach ($fits as $fit)
        {
            $app->render('fit/fit.twig', array('fit' => $fit ));
        }
    }


} 