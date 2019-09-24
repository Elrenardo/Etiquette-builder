<?php


function debug( $err )
{
	echo '<pre>';
	var_dump( $err  );
	echo '</pre>';
}

function PrintJson( $tab )
{
	echo json_encode([['success'=>true, 'tab'=>$tab]]);
}
function PrintError($msg)
{
	exit( json_encode([['success'=>false, 'error'=>$msg]]));
}
function clearTab( $tab )
{
	$rep = [];
	$nb = count($tab);

	for( $i=0; $i<$nb; $i++)
	{
		$buffer = [];
		foreach ($tab[$i] as $key => $value)
		{
			if( !is_int($key))
				$buffer[ $key ] = $value;
		}
		array_push($rep, $buffer);
	}
	return $rep;
}



class label
{
	const UPLOAD_DIR = 'label/';


	//-------------------------------------------------------------------
	//-------------------------------------------------------------------
	//-------------------------------------------------------------------
	public function __construct()
	{

	}


	//-------------------------------------------------------------------
	//-------------------------------------------------------------------
	//-------------------------------------------------------------------
	public function list( $data, $params )
	{
		//Insert BDD
		$sth = $data['bdd']->prepare('SELECT * FROM label');
		$sth->execute();
		$data = clearTab($sth->fetchAll());


		echo json_encode($data);
	}


	//-------------------------------------------------------------------
	//-------------------------------------------------------------------
	//-------------------------------------------------------------------
	public function get( $data, $params )
	{
		if(!isset($_GET['uid']))
			PrintError('error uid');

		$uid = $_GET['uid'];

		//Insert BDD
		$sth = $data['bdd']->prepare('SELECT * FROM label WHERE uid=:std');
		$sth->bindParam(':std', $uid  , PDO::PARAM_STR);
		$sth->execute();

		$data = clearTab($sth->fetchAll());
		echo json_encode($data);
	}


	//-------------------------------------------------------------------
	//-------------------------------------------------------------------
	//-------------------------------------------------------------------
	public function add( $data, $params )
	{
		if(!isset($_GET['name']))
			PrintError('error name');

		$name = htmlentities($_GET['name']);
		$uid  = uniqid();	

		//Insert BDD
		$sth = $data['bdd']->prepare('INSERT INTO label( uid, name ) VALUES (:val0, :val1)');
		$sth->bindParam(':val0', $uid  , PDO::PARAM_STR);
		$sth->bindParam(':val1', $name, PDO::PARAM_STR);
		$sth->execute();
		//debug( $sth->errorInfo() );

		PrintJson([]);
	}



	//-------------------------------------------------------------------
	//-------------------------------------------------------------------
	//-------------------------------------------------------------------
	public function upload( $data, $params )
	{
		if(!isset($_GET['label']))
			PrintError('error label');
		if(!isset($_GET['uid']))
			PrintError('error uid');

		$label = $_GET['label'];
		$uid   = $_GET['uid'];

		//Get Label
		$sth = $data['bdd']->prepare('SELECT recto,verso FROM label WHERE uid=:std');
		$sth->bindParam(':std', $uid  , PDO::PARAM_STR);
		$sth->execute();
		$label_data = $sth->fetch();


		//Si le fichier RECTO est présent
		if(isset($_FILES['recto']))
		{
			$delete = label::UPLOAD_DIR.$label_data['recto'];
			if( file_exists($delete))
				unlink( $delete );

			$file_recto = label::UPLOAD_DIR.basename($uid.'_r_'.$_FILES['recto']['name']);
			move_uploaded_file($_FILES['recto']['tmp_name'], $file_recto);

			//Update BDD
			$f = explode('/',$file_recto);
			$sth = $data['bdd']->prepare('UPDATE label SET recto=:rec WHERE uid=:std');
			$sth->bindParam(':rec', $f[1], PDO::PARAM_STR);
			$sth->bindParam(':std', $uid       , PDO::PARAM_STR);
			$sth->execute();

			PrintJson([]);
			return;
		}

		//Si le fichier VERSO est présent
		if(isset($_FILES['verso']))
		{
			$delete = label::UPLOAD_DIR.$label_data['verso'];
			if( file_exists($delete))
				unlink( $delete );

			$file_verso = label::UPLOAD_DIR.basename($uid.'_v_'.$_FILES['verso']['name']);
			move_uploaded_file($_FILES['verso']['tmp_name'], $file_verso);

			//Update BDD
			$f = explode('/',$file_verso);
			$sth = $data['bdd']->prepare('UPDATE label SET verso=:ver WHERE uid=:std');
			$sth->bindParam(':ver', $f[1], PDO::PARAM_STR);
			$sth->bindParam(':std', $uid       , PDO::PARAM_STR);
			$sth->execute();

			PrintJson([]);
			return;
		}

		PrintError('input error');
	}



