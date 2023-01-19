$(document).ready(function(){

    // Modal For Add Product

    $('.add_product').click(function(event){

        event.preventDefault();
        var producto = $(this).attr('product');
        var action = 'infoProducto';

        $.ajax({

            url: 'ajax.php',
            type: 'POST',
            async: true,
            data: {action:action,producto:producto},

            success: function(response){
                if(response != 'error'){
                    var info = JSON.parse(response);


                 //   $('#producto_id').val(info.cod_product);
                 //   $('.nameProducto').html(info.nombre);

                    $('.bodyModal').html('<form action="" method= "post" name="form_add_product" id="form_add_product" onsubmit="'+
                                                'event.preventDefault(); sendDataProduct(); ">'+
                                                '<div class="card">'+
                                                '<div class="card-body">'+
                                               '<h1>Agregar Producto</h1>'+
                                                '<h3 class="nameProducto">'+info.nombre+'</h3><br>'+
                                                '<input class="form-control" type="number" name="cantidad" id="txtCantidad" placeholder="Cantidad del Producto" require><br>'+
                                                '<input class="form-control" type="text" name="precio" id="txtPrecio" placeholder="Precio del Producto" require>'+
                                                '<input type="hidden" name="producto_id" id="producto_id" value="'+info.cod_product+'"  require>'+    
                                                '<input type="hidden" name="action" value="addProduct" require>'+  
                                                '<div class="alert alertAddProduct"></div>'+
                                                '<button type="submit" class="btn_new">Agregar</button>'+   
                                                '<a href="#" class="btn_ok coloseModal" onclick="coloseModal();"  class="btn_ok closeModal">Cerrar</a>'+
                                                '</div>'+
                                                '</div>'+
                                         '</form>');
                }
            },

            error: function(error){
                console.log(error);
            }

        });


        $('.modal').fadeIn();

    });

     // Modal For Delete Product

    $('.del_product').click(function(event){

        event.preventDefault();
        var producto = $(this).attr('product');
        var action = 'infoProducto';

        $.ajax({

            url: 'ajax.php',
            type: 'POST',
            async: true,
            data: {action:action,producto:producto},

            success: function(response){
                if(response != 'error'){
                    var info = JSON.parse(response);


                 //   $('#producto_id').val(info.cod_product);
                 //   $('.nameProducto').html(info.nombre);

                    $('.bodyModal').html('<form action="" method= "post" name="form_del_product" id="form_del_product" onsubmit="'+
                                                'event.preventDefault(); delProduct(); ">'+
                                                '<div class="card">'+
                                                '<div class="card-body">'+
                                                '<h1>Eliminar Producto</h1><br>'+
                                                '<h4>¿Esta seguro de eliminar el siguiente registro?</h4><br>'+
                                                '<h3 class="nameProducto">'+info.nombre+'</h3>'+
                                                '<input type="hidden" name="producto_id" id="producto_id" value="'+info.cod_product+'"  require>'+    
                                                '<input type="hidden" name="action" value="delProduct" require>'+  
                                                '<div class="alert alertAddProduct"></div>'+
                                                '<a href="#" class="btn_cancel" onclick="coloseModal()";>Cancelar</a>'+  
                                                '<button type="submit" class="btn_ok" >Eliminar</button>'+
                                                '</div>'+
                                                '</div>'+
                                               
                                         '</form>');
                }
            },

            error: function(error){
                console.log(error);
            }

        });


        $('.modal').fadeIn();

    });


    // Buscar Producto

    $('#txt_cod_producto').keyup(function(e){
        e.preventDefault();

        var producto = $(this).val();
        var action = 'infoProducto';

        if(producto != '')
        {
            $.ajax({
                
                url: 'ajax.php',
                type: 'POST',
                async: true,
                data: {action:action,producto:producto},

                success: function(response)
                {     
                    if(response != 'error')
                    {
                        var info = JSON.parse(response);
                        $('#txt_descripcion').html(info.nombre);
                        $('#txt_existencia').html(info.cant_product);
                        $('#txt_cant_producto').val('1');
                        $('#txt_precio').html(info.precio_product);
                        $('#txt_precio_total').html(info.precio_product);


                        //Activar Cantidad
                        $('#txt_cant_producto').removeAttr('disabled');


                        //Mostar boton agregar
                        $('#add_product_venta').slideDown();
                    }else{

                        $('#txt_descripcion').html('-');
                        $('#txt_existencia').html('-');
                        $('#txt_cant_producto').val('0');
                        $('#txt_precio').html(0.00);
                        $('#txt_precio_total').html(0.00);

                        //Bloquear Cantidad

                        $('#txt_cant_producto').attr('disabled', 'disable');

                        //Ocultar boton agregar

                        $('#add_product_venta').slideUp(s);

                    }     
                },
                
                error: function(error){
                }
            });

        }
    
    });


    // Validar la cantidad de producto antes de agregar

    $('#txt_cant_producto').keyup(function(e){
        e.preventDefault();

        var precio_total = $(this).val() * $('#txt_precio').html();
        var existencia = parseInt($('#txt_existencia').html());
        $('#txt_precio_total').html(precio_total);
        

        // ocultar el boton agregar si la cantidad es menor que 1
        if( ( $(this).val() < 1 || isNaN($(this).val())) ||  ($(this).val() > existencia) ) {
            $('#add_product_venta').slideUp();
        }else{
            $('#add_product_venta').slideDown();
        }
    });


    // Agergar Porducto al detalle

    $('#add_product_venta').click(function(e){
        e.preventDefault();

        if($('#txt_cant_producto').val() > 0){

            var codproducto = $('#txt_cod_producto').val();
            var cantidad = $('#txt_cant_producto').val();
            var action = 'addProductoDetalle';
    
                $.ajax({
    
                    url: 'ajax.php',
                    type: "POST",
                    async: true,
                    data: {action:action, producto:codproducto, cantidad:cantidad},
    
                    success: function(response){
                        if(response != 'error'){

                            var info = JSON.parse(response);
                            $('#detalle_venta').html(info.detalle);
                            $('#detalle_totales').html(info.totales);

                            $('#txt_cod_producto').val('');
                            $('#txt_descripcion').html('-');
                            $('#txt_existencia').html('-');
                            $('#txt_cant_producto').val('0');
                            $('#txt_precio').html('0.00');
                            $('#txt_precio_total').html('0.00');
                            
                            //bloquear cantidad
                            $('#txt_cant_producto').attr('disable', 'disable');

                            //ocuultar boton agregar
                            $('#add_product_venta').slideUp();


                        }else{
                            console.log('no data');
                        }

                        viewProcesar();
                    },
                    error: function(error){
                    }
                });
        }

    });


     // Anular Venta

    $('#btn_anular_venta').click(function(e){
        e.preventDefault();

   
        var rows = $('#detalle_venta tr').length;
        if(rows > 0){

            var action = 'anularVenta';
    
                $.ajax({
    
                    url: 'ajax.php',
                    type: "POST",
                    async: true,
                    data: {action:action},
    
                    success: function(response){
                    
                       
                       if(response != 'error'){
                              
                             location.reload(); 
                       }        
                    },
                    error: function(error){
                      
                    }
                });
        } 

    });

    // Factuar venta
    $('#btn_facturar_venta').click(function(e){
        e.preventDefault();

   
        var rows = $('#detalle_venta tr').length;
        if(rows > 0){

            var action = 'procesarVenta';
    
                $.ajax({
    
                    url: 'ajax.php',
                    type: "POST",
                    async: true,
                    data: {action:action},
    
                    success: function(response){
    
                       if(response != 'error'){

                            var info = JSON.parse(response);
                            generarPDF(info.nofactura);
                         //  console.log(info);
                              
                             location.reload(); 
                       }else{
                           console.log('no data');
                       }
                          
                    },
                    error: function(error){
                      
                    }
                });
        } 

    });

    // Modal For Anular Factura

    $('.anular_factura').click(function(event){
        event.preventDefault();

        var nofactura = $(this).attr('fac');
        var action = 'infoFactura';

        $.ajax({

            url: 'ajax.php',
            type: 'POST',
            async: true,
            data: {action:action,nofactura:nofactura},

            success: function(response){
                if(response != 'error'){
                    var info = JSON.parse(response);
                    

                    $('.bodyModal').html('<form action="" method= "post" name="form_anular_factura" id="form_anular_factura" onsubmit="'+
                                                'event.preventDefault(); anularFactura(); ">'+
                                                '<div class="card">'+
                                                '<div class="card-body">'+
                                                '<h1>Anular Factura</h1><br>'+
                                                '<h4>¿Realmente desea anular la factura?</h4><br>'+
                                                
                                                '<p><strong>No. '+info.nofactura+'</strong></p>'+
                                                '<p><strong>Monto. COP. '+info.totalfactura+'</strong></p>'+
                                                '<p><strong>Fecha. '+info.fecha+'</strong></p>'+
                                                '<input type="hidden" name="action" value = "anularFactura">'+
                                                '<input type="hidden" name="no_factura" id = "no_factura" value="'+info.nofactura+'" required>'+



                                                '<div class="alert alertAddProduct"></div>'+
                                                '<button type="submit" class="btn_ok" >Anular</button>'+
                                                '<a href="#" class="btn_cancel" onclick="coloseModal()";>Cancelar</a>'+  
                                                '</div>'+
                                                '</div>'+
                                               
                                         '</form>');
                                         
                }
            },

            error: function(error){
                console.log(error);
            }

        });


        $('.modal').fadeIn();

    });


    //Ver factura

    $('.view_factura').click(function(e){
        e.preventDefault();

        var noFactura = $(this).attr('f');
        generarPDF(noFactura);

    });



});   // End del Ready


