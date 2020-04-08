<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Acl;

/**
 * Description of RoleResources
 *
 * @author pes2704
 */
class RoleResources implements RoleResourcesInterface {

    private $role;
    private $resouces =[];

    public function __construct($role, array $resources) {
        $this->role = $role;
        $this->resouces = $resources;
    }

    public function getRole() {
        return $this->role;
    }

    public function getResouces() {
        return $this->resouces;
    }
}
