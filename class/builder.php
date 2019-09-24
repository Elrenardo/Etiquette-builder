<?php

class f_A4
{
	//En pixel
	const pixel_x = 2480;
	const pixel_y = 3508;

	//En pt
	const pt_x = 595.2;
	const pt_y = 841.9;
}

function pixelToPtA4( $x, $y )
{
	$ret = [];
	$ret['x'] = ($x*f_A4::pt_x)/f_A4::pixel_x;
	$ret['y'] = ($y*f_A4::pt_y)/f_A4::pixel_y;

	return $ret;
}



class builder
{

	private $size_format_width  = 2480;
	private $size_format_height = 3508;

	public function __construct()
	{

	}


	//-------------------------------------------------------
	//-------------------------------------------------------
	//-------------------------------------------------------
	//Generation du PDF
	public function pdf( $data, $params )
	{
		$label = $this->getLabel( $data['bdd'] );

		if($label['horizontal'])
			$this->modelH($label);
		else
			$this->modelV($label);
	}


	//-------------------------------------------------------
	//-------------------------------------------------------
	//-------------------------------------------------------
	private function getLabel( $bdd )
	{
		if(!isset($_GET['uid']))
		{
			echo 'No UID';
			exit();
		}

		if(empty($_GET['uid']))
		{
			echo 'UID empty';
			exit();
		}

		$p = $_GET['uid'];

		//Get Ingo Label
		$sth = $bdd->prepare('SELECT * FROM label WHERE uid=:std');
		$sth->bindParam(':std', $p, PDO::PARAM_STR);
		$sth->execute();

		//get first DATA
		$label = $sth->fetch();

		//close req
		$sth->closeCursor();

		return $label;
	}


	//-------------------------------------------------------
	//-------------------------------------------------------
	//-------------------------------------------------------
	private function setPdf()
	{
		//Generation
		$mpdf = new \Mpdf\Mpdf([
			'mode'          => 'utf-8',
			'margin_left'   => 0,
			'margin_right'  => 0,
			'margin_top'    => 0,
			'margin_bottom' => 0,
			'margin_header' => 0,
			'margin_footer' => 0
		]);
		$mpdf->allow_charset_conversion=true;
		$mpdf->charset_in='UTF-8';
		$mpdf->setAutoBottomMargin = 'stretch';

		//Title
		$mpdf->SetTitle( 'Label' );

		return $mpdf;
	}


