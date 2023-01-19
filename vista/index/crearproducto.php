<?php

session_start();

if($_SESSION['id_rol'] != 3 and $_SESSION['id_rol'] != 2){
    header('location: ./');
}

include('../../control/conexion.php');

	if(!empty($_POST))
	{
		$alert='';

		if(empty($_POST['cod_product']) || empty($_POST['nombre']) || empty($_POST['descripcion']) || 
        empty($_POST['cant_product']) || $_POST['cant_product'] <= 0  || empty($_POST['precio_product']) ||  $_POST['precio_product'] <= 0 || empty($_POST['unidad_com']))
		{
			$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{
            
            $cod_product = $_POST['cod_product'];
			$nombre = $_POST['nombre'];
			$descripcion  = $_POST['descripcion'];
			$cant_product  = $_POST['cant_product'];
            $precio_product = $_POST['precio_product'];
            $unidad_com   = $_POST['unidad_com'];
            $id_usuario   = $_SESSION['idusuario'];

          
            $query = mysqli_query($conection,"SELECT * FROM producto WHERE  nombre = '$nombre' OR cod_product = '$cod_product' ");
		
            $result = mysqli_fetch_array($query);

			if($result > 0){
				$alert='<p class="msg_error">El nombre o el cod. de producto ya existe.</p>';
			}else{

			$query_insert = mysqli_query($conection,"INSERT INTO producto(cod_product, nombre, descripcion, cant_product, precio_product, unidad_com, id_usuario)
																	VALUES('$cod_product', '$nombre', '$descripcion', '$cant_product', '$precio_product', '$unidad_com', '$id_usuario')");
			if($query_insert){
				$alert='<p class="msg_save">Porducto creado correctamente.</p>';
			}else{
				$alert='<p class="msg_error">Error al crear el producto .</p>';
			}

            }


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
    <title>MyTierra || Crear Porducto</title>
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
                    <h3 class="text-center" >Nuevo Producto</h3>  <br>
                    <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
                    <div class="card">
                        <div class="card-body">
                            <input type="text" class="form-control" name="cod_product" id="" placeholder="Cod. Producto" required autofocus>
                            <br>
                            <input type="text" class="form-control" name="nombre" id="" placeholder="Nombre" required autofocus>
                            <br>
                            <input type="text" class="form-control" name="descripcion" id="" placeholder="Descripcion" required>
                            <br>
                            <input type="number" class="form-control" name="cant_product" id="" placeholder="Cantidad" required>
                            <br>
                            <input type="text" class="form-control" name="precio_product" id="" placeholder="Precio" required>
                            <br>
                            <input type="text" class="form-control" name="unidad_com" id="" placeholder="Unidad Comercial" required>
                        </div>
                            <button  type="submit" class="btn btn-outline-success form-control">Crear Producto </button>
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