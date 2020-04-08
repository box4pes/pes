<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Acl;

/**
 *
 * @author pes2704
 */
interface RoleResourcePermissionInterface {
    public function deny(RoleResources $roleResources);
    public function allow(RoleResources $roleResources);
}
