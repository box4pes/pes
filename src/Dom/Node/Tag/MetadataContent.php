<?php

namespace Pes\Dom\Node\Tag;

/**
 * Description of Metadata - třída je potomkem TagAbstract a slouží jen k jako společný předek všem tagům s metadata obsahem: 
 * Title, Style, Base, Link, Meta, Script, Noscript. 
 * Metadata tagy jsou tagy přípustné jako potomci tagu Head.
 *
 * @author pes2704
 */
class MetadataContent extends TagAbstract {

    public function getAttributesNode() {
        ;
    }    
}
