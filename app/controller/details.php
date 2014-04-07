<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 4/3/14
 * Time: 1:00 AM
 */

namespace eveATcheck\controller;


use Slim\Slim;

class details
{
    /**
     * Detail page
     *
     * @param \Slim\Slim $app
     */
    public function action_index(Slim $app, $setupId)
    {
        if (!$app->user->isLoggedin()) return false;

        $setup  = $app->evefit->getSetup($setupId);
        $tour   = $app->rulechecker->getTournament();

        if ($app->request()->isAjax())
            $app->render('setup/setupDetails.twig', array('setup' => $setup, 'tournament' => $tour, 'user' => $app->user));
        else
            $app->render('setup/details.twig', array('setup' => $setup, 'tournament' => $tour, 'user' => $app->user));
    }

    /**
     * Returns partialHTML of a list of fits.
     *
     * @param \Slim\Slim $app
     */
    public function action_fitList(Slim $app, $setupId, $fitId)
    {
        if (!$app->user->isLoggedin()) return false;

        $setup = $app->evefit->getSetup($setupId);
        $fit   = $setup->getFit($fitId);
        $tour   = $app->rulechecker->getTournament();

        $app->render('fit/fit.twig', array('setup' => $setup, 'fit' => $fit, 'tournament' => $tour ));
    }

    public function action_heartbeat(Slim $app, $setupId)
    {
        if (!$app->user->isLoggedin()) return false;

        $lastModified = new \DateTime('00-00-0000');

        $setup = $app->evefit->getSetup($setupId);
        foreach ($setup->getFits() as $fit)
        {
            $lastModTmp = $fit->getUpdateDate();

            if ($lastModTmp > $lastModified) $lastModified = $lastModTmp;
        }

        echo $lastModified->format('U');
    }
} 