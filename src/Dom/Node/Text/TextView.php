<?php

namespace Pes\Dom\Node\Text;

use Pes\View\ViewInterface;

/**
 * TextView je zjednodušeným modelem elementu node v DOM HTML. Má pouze textový obsah. Textový obsah je vytvářen "lazy" při  
 * volání metody getText() a je generován pomocí view - objektem typu ViewInterface - zadaným v konstruktoru.
 *
 * @author pes2704
 */
class TextView extends TextAbstract implements TextViewInterface {

    /**
     * @var ViewInterface 
     */
    private $view;

    /**
     * Prijímá View určený ge generování textového obsahu. View generuje (lazy) textový obsah s použitím dat zadaných při 
     * volání matody getText($data).
     * 
     * @param ViewInterface $view
     */
    public function __construct(ViewInterface $view) {
        parent::__construct();
        $this->view = $view;
    }
    
    /**
     * 
     * @return ViewInterface
     */
    public function getView(): ViewInterface {
        return $this->view;
    }
        
    /**
     * Vrací návratovou hodnotu renderování view zadaného jako prametr konstruktoru. 
     * V této metodě je volána metoda render() zadaného view a jsou jí předána data zadaná jako parametr metody ($view->render(NULL, $data)). 
     * View objekt tedy generuje textový obsah s použitím template zadané metodou setTemplate() a dat zadaných metodou setData() nebo dat zadaných při volání této matody. 
     * 
     * @param array $data
     * @return string
     */
    public function getText($data=[]) {
        return $this->view->getString(NULL, $data);
    }  
}
