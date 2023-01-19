<?php
session_start();
if($_SESSION['id_rol'] != 3){
    header('location: ./');
}

include('../../control/conexion.php');

?>





<!DOCTYPE html>
<html lang="us">
<head>
   
    <meta charset="UTF-8">
    <meta name="author" content="GrupoADSI">
    <meta name="Descripcion" content="Sistema de Facturacion">
    <meta name="keywords" content="sistema, verduras, facturacion, frutas, mytierra">
    <?php include "scripts.php"; ?>
    <title>MyTierra || Lista Usuarios</title>

     
</head>

<body>

    <!-- barra de navegación -->
    <div class="contenedor">
        <header>
            <nav> <?php include "nav.php"; ?> </nav>
        </header>
    </div>


   <?php

   $busqueda = strtolower($_REQUEST['busqueda']);
   if(empty($busqueda)){
       header('location: listausuario.php');
       mysqli_close($conection);
   }

   ?>

    <h3 id="li"> Lista de Usuarios</h3>
    <a class="btn_new" href="crearusuario.php"> Crear Usuario</a>


    <form class="form_search" action="burcarusuario.php" method="get">

        <input type="text" name="busqueda" id="busquueda" placeholder="Buscar" value="<?php echo $busqueda; ?>">
        <input class="btn_search" type="submit" value="Buscar">
    
    </form>


    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellidos</th>
            <th>Telefono</th>
            <th>Direccion</th>
            <th>Email</th>
            <th>Estado</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>

    <?php

    //paginador
   $rol='';

   if($busqueda == "cajero"){

       $rol = " OR id_rol LIKE '%1%' ";

   }else if($busqueda == "supervisor"){
       
    $rol = " OR id_rol LIKE '%2%' ";

}else if($busqueda == "administrador"){
       
    $rol = " OR id_rol LIKE '%3%' ";
}

    $sql_registro = mysqli_query($conection, "SELECT COUNT(*) as total_registros FROM `usuario` 
                                                WHERE
                                                    (
                                                    identificacion LIKE '%$busqueda%' OR 
                                                    nombre         LIKE '%$busqueda%' OR
                                                    apellido       LIKE '%$busqueda%' OR 
                                                    telefono       LIKE '%$busqueda%' OR
                                                    direccion      LIKE '%$busqueda%' OR 
                                                    email          LIKE '%$busqueda%' OR
                                                    estado         LIKE '%$busqueda%'
                                                    $rol
                                                    )    
                                                AND estado = 1");

    $result_registro = mysqli_fetch_array($sql_registro);
    $total_registro = $result_registro['total_registros'];
    
    $por_pagina= 5;

    if(empty($_GET['pagina'])){
        $pagina = 1;
    }else{
        $pagina = $_GET['pagina'];
    }

    $desde= ($pagina-1) * $por_pagina;
    $total_paginas = ceil($total_registro / $por_pagina);


    $query = mysqli_query($conection, "SELECT u.id_usuario, u.identificacion, u.nombre, u.apellido, u.telefono, u.direccion, u.email, u.estado, r.nombre_rol  
                                            FROM usuario u 
                                            INNER JOIN rol r ON u.id_rol = r.id_rol 
                                            WHERE
                                            (
                                                u.identificacion LIKE '%$busqueda%' OR 
                                                u.nombre         LIKE '%$busqueda%' OR
                                                u.apellido       LIKE '%$busqueda%' OR 
                                                u.telefono       LIKE '%$busqueda%' OR
                                                u.direccion      LIKE '%$busqueda%' OR 
                                                u.email          LIKE '%$busqueda%' OR
                                                u.estado         LIKE '%$busqueda%' OR
                                                r.nombre_rol LIKE '%$busqueda%'
                                            )   
                                            AND estado = 1 
                                            ORDER BY identificacion ASC
                                            LIMIT $desde,$por_pagina
                                            ");
     mysqli_close($conection);


    $result = mysqli_num_rows($query);
    if($result > 0){
        while($data = mysqli_fetch_array($query)){
    ?>
            <tr>
                <td><?php echo $data["identificacion"];  ?></td>
                <td><?php echo $data["nombre"];  ?></td>
                <td><?php echo $data["apellido"];  ?></td>
                <td><?php echo $data["telefono"];  ?></td>
                <td><?php echo $data["direccion"];  ?></td>
                <td><?php echo $data["email"];  ?></td>
                <td><?php echo $data["estado"];  ?></td>
                <td><?php echo $data["nombre_rol"];  ?></td>
                <td> 
                    <a class="link_edit" href="actualizarusuario.php?id=<?php echo $data["id_usuario"];?>"> Editar </a>
                    |
                <?php if($data["id_usuario"] != 1 ){?> 
                    <a class="link_delete" href="eliminarusuario.php?id=<?php echo $data["id_usuario"];?>"> Eliminar </a>
                <?php } ?>

                </td>
            </tr>
    <?php
        }
    }

    ?>     
    </table>

    <?php 

        if($total_registro != 0){

    ?>
   

    <div class="paginador"> 
        <ul>
        <?php

        if($pagina != 1)
        {
        ?>
             <li> <a href="?pagina=<?php echo 1; ?>&busqueda=<?php echo $busqueda; ?>">|<</a></li>
             <li> <a href="?pagina=<?php echo $pagina-1; ?>&busqueda=<?php echo $busqueda; ?>"><<</a></li>
       
        <?php
        }
             for ($i=1; $i <= $total_paginas; $i++){

                 echo '<li><a href="?pagina='.$i.'&busqueda='.$busqueda.'">'.$i.'</a></li>';
             }
       if($pagina != $total_paginas)
       {
       ?>   
             <li> <a href="?pagina=<?php echo $pagina+1; ?>&busqueda=<?php echo $busqueda; ?>">>></a></li>
             <li> <a href="?pagina=<?php echo $total_paginas; ?> &busqueda=<?php echo $busqueda; ?>">|></a></li>

       <?php } ?>
        </ul>
    </div>

    <?php } ?>

    


<br><br><br><br><br><br>


    <!-- pie de pagina -->
<iframe src="pie.html"></iframe>

    
</body>
</html>