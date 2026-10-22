<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Model\Repository\Association;

use Pes\Model\Hydrator\HydratorInterface;
use Pes\Model\Repository\RepoInterface;

/**
 * Description of AssotiationAbstract
 *
 * @author pes2704
 */
abstract class AssociationAbstract implements AssociationInterface {

    /**
     *
     * @var HydratorInterface
     */
    protected $childHydrator;

    /**
     * @var RepoInterface
     */
    protected $childRepo;  // zde je jen pro případ, že by nebyla definována v konkrétní asociaci

    /**
     *
     * @param HydratorInterface $childHydrator
     * @param RepoAssotiatedOneInterface $childRepo
     */
    public function __construct(HydratorInterface $childHydrator, RepoInterface $childRepo) {
        $this->childHydrator = $childHydrator;
        $this->childRepo = $childRepo;
    }

    public function flushChildRepo(): void {
        $this->childRepo->flush();
    }
}
