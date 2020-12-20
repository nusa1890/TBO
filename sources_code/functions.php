<?php 

function read_file($path){
    $file = fopen($path,"r");
    $container = [];
    while ( $line = fgets($file) ){
        $container[] =  trim(strtolower($line));
    }
    fclose($file);
    return array_unique($container);
}

function get_rules($path){
    $clean_rules = [];
    $file = fopen($path,"r");
    $leksikon = ['BdLeksikon', 'SfLeksikon', 'BilLeksikon', 'GtLeksikon', 'KjLeksikon', 'PnLeksikon', 'PsLeksikon'];
    // explode tanda panah
    while( $rule = fgets($file) ){
        $new_rule = explode("->", $rule);
        $nonTerminal = trim($new_rule[0]); // bersihin spasi
        $rhs = trim($new_rule[1]); // bersihin spasi

        // jika rhs adalah leksikon
        if( in_array($rhs, $leksikon) ){
            $rhs = read_file("./assets/" . $rhs . ".txt");
            $clean_rules[$nonTerminal] =  $rhs;
        }else{
            $clean_rules[$nonTerminal][] =  $rhs;
        }
    }
    return $clean_rules;
}

function part_of_speech($rules, $value){

    $arr = [];
    foreach($rules as $nonTerminal => $rhs){
        if( in_array($value, $rhs) ){
            $arr[] = $nonTerminal;
        }
    }
    return $arr;
}

function combine($left, $right){
    // ubah ke array
    $left = explode(" ", $left);
    $right = explode(" ", $right);

    // kombinasi nested loop
    $new = [];
    foreach( $left as $l ){
        foreach( $right as $r ){
            $new[] = $l . " " . $r;
        }
    }

    return $new;
}

function get_combinations($arrays) {
	$result = array(array());
	foreach ($arrays as $property => $property_values) {
		$tmp = array();
		foreach ($result as $result_item) {
			foreach ($property_values as $property_value) {
				$tmp[] = array_merge($result_item, array($property => $property_value));
			}
		}
		$result = $tmp;
	}
	return $result;
}

function save ($files){
	// membentuk array multidimensi, sudah pasti ada indeks array name, size, error, tmp_name
	$namafile = $files['name'];
	$ukuranfile = $files['size'];
	$error = $files['error'];
	$tmpname = $files['tmp_name']; // tmp_name = letak file sementara

	// cek apakah tdk ada yg diupload
	// 4 = pesan error yang berarti tdk ada  yang diupload
	if ($error === 4){
        return false;
	}

	// cek apakah yg di upload adalah teks
	$ekstensivalid = ['txt'];
	$ekstensigambar = explode('.', $namafile);
	// explode(delimiter, string) = berfungsi untuk memecah string, delimiter adalah pembatasnya
	$ekstensigambar = strtolower(end($ekstensigambar)); 
	//end = berfungsi untuk mengambil indeks array terakhir

	if ( !in_array($ekstensigambar, $ekstensivalid) ){
		// in_array(needle, haystack), berfungsi untuk mencari string dalam array

		echo "<script>
				alert ('yang anda upload bukan txt !');
				</script>";
		return false;
	}

	// cek jika ukurannya terlalu besar
	if ($ukuranfile > 1000000){
		echo "<script>
				alert ('ukuran gambar terlalu besar !');
				</script>";
		return false;

	}

	// lolos pengecekan, teks siap diupload
	// generate nama teks baru
	$namafilebaru = uniqid(); //uniqid (), membuat str random
	// var_dump($namafilebaru); die;
	$namafilebaru .= '.'; 
	$namafilebaru .= $ekstensigambar;
	// var_dump($namafilebaru); die;

	move_uploaded_file($tmpname, './doc/'. $namafilebaru);
		// move_uploaded_file(filename, destination), memindahkan file yang diupload ke direktori yang diinginkan
	return $namafilebaru;
}

function insert(){

}