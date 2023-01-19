<?php

session_start();
if($_SESSION['id_rol'] != 3){
    header('location: ./');
}

include('../../control/conexion.php');

	if(!empty($_POST))
	{
		$alert='';

		if(empty($_POST['identificacion']) || empty($_POST['nombre']) || empty($_POST['apellido']) || 
        empty($_POST['telefono']) || empty($_POST['direccion']) || empty($_POST['email']) || empty($_POST['contrasenia']) 
        || empty($_POST['rol']))
		{
			$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{
            
            $identificacion = $_POST['identificacion'];
			$nombre = $_POST['nombre'];
			$apellido  = $_POST['apellido'];
			$telefono   = $_POST['telefono'];
            $direccion   = $_POST['direccion'];
            $email   = $_POST['email'];
			$clave  = md5($_POST['contrasenia']);
			$rol    = $_POST['rol'];


			$query = mysqli_query($conection,"SELECT * FROM usuario WHERE  email = '$email' OR identificacion = '$identificacion' ");
		
            $result = mysqli_fetch_array($query);

			if($result > 0){
				$alert='<p class="msg_error">El No. de Identificacion o Correo ya existe.</p>';
			}else{

				$query_insert = mysqli_query($conection,"INSERT INTO usuario(identificacion, nombre, apellido, telefono, direccion, email, contrasenia, id_rol)
																	VALUES('$identificacion', '$nombre', '$apellido', '$telefono', '$direccion', '$email', '$clave', '$rol')");
				mysqli_close($conection);
                if($query_insert){
					$alert='<p class="msg_save">Usuario creado correctamente.</p>';
				}else{
					$alert='<p class="msg_error">Error al crear el usuario.</p>';
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
    <title>MyTierra || Crear Usuario</title>
    <link rel="shortcut icon" href="../img/logo.ico">
    
    

</head>

<body>

    <!-- barra de navegación -->
    <div class="contenedor">
        <header>
            <nav>  <?php include "nav.php"; ?> </nav>
        </header>
    </div>

     <!-- formulario para crear usuarios -->
    <div class="container ">
        <div class="row justify-content-center">
            <div class="col-6">
                <form method="post" action=""> 
                <h3 class="text-center">Nuevo Usuario</h3>
                <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
                    <div class="card">
                        <div class="card-body">
                            <input type="number" class="form-control" name="identificacion" id="" placeholder="No. Identificacion" required autofocus> 
                            <br>
                            <input type="text" class="form-control" name="nombre" id="" placeholder="Nombre Completo" required>
                            <br>
                            <input type="text" class="form-control" name="apellido" id="" placeholder="Apellidos" required>
                            <br>
                            <input type="tel" class="form-control" name="telefono" id="" placeholder="Telefono" required>
                            <br>
                            <input type="text" class="form-control" name="direccion" id="" placeholder="Direccion" required>
                            <br>
                            <input type="email" class="form-control" name="email" id="" placeholder="Email" required>
                            <br>
                            <input type="password" class="form-control" name="contrasenia" id="pwd" placeholder="Contraseña" required>
                            <br>

                            <?php 

                                $query_rol = mysqli_query($conection,"SELECT * FROM rol");
                                mysqli_close($conection);
                                $result_rol = mysqli_num_rows($query_rol);

				            ?>

                            <select  class="form-control" name="rol" id="rol">
                                <?php 
                                    if($result_rol > 0)
                                    {
                                        while ($rol = mysqli_fetch_array($query_rol)) {
                                ?>
                                        <option value="<?php echo $rol["id_rol"]; ?>"><?php echo $rol["nombre_rol"] ?></option>
                                <?php 
                                            # code...
                                        }
                                        
                                    }
                                ?>
                            </select>
                            <br>           
                        </div>
                            <button  type="submit" class="btn btn-outline-success form-control">Crear Usuario </button> 
                            <button  type="reset" class="btn btn-outline-danger form-control">Limpiar Formulario </button>
                    </div> 
                </form>
            </div>   
        </div>
    </div>
    <br>



    <!-- pie de pagina -->
<iframe src="pie.html"></iframe>

    
</body>
</html>