<?php

namespace Kurumi\UserBundle\User;

use Kurumi\UserBundle\Entity\User;

class SearchInfoProvider
{
    public function getSearchInfo(User $user)
    {
        $searchInfo = "Looking for";
        if ($user->getProfile()->getLookingFor() === 1) {
            $searchInfo .= " a guy";
        } elseif ($user->getProfile()->getLookingFor() === 2) {
            $searchInfo .= " a girl";
        } else {
            $searchInfo .= " someone";
        }
        if ($user->getProfile()->getLookingAgedFrom() || $user->getProfile()->getLookingAgedTo()) {
            $searchInfo .= " aged";
            if ($user->getProfile()->getLookingAgedFrom()) {
                $searchInfo .= " from " . $user->getProfile()->getLookingAgedFrom();
            }
            if ($user->getProfile()->getLookingAgedTo()) {
                $searchInfo .= " to " . $user->getProfile()->getLookingAgedTo();
            }
        }
        if ($user->getProfile()->getCity()) {
            $city = $user->getProfile()->getCity();
            $searchInfo .= ", near " . $city->getName();
        }
        $searchInfo .= ".";
        return $searchInfo;
    }
}