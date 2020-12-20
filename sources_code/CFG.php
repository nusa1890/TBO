<?php 

class CFG{
    public $variabels;
    public $terminals;
    public $productions;
    public $startSymbol;

    public function __construct($productions, $startSymbol, $variabels = " ", $terminals = " "){
        $this->variabels = $variabels;
        $this->terminals = $terminals;
        $this->productions = $productions;
        $this->startSymbol = $startSymbol;
    }

}