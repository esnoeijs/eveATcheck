<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 4/3/14
 * Time: 1:00 AM
 */

namespace eveATcheck\controller;


class details
{
    /**
     * Detail page
     *
     * @param \Slim\Slim $app
     */
    public function action_index(\Slim\Slim $app, $setupId)
    {
        if (!$app->user->isLoggedin()) return false;

        $setup  = $app->evefit->getSetup($setupId);
        $tour   = $app->rulechecker->getTournament();

        if ($app->request()->isAjax())
            $app->render('setup/setupDetails.twig', array('setup' => $setup, 'tournament' => $tour, 'user' => $app->user));
        else
            $app->render('setup/details.twig', array('setup' => $setup, 'tournament' => $tour, 'user' => $app->user));
    }
} 