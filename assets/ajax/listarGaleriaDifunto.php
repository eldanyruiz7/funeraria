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
        // require "../php/responderJSON.php";
        require ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("listarDifuntos",$mysqli);
        if (!$permiso)
        {
        ?>
            Usuario sin permiso suficiente para mostrar esta información.
        <?php
            die;
        }
        usleep(10);
        $idDifunto = $_POST['idDifunto'];
        $directorio = '../images/avatars/difuntos/'.$idDifunto;
        $total_imagenes = count(glob($directorio."/{*.jpg,*.gif,*.png,*.BMP,*.JPG,*.GIF,*.PNG,*.bmp}",GLOB_BRACE));
        // echo "total_imagenes = ".$total_imagenes;
        //var_dump(glob($directorio));
        if (is_dir($directorio) == FALSE || $total_imagenes < 1)
        {
        ?>
            No hay imágenes para este registro.
        <?php
            die;
        }
        $dir = opendir($directorio);
        while($file=readdir($dir)){
            if(!is_dir($file))
            {
                // $sql = "SELECT href, fechaCreacion FROM bitacoraImagenes WHERE idPaciente = $idDifunto AND activo = 1 LIMIT 1";
                // $res = $mysqli->query($sql);
                // $row = $res->fetch_assoc();
                // $fechaCreacion = $row['fechaCreacion'];
                // $data[] = array($file, date("Y-m-d H:i:s",strtotime($fechaCreacion)));
                $data[] = array($file);//, date("Y-m-d H:i:s",strtotime($fechaCreacion)));
                // $dates[] = date("Y-m-d H:i:s",strtotime($fechaCreacion));
            }
        }
        closedir($dir);
        // array_multisort($dates, SORT_ASC, $data);
        ?>
            <!-- <ul class="ace-thumbnails clearfix"> -->
        <?php
        foreach ($data as $file)
        {
            $im = file_get_contents('../images/avatars/difuntos/'.$idDifunto.'/'.$file[0]);
            $imdata = base64_encode($im);
            $partes = explode(".",$file[0]);
            $extImg = $partes[1];
            if (strtolower($extImg) 	== 'jpg' || strtolower($extImg) == 'jpeg')
                $tipoImg = 'image/jpeg';
            elseif (strtolower($extImg) 	== 'png')
                $tipoImg = 'image/png';
            ?>
                <a rel="<?php echo 'galeria'.$idDifunto;?>" data-fancybox="images" href="assets/images/avatars/difuntos/<?php echo $idDifunto.'/'.$file[0];?>" data-rel="colorbox" class=" imgGrupo img-responsive img-thumbnail" name="<?php echo $file[0];?>">
                    <img height="180px" alt="150x150" src="assets/images/avatars/difuntos/<?php echo $idDifunto.'/'.$file[0];?>"/>
                </a>
            <?php
        }

        ?>
<?php
    }
?>
