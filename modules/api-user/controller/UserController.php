<?php
/**
 * UserController
 * @package api-user
 * @version 0.0.1
 */

namespace ApiUser\Controller;

use LibUser\Library\Fetcher;
use LibFormatter\Library\Formatter;

class UserController extends \Api\Controller
{
    private function format(object &$user): void{
        $rf = $this->config->apiUser->formatter->remove ?? [];
        if(!$rf)
            return;
        foreach($rf as $fl => $vl){
            if(property_exists($user, $fl))
                unset($user->$fl);
        }
    }

    private function formatMany(array &$users): void{
        foreach($users as &$user)
            self::format($user);
    }

    public function indexAction(){
        if(!$this->app->isAuthorized())
            return $this->resp(401);

        list($page, $rpp) = $this->req->getPager();

        $cond = [];
        if($q = $this->req->getQuery('q'))
            $cond['q'] = $q;

        $users = Fetcher::get($cond, $rpp, $page, ['name' => 'DESC']);
        $users = !$users ? [] : Formatter::formatMany('user', $users);
        self::formatMany($users);

        foreach($users as &$pg)
            unset($pg->content, $pg->meta);
        unset($pg);

        $this->resp(0, $users, null, [
            'meta' => [
                'page'  => $page,
                'rpp'   => $rpp,
                'total' => Fetcher::count($cond)
            ]
        ]);
    }

    public function singleAction(){
        if(!$this->app->isAuthorized())
            return $this->resp(401);

        $identity = $this->req->param->identity;

        $user = Fetcher::getOne(['id'=>$identity]);
        if(!$user)
            $user = Fetcher::getOne(['name'=>$identity]);

        if(!$user)
            return $this->resp(404);

        $user = Formatter::format('user', $user);
        self::format($user);

        $this->resp(0, $user);
    }
}