//Anular Factura

function anularFactura(){
    var noFactura = $('#no_factura').val();
    var action = 'anularFactura';

    $.ajax({
    
        url: 'ajax.php',
        type: "POST",
        async: true,
        data: {action:action, noFactura:noFactura},

        success: function(response)
        {
            if(response == 'error'){
                $('.alertAddProduct').html('<p style="color: red;"> Error al anular la factura </p>');
            }else{
                $('#row_'+noFactura+' .estado').html('<span class="anulada">Anulada</span>');
                $('#form_anular_factura .btn_ok').remove();
                $('#row_'+noFactura+' .div_factura').html('<button type="button" class="btn_anular inactive"> <i class="fa fa-ban" aria-hidden="true"></i></button>');
                $('.alertAddProduct').html('<p>Factura anulada.</p>');
            }
        },
        error: function(error){
        }
    });

}


// Generar PDF

function generarPDF(factura){
    
    var ancho = 1000;
    var alto = 800;

    //Calcular posicion x,y para centrar la ventana
    var x = parseInt((window.screen.width/2) - (ancho/2));
    var y = parseInt((window.screen.height/2) - (alto/2));

    $url = '../factura/generaFactura.php?f='+factura;
    window.open($url, "Factura","left="+x+",top="+y+",height="+alto+",width="+ancho+",scrollbar=si,location=no,resizable=si,menubar=no");
      
}