	//-------------------------------------------------------
	//-------------------------------------------------------
	//-------------------------------------------------------
	//création du PDF
	private function modelH( $label )
	{
		$mpdf  = $this->setPdf();

		//Get Info label
		$label_recto = $this->readImg64('label/'.$label['recto']);

		$nb_y = $label['nb_y'];
		$nb_x = $label['nb_x'];

		$margin_y = $label['margin_y'];
		$margin_x = $label['margin_x'];

		$spacing_x = $label['spacing_x'];
		$spacing_y = $label['spacing_y'];


		//Page Une
		$n = pixelToPtA4( $label_recto['width'], $label_recto['height'] );

		for( $y=0; $y<$nb_y; $y++ )
		{
			for( $x=0; $x<$nb_x; $x++ )
			{
				$mpdf->WriteHTML('<div style="
					position:absolute;
					rotate:-180;
					top:'.($margin_y+($y*$n['y'])).'pt;
					left:'.($margin_x+($x*$n['x'])).'pt;
					width:'.$label_recto['width_pt'].'pt;
					height:'.$label_recto['height_pt'].'pt;"
					><img src="'.$label_recto['url'].'"/></div>');

				$margin_x += $spacing_x;
			}
			$margin_x = $label['margin_x'];
			$margin_y += $spacing_y;
		}

		//Deuxiéme page
		if($label['verso'])
		{
			$label_verso = $this->readImg64('label/'.$label['verso']);

			//Add page
			$mpdf->AddPage();

			//Size PT
			$n = pixelToPtA4( $label_verso['width'], $label_verso['height'] );

			//Build render
			for( $y=0; $y<$nb_y; $y++ )
			{
				for( $x=0; $x<$nb_x; $x++ )
				{
					$mpdf->WriteHTML('<div style="
						position:absolute;
						rotate:180;
						top:'.($margin_y+$y*$n['y']).'pt;
						left:'.($margin_x+$x*$n['x']).'pt;
						width:'.$label_verso['width_pt'].'pt;
						height:'.$label_verso['height_pt'].'pt;"
						><img src="'.$label_verso['url'].'"/></div>');

					$margin_x += $spacing_x;
				}
				$margin_x = $label['margin_x'];
				$margin_y += $spacing_y;
			}
		}

		//Afficher PDF
		$mpdf->Output();
	}




	//-------------------------------------------------------
	//-------------------------------------------------------
	//-------------------------------------------------------
	private function modelV( $label )
	{
		$mpdf  = $this->setPdf();

		//Get Info label
		$label_recto = $this->readImg64('label/'.$label['recto']);

		$nb_y = $label['nb_y'];
		$nb_x = $label['nb_x'];

		$margin_y = $label['margin_y'];
		$margin_x = $label['margin_x'];

		$spacing_x = $label['spacing_x'];
		$spacing_y = $label['spacing_y'];


		//PAGE UNE
		//Size PT
		$n = pixelToPtA4( $label_recto['width'], $label_recto['height'] );
		//Build render
		for( $y=0; $y<$nb_y; $y++ )
		{
			for( $x=0; $x<$nb_x; $x++ )
			{
				$mpdf->WriteHTML('<div style="
					position:absolute;
					rotate:90;
					top:'.($margin_y+$y*$n['x']).'pt;
					left:'.($margin_x+$x*$n['y']).'pt;
					width:'.$label_recto['width_pt'].'pt;
					height:'.$label_recto['height_pt'].'pt;"
					><img src="'.$label_recto['url'].'"/></div>');

				$margin_x += $spacing_x;
			}
			$margin_x = $label['margin_x'];
			$margin_y += $spacing_y;
		}


		//Deuxiéme page
		if($label['verso'])
		{
			$label_verso = $this->readImg64('label/'.$label['verso']);

			//Add page
			$mpdf->AddPage();

			//Size PT
			$n = pixelToPtA4( $label_verso['width'], $label_verso['height'] );

			//Build render
			for( $y=0; $y<$nb_y; $y++ )
			{
				for( $x=0; $x<$nb_x; $x++ )
				{
					$mpdf->WriteHTML('<div style="
						position:absolute;
						rotate:-90;
						top:'.($margin_y+$y*$n['x']).'pt;
						left:'.($margin_x+$x*$n['y']).'pt;
						width:'.$label_verso['width_pt'].'pt;
						height:'.$label_verso['height_pt'].'pt;"
						><img src="'.$label_verso['url'].'" style="
						width:'.$label_verso['width_pt'].'pt;
						height:'.$label_verso['height_pt'].'pt;
					"/></div>');

					$margin_x += $spacing_x;
				}
				$margin_x = $label['margin_x'];
				$margin_y += $spacing_y;
			}
		}

		//Afficher PDF
		$mpdf->Output();
	}


	//-------------------------------------------------------
	//-------------------------------------------------------
	//-------------------------------------------------------
	//Lecture d'une image au format base64
	private function readImg64( $path )
	{
		$ret;
		if(file_exists($path ))
		{
			$type = pathinfo($path, PATHINFO_EXTENSION);
			//$data = file_get_contents($path);

			$size = getimagesize($path);
			$ret['width']  = $size[0];
			$ret['height'] = $size[1];
			$ret['url']    = $path;
			//$ret['data']   = 'data:image/' . $type . ';base64,' . base64_encode($data);

			$s = pixelToPtA4( $ret['width'], $ret['height'] );
			$ret['width_pt']  = $s['x'];
			$ret['height_pt'] = $s['y'];
			return $ret;
		}
		return NULL;
	}
}