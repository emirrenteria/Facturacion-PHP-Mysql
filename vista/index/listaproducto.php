<?php

session_start();
if($_SESSION['id_rol'] != 3 and $_SESSION['id_rol'] != 2){
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
    <title>MyTierra || Lista Productos</title>

 

    
</head>

<body>

    <!-- barra de navegación -->
    
    <div class="contenedor">
        <header>
            <nav> <?php include "nav.php"; ?> </nav>
        </header>
    </div>
    



    <h3 id="li"> Lista de Productos</h3>
    <a class="btn_new" href="crearproducto.php"> Crear Producto</a>


    <form class="form_search" action="buscarproducto.php" method="get">

        <input type="text" name="busqueda" id="busqueda" placeholder="Buscar">
        <input class="btn_search" type="submit" value="Buscar">
    
    </form>


    <table>
        <tr>
            <th>Codigo</th>
            <th>Nombre</th>
            <th>Descripcion</th>
            <th>Catidad Producto</th>
            <th>Precio Producto</th>
            <th>Unidad Comercial</th>
            <th>Acciones</th>
        </tr>

    <?php

    //paginador
    $sql_registro = mysqli_query($conection, "SELECT COUNT(*) as total_registros FROM `producto` WHERE estado = 1");
    $result_registro = mysqli_fetch_array($sql_registro);
    $total_registro = $result_registro['total_registros'];
    
    $por_pagina= 3;

    if(empty($_GET['pagina'])){
        $pagina = 1;
    }else{
        $pagina = $_GET['pagina'];
    }

    $desde= ($pagina-1) * $por_pagina;
    $total_paginas = ceil($total_registro / $por_pagina);


    $query = mysqli_query($conection, "SELECT id_producto, cod_product, nombre, descripcion, cant_product, precio_product, unidad_com 
                                            FROM producto
                                            WHERE estado = 1 
                                            ORDER BY id_producto DESC
                                            LIMIT $desde,$por_pagina
                                            ");
     
    mysqli_close($conection);

    $result = mysqli_num_rows($query);
    if($result > 0){
        while($data = mysqli_fetch_array($query)){
    ?>
            <tr class="row<?php echo $data["cod_product"]; ?>">
                <td><?php echo $data["cod_product"];  ?></td>
                <td><?php echo $data["nombre"];  ?></td>
                <td><?php echo $data["descripcion"];  ?></td>
                <td class="celCantidad"><?php echo $data["cant_product"];  ?></td>
                <td class="celPrecio"><?php echo $data["precio_product"];  ?></td>
                <td><?php echo $data["unidad_com"];  ?></td>
                <td> 
                    <?php if($_SESSION['id_rol'] == 3 || $_SESSION['id_rol'] == 2){?> 
                        <a class="link_add add_product" product="<?php echo $data["cod_product"];?>" href="#"> Agregar </a>
                        |
                        <a class="link_edit" href="actualizarproducto.php?id=<?php echo $data["cod_product"];?>"> Editar </a>
                        |
                        <a class="link_delete del_product" product="<?php echo $data["cod_product"];?>" href="#"> Eliminar </a>
                </td>
                    <?php } ?>
            </tr>
    <?php
        }
    }

    ?>     
    </table>

    <div class="paginador"> 
        <ul>
        <?php

        if($pagina != 1)
        {
        ?>
             <li> <a href="?pagina=<?php echo 1; ?>">|<</a></li>
             <li> <a href="?pagina=<?php echo $pagina-1; ?>"><<</a></li>
       
        <?php
        }
             for ($i=1; $i <= $total_paginas; $i++){

                 echo '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
             }
       if($pagina != $total_paginas)
       {
       ?>   
             <li> <a href="?pagina=<?php echo $pagina+1; ?>">>></a></li>
             <li> <a href="?pagina=<?php echo $total_paginas; ?>">|></a></li>

       <?php } ?>
        </ul>
    </div>




<br><br><br><br><br><br>


    <!-- pie de pagina -->
<iframe src="pie.html"></iframe>


</body>
</html>