<?php
require_once __DIR__ . '/vendor/autoload.php';

use elrenardo\FoxPHPsrv;

//Création default Serveur
$srv = new FoxPHPsrv( __DIR__, 'class');


//Add BDD
try
{
	$bdd = new PDO('mysql:host=127.0.0.1;dbname=label_build', 'root', '');
}
catch(PDOException $e)
{
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}
$srv->addData('bdd', $bdd);


//Test rajout d'une route en mode AltoRouter
$srv->route('/', function( $data )
{
	header('Location: ./view/');
});

//Lancement ctrl
$srv->exec();