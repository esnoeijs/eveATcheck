<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/21/14
 * Time: 2:47 PM
 */

namespace eveATcheck\controller;


class user
{

    public function action_login(\Slim\Slim $app)
    {
        if ($app->request()->isPost())
        {
            $errors = array();

            $username  = $app->request()->post('username');
            $password  = $app->request()->post('password');

            $success = $app->user->login($username, $password);

            if (!$success)
            {
                $errors['general'] = 'Invalid login credentials';
            }

            $response = array(
                'errors' => $errors,
                'success' => (count($errors)==0)
            );

            echo json_encode($response);
            return;
        }

        $app->render('user/login.twig', array('user' => $app->user));
    }

    public function action_logout(\Slim\Slim $app)
    {
        $app->user->logout();
        $app->redirect("/");
    }

    public function action_register(\Slim\Slim $app)
    {
        if ($app->request()->isPost())
        {
            $errors = array();

            $username  = $app->request()->post('username');
            $password  = $app->request()->post('password');
            $password2 = $app->request()->post('password_verification');

            // Default checks
            if ($password !== $password2) $errors['password_verification'] = 'Passwords don\'t match';
            if (strlen($username)<=1) $errors['username'] = 'Username is too short';
            if (strlen($password)<=1) $errors['password'] = 'Password is too short';
            if ($app->model->getModel('user')->userExists($username)) $errors['username'] = 'Username already exists';

            if (count($errors)==0)
            {
                $errors = $app->user->register($username, $password);
            }

            $response = array(
                'errors' => $errors,
                'success' => (count($errors)==0)
            );

            echo json_encode($response);
            return;
        }

        $app->render('user/register.twig', array('user' => $app->user));
    }
} 