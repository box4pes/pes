<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Acl;

/**
 * Description of UserGroups
 *
 * @author pes2704
 */
class UserRoles implements UserRolesInterface {

    private $user;
    private $roles=[];

    public function __construct($user, array $roles) {
        $this->user = $user;
        $this->roles = $roles;
    }

    public function getUser() {
        return $this->user;
    }

    public function getGroups() {
        return $this->roles;
    }
}
