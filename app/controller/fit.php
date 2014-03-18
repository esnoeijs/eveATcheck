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

        $evefit = new evefit($db);
        $fits = $evefit->parseEFT($fit);

        if (!isset($_SESSION['fits'])) $_SESSION['fits'] = array();
        $_SESSION['fits'] = array_merge($fits, $_SESSION['fits']);


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

    public function action_list(\Slim\Slim $app)
    {
        if (!isset($_SESSION['fits'])) $_SESSION['fits'] = array();

        foreach ($_SESSION['fits'] as $fit)
        {
            $app->render('fit/fit.twig', array('test' => 'hank', 'fit' => $fit ));
        }
    }


} 