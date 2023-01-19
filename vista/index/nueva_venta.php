<?php

session_start();
include('../../control/conexion.php');



?>


<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="author" content="GrupoADSI">
    <meta name="Descripcion" content="Sistema de Facturacion">
    <meta name="keywords" content="sistema, verduras, facturacion, frutas, mytierra">
    <?php include "scripts.php"; ?>
    <title>Nueva Venta</title>
    <link rel="shortcut icon" href="../img/logo.ico">


</head>
<body>

 <!-- barra de navegación -->
    
 <div class="contenedor">
        <header>
            <nav> <?php include "nav.php"; ?> </nav>
        </header>
    </div>
    

<section>
    <div class="datos_venta">
        <h4> Datos Venta</h4>
            <div class="datos">
                <div class="wd50">
                    <label> Vendedor </label>
                    <p><?php echo $_SESSION['nombre']; ?></p>
                </div>
                <div class="wd50">
                    <label> Acciones </label>
                    <div id="acciones_ventas">
                        <a href="#" class="btn_ok textcenter" id="btn_anular_venta"> Anular </a>
                        <a href="#" class="btn_new textcenter" id="btn_facturar_venta" style="display: none;"> Procesar </a>
                    </div>
                </div>
            </div>
    </div>

    <table class="tbl_venta">
        <thead>
            <tr>
                <th width="100px">Codigo</th>
                <th>Descripcion</th>
                <th>Existencias</th>
                <th width="100px">Cantidad</th>
                <th classs="textright">Precio</th>
                <th class="textright">Precio Total</th>
                <th>Accion</th>
            </tr>
            <tr>
                <td><input type="text" name="txt_cod_producto" id="txt_cod_producto"></td>
                <td id="txt_descripcion">-</td>
                <td id="txt_existencia">-</td>
                <td><input type="text" name="txt_cant_producto" id="txt_cant_producto" value="0" min="1" disabled></td>
                <td id="txt_precio" class="textright">0.00</td>
                <td id="txt_precio_total" class="textright">0.00</td>
                <td> <a href="#" id="add_product_venta" class="link_add">Agregar</a></td>
            </tr>
            <tr>
                <th>Codigo</th>
                <th colspan="2">Descripcion</th>
                <th>Cantidad</th>
                <th class="textright">Precio</th>
                <th class="textright">Precio Total</th>
                <th>Accion</th>
            </tr>
        </thead>
        <tbody id="detalle_venta">
            <!--Contenido AJAX-->
        </tbody>
        <tfoot id="detalle_totales">
            <!-- CONTENIDO AJAX -->    
        </tfoot>
    </table>
</section>



<!-- pie de pagina -->
<iframe src="pie.html"></iframe>


<script type="text/javascript">
    $(document).ready(function(){

        var usuarioid = '<?php echo $_SESSION['idusuario']; ?>';
        serchForDetalle(usuarioid);

    });
</script>
    
</body>
</html>