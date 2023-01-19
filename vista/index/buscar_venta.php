<?php

session_start();
include('../../control/conexion.php');

$busqueda = '';

if(isset($_REQUEST['busqueda'])  && $_REQUEST['busqueda'] ==''){
    header("location: ventas.php");
}

if(!empty($_REQUEST['busqueda'])){
    if(!is_numeric($_REQUEST['busqueda'])){
        header("location: ventas.php");

    }

    $busqueda = strtolower($_REQUEST['busqueda']);
    $where = "nofactura = $busqueda";
    $buscar = "busqueda = $busqueda";
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
    <title>MyTierra || Lista de Ventas</title>
    <link rel="shortcut icon" href="../img/logo.ico">

     
    
</head>

<body>

    <!-- barra de navegación -->
    <div class="contenedor">
        <header>
            <nav><<?php include "nav.php"; ?> </nav>
        </header>
    </div>




    <h3 id="li"> Lista de Ventas</h3>
    <a class="btn_new" href="nueva_venta.php"> Nueva Venta</a>


    <form class="form_search" action="buscar_venta.php" method="get">

        <input type="text" name="busqueda" id="busquueda" placeholder="No. factura" value="<?php echo $busqueda; ?>">
        <input class="btn_search" type="submit" value="Buscar">
    
    </form>


    <table>
        <tr>
            <th>No.</th>
            <th>Fecha / Hora </th>
            <th>Usuario</th>
            <th>Estado</th>
            <th class="textright">Total Factura</th>
            <th class="textright">Acciones</th>
        </tr>

    <?php

    //paginador
    $sql_registro = mysqli_query($conection, "SELECT COUNT(*) as total_registros FROM `factura` WHERE $where");
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


    $query = mysqli_query($conection, "SELECT f.nofactura, f.fecha, f.totalfactura, f.estado, u.nombre as vendedor
                                            FROM factura f
                                            INNER JOIN usuario u
                                            ON f.usuario = u.id_usuario 
                                            WHERE $where AND f.estado != 10
                                            ORDER BY f.fecha ASC
                                            LIMIT $desde,$por_pagina
                                            ");
     
    mysqli_close($conection);

    $result = mysqli_num_rows($query);
    if($result > 0){
        while($data = mysqli_fetch_array($query)){

            if($data["estado"] == 1){
                $estado = '<span class= "pagada">Pagada</span>';
            }else{
                $estado = '<span class= "anulada">Anulada</span>';
            }
    ?>
            <tr id="row_<?php echo $data["nofactura"]; ?>">
                <td><?php echo $data["nofactura"];  ?></td>
                <td><?php echo $data["fecha"];  ?></td>
                <td><?php echo $data["vendedor"];  ?></td>
                <td class="estado"><?php echo $estado;  ?></td>
                <td class="textright totalfactura"><span>COP.</span><?php echo $data["totalfactura"]; ?></td>

                <td> 
                    <div class="div_acciones">
                        <div>
                        <button class="btn_view view_factura" type="button" f="<?php echo $data["nofactura"]; ?>">
                        <i class="fa fa-eye" aria-hidden="true"></i></button>
                        </div>

                <?php if($_SESSION['id_rol'] == 3 || $_SESSION['id_rol'] == 2 ){

                        if($data["estado"] == 1){
                ?>
                    <div class="div_factura">
                        <button class="btn_anular anular_factura" fac="<?php echo $data["nofactura"]; ?>">
                        <i class="fa fa-ban" aria-hidden="true"></i></button>
                    </div>
                <?php       }else{ ?>
                    <div class="div_factura">
                        <button type="button" class="btn_anular inactive"><i class="fa fa-ban" aria-hidden="true"></i></button>
                    </div>
                <?php            } 
                          } 
                ?>
                     </div>
                </td>
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
             <li> <a href="?pagina=<?php echo 1;?>&<?php echo $buscar; ?>">|<</a></li>
             <li> <a href="?pagina=<?php echo $pagina-1; ?>&<?php echo $buscar; ?>"><<</a></li>
       
        <?php
        }
             for ($i=1; $i <= $total_paginas; $i++){

                 echo '<li><a href="?pagina='.$i.'&'.$buscar.'">'.$i.'</a></li>';
             }
       if($pagina != $total_paginas)
       {
       ?>   
             <li> <a href="?pagina=<?php echo $pagina+1; ?>&<?php echo $buscar; ?>">>></a></li>
             <li> <a href="?pagina=<?php echo $total_paginas; ?>&<?php echo $buscar; ?>">|></a></li>

       <?php } ?>
        </ul>
    </div>

    


<br><br><br><br><br><br>


    <!-- pie de pagina -->
<iframe src="pie.html"></iframe>

    
</body>
</html>