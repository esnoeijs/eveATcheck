<?php

namespace eveATcheck\lib\evefit;


use Slim\Middleware;

class shipSortMiddleware extends Middleware
{
    /**
     * Call
     *
     * Perform actions specific to this middleware and optionally
     * call the next downstream middleware.
     */
    public function call()
    {
        $sortOrder = $this->app->request->get('shipSort', null);
        if (is_null($sortOrder)) {
            $sortOrder = $this->app->getCookie('sortOrder');
        }else{
            $this->app->setCookie('sortOrder', $sortOrder, time()+31556926);
        }
        $this->app->view()->set('sortOrder',$sortOrder);
        $this->app->evefit->setSort($sortOrder);
        $this->next->call();
    }

}