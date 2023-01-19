
        <header class="cabeza">
            <img class="logo"  src="../img/logo.png" alt="loco de la empre MyTierra S.A">
            <h2 class="texto"> <a class="sinlinea"  href="inicio.html"> Frutas y Verduras My Tierra S.A.  </a> </h2>
            
            
            <h6 class="usuario"> <?php session_start(); echo $_SESSION['nombre'].'-'.$_SESSION['id_rol']; ?> </h6>
            <a   href="../../control/salir.php" class="salir"> <i class="fa fa-power-off" aria-hidden="true"> </i> Cerrar Sesion </a>


        </header>


        <nav id= "nv">
            <ul class="nav">
                <li> <a href="inicio.php"> <i class="fa fa-home" aria-hidden="true"> </i> INICIO</a> </li>

                <?php
                    
                    if($_SESSION['id_rol'] == 3){
                
                ?>
                    
                <li> <a href="#"> <i class="fa fa-user" aria-hidden="true"></i>  USUARIOS </a> 
                    <ul>
                        <li> <a href="crearusuario.php"> Crear Usuarios </a> </li>
                        <li> <a href="listausuario.php"> Lista Usuarios </a> </li>
                    </ul>
                </li>

                <?php } ?>

                <?php
                    
                    if($_SESSION['id_rol'] == 3 || $_SESSION['id_rol'] == 2){
                
                ?>
                   
                <li> <a href="#"> <i class="fa fa-apple" aria-hidden="true"></i> PRODUCTOS </a> 
                    <ul>
                        <li> <a href="crearproducto.php"> Crear Productos </a> </li>
                        <li> <a href="listaproducto.php"> Lista Productos </a> </li>
                    </ul>
                </li>
                
                <?php } ?>

                <li> <a href="#"> <i class="fa fa-file-text" aria-hidden="true"></i> VENTAS </a>
                    <ul>
                        <li> <a href="nueva_venta.php"> Nueva Venta  </a> </li>
                        <li> <a href="ventas.php"> Lista de Ventas </a> </li>
                    </ul>
               </li>
                <!--   
                <li> <a href="#"><i class="fa fa-industry" aria-hidden="true"></i> REPORTES </a> </li>
                -->
            </ul>
        </nav>
