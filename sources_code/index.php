<?php 
include_once("functions.php");
include_once("CFG.php");
include_once("CYKGenerator.php");

if( isset($_POST['submit']) ){
    if( isset($_POST['stcInput']) && !empty($_POST['stcInput']) ){

        $sentence = $_POST['stcInput'];
        $cfg = new CFG(get_rules("./assets/rules.txt"), "K"); // buat cfg
        $cyk = new CYKGenerator($cfg); // buat cyk
        $cyk = $cyk->generate_table($sentence);
        $cyk = $cyk->solve();

        if( $cyk->validation() ){
            $message =  "The sentence is valid";
            $color = "blueT";
        }else{
            $message = "Invalid sentence";
            $color = "redT";
        }
    }
}

if( isset($_POST['files']) ){
    if( isset($_FILES['fileTest'])  && isset($_FILES['fileValid'])){
        $fileTest = save($_FILES['fileTest']);
        $fileValid = save($_FILES['fileValid']);

        // test
        $status = [];
        $tests = read_file("./doc/$fileTest");
        $cfg = new CFG(get_rules("./assets/rules.txt"), "K"); // buat cfg
        $cyk = new CYKGenerator($cfg); // buat cyk
        foreach( $tests as $test ){
            $test = trim($test, ".");
            
            $cyk = $cyk->generate_table($test);
            $cyk = $cyk->solve();
            if( $cyk->validation() ){
                $status[] =  "valid";
            }else{
                $status[] = "invalid";
            }
        }

        // hitung akurasi
        $benar = 0;
        $valids = read_file("./doc/$fileValid");
        foreach( $valids as $pos => $valid ){
            $valid = explode(".", $valid);
            $validStatus = trim(end($valid));
            $validStatus = strtolower(trim(explode(" ", $validStatus)[0]));
            if( $validStatus == 'tidak'){
                $validStatus = "invalid";
            }else{
                $validStatus = "valid";
            }

            if( $status[$pos] == $validStatus ){
                $benar++;
            }
        }

        $akurasi = ($benar/count($valids)) * 100;
        unlink("./doc/$fileTest");
        unlink("./doc/$fileValid");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CYK Algorithm</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

    <div class="container">
        <div class="section1">
            <h2>Application of CFG in Syntatic Parsing of A Balinese Sentence</h2>
            <div class="form">
                <form action="./index.php" method="POST">
                    <label for="sentence">Sentence : </label>
                    <input type="text" id="sentence" name="stcInput" value="<?php if( isset($sentence) ) echo $sentence;?>">

                    <button type="submit" name="submit" class="mybtn">submit</button>
                </form>
            </div>
        </div>
        <hr class="line">
        <div class="section2">
            <?php if( isset($sentence) ) : ?>
                <h2 class="<?php echo $color; ?>"><?php echo $message; ?>.</h2>
            <?php endif; ?>
        </div>
        <div class="section3">
            <div class="form">
                <form action="./index.php" method="POST" enctype="multipart/form-data">
                    <label for="fileTest">File Test  : </label>
                    <input type="file" id="fileTest" name="fileTest"><br>

                    <label for="fileValid">File Validasi : </label>
                    <input type="file" id="fileValid" name="fileValid">

                    <button type="submit" name="files" >submit</button>
                </form>
            </div>
        </div>
        <div class="section4">
            <ul type="none">
                <?php if( isset($_POST['files']) ) :  ?>
                    <h3>Akurasi : </h3>
                    <pre><?php echo $akurasi; ?> %</pre>
                    <?php foreach( $tests as $pos => $test ) : ?>
                        <li><?php echo $pos+1 . ". " . $test;?> -- 
                            <span class="<?php if($status[$pos] == "valid") echo 'blueT'; else echo 'redT'; ?>"><?php echo $status[$pos]; ?></span> 
                        </li>
                        <hr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    
</body>
</html>