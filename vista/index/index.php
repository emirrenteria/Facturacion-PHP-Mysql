<?php

include('../../control/conexion.php');

$alert = '';
session_start();

if(!empty($_SESSION['active']))
{
	   header("location: inicio.php");
}else{

	if(!empty($_POST))
	{
		if(empty($_POST['email']) || empty($_POST['password']))
		{
	;

			$alert = 'Ingrese su usuario y su calve';

		}else{

			$email = mysqli_real_escape_string($conection, $_POST['email']);
			$pass =  md5(mysqli_real_escape_string($conection, $_POST['password']));


			$query = mysqli_query($conection, "SELECT * FROM usuario WHERE email='$email' and contrasenia='$pass'");
		 	mysqli_close($conection);
			$result = mysqli_num_rows($query);
			


			if($result)
			{  
				$data = mysqli_fetch_array($query);
				$_SESSION['active'] = true;
				$_SESSION['idusuario'] = $data['id_usuario'];
				$_SESSION['identificacion'] = $data['identificacion'];
				$_SESSION['nombre']  = $data['nombre'];
				$_SESSION['apellido']   = $data['apellido'];
				$_SESSION['telefono']    = $data['telefono'];
                $_SESSION['direccion'] = $data['direccion'];
				$_SESSION['email']  = $data['email'];
				$_SESSION['contrasenia']   = $data['contrasenia'];
				$_SESSION['estado']    = $data['estado'];
                $_SESSION['usuario_creacion'] = $data['usuario_creacion'];
				$_SESSION['fecha_creacion']  = $data['fecha_creacion'];
				$_SESSION['usuario_modificacion']   = $data['usuario_modificacion'];
				$_SESSION['fecha_modificacion']    = $data['fecha_modificacion'];
                $_SESSION['id_rol']    = $data['id_rol'];  


				header('location: inicio.php');

				
			}else{
					$alert = 'El usuario o la clave son incorrectos';
					session_destroy();
				
				}
	
	
			}
	
		}
	}

?>

<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">
    <meta name="author" content="GrupoADSI">
    <meta name="Descripcion" content="Sistema de Facturacion">
    <meta name="keywords" content="sistema, verduras, facturacion, frutas, mytierra">
    <?php include "scripts.php"; ?>
    <title>MyTierra || Bienvenido</title>


   

    
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 text-center">
              
            </div>
            <div class="col-md-4 text-center text-uppercase">
                <div class="container">
                    <br>
                    <br>
                    <form  method="post" action=""> 
                    <h1 class="text-center">Ingreso </h1>
                    <img src="../img/login.webp" width="200" height="200" alt="login">
                        <br>
                        <br>
                    <div class="card">
                        <div class="card-body">
                            
                            <input type="email" class="form-control" name="email" id="" placeholder="E-mail@example.com"  autofocus>
                            <br>
                            <input type="password" class="form-control" name="password" id="pwd" placeholder="Contraseña" >
                        </div>
                        <div id="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
                        <div>  <button  type="submit" class="btn btn-outline-success form-control">Iniciar Sesion </button>  </div>
                    </div>
                   </form> 

                </div>
            </div>
        
        </div>
    </div>

    
    

</body>

</html>