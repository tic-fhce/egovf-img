<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Images</title>
</head>
<body>
    <form action="foto.php" enctype="multipart/form-data" method="post">
        <input type="file" name="archivo">
        <button>Guardar</button>
    </form>
    <?php
        echo(time());
    ?>
</body>
</html>
