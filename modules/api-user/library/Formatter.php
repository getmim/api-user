<?php
/**
 * Formatter
 * @package api-user
 * @version 0.0.1
 */

namespace ApiUser\Library;


class Formatter
{
    static function format(object &$user): void{
        $rf = \Mim::$app->config->apiUser->formatter->remove ?? [];
        if(!$rf)
            return;
        foreach($rf as $fl => $vl){
            if(property_exists($user, $fl))
                unset($user->$fl);
        }
    }

    static function formatMany(array &$users): void{
        foreach($users as &$user)
            self::format($user);
    }
}