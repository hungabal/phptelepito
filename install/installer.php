<html>
<head>
    <title>Telepítő</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.4/css/bulma.min.css" integrity="sha512-HqxHUkJM0SYcbvxUw5P60SzdOTy/QVwA1JJrvaXJv4q7lmbDZCmZaqz01UPOaQveoxfYRv1tHozWGPMcuTBuvQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<div class="container">
<?php
$html = '';

/*
 * 1:
 * TELEPÍTÉSI KÖVETELMÉNYEK:
 */
if(isset($_POST) && isset($_POST["kovetelmeny_kuld"])){
    $hiba = false;

    if(!isset($_POST["adatbazis"]) && $_POST["adatbazis"] != "on"){
        $hiba = true;
    }
    if(isset($_POST["composer"]) && $_POST["composer"] == "on"){
        $output = shell_exec('composer --version');
        if(!$output){
            $hiba = true;
        }
    } else {
        $hiba = true;
    }
    if(!extension_loaded('curl')){
        $hiba = true;
    }
    if(!extension_loaded('gd')){
        $hiba = true;
    }
    if(!extension_loaded('intl')){
        $hiba = true;
    }
    if(!extension_loaded('mysqli')){
        $hiba = true;
    }
    if(!extension_loaded('pdo_mysql')){
        $hiba = true;
    }

    if($hiba){
        header('Location: http://localhost/dashboard/telepito/install/installer.php?step=1&hiba=1');
    } else {
        unlink("../.env");

        header('Location: http://localhost/dashboard/telepito/install/installer.php?step=2&hiba=0');
    }
}

/*
 * 2:
 * ADATBEKÉRÉSEK:
 */
if(isset($_POST) && isset($_POST["adatbekeres_kuld"])){
    $json = file_get_contents('../composer.json');
    $json_data = json_decode($json,true);
    if($json_data["name"] != "laravel/laravel"){
        $host = $_POST["host"];
        $database = $_POST["database"];
        $user = $_POST["user"];
        $password = $_POST["password"];

        copy("db.conf.php","../public/db.conf.php");

        $db_file_string = file_get_contents("../public/db.conf.php");

        $db_file_string = str_replace("localhost",$host,$db_file_string);
        $db_file_string = str_replace("db",$database,$db_file_string);
        $db_file_string = str_replace("user",$user,$db_file_string);
        $db_file_string = str_replace("password",$password,$db_file_string);

        file_put_contents("../public/db.conf.php",$db_file_string);

        include_once '../public/db.conf.php';

        $query = file_get_contents('adatdb.sql');
        if (mysqli_multi_query($connection, $query)){
            header('Location: http://localhost/dashboard/telepito/install/installer.php?step=3');
        }
        else {
            header('Location: http://localhost/dashboard/telepito/install/installer.php?step=2&hiba=1');
        }
    } else {
        $host = $_POST["host"];
        $port = $_POST["port"];
        $database = $_POST["database"];
        $user = $_POST["user"];
        $password = $_POST["password"];

        copy(".env","../public/.env");

        $db_file_string = file_get_contents("../public/.env");
        $db_file_string = str_replace("127.0.0.1",$host,$db_file_string);
        $db_file_string = str_replace("3306",$port,$db_file_string);
        $db_file_string = str_replace("laravel",$database,$db_file_string);
        $db_file_string = str_replace("root",$user,$db_file_string);
        $db_file_string = str_replace("password",$password,$db_file_string);

        file_put_contents("../public/.env",$db_file_string);

        $output = shell_exec('composer update');
        die(var_dump($output));

        $output = shell_exec('php artisan migrate');
        die(var_dump($output));
    }
}

/*
 * 3:
 * KÉSZ:
 */
if(isset($_POST) && isset($_POST["vege_kuld"])){
    $output = shell_exec('php artisan key:generate');

    header('Location: http://localhost/dashboard/telepito/');
}

/*
 * 1:
 * TELEPÍTÉSI KÖVETELMÉNYEK:
 */