////////////////

function del_product_detalle(correlativo){
    var action = 'delProductoDetalle';
    var id_detalle = correlativo;

    $.ajax({
    
        url: 'ajax.php',
        type: "POST",
        async: true,
        data: {action:action, id_detalle:id_detalle},

        success: function(response)
        {
            if(response != 'error'){

                var info = JSON.parse(response);      
                $('#detalle_venta').html(info.detalle);
                $('#detalle_totales').html(info.totales);

                $('#txt_cod_producto').val('');
                $('#txt_descripcion').html('-');
                $('#txt_existencia').html('-');
                $('#txt_cant_producto').val('0');
                $('#txt_precio').html('0.00');
                $('#txt_precio_total').html('0.00');
                            
                //bloquear cantidad
                $('#txt_cant_producto').attr('disable', 'disable');

                //ocuultar boton agregar
                $('#add_product_venta').slideUp();        

            }else{
                $('#detalle_venta').html('');
                $('#detalle_totales').html('');
            }
            viewProcesar();
        },
        error: function(error){
        }
    });
}


//Mostrar, ocurtar boton procesar

function viewProcesar(){
    if($('#detalle_venta tr').length > 0){

        $('#btn_facturar_venta').show();
    }else{
        $('#btn_facturar_venta').hide();
        }
}


function serchForDetalle(id){
    var action = 'serchForDetalle';
    var user = id;

    $.ajax({
    
        url: 'ajax.php',
        type: "POST",
        async: true,
        data: {action:action, user:user},

        success: function(response){
            if(response != 'error'){

                var info = JSON.parse(response);
                $('#detalle_venta').html(info.detalle);
                $('#detalle_totales').html(info.totales);

            }else{
                console.log('no data');
            }

            viewProcesar();
        },
        error: function(error){
        }
    });
}


// Agregar Producto

function sendDataProduct(){
    $('.alertAddProduct').html('');


    $.ajax({

        url: 'ajax.php',
        type: 'POST',
        async: true,
        data: $('#form_add_product').serialize(),

        success: function(response){
            if(response == 'error'){
                $('.alertAddProduct').html('<p style="color: red;"> Error al ingresar el Producto.</p>');
            }else{
                var info = JSON.parse(response);

               
                $('.row'+info.cod_product+' .celPrecio').html(info.nuevo_precio);
                $('.row'+info.cod_product+' .celCantidad').html(info.nueva_existencia);
                $('#txtPrecio').val('');
                $('#txtCantidad').val('');
                $('.alertAddProduct').html('<p> Producto guardado correctamente.</p>');

            }
        },


        error: function(error){
            console.log(error);
        }

    });

}

// Eliminar Producto
function delProduct(){

    var pr = $('#producto_id').val();
    $('.alertAddProduct').html('');


    $.ajax({

        url: 'ajax.php',
        type: 'POST',
        async: true,
        data: $('#form_del_product').serialize(),

        success: function(response){

            console.log(response);
            
            if(response == 'error'){
                $('.alertAddProduct').html('<p style="color: red;"> Error al eliminar el producto.</p>');
            }else{

                $('.row'+pr).remove();
                $('#form_del_product .btn_ok').remove();
                $('.alertAddProduct').html('<p> Producto eliminado correctamente.</p>');

            }
        },


        error: function(error){
            console.log(error);
        }

    });

}

function coloseModal(){
    $('.alertAddProduct').html('');
    $('#txtCantidad').val('');
    $('#txtPrecio').val('');
    $('.modal').fadeOut();
}

