<?php

/**
 * Función para reducir y recortar imágenes manteniendo relación de aspecto
 * 
 * @param string $origen Ruta de la imagen original
 * @param string $destino Ruta de destino para la imagen procesada
 * @param int $tamanio Tamaño máximo en píxeles (cuadrado)
 * @param int $calidad Calidad para imágenes JPEG (0-100)
 * @return bool True si tuvo éxito, false si falló
 * @throws Exception Si no se puede procesar la imagen
 */
    function reducirImagen($origen, $destino, $tamanio = 200, $calidad = 80) {
        // Obtener información de la imagen original
        $info = getimagesize($origen);
        
        if (!$info) {
            return false;
        }
        
        // Determinar el tipo de imagen
        $tipo = $info[2];
        
        // Crear la imagen original según su tipo
        switch ($tipo) {
            case IMAGETYPE_JPEG:
                $img_original = imagecreatefromjpeg($origen);
                break;
            case IMAGETYPE_PNG:
                $img_original = imagecreatefrompng($origen);
                break;
            case IMAGETYPE_GIF:
                $img_original = imagecreatefromgif($origen);
                break;
            default:
                return false;
        }
        
        // Dimensiones originales
        $ancho_orig = imagesx($img_original);
        $alto_orig = imagesy($img_original);
        
        // Calcular recorte (priorizando mantener parte superior)
        $x_origen = 0;
        $y_origen = 0;
        
        if ($ancho_orig > $alto_orig) {
            // Imagen horizontal - recortar lados
            $x_origen = ($ancho_orig - $alto_orig) / 2;
            $lado_recorte = $alto_orig;
        } else {
            // Imagen vertical - recortar más abajo
            $lado_recorte = $ancho_orig;
            // Solo recortar un 20% desde arriba y 80% desde abajo
            $altura_excedente = $alto_orig - $ancho_orig;
            $y_origen = $altura_excedente * 0.2; // Recortar solo 20% de arriba
        }
        
        // Crear imagen cuadrada nueva
        $img_cuadrada = imagecreatetruecolor($tamanio, $tamanio);
        
        // Redimensionar la parte recortada al tamaño deseado
        imagecopyresampled($img_cuadrada, $img_original, 
                        0, 0, 
                        $x_origen, $y_origen, 
                        $tamanio, $tamanio, 
                        $lado_recorte, $lado_recorte);
        
        // Guardar la imagen resultante según su tipo
        $resultado = false;
        switch ($tipo) {
            case IMAGETYPE_JPEG:
                $resultado = imagejpeg($img_cuadrada, $destino, $calidad);
                break;
            case IMAGETYPE_PNG:
                $resultado = imagepng($img_cuadrada, $destino, round(9 * $calidad / 100));
                break;
            case IMAGETYPE_GIF:
                $resultado = imagegif($img_cuadrada, $destino);
                break;
        }
        
        // Liberar memoria
        imagedestroy($img_original);
        imagedestroy($img_cuadrada);
        
        return $resultado;
    }

/**
 * archivo foto.php de subida de Fotos del personal para identificacion de los mismos en el sistema y credenciales
 * crear las Carpetas imagenes, 800, 200
 */

// Recibimos la imagen
    $dir_subida = 'imagenes/';

// Creamos el Nombre imagen ymd.jpg
    $extension = pathinfo(basename($_FILES['archivo']['name']), PATHINFO_EXTENSION);
    $name = date('Ymd').time().'.'.$extension;
    $fichero_subido = $dir_subida . $name;

// Subimos la Imagen al directorio 
    move_uploaded_file($_FILES['archivo']['tmp_name'], $fichero_subido); 
    
// Reducimos la imagen para diferentes ambitos
    reducirImagen($fichero_subido, $dir_subida."800/".$name, 800, 85);
    reducirImagen($fichero_subido, $dir_subida."200/".$name, 200, 85);

// Respondemos con los datos para la API-REST
    $datos = array(
        'nombreorigen' => basename($_FILES['archivo']['name']),
        'nombre' => $name
    );

    echo json_encode($datos, JSON_FORCE_OBJECT);
?>