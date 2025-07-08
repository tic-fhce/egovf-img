<?php
/**
 * Reduce el tamaño de una imagen manteniendo la relación de aspecto
 * 
 * @param string $origen Ruta de la imagen original
 * @param string $destino Ruta donde se guardará la imagen reducida
 * @param int $ancho_max Ancho máximo deseado (en píxeles)
 * @param int $alto_max Alto máximo deseado (en píxeles)
 * @param int $calidad Calidad de la imagen resultante (0-100)
 * @return bool True si tuvo éxito, False si hubo error
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


function listarArchivos($directorio) {
    // Verificar si el directorio existe
    if (!is_dir($directorio)) {
        return "El directorio no existe";
    }

    // Abrir el directorio
    if ($gestor = opendir($directorio)) {
        echo "<ul>";
        // Leer cada archivo del directorio
        while (false !== ($archivo = readdir($gestor))) {
            // Ignorar los directorios . y ..
            if ($archivo != "." && $archivo != "..") {
                if (reducirImagen($directorio."/".$archivo, "800/".$archivo, 800, 85)) {
                    echo "<li> Imagen reducida con éxito". htmlspecialchars($archivo) . "</li>";
                } else {
                    echo "<li>Error al reducir la imagen". htmlspecialchars($archivo) . "</li>";
                }
            }
        }       
        echo "</ul>";
        // Cerrar el gestor de directorio
        closedir($gestor);
    }
}

listarArchivos('origen');

?>