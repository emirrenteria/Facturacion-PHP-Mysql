<?php

session_start();
if($_SESSION['id_rol'] != 3){
    header('location: ./');
}

include('../../control/conexion.php');

if(!empty($_POST)){

    if($_POST['id_usuario'] == 1){
        header('location: listausuario.php');
        mysqli_close($conection);
        exit;

    }

    $id_usuario = $_POST['id_usuario'];
    //  $query_delete = mysqli_query($conection, "DELETE FROM usuario WHERE id_usuario = $id_usuario");
    $query_delete = mysqli_query($conection, "UPDATE usuario SET estado = 0 WHERE id_usuario = $id_usuario");
    mysqli_close($conection);


    if($query_delete){
        header('location: listausuario.php');
    
    }else{
        echo "Error al eliminar";
    }

}



if(empty($_REQUEST['id']) || $_REQUEST['id'] == 1 ){
    header('location: listausuario.php');
    mysqli_close($conection);

}else{

    include('../../control/conexion.php');

    $id_usuario = $_REQUEST['id'];

    $query = mysqli_query($conection, "SELECT u.nombre, u.apellido, r.nombre_rol
                                        FROM usuario u
                                        INNER JOIN rol r
                                        ON u.id_rol = r.id_rol
                                        WHERE u.id_usuario = $id_usuario");

    mysqli_close($conection);
    $resul = mysqli_num_rows($query);

    if($resul > 0){

        while($data =  mysqli_fetch_array($query)){
            $nombre = $data['nombre'];
            $apellido = $data['apellido'];
            $nombre_rol = $data['nombre_rol'];
        }
    }else{
        header('location: listausuario.php');
    }

}


?>

<!DOCTYPE html>
<html lang="us">
<head>

    <meta charset="UTF-8">
    <meta name="author" content="GrupoADSI">
    <meta name="Descripcion" content="Sistema de Facturacion">
    <meta name="keywords" content="sistema, verduras, facturacion, frutas, mytierra">
    <?php include "scripts.php"; ?>
    <title>MyTierra || Eliminar Usuario</title>
    <link rel="shortcut icon" href="../img/logo.ico">
    


</head>

<body>

    <!-- menu de pagina -->
    <div class="contenedor">
        <header>
            <nav> <?php include "nav.php"; ?> </nav>
        </header>
    </div>


   <!-- cuerpo -->

   <section id="container">
       <br>
       <div class="data_delete">
           <h3>¿Esta seguro de eliminar el siguiente registro?</h3>
           <p>Nombre: <span><?php echo $nombre; ?> </span> </p>
           <p>Apellido: <span><?php echo $apellido; ?> </span> </p>
           <p>Tipo Usuario: <span><?php echo $nombre_rol; ?> </span> </p>
           <form method="post" action=""> 
               <input type="hidden" name="id_usuario" value="<?php echo $id_usuario ?>">
               <a class="btn_cancel" href="listausuario.php">Cancelar</a>
               <input class="btn_ok" type="submit" value="Aceptar">
           </form>

       </div>

   </section>

   <br><br><br><br><br><br><br>


  
    <!-- pie de pagina -->
    <iframe src="pie.html" class="pie"></iframe>


</body>
</html>