	//-------------------------------------------------------------------
	//-------------------------------------------------------------------
	//-------------------------------------------------------------------
	public function set( $data, $params )
	{
		if( isset($_GET['uid']))
		if( isset($_POST['mode']))
		if( isset($_POST['margin_x']))
		if( isset($_POST['margin_y']))
		if( isset($_POST['nb_x']))
		if( isset($_POST['nb_y']))
		if( isset($_POST['spacing_x']))
		if( isset($_POST['spacing_y']))
		{
			$uid       = htmlentities($_GET['uid']);
			$mode      = htmlentities($_POST['mode']);
			$margin_x  = htmlentities($_POST['margin_x']);
			$margin_y  = htmlentities($_POST['margin_y']);
			$nb_x      = htmlentities($_POST['nb_x']);
			$nb_y      = htmlentities($_POST['nb_y']);
			$spacing_x = htmlentities($_POST['spacing_x']);
			$spacing_y = htmlentities($_POST['spacing_y']);


			$sth = $data['bdd']->prepare('UPDATE label SET horizontal=:mode, margin_x=:mx, margin_y=:my, nb_x=:nx, nb_y=:ny, spacing_x=:spx, spacing_y=:spy WHERE uid=:std');
			$sth->bindParam(':mode', $mode     , PDO::PARAM_STR);
			$sth->bindParam(':mx'  , $margin_x , PDO::PARAM_STR);
			$sth->bindParam(':my'  , $margin_y , PDO::PARAM_STR);
			$sth->bindParam(':nx'  , $nb_x     , PDO::PARAM_STR);
			$sth->bindParam(':ny'  , $nb_y     , PDO::PARAM_STR);
			$sth->bindParam(':spx' , $spacing_x, PDO::PARAM_STR);
			$sth->bindParam(':spy' , $spacing_y, PDO::PARAM_STR);
			$sth->bindParam(':std' , $uid      , PDO::PARAM_STR);
			$sth->execute();

			PrintJson([]);
			return;
		}
		PrintError('input error');
	}



	//-------------------------------------------------------------------
	//-------------------------------------------------------------------
	//-------------------------------------------------------------------
	public function delete( $data, $params )
	{
		if(!isset($_GET['uid']))
			PrintError('error uid');

		$uid = htmlentities($_GET['uid']);

		//get 
		$sth = $data['bdd']->prepare('SELECT * FROM label WHERE uid=:std');
		$sth->bindParam(':std', $uid, PDO::PARAM_STR);
		$sth->execute();

		//get first DATA
		$label = $sth->fetch();

		$recto_file = $label['recto'];
		$verso_file = $label['verso'];

		//close req
		$sth->closeCursor();

		//Suppresion entré
		$sth = $data['bdd']->prepare('DELETE FROM label WHERE uid=:std');
		$sth->bindParam(':std', $uid, PDO::PARAM_STR);
		$sth->execute();

		//Suppresion des fichiers
		if($recto_file)
		if(file_exists( label::UPLOAD_DIR.$recto_file ))
			unlink( label::UPLOAD_DIR.$recto_file );

		if($verso_file)
		if(file_exists( label::UPLOAD_DIR.$verso_file ))
			unlink( label::UPLOAD_DIR.$verso_file );

		PrintJson([]);
	}
}