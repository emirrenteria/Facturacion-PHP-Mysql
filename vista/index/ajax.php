<?php

include('../../control/conexion.php');
// print_r($_POST); exit;
session_start();
if(!empty($_POST)){

    // Extraer datos del producto
    if($_POST['action'] == 'infoProducto'){

        $producto_id = $_POST['producto'];

        $query = mysqli_query($conection, "SELECT cod_product, nombre, cant_product, precio_product FROM producto
                                            WHERE cod_product = $producto_id  AND estado = 1");

        mysqli_close($conection);

        $result = mysqli_num_rows($query);
        if($result > 0){
            $data = mysqli_fetch_assoc($query);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            exit;     
        } 
           
          echo 'error';
          exit;
          
    }

    // Agregar productos a entrada
    if($_POST['action'] == 'addProduct'){

        if(!empty($_POST['cantidad']) || !empty($_POST['precio']) || !empty($_POST['producto_id']))
        {
            $cantidad = $_POST['cantidad'];
            $precio = $_POST['precio'];
            $producto_id = $_POST['producto_id'];
            $usuario_id = $_SESSION['idusuario'];

            $query_insert = mysqli_query($conection, "INSERT INTO entrada(cod_product,
                                                                           cant_product,
                                                                           precio_product,
                                                                           id_usuario)
                                                                VALUES($producto_id,
                                                                       $cantidad,
                                                                       $precio,
                                                                       $usuario_id)");
            if($query_insert){

                $quuery_upd = mysqli_query($conection, "CALL actualizar_precio_producto($cantidad,  $precio,  $producto_id)");
                $result_pro = mysqli_num_rows($quuery_upd);
                if($result_pro > 0){
                    $data = mysqli_fetch_assoc($quuery_upd);
                    $data['cod_product'] =  $producto_id;
                    echo json_encode($data, JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }else{
                echo 'error 1';
            }
            mysqli_close($conection);
        }else{
            echo 'error 2';
        }

         exit;
    }

    // Eliminar producto
    if($_POST['action'] == 'delProduct'){

        if(empty($_POST['producto_id']) || !is_numeric($_POST['producto_id'])){

            echo "Error";
        }else{

            $idproducto = $_POST['producto_id'];
        
            $query_delete = mysqli_query($conection, "UPDATE producto SET estado = 0 WHERE cod_product =  $idproducto");
            mysqli_close($conection);
                
            if($query_delete){
                echo "Ok";
            }else{
                echo "Error al eliminar";
            }
        }
        echo "Error";
    }

    // Agragra producto al detalle temporal
    if($_POST['action'] == 'addProductoDetalle'){
        
        if(empty($_POST['producto']) || empty($_POST['cantidad'])){
            echo 'error';
        }else{
            $codproducto = $_POST['producto'];
            $cantidad = $_POST['cantidad'];
            $token = md5($_SESSION['idusuario']);


            $query_iva = mysqli_query($conection, "SELECT iva FROM configuracion");
            $result_iva = mysqli_num_rows($query_iva);

            $query_detalle_temp = mysqli_query($conection, "CALL add_detalle_temp($codproducto, $cantidad, '$token')");
            $result = mysqli_num_rows($query_detalle_temp);

            $detalleTabla = '';
            $sub_total = 0;
            $iva = 0;
            $total = 0;
            $arrayData = array();
            
            if($result > 0){
                if($result_iva > 0){
                    $info_iva = mysqli_fetch_assoc($query_iva);
                    $iva = $info_iva['iva'];
                }

                while($data = mysqli_fetch_assoc($query_detalle_temp)){
                    $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                    $sub_total = round($sub_total + $precioTotal, 2);
                    $total = round($total + $precioTotal, 2);

                    $detalleTabla .= '<tr>
                                        <td>'.$data['cod_product'].'</td>
                                        <td colspan="2">'.$data['nombre'].'</td>
                                        <td class="textcenter">'.$data['cantidad'].'</td>
                                        <td class="textright">'.$data['precio_venta'].'</td>
                                        <td class="textright">'.$precioTotal.'</td>
                                        <td class="">
                                            <a class="link_delete" href="#" onclick="event.preventDefault();
                                            del_product_detalle('.$data['correlativo'].');"> Eliminar </a>
                                        </td>
                                    </tr>';
                }

                $impuesto = round($sub_total * ($iva / 100), 2);
                $tl_sniva = round($sub_total - $impuesto, 2);
                $total = round($tl_sniva + $impuesto, 2);

                $detalleTotales = '<tr>
                                        <td colspan="5" class="textright">SUBTOTAL Q.</td>
                                        <td  class="textright">'.$tl_sniva.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="textright">IVA ('.$iva.'%)</td>
                                        <td  class="textright">'.$impuesto.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="textright">TOTAL Q.</td>
                                        <td  class="textright">'.$total.'</td>
                                    </tr>';

                $arrayData['detalle'] = $detalleTabla;
                $arrayData['totales'] = $detalleTotales;

                echo json_encode($arrayData, JSON_UNESCAPED_UNICODE);
            }else{
                echo 'error';
            }

            mysqli_close($conection);
        }
         exit;  
    } 

    // Extraer datos del detalle_temp
    if($_POST['action'] == 'serchForDetalle'){
        
        if(empty($_POST['user'])){
            echo 'error';
        }else{
           
            $token = md5($_SESSION['idusuario']);

            $query = mysqli_query($conection, "SELECT tmp.correlativo,
                                                      tmp.token_user,
                                                      tmp.cantidad,
                                                      tmp.precio_venta,
                                                      p.cod_product,
                                                      p.nombre
                                                FROM detalle_temp tmp
                                                INNER JOIN producto p
                                                ON tmp.cod_product = p.cod_product
                                                WHERE token_user = '$token' ");

             $result = mysqli_num_rows($query);

            $query_iva = mysqli_query($conection, "SELECT iva FROM configuracion");
            $result_iva = mysqli_num_rows($query_iva);

            $detalleTabla = '';
            $sub_total = 0;
            $iva = 0;
            $total = 0;
            $arrayData = array();
            
            if($result > 0){
                if($result_iva > 0){
                    $info_iva = mysqli_fetch_assoc($query_iva);
                    $iva = $info_iva['iva'];
                }

                while($data = mysqli_fetch_assoc($query)){
                    $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                    $sub_total = round($sub_total + $precioTotal, 2);
                    $total = round($total + $precioTotal, 2);

                    $detalleTabla .= '<tr>
                                        <td>'.$data['cod_product'].'</td>
                                        <td colspan="2">'.$data['nombre'].'</td>
                                        <td class="textcenter">'.$data['cantidad'].'</td>
                                        <td class="textright">'.$data['precio_venta'].'</td>
                                        <td class="textright">'.$precioTotal.'</td>
                                        <td class="">
                                            <a class="link_delete" href="#" onclick="event.preventDefault();
                                            del_product_detalle('.$data['correlativo'].');"> Eliminar </a>
                                        </td>
                                    </tr>';
                }

                $impuesto = round($sub_total * ($iva / 100), 2);
                $tl_sniva = round($sub_total - $impuesto, 2);
                $total = round($tl_sniva + $impuesto, 2);

                $detalleTotales = '<tr>
                                        <td colspan="5" class="textright">SUBTOTAL Q.</td>
                                        <td  class="textright">'.$tl_sniva.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="textright">IVA ('.$iva.'%)</td>
                                        <td  class="textright">'.$impuesto.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="textright">TOTAL Q.</td>
                                        <td  class="textright">'.$total.'</td>
                                    </tr>';

                $arrayData['detalle'] = $detalleTabla;
                $arrayData['totales'] = $detalleTotales;

                echo json_encode($arrayData, JSON_UNESCAPED_UNICODE);
            }else{
                echo 'error';
            }

            mysqli_close($conection);
        }
         exit;  
    } 

    //Eliminar produucto del detalle

    if($_POST['action'] == 'delProductoDetalle'){
        if(empty($_POST['id_detalle'])){

            echo 'error';

        }else{
            
            $id_detalle = $_POST['id_detalle'];
            $token = md5($_SESSION['idusuario']);

            $query_iva = mysqli_query($conection, "SELECT iva FROM configuracion");
            $result_iva = mysqli_num_rows($query_iva);

            $query_detalle_temp = mysqli_query($conection, "CALL del_detalle_temp($id_detalle, '$token')");
            $result = mysqli_num_rows($query_detalle_temp);

            $detalleTabla = '';
            $sub_total = 0;
            $iva = 0;
            $total = 0;
            $arrayData = array();
            
            if($result > 0){
                if($result_iva > 0){
                    $info_iva = mysqli_fetch_assoc($query_iva);
                    $iva = $info_iva['iva'];
                }

                while($data = mysqli_fetch_assoc($query_detalle_temp)){
                    $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                    $sub_total = round($sub_total + $precioTotal, 2);
                    $total = round($total + $precioTotal, 2);

                    $detalleTabla .= '<tr>
                                        <td>'.$data['cod_product'].'</td>
                                        <td colspan="2">'.$data['nombre'].'</td>
                                        <td class="textcenter">'.$data['cantidad'].'</td>
                                        <td class="textright">'.$data['precio_venta'].'</td>
                                        <td class="textright">'.$precioTotal.'</td>
                                        <td class="">
                                            <a class="link_delete" href="#" onclick="event.preventDefault();
                                            del_product_detalle('.$data['correlativo'].');"> Eliminar </a>
                                        </td>
                                    </tr>';
                }

                $impuesto = round($sub_total * ($iva / 100), 2);
                $tl_sniva = round($sub_total - $impuesto, 2);
                $total = round($tl_sniva + $impuesto, 2);

                $detalleTotales = '<tr>
                                        <td colspan="5" class="textright">SUBTOTAL Q.</td>
                                        <td  class="textright">'.$tl_sniva.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="textright">IVA ('.$iva.'%)</td>
                                        <td  class="textright">'.$impuesto.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="textright">TOTAL Q.</td>
                                        <td  class="textright">'.$total.'</td>
                                    </tr>';

                $arrayData['detalle'] = $detalleTabla;
                $arrayData['totales'] = $detalleTotales;

                echo json_encode($arrayData, JSON_UNESCAPED_UNICODE);
            }else{
                echo 'error';
            }

            mysqli_close($conection);
        }
         exit;  
    } 

    // Anular venta

    if($_POST['action'] == 'anularVenta'){
     
        
        $token = md5($_SESSION['idusuario']);

        $query_del = mysqli_query($conection, "DELETE FROM detalle_temp WHERE token_user  = '$token' ");
        mysqli_close($conection);
        if($query_del){
            echo 'Ok';
        }else{
            echo 'error';
        }
        exit;

        
    }

    // Procesar venta

    if($_POST['action'] == 'procesarVenta'){

        $token = md5($_SESSION['idusuario']);
        $usuario = $_SESSION['idusuario'];

        $query = mysqli_query($conection, "SELECT * FROM detalle_temp WHERE token_user = '$token'");
        $result = mysqli_num_rows($query);

        if($result > 0){
            $query_procesar = mysqli_query($conection, "CALL procesar_venta($usuario, '$token')");
            $result_detalle = mysqli_num_rows($query_procesar);

            if($result_detalle > 0){
                $data = mysqli_fetch_assoc($query_procesar);
                echo json_encode($data, JSON_UNESCAPED_UNICODE);
            }else{
                echo "error";
            }
        }else{
            echo "error";
        }
        mysqli_close($conection);
        exit;
    }

    //Info Factura
    if($_POST['action'] == 'infoFactura'){
        if(!empty($_POST['nofactura'])){

            $nofactura = $_POST['nofactura'];
            $query = mysqli_query($conection, "SELECT * FROM factura WHERE nofactura='$nofactura' AND estado=1");
            mysqli_close($conection);

            $result = mysqli_num_rows($query);
            if($result > 0){

                $data = mysqli_fetch_assoc($query);
                echo json_encode($data, JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
        echo "error";
        exit;

    }

     // Anular Factura
     if($_POST['action'] == 'anularFactura'){

        if(!empty($_POST['noFactura'])){

            $noFactura = $_POST['noFactura'];

            $query_anular = mysqli_query($conection, "CALL anular_factura($noFactura)");
            mysqli_close($conection);
            
            $result = mysqli_num_rows($query_anular);
            if($result > 0){

                $data = mysqli_fetch_assoc($query_anular);
                echo json_encode($data, JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
        echo "error";
        exit;

    }


    


}

exit;

?>