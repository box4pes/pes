<?php

namespace Pes\Document;

/**
 *
 * @author pes2704
 */
interface DocumentInterface {
    public function includeDocument(DocumentInterface $mergedDocument);
    public function getString();
}
