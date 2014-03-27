<?php

namespace eveATcheck\controller;

use eveATcheck\lib\database\database;
use eveATcheck\lib\evefit\evefit;

class fit
{


    public function action_addDialog(\Slim\Slim $app, $setupId)
    {

        $app->render('fit/addDialog.twig', array('setupId' => $setupId));
    }

    /**
     * Add a fit to the user session
     *
     * @method POST
     * @param \Slim\Slim $app
     */
    public function action_add(\Slim\Slim $app, $setupId)
    {
        $fit  = $app->request()->post('fit');
        $desc = $app->request()->post('description');

        $app->evefit->addFit($fit, $desc, $setupId);
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