<?php

    $dir_subida = 'imagenes/';

    $extension = pathinfo(basename($_FILES['archivo']['name']), PATHINFO_EXTENSION);
    $name = date('Ymd').time().'.'.$extension;
    $fichero_subido = $dir_subida . $name;

    move_uploaded_file($_FILES['archivo']['tmp_name'], $fichero_subido); 

    $datos = array(
        'nombreorigen' => basename($_FILES['archivo']['name']),
        'nombre' => $name
    );

    echo json_encode($datos, JSON_FORCE_OBJECT);
?>