if(isset($_GET['step']) && $_GET['step'] == 1){
    if($_GET['hiba'] == 1){
        $html .= '<p style="color: red">A követelménynek nem felesz meg!</p>';
        $html .= '<br><br>';
    }
    $html .= '<section class="section is-medium">';
    $html .= '<form action="#" method="post">';
    $html .= '<h1 style="font-weight: bold">Követelmények:</h1>';
    $html .= '<br>';
    $html .= '<ul>';
    $php_verzio = explode(".",phpversion());
    if($php_verzio[0] >= 7){
        $html .= '<li>PHP --- <i class="fa-solid fa-check" style="color: greenyellow"></i></li>';
    } else {
        $html .= '<li>PHP --- <i class="fa-solid fa-xmark" style="color: red"></i></li>';
    }
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $html .= '<li>WINDOWS --- <i class="fa-solid fa-check" style="color: greenyellow"></i></li>';
    } elseif (strtoupper(substr(PHP_OS, 0, 3)) === 'LIN') {
        $html .= '<li>LINUX --- <i class="fa-solid fa-check" style="color: greenyellow"></i></li>';
    } else {
        $html .= '<li>'.PHP_OS.' --- <i class="fa-solid fa-xmark" style="color: red"></i></li>';
    }
    if(extension_loaded('curl')){
        $html .= '<li>CURL --- <i class="fa-solid fa-check" style="color: greenyellow"></i></li>';
    } else {
        $html .= '<li>CURL --- <i class="fa-solid fa-xmark" style="color: red"></i></li>';
    }
    if(extension_loaded('gd')){
        $html .= '<li>GD --- <i class="fa-solid fa-check" style="color: greenyellow"></i></li>';
    } else {
        $html .= '<li>GD --- <i class="fa-solid fa-xmark" style="color: red"></i></li>';
    }
    if(extension_loaded('intl')){
        $html .= '<li>INTL --- <i class="fa-solid fa-check" style="color: greenyellow"></i></li>';
    } else {
        $html .= '<li>INTL --- <i class="fa-solid fa-xmark" style="color: red"></i></li>';
    }
    if(extension_loaded('mysqli')){
        $html .= '<li>MYSQLI --- <i class="fa-solid fa-check" style="color: greenyellow"></i></li>';
    } else {
        $html .= '<li>MYSQLI --- <i class="fa-solid fa-xmark" style="color: red"></i></li>';
    }
    if(extension_loaded('pdo_mysql')){
        $html .= '<li>PDO_MYSQL --- <i class="fa-solid fa-check" style="color: greenyellow"></i></li>';
    } else {
        $html .= '<li>PDO_MYSQL --- <i class="fa-solid fa-xmark" style="color: red"></i></li>';
    }
    $html .= '<li><label class="checkbox" for="adatbazis">Adatbázis létrehozva? ---&nbsp;</label><input type="checkbox" id="adatbazis" name="adatbazis"></li>';
    $html .= '<li><label class="checkbox" for="composer">Composer telepítve? ---&nbsp;</label><input type="checkbox" id="composer" name="composer"></li>';
    $html .= '</ul>';
    $html .= '<br>';
    $html .= '<input class="button is-info" type="submit" name="kovetelmeny_kuld" value="Tovább">';
    $html .= '</form>';
    $html .= '</section>';
    echo $html;
}

/*
 * 2:
 * ADATBEKÉRÉSEK:
 */
if(isset($_GET['step']) && $_GET['step'] == 2){
    if($_GET['hiba'] == 1){
        $html .= '<p style="color: red">A belépéshez szükséges adatok hibásak!</p>';
        $html .= '<br><br>';
    }
    $json = file_get_contents('../composer.json');
    $json_data = json_decode($json,true);
    if($json_data["name"] != "laravel/laravel"){
        $html .= '<section class="section is-medium">';
        $html .= '<form action="#" method="post">';
        $html .= '<div class="field">';
        $html .= '<label class="label" for="host">Weboldal url:</label><br><input class="input" type="text" id="host" name="host" value="" placeholder="localhost"><br>';
        $html .= '</div>';
        $html .= '<div class="field">';
        $html .= '<label class="label" for="database">Adatbázis neve:</label><br><input class="input" type="text" id="database" name="database" value="" placeholder="adatbazisnev"><br>';
        $html .= '</div>';
        $html .= '<div class="field">';
        $html .= '<label class="label" for="user">Adatbázis felhasználónév:</label><br><input class="input" type="text" id="user" name="user" value="" placeholder="root"><br>';
        $html .= '</div>';
        $html .= '<div class="field">';
        $html .= '<label class="label" for="password">Adatbázis jelszó:</label><br><input class="input" type="text" id="password" name="password" value="" placeholder=""><br>';
        $html .= '</div>';
        $html .= '<br>';
        $html .= '<input class="button is-info" type="submit" name="adatbekeres_kuld" value="Tovább">';
        $html .= '</div>';
        $html .= '</form>';
        $html .= '</section>';
        echo $html;
    } else {
        $html .= '<section class="section is-medium">';
        $html .= '<form action="#" method="post">';
        $html .= '<div class="field">';
        $html .= '<label class="label" for="host">Weboldal url:</label><br><input class="input" type="text" id="host" name="host" value="" placeholder="localhost"><br>';
        $html .= '<label class="label" for="port">Weboldal port:</label><br><input class="input" type="text" id="port" name="port" value="" placeholder="3306"><br>';
        $html .= '<label class="label" for="database">Adatbázis neve:</label><br><input class="input" type="text" id="database" name="database" value="" placeholder="adatbazisnev"><br>';
        $html .= '<label class="label" for="user">Adatbázis felhasználónév:</label><br><input class="input" type="text" id="user" name="user" value="" placeholder="root"><br>';
        $html .= '<label class="label" for="password">Adatbázis jelszó:</label><br><input class="input" type="text" id="password" name="password" value="" placeholder=""><br>';
        $html .= '<br>';
        $html .= '<input class="button is-info" type="submit" name="adatbekeres_kuld" value="Tovább">';
        $html .= '</div>';
        $html .= '</form>';
        $html .= '</section>';
        echo $html;
    }
}

/*
 * 3:
 * KÉSZ:
 */
if(isset($_GET['step']) && $_GET['step'] == 3){
    $html .= '<section class="section is-medium">';
    $html .= '<form action="#" method="post">';
    $html .= '<h3>A beállítás sikeres!</h3>';
    $html .= '<div class="field">';
    $html .= '<input class="button is-success" type="submit" name="vege_kuld" value="Bezár">';
    $html .= '</div>';
    $html .= '</form>';
    $html .= '</section>';
    echo $html;
}

?>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" integrity="sha512-GWzVrcGlo0TxTRvz9ttioyYJ+Wwk9Ck0G81D+eO63BaqHaJ3YZX9wuqjwgfcV/MrB2PhaVX9DkYVhbFpStnqpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>
</html>
