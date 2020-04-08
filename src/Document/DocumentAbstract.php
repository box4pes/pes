<?php

namespace Pes\Document;

/**
 *
 * @author pes2704
 * 
 * TODO: !!!! upravit - celý koncept je špatně - dokument je model, měl by mít objekt pro ukládání (např. do souboru), např. fileMapper.
 * K tomu snad může mít metodu, která vrací textovou reprezentaci modelu (viz DOMdocument) a tuto metodu pak budeš volat např. ve view
 */
abstract class DocumentAbstract implements DocumentInterface {

}
