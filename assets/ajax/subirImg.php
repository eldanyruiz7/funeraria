<?php
	require_once ('../connect/bd.php');
	require_once ("../connect/sesion.class.php");
	$sesion = new sesion();
	require_once ("../connect/cerrarOtrasSesiones.php");
	require_once ("../connect/usuarioLogeado.php");
	if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
	{
		header("Location: ".dirname(__FILE__)."../../salir.php");
	}
	else
	{
		require "../php/responderJSON.php";
		if(isset($_FILES["inputFileImagen"]))
		{
			$error = $_FILES["inputFileImagen"]["error"];
		    if($error == 0)
		    {
		        $nombreArchivo = $_FILES['inputFileImagen']['name'];
		        $extensiones = array('jpg', 'jpeg', 'JPG', 'JPEG');
				$tmp = explode('.', $nombreArchivo);
				$extension = end($tmp);
				// $info = new SplFileInfo('foo.txt');
				// var_dump($info->getExtension());
		        if(!in_array($extension, $extensiones))//&&($mTipo != IMAGETYPE_JPEG) && ($mTipo != IMAGETYPE_PNG) && ($mTipo != IMAGETYPE_BMP))
		        {
					$response["exito"] 	= 0;
					$response["titulo"] = '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> FORMATO DE IMAGEN NO V&Aacute;LIDO';
					$response["texto"] 	= 'No se pudo cargar la imagen seleccionada. </br> El formato del archivo para imagen de perfil de paciente debe ser de tipo: <b>JPG/JPEG</b>';
					responder($response, $mysqli);
		        }
		        else
		        {
				//Si la extensión es correcta, procedemos a comprobar el tamaño del archivo subido
				//Y definimos el máximo que se puede subir
				//Por defecto el máximo es de 2 MB, pero se puede aumentar desde el .htaccess o en la directiva 'upload_max_filesize' en el php.ini
		        //Convertimos la información de la imagen en binario para insertarla en la BBDD
		            $imagenBinaria 		= base64_encode(file_get_contents($_FILES['inputFileImagen']['tmp_name']));
					$tipoImagen			= $_FILES['inputFileImagen']['type'];
		    		$tamañoArchivo 		= $_FILES['inputFileImagen']['size']; //Obtenemos el tamaño del archivo en Bytes
		    		$tamañoArchivoKB 	= round(intval(strval( $tamañoArchivo / 1024 ))); //Pasamos el tamaño del archivo a KB

		    		$tamañoMaximoKB 	= "5120"; //Tamaño máximo expresado en KB
		    		$tamañoMaximoBytes 	= $tamañoMaximoKB * 1024; // -> 2097152 Bytes -> 2 MB
		    		//Comprobamos el tamaño del archivo, y mostramos un mensaje si es mayor al tamaño expresado en Bytes
		    		if($tamañoArchivo > $tamañoMaximoBytes)
		            {
						$response["exito"] = 0;
						$response["respuesta"] = '<div class="alert alert-danger alert-dismissable">';
		                $response["respuesta"] .='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
						$response["respuesta"] .=	"El archivo <b>";
						$response["respuesta"] .=	$nombreArchivo;
						$response["respuesta"] .=" 	</b>es demasiado grande. El tamaño máximo del archivo es de ";
						$response["respuesta"] .=	$tamañoMaximoKB;
						$response["respuesta"] .=	"Kb. ";
						$response["respuesta"] .=	"Inténtalo con una imagen de menor tamaño";
						$response["respuesta"] .='</div>';
						responder($response, $mysqli);
		    		};
		            $directorio = 'img/';
		            // Muevo la imagen desde el directorio temporal a nuestra ruta indicada anteriormente
		            if(move_uploaded_file($_FILES['inputFileImagen']['tmp_name'],$directorio.$nombreArchivo))
					{
						$response["exito"] 		= 1;
						$response["respuesta"] 	= '<img class="vistaPrevia img-thumbnail" style="cursor:zoom-in" width="100%" height="100%" src="data:'.$tipoImagen.';base64,'.$imagenBinaria.'" >';
						$response["src"] 		= $directorio.$nombreArchivo;
						$response["binario"] 	= $imagenBinaria;
						$response['tipo'] 		= $tipoImagen;
						unlink($directorio.$nombreArchivo);
						responder($response, $mysqli);
					}
		            else
					{
						$response["exito"] = 0;
						$response["respuesta"] = '<div class="alert alert-danger alert-dismissable">';
		                $response["respuesta"] .='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
						$response["respuesta"] .= 	"No se pudo adjuntar la imagen. Vuelve a intentarlo. Si el problema persiste por favor notifícaselo al Administrador del Sistema";
						$response["respuesta"] .='</div>';
						responder($response, $mysqli);
		            //unlink($_SERVER["SERVER_ROOT"].$directorio.$nombreArchivo);
					}
		    	}
		    }
		    else
		        echo $error;
		 }
		 if (isset($_FILES['file']) && !$_FILES["file"]["error"])
		 {
			 $nombreArchivo = $_FILES['file']['name'];
			 $extensiones = array('jpg', 'jpeg', 'JPG', 'JPEG');
			 $tmp = explode('.', $nombreArchivo);
			 $extension = end($tmp);
			 // $info = new SplFileInfo('foo.txt');
			 // var_dump($info->getExtension());
			 if(!in_array($extension, $extensiones))//&&($mTipo != IMAGETYPE_JPEG) && ($mTipo != IMAGETYPE_PNG) && ($mTipo != IMAGETYPE_BMP))
			 {
				 $response["exito"] 	= 0;
				 $response["titulo"] = '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> FORMATO DE IMAGEN NO V&Aacute;LIDO';
				 $response["texto"] 	= 'No se pudo cargar la imagen seleccionada. </br> El formato del archivo para imagen de perfil de paciente debe ser de tipo: <b>JPG/JPEG</b>';
				 responder($response, $mysqli);
			 }
			 else
			 {
			 //Si la extensión es correcta, procedemos a comprobar el tamaño del archivo subido
			 //Y definimos el máximo que se puede subir
			 //Por defecto el máximo es de 2 MB, pero se puede aumentar desde el .htaccess o en la directiva 'upload_max_filesize' en el php.ini
			 //Convertimos la información de la imagen en binario para insertarla en la BBDD
				 $imagenBinaria 		= base64_encode(file_get_contents($_FILES['file']['tmp_name']));
				 $tipoImagen			= $_FILES['file']['type'];
				 $tamañoArchivo 		= $_FILES['file']['size']; //Obtenemos el tamaño del archivo en Bytes
				 $tamañoArchivoKB 	= round(intval(strval( $tamañoArchivo / 1024 ))); //Pasamos el tamaño del archivo a KB

				 $tamañoMaximoKB 	= "5120"; //Tamaño máximo expresado en KB
				 $tamañoMaximoBytes 	= $tamañoMaximoKB * 1024; // -> 2097152 Bytes -> 2 MB
				 //Comprobamos el tamaño del archivo, y mostramos un mensaje si es mayor al tamaño expresado en Bytes
				 if($tamañoArchivo > $tamañoMaximoBytes)
				 {
					 $response["exito"] = 0;
					 $response["respuesta"] = '<div class="alert alert-danger alert-dismissable">';
					 $response["respuesta"] .='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
					 $response["respuesta"] .=	"El archivo <b>";
					 $response["respuesta"] .=	$nombreArchivo;
					 $response["respuesta"] .=" 	</b>es demasiado grande. El tamaño máximo del archivo es de ";
					 $response["respuesta"] .=	$tamañoMaximoKB;
					 $response["respuesta"] .=	"Kb. ";
					 $response["respuesta"] .=	"Inténtalo con una imagen de menor tamaño";
					 $response["respuesta"] .='</div>';
					 responder($response, $mysqli);
				 };
				 $directorio = 'img/';
				 // Muevo la imagen desde el directorio temporal a nuestra ruta indicada anteriormente
				 if(move_uploaded_file($_FILES['file']['tmp_name'],$directorio.$nombreArchivo))
				 {
					 $response["exito"] 		= 1;
					 $response["respuesta"] 	= '<img class="vistaPrevia img-thumbnail" style="cursor:zoom-in" width="100%" height="100%" src="data:'.$tipoImagen.';base64,'.$imagenBinaria.'" >';
					 $response["src"] 			= $directorio.$nombreArchivo;
					 $response["binario"] 		= $imagenBinaria;
					 $response['tipo'] 			= $tipoImagen;
					 // <a src-img='assets/images/gallery/pacientes/$idPaciente.'/'.$file[0];' href='' data-rel='colorbox' class='cboxElement' name='".$file[0]."'">
					 $response['htmlImagen']	= "<li>
	                     <a href='data:image/jpeg;base64,$imagenBinaria' data='$imagenBinaria' data-fancybox='images' data-caption='".$_FILES['file']['name']."' class='cboxElement'>
	                         <img height='180px' alt='150x150' src='data:image/jpeg;base64,$imagenBinaria'/>
	                         <div class='text'>
	                             <div class='inner'></div>
	                         </div>
	                     </a>

	                     <div class='tools tools-bottom'>
	                         <a class='timesEliminarImg pointer'>
	                             <i class='ace-icon fa fa-times red'></i>
	                         </a>

	                     </div>
	                 </li>";
					 unlink($directorio.$nombreArchivo);
					 responder($response, $mysqli);
				 }
				 else
				 {
					 $response["exito"] = 0;
					 $response["respuesta"] = '<div class="alert alert-danger alert-dismissable">';
					 $response["respuesta"] .='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
					 $response["respuesta"] .= 	"No se pudo adjuntar la imagen. Vuelve a intentarlo. Si el problema persiste por favor notifícaselo al Administrador del Sistema";
					 $response["respuesta"] .='</div>';
					 responder($response, $mysqli);
				 //unlink($_SERVER["SERVER_ROOT"].$directorio.$nombreArchivo);
				 }
			 }
			 echo "Sin error";


		 }
	 }
?>
