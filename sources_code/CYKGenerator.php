<?php 

class CYKGenerator{

    public $table;
    public $cfg;
    public $sentence;
    public $words;
    public $n;
    public $valid;

    public function __construct($cfg){
        $this->table = [];
        $this->cfg = $cfg;
    }

    public function generate_table($sentence){

        $this->sentence = $sentence = trim(strtolower($sentence)); // bersihkan string
        $this->words = $words = explode(" ", $sentence); // mencari tiap kata
        $this->n = $n = count($words); // jumlah kata

        // generate tabel baris pertama
        for( $i = 0; $i < $n; $i++ ){
            $words[$i] = trim($words[$i]);
            $this->table[$i][$i] = implode(" ", part_of_speech($this->cfg->productions, $words[$i]));
        }
        // var_dump($this->n);
        // var_dump($this->table); die;
        return $this;
    }

    public function solve(){

        $tableFill = $this->table;
        $n = $this->n;
        $rules = $this->cfg->productions;

        for( $j = 1; $j < $n; $j++ ){
            for( $i=$j-1 ; $i >= 0 ; $i-- ){
                $tableFill[$i][$j] = []; //  himpunan kosong
                for($h=$i; $h <= $j-1; $h++ ){

                    // kombinasi RHS baru
                    // $new_rhs = [];
                    // if(!empty($tableFill[$i][$h]) && !empty($tableFill[$h+1][$j])){
                    //     $new_rhs = combine($tableFill[$i][$h], $tableFill[$h+1][$j]);
                    // }

                    $new_rhs = combine($tableFill[$i][$h], $tableFill[$h+1][$j]);

                    // perulangan untuk setiap kombinasi RHS baru, cek apakah memiliki terminal
                    foreach( $new_rhs as $rhs ){
                        $nonTerminal = part_of_speech($rules, $rhs);
                        if( count($nonTerminal) > 0 ){
                            $tableFill[$i][$j] = array_merge($tableFill[$i][$j], $nonTerminal);
                        }
                    }
                    // var_dump($tableFill);
                }
                // union
                $tableFill[$i][$j] = implode(" ", array_unique($tableFill[$i][$j]));
            }
        }

        $this->table = $tableFill;
        return $this;
    }

    public function validation(){

        $tableFill = $this->table;
        $n = $this->n;
        // var_dump($tableFill[0][$n-1]);
        $arr = explode(" ", $tableFill[0][$n-1]);
        if( in_array($this->cfg->startSymbol, $arr) ){
            return true;
        }else{
            return false;
        }
    }
}