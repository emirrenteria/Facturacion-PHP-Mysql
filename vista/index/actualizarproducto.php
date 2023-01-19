<?php

session_start();

if($_SESSION['id_rol'] != 3 and $_SESSION['id_rol'] != 2){
    header('location: ./');
}

include('../../control/conexion.php');

	if(!empty($_POST))
	{
		$alert='';

		if(empty($_POST['id']) || empty($_POST['nombre']) || empty($_POST['descripcion']) || 
        empty($_POST['precio_product']) ||  $_POST['precio_product'] <= 0 || empty($_POST['unidad_com']))
		{    
			$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{
            
            $cod_product = $_POST['id'];
			$nombre = $_POST['nombre'];
			$descripcion  = $_POST['descripcion'];
            $precio_product = $_POST['precio_product'];
            $unidad_com   = $_POST['unidad_com'];
            $id_usuario   = $_SESSION['idusuario'];



			$query_update= mysqli_query($conection,"UPDATE producto
                                                    SET nombre = '$nombre',
                                                        descripcion = '$descripcion',
                                                        precio_product =  '$precio_product',
                                                        unidad_com = '$unidad_com'
                                                        WHERE cod_product = $cod_product");
			
            
            
            if($query_update){
				$alert='<p class="msg_save">Porducto actuualizado correctamente.</p>';
			}else{
				$alert='<p class="msg_error">Error al actualizar el producto .</p>';
			}


		}

	}

    
    //VALIDAR PRODUCTO

    if(empty($_REQUEST['id'])){
        header("location: listaproducto.php");

    }else{

        $id_producto = $_REQUEST['id'];

        if(!is_numeric($id_producto)){
            header("location: listaproducto.php");
        }

        $query_producto = mysqli_query($conection, "SELECT cod_product, nombre, descripcion, precio_product, unidad_com FROM producto WHERE cod_product = $id_producto AND estado = 1");
        $result_producto = mysqli_num_rows($query_producto);

        if($result_producto > 0){
            $data_producto = mysqli_fetch_assoc($query_producto);

         //   print_r($data_producto);
        }else{
            header("location: listaproducto.php");
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
    <title>MyTierra || Actualizar Porducto</title>
    <link rel="shortcut icon" href="../img/logo.ico">
    
    
    
    

</head>

<body>

    <!-- menu de pagina -->
    <div class="contenedor">
        <header>
            <nav> <?php include "nav.php"; ?> </nav>
        </header>
    </div>


   <!-- formulario para crear productos -->
    <div class="container " id="contepro">
        <div class="row justify-content-center">
            <div class="col-6">  
                <form name="" method="post" action=""> 
                    <input type="hidden" name="id"  value="<?php echo  $data_producto['cod_product']; ?>" >  
                    <h3 class="text-center" >Actualizar Producto</h3>  <br>
                    <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
                    <div class="card">
                        <div class="card-body">
                       
                            <input type="text" class="form-control" name="nombre" id="" placeholder="Nombre" value="<?php echo  $data_producto['nombre']; ?>" required autofocus>
                            <br>
                            <input type="text" class="form-control" name="descripcion" id="" placeholder="Descripcion" value="<?php echo $data_producto['descripcion']; ?>" required>
                            <br>
                            <input type="text" class="form-control" name="precio_product" id="" placeholder="Precio" value="<?php echo $data_producto['precio_product']; ?>" required>
                            <br>
                            <input type="text" class="form-control" name="unidad_com" id="" placeholder="Unidad Comercial" value="<?php echo $data_producto['unidad_com']; ?>" required>
                        </div>
                            <button  type="submit" class="btn btn-outline-success form-control">Actualizar Producto </button>
                            <button  type="reset" class="btn btn-outline-danger form-control">Limpiar Formulario </button>  
                    </div> 
                </form> 
                
            </div>   
        </div>
    </div>
    <br><br><br><br><br>


  
    <!-- pie de pagina -->
<iframe src="pie.html" class="pie"></iframe>


</body>
</html>