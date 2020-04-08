<?php
namespace Pes\Entity\Persistable;

/**
 * Description of IdentityFiald
 *
 * @author pes2704
 */
class Identity implements IdentityInterface {
    
    private $id=FALSE;
    
    /**
     * 
     * @param scalar $id Pro použití collection a repository je nutné pouřívat id skalárního typu - používá se jako index.
     */
    public function __construct($id) {
        $this->id = $id;
    }
    
    public function getId() {
        return $this->id;
    }
}
