<?php
/**
 * UserController
 * @package api-user
 * @version 0.2.1
 */

namespace ApiUser\Controller;

use LibUser\Library\Fetcher;
use LibFormatter\Library\Formatter;
use ApiUser\Library\Formatter as _Fmt;
use LibForm\Library\Form;
use LibUserMain\Model\User;

class UserController extends \Api\Controller
{
    public function createAction()
    {
        if(!$this->app->isAuthorized())
            return $this->resp(401);
        if(!$this->app->hasScope('user-create'))
            return $this->resp(401);

        $form = new Form('api-user.create');
        if(!($valid = $form->validate())) {
            return $this->resp(422, $form->getErrors());
        }

        $valid->password = $this->user->hashPassword($valid->password);

        if(!($id = User::create((array)$valid)))
            return $this->resp(500, User::lastError());

        $user = Fetcher::getOne(['id' => $id]);

        $user = Formatter::format('user', $user);
        _Fmt::format($user);
        return $this->resp(0, $user);
    }

    public function indexAction(){
        if(!$this->app->isAuthorized())
            return $this->resp(401);

        list($page, $rpp) = $this->req->getPager();

        $cond = [
            'status' => ['__op', '>', 0]
        ];
        if($q = $this->req->getQuery('q'))
            $cond['q'] = $q;

        $users = Fetcher::get($cond, $rpp, $page, ['name' => 'DESC']);
        $users = !$users ? [] : Formatter::formatMany('user', $users);
        _Fmt::formatMany($users);

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

        $cond = [
            'id' => $identity,
            'status' => ['__op', '>', 0]
        ];

        $user = Fetcher::getOne($cond);
        if(!$user){
            $cond = [
                'status' => ['__op', '>', 0],
                'name' => $identity
            ];
            $user = Fetcher::getOne($cond);
        }

        if(!$user)
            return $this->resp(404);

        $user = Formatter::format('user', $user);
        _Fmt::format($user);

        $this->resp(0, $user);
    }
}
