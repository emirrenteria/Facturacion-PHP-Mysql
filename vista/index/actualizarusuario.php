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
        empty($_POST['telefono']) || empty($_POST['direccion']) || empty($_POST['email']) 
        || empty($_POST['rol']))
		{
			$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{
            
            $id_usuario= $_POST['id_usuario'];
            $identificacion = $_POST['identificacion'];
			$nombre = $_POST['nombre'];
			$apellido  = $_POST['apellido'];
			$telefono   = $_POST['telefono'];
            $direccion   = $_POST['direccion'];
            $email   = $_POST['email'];
			$rol    = $_POST['rol'];


			$query = mysqli_query($conection,"SELECT * FROM `usuario`
                                                WHERE (identificacion = $identificacion AND id_usuario != $id_usuario) 
                                                OR (email = '$email' AND id_usuario != $id_usuario) ");

			$result = mysqli_fetch_array($query);

			if($result > 0){
				$alert='<p class="msg_error">El No. de Identificacion o Correo ya existe.</p>';
			}else{

				$query_update = mysqli_query($conection,"UPDATE usuario
														 SET identificacion = '$identificacion', nombre = '$nombre', apellido = '$apellido', telefono ='$telefono',
                                                             direccion = '$direccion', email = '$email',  id_rol = '$rol'
                                                         WHERE id_usuario = $id_usuario ");


				if($query_update){
					$alert='<p class="msg_save">Usuario actualizado correctamente.</p>';
				}else{
					$alert='<p class="msg_error">Error al actualizar el usuario.</p>';
				}

			}


		}
        mysqli_close($conection);

	}





    // Mostrar datos

    if(empty($_GET['id'])){
        header('Location: listausuario.php');
        mysqli_close($conection);
    }

    $id_usuario = $_GET['id'];

    $sql = mysqli_query($conection, "SELECT u.id_usuario, u.identificacion, u.nombre, u.apellido, u.telefono, u.direccion, u.email, (u.id_rol) as id_rol, (r.nombre_rol) as nombre_rol
                                        FROM usuario u
                                        INNER JOIN rol r
                                        on u.id_rol = r.id_rol
                                        WHERE id_usuario = $id_usuario 
                                        AND estado = 1");

   mysqli_close($conection);
   
   $result_sql = mysqli_num_rows($sql);

   if($result_sql == 0){
       header('Location: listausuario.php');
   }else{
       $option= '';
       while($data = mysqli_fetch_array($sql)){
           
           $id_usuario = $data['id_usuario'];
           $identificacion = $data['identificacion'];
           $nombre = $data['nombre'];
           $apellido = $data['apellido'];
           $telefono = $data['telefono'];
           $direccion = $data['direccion'];
           $email = $data['email'];
           $id_rol = $data['id_rol'];
           $nombre_rol = $data['nombre_rol'];

           if($id_rol == 1){
               $option = '<option value="'.$id_rol.'" select>'.$nombre_rol.'</option>';
           }else if($id_rol == 2){
               $option = '<option value="'.$id_rol.'" select>'.$nombre_rol.'</option>';
           }else if($id_rol == 3){
               $option = '<option value="'.$id_rol.'" select>'.$nombre_rol.'</option>';
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
    <title>MyTierra || Actualizar Usuario</title>
    <link rel="shortcut icon" href="../img/logo.ico">



</head>

<body>

    <!-- barra de navegación -->
    <div class="contenedor">
        <header>
            <nav> <?php include "nav.php"; ?> </nav>
        </header>
    </div>

     <!-- formulario para crear usuarios -->
    <div class="container ">
        <div class="row justify-content-center">
            <div class="col-6">
                <form method="post" action=""> 
                <h3 class="text-center">Actualizar Usuario</h3>
                <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
                    <div class="card">
                        <div class="card-body">
                            <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>"> 
                            <br>
                            <input type="number" class="form-control" name="identificacion" id="" placeholder="No. Identificacion" value="<?php echo $identificacion; ?>" required autofocus> 
                            <br>
                            <input type="text" class="form-control" name="nombre" id="" placeholder="Nombre Completo" value="<?php echo $nombre; ?>" required>
                            <br>
                            <input type="text" class="form-control" name="apellido" id="" placeholder="Apellidos" value="<?php echo $apellido; ?>" required>
                            <br>
                            <input type="tel" class="form-control" name="telefono" id="" placeholder="Telefono" value="<?php echo $telefono; ?>" required>
                            <br>
                            <input type="text" class="form-control" name="direccion" id="" placeholder="Direccion" value="<?php echo $direccion; ?>" required>
                            <br>
                            <input type="email" class="form-control" name="email" id="" placeholder="Email" value="<?php echo $email; ?>" required>
                            <br>

                            <?php 
                            
                                include('../../control/conexion.php');
                                $query_rol = mysqli_query($conection,"SELECT * FROM rol");
                                mysqli_close($conection);
                                $result_rol = mysqli_num_rows($query_rol);

				            ?>

                            <select  class="form-control notItemOne" name="rol" id="rol">
                                <?php 
                                    echo $option;
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
                            <button  type="submit" class="btn btn-outline-success form-control">Actualizar Usuario </button> 
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