<?php
    require_once ('../../connect/bd.php');
    require_once ("../../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../../connect/cerrarOtrasSesiones.php");
    require_once ("../../connect/usuarioLogeado.php");
    require_once ("../../php/funcionesVarias.php");
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require '../../php/PHPMailer-master/src/Exception.php';
    require '../../php/PHPMailer-master/src/PHPMailer.php';
    require '../../php/PHPMailer-master/src/SMTP.php';
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
        header("Location: salir.php");
    }
    else
    {
        require "../../php/responderJSON.php";
        require ("../../php/usuario.class.php");
		require ("../../php/query.class.php");
		$query = new Query();
        $usuario = new usuario($idUsuario,$mysqli);
        $emailEnviar = $usuario->email;
        if (!$usuario->tipo == 1 && !$usuario->tipo == 0)
        {
            $response['mensaje']        = "Error. Usuario con permisos insuficientes para realizar esta acción";
            $response['status']         = 0;
            responder($response, $mysqli);
        }
        $response = array(
            "status"                    => 1
        );

    	$fecha = date("YmdHis"); //Obtenemos la fecha y hora para identificar el respaldo

    	// Construimos el nombre de archivo SQL Ejemplo: mibase_20170101-081120.sql
    	$salida_sql = 'Funeraria_'.$fecha.'.sql';
		$backup = $query->backup();
	    if($archivo = fopen($salida_sql, "w"))
	    {
	        if(fwrite($archivo, $backup))
	        {
				fclose($archivo);
				$zip = new ZipArchive(); //Objeto de Libreria ZipArchive

		    	//Construimos el nombre del archivo ZIP Ejemplo: mibase_20160101-081120.zip
		    	$salida_zip = 'respaldo_'.$fecha.'.zip';
				// echo "SALIDA:".$salida_zip;
		    	if($zip->open($salida_zip, ZipArchive::CREATE | ZipArchive::OVERWRITE)===true) { //Creamos y abrimos el archivo ZIP
		    		$zip->addFile($archivo); //Agregamos el archivo SQL a ZIP
		    		$zip->close(); //Cerramos el ZIP
		    		unlink($archivo); //Eliminamos el archivo temporal SQL
		            /////////////////////////////Envío por e-mail////////////////////////////////////
		            $mail = new PHPMailer();
		            $mail->isSMTP();
		            //Set the hostname of the mail server
		            $mail->Host = 'smtp.gmail.com';
		            $mail->Port = 587;
		            $mail->SMTPSecure = 'tls';
		            $mail->SMTPAuth = true;
		            //Username to use for SMTP authentication - use full email address for gmail
		            $mail->Username = "gamb2006@gmail.com";
		            //Password to use for SMTP authentication
		            $mail->Password = "dvrselvsclpssasF";
		            //Set who the message is to be sent from
		            $mail->From='gamb2006@gmail.com';
		            $mail->FromName = utf8_decode("Sistema de funeraria"); //A RELLENAR Nombre a mostrar del remitente.
		            //Set an alternative reply-to address
		            //$mail->addReplyTo('replyto@example.com', 'First Last');
		            //Set who the message is to be sent to
		            $mail->addAddress($emailEnviar);
		            //Set the subject line
		            $mail->Subject = utf8_decode('Envío de respaldo!');
		            //Read an HTML message body from an external file, convert referenced images to embedded,
		            //convert HTML into a basic plain-text alternative body
		            //$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
		            //Replace the plain text body with one created manually
		            //$mail->AltBody = 'This is a plain-text message body';
		            $msg = '<p>Envío respaldo Funeraria.</p></br></br></br>Email enviado con <a href="https://www.facebook.com/ingeniasystemmzo/" style="color: #5b7a91; text-decoration: none; background-color: transparent;">ingenia system</a>';

		            $mail->IsHTML(true); // El correo se envía como HTML
		            $mail->Body    = $msg;
		            //Attach an image file
		            //A
					// $mail->addAttachment($salida_zip);
		            $mail->addAttachment($salida_sql);
		            //send the message, check for errors
		            if (!$mail->send())
		            {
		                $response['mensaje']        = "No se pudo enviar el respaldo, por favor, vuelve a intentarlo";
		                $response['status']         = 0;
		                unlink($salida_zip);
		                responder($response, $mysqli);
		            } else
		            {
						$idUsuario     				= $sesion->get('id');
				        $sql            			= "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
				        $res_noSucursal 			= $mysqli->query($sql);
				        $row_noSucursal 			= $res_noSucursal->fetch_assoc();
				        $idSucursal     			= $row_noSucursal['idSucursal'];
						// Agregar evento en la bitácora de eventos ///////
						$ipUsuario 					= $sesion->get("ip");
						$pantalla					= "Realizar respaldo";
						$descripcion				= "Se ha enviado un respaldo al correo=$emailEnviar";
						$sql						= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
						$mysqli						->query($sql);
		                $response['mensaje']        = "Respaldo enviado correctamente al correo registrado";
		                $response['status']         = 1;
		                unlink($salida_sql);
		                responder($response, $mysqli);
		            }
	        }
	        else
	        {
				$response['mensaje']        = "No se pudo enviar el respaldo, por favor, vuelve a intentarlo";
				$response['status']         = 0;
				unlink($salida_sql);
				responder($response, $mysqli);
	        }

	    }
    	//Comando para genera respaldo de MySQL, enviamos las variales de conexion y el destino
        // mysqldump --opt -h $dbhost -u $dbuser -p$dbpass -v $dbname > $backup_file
		// $dump = "mysqldump --h$db_host -u$db_user -p$db_pass --opt $db_name > $salida_sql";
		// system($dump, $output); //Ejecutamos el comando para respaldo

    	// $dump = "mysqldump -h$servidor -u$usr -p$contrasena --opt $bd > $salida_sql";
    	// system($dump, $output); //Ejecutamos el comando para respaldo


            ////////////////////////////////////////////////////////////////////////////////////

            // $mysqli->rollback();

        }
    }
?>
