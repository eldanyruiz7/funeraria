<?php
    function validarFormulario($tipo, $form, $longitud = 0) //Devuelve el input sanitizado o FALSE en caso de no cumplir con la condición
    {
        $form = htmlspecialchars($form);
        $search = array(
            '@<script[^>]*?>.*?</script>@si',   // Elimina javascript
            '@<[\/\!]*?[^<>]*?>@si',            // Elimina las etiquetas HTML
            '@<style[^>]*?>.*?</style>@siU',    // Elimina las etiquetas de estilo
            '@<![\s\S]*?--[ \t\n\r]*>@'         // Elimina los comentarios multi-línea
          );

        $form = preg_replace($search, '', $form);
        switch ($tipo)
        {
            case 'i':
                $form = floatval($form);
                if (!is_numeric($form))
                    $validacion = FALSE;
                else
                    if ($form < $longitud)
                    {
                        $validacion = FALSE;
                    }
                    else
                    {
                        $validacion = $form;
                    }
                break;
            case 's':
                if ($longitud == FALSE)
                    $validacion = trim($form);
                else
                {
                    if (strlen($form) <= $longitud)
                        $validacion = FALSE;
                    else
                        $validacion = trim($form);
                }
                break;
            case 'd':
                $fff = explode('-',$form);
                if (@checkdate($fff[1], $fff[2], $fff[0]) == FALSE)
                    $validacion = FALSE;
                else
                    $validacion = $form;
                break;
            default:
                $validacion = FALSE;
        }
        return $validacion;
    }
?>
