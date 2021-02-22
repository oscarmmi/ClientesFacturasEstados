
let rutaPpal='acciones.php';
let indicadorTablaClientes=0;
let indicadorTablaEstadosFacturas=0;
let aFacturasSeleccionadas=[];

let BUSCAR_CLIENTES=1;
let GUARDAR_CLIENTES=2;
let BUSCAR_ESTADOS_FACTURAS=3;
let GUARDAR_ESTADOS_FACTURAS=4;
let ELIMINAR_CLIENTE=5;
let ELIMINAR_ESTADO_FACTURA=6;
let BUSCAR_FACTURAS=7;
let CARGAR_SELECTS_FACTURAS=8;
let GUARDAR_FACTURAS=9;
let ELIMINAR_FACTURA=10;
let CONSULTAR_TOTAL_FACTURAS_SELECCIONADAS=11;

$(document).ready(function () {
    eventosClick();
    $("#liclientes").click();    
});

function eventosClick(){
    $("#liclientes").click(function(){
        setTimeout(
            function(){
                buscarClientes();
            }, 300
        );
    });
    $("#liestadosfacturas").click(function(){
        setTimeout(
            function(){
                buscarEstadosFacturas();
            }, 300
        );        
    });
    $("#lifacturas").click(function(){
        aFacturasSeleccionadas=[];
        setTimeout(
            function(){
                buscarFacturas();
            }, 300
        );        
    });
    $("#btnMostrarModalCliente").click(abrirModalClientes);  
    $("#btnMostrarModalEstadosClientes").click(abrirModalEstadosFacturas);    
    $("#btnMostrarModalFacturas").click(abrirModalFacturas);
    $("#btnGuardarCliente").click(validarDatosCliente);
    $("#btnGuardarEstadoFactura").click(validarDatosEstadosFactura);
    $("#btnGuardarFactura").click(validarDatosFactura);
    $("#btnConsultarValorTotales").click(consultarValorTotalFacturasSeleccionadas);
}

function buscarEstadosFacturas(codi_estado=undefined){
    let datos={
        accion: BUSCAR_ESTADOS_FACTURAS
    };
    if(codi_estado){
        datos.codi_estado=codi_estado;
    }
    $.ajax({
        url: rutaPpal,
        data: datos, 
        type: 'POST',
        dataType: 'json',
        success: function (respuesta) {
            if(codi_estado){
                cargarDatosEstadosFacturas(respuesta.datos[0]);
                return;
            }
            cargarTablaEstadosFacturas(respuesta.datos);
        }
    });  
}

function abrirModalEstadosFacturas(codi_estado){
    $(".estadosfacturas").val('');
    $("#modalEstadosFacturas").modal('show');
    if(codi_estado){
        buscarEstadosFacturas(codi_estado);
    }
}

function abrirModalFacturas(id_factura){    
    $(".facturas").val('');
    $("#modalFacturas").modal('show');
    cargarSelectsFacturas();
    if(id_factura){
        setTimeout(
            function(){
                buscarFacturas(id_factura);
            },400
        );        
    }
}

function cargarSelectsFacturas(){
    $.ajax({
        url: rutaPpal,
        data: {
            accion: CARGAR_SELECTS_FACTURAS
        },
        type: 'POST',
        dataType: 'json',
        success: function (respuesta) {
            if(parseInt(respuesta.clientes.length)){
                cargarOpcionesenUnSelect("#facturas_nume_doc", respuesta.clientes);
            }
            if(parseInt(respuesta.estados.length)){
                cargarOpcionesenUnSelect("#facturas_codi_estado", respuesta.estados);
            }
        }
    });   
}

function cargarOpcionesenUnSelect(identificador, datos) {
    let html = '';
    for (let a in datos) {
        html += '<option value="' + datos[a]['id'] + '" class="text-capitalize">' + datos[a]['descripcion'] + '</option>';
    }
    $(identificador).html(html);
}

function abrirModalClientes(documento=undefined){
    $(".datoscliente").val('');
    $("#modalClientes").modal('show');
    if(documento){
        buscarClientes(documento);
    }
}

function validarDatosEstadosFactura(){
    let datos={};    
    if($("#estadosfacturas_codi_estado").val()!='' && !isNaN($("#estadosfacturas_codi_estado").val())){
        datos.codi_estado=$("#estadosfacturas_codi_estado").val();
    }       
    if($("#estadosfacturas_descripcion").val()==''){
        alertify.error("- Debe ingresar la descripcion del estado");
        return;
    }
    datos.descripcion=$("#estadosfacturas_descripcion").val();
    guardarEstadoFactura(datos);
}

function validarDatosFactura(){
    let datos={};    
    if($("#facturas_id_factura").val()!='' && !isNaN($("#facturas_id_factura").val())){
        datos.id_factura=$("#facturas_id_factura").val();
    }    
    if($("#facturas_factura").val()==''){
        alertify.error("- Debe ingresar un número de factura");
        return;
    }
    datos.factura=$("#facturas_factura").val();        
    if($("#facturas_nume_doc").val()==''){
        alertify.error("- Debe ingresar un Cliente");
        return;
    }
    datos.nume_doc=$("#facturas_nume_doc").val(); 
    if($("#facturas_codi_estado").val()==''){
        alertify.error("- Debe ingresar un estado para la factura");
        return;
    }
    datos.codi_estado=$("#facturas_codi_estado").val(); 
    if($("#facturas_valor_fac").val()==''){
        alertify.error("- Debe ingresar un valor para la factura");
        return;
    }
    if(isNaN($("#facturas_valor_fac").val())){
        alertify.error("- El valor de la factura no es valido");
        return;
    }
    datos.valor_fac=$("#facturas_valor_fac").val();
    if($("#facturas_fecha_fac").val()==''){
        alertify.error("- Debe ingresar una fecha para la factura");
        return;
    }
    datos.fecha_fac=$("#facturas_fecha_fac").val(); 
    guardarFactura(datos);
}

function guardarFactura(datos){
    $.ajax({
        url: rutaPpal,
        data: {
            accion: GUARDAR_FACTURAS, 
            datos:JSON.stringify(datos)
        },
        type: 'POST',
        dataType: 'json',
        success: function (respuesta) {
            if(respuesta.errores){
                for (let a in respuesta.errores) {
                    alertify.error(respuesta.errores[a]);    
                }
                return;
            }
            alertify.success("- La factura fue agregada exitosamente");
            if($(".checkgeneral").prop('checked')){
                $(".checkgeneral").prop('checked', false);
            }            
            $("#modalFacturas").modal('hide');
            cargarTablaFacturas(respuesta.datos);
        }
    });  
}

function validarDatosCliente(){
    let datos={};    
    if($("#clientes_documentoanterior").val()!='' && !isNaN($("#clientes_documentoanterior").val())){
        datos.documentoanterior=$("#clientes_documentoanterior").val();
    }    
    if($("#clientes_nombres").val()==''){
        alertify.error("- Debe ingresar el nombre del Cliente");
        return;
    }
    datos.nombre=$("#clientes_nombres").val();        
    if($("#clientes_direccion").val()==''){
        alertify.error("- Debe ingresar la dirección del Cliente");
        return;
    }
    datos.direccion=$("#clientes_direccion").val(); 
    if($("#clientes_numerodocumento").val()==''){
        alertify.error("- Debe ingresar el numero de documento del Cliente");
        return;
    }
    if(isNaN($("#clientes_numerodocumento").val())){
        alertify.error("- El numero de documento debe ser un valor numerico");
        return;
    }
    datos.documento=$("#clientes_numerodocumento").val();
    guardarCliente(datos);
}

function guardarEstadoFactura(datos){
    $.ajax({
        url: rutaPpal,
        data: {
            accion: GUARDAR_ESTADOS_FACTURAS, 
            datos:JSON.stringify(datos)
        },
        type: 'POST',
        dataType: 'json',
        success: function (respuesta) {
            if(respuesta.errores){
                for (let a in respuesta.errores) {
                    alertify.error(respuesta.errores[a]);    
                }
                return;
            }
            alertify.success("- El estado de factura fue agregado exitosamente");
            $("#modalEstadosFacturas").modal('hide');
            cargarTablaEstadosFacturas(respuesta.datos);
        }
    });   
}

function guardarCliente(datos){
    $.ajax({
        url: rutaPpal,
        data: {
            accion: GUARDAR_CLIENTES, 
            datos:JSON.stringify(datos)
        },
        type: 'POST',
        dataType: 'json',
        success: function (respuesta) {
            if(respuesta.errores){
                for (let a in respuesta.errores) {
                    alertify.error(respuesta.errores[a]);    
                }
                return;
            }
            alertify.success("- El cliente fue agregado exitosamente");
            $("#modalClientes").modal('hide');
            cargarTablaClientes(respuesta.datos);
        }
    });   
}

function buscarFacturas(id_factura=undefined){    
    let datos={
        accion: BUSCAR_FACTURAS
    };
    if(id_factura){
        datos.id_factura=id_factura;
    }
    $.ajax({
        url: rutaPpal,
        data: datos, 
        type: 'POST',
        dataType: 'json',
        success: function (respuesta) {
            if(id_factura){
                cargarDatosFacturas(respuesta.datos[0]);
                return;
            }
            cargarTablaFacturas(respuesta.datos);
        }
    });    
}

function cargarDatosFacturas(datos){
    $("#facturas_id_factura").val(datos.id_factura);
    $("#facturas_factura").val(datos.factura);
    $("#facturas_nume_doc").val(datos.nume_doc);
    $("#facturas_codi_estado").val(datos.codi_estado);
    $("#facturas_valor_fac").val(datos.valor_fac);
    $("#facturas_fecha_fac").val(datos.fecha_fac);
}

function buscarClientes(documento=undefined){
    let datos={
        accion: BUSCAR_CLIENTES
    };
    if(documento){
        datos.documento=documento;
    }
    $.ajax({
        url: rutaPpal,
        data: datos, 
        type: 'POST',
        dataType: 'json',
        success: function (respuesta) {
            if(documento){
                cargarDatosCliente(respuesta.datos[0]);
                return;
            }
            cargarTablaClientes(respuesta.datos);
        }
    });    
}

function cargarDatosEstadosFacturas(datos){
    $("#estadosfacturas_codi_estado").val(datos.codi_estado);
    $("#estadosfacturas_descripcion").val(datos.descripcion);
}

function cargarDatosCliente(datos){
    $("#clientes_documentoanterior").val(datos.nume_doc);
    $("#clientes_nombres").val(datos.nombre);
    $("#clientes_numerodocumento").val(datos.nume_doc);
    $("#clientes_direccion").val(datos.direccion);
}

function consultarValorTotalFacturasSeleccionadas(){
    if(!parseInt(aFacturasSeleccionadas.length)){
        alertify.error("- No se ha seleccionado ninguna factura");
        return;
    }
    $.ajax({
        url: rutaPpal,
        data: {
            accion: CONSULTAR_TOTAL_FACTURAS_SELECCIONADAS,
            facturasseleccionadas:JSON.stringify(aFacturasSeleccionadas)
        }, 
        type: 'POST',
        dataType: 'json',
        success: function (respuesta) {
            if(respuesta.nofactura){
                alertify.error("- Debe seleccionar alguna factura");
                return;
            }
            if(respuesta.errores){
                alertify.error("- Las facturas seleccionadas no son validas");
                return;
            }
            alertify.confirm(
                'El total de las facturas seleccionadas es '+respuesta.datos,
                function () {
                    
                }
            );
        }
    }); 
}

function seleccionarTodasLasFacturas(event){
    let facturas=$("#tabla_facturas").dataTable().fnGetData();    
    if($(event.target).prop('checked')){
        aFacturasSeleccionadas=[];
        for (let a in facturas) {        
            $("#check_"+facturas[a]['id_factura']).prop('checked', true);
            aFacturasSeleccionadas.push(parseInt(facturas[a]['id_factura']));
        }
        return;
    }
    for (let a in facturas) {        
        $("#check_"+facturas[a]['id_factura']).prop('checked', false);
    }
    aFacturasSeleccionadas=[];
}

function seleccionarFactura(id_factura){
    let indice=aFacturasSeleccionadas.indexOf(parseInt(id_factura));
    if(!$("#check_"+id_factura).prop('checked')){        
        if(indice>=0){
            aFacturasSeleccionadas.splice(indice, 1);
        }
        $(".checkgeneral").prop('checked', false);
    }else{
        let facturas=$("#tabla_facturas").dataTable().fnGetData();  
        if(indice==-1){
            aFacturasSeleccionadas.push(parseInt(id_factura));
        }
        if(parseInt(aFacturasSeleccionadas.length)===parseInt(facturas.length)){
            $(".checkgeneral").prop('checked', true);
        }        
    }    
}

function cargarTablaFacturas(datos){
    if (!parseInt(datos.length)) {
        datos = [];
    }
    let tabla = $('#tabla_facturas').DataTable({
        paging: 'numbers',
        bFilter: false,
        destroy: true,
        select: true,
        dom: 'T<"clear">lfrtip', // Permite cargar la herramienta tableTools
        tableTools: {
            aButtons: [],
            sRowSelect: "single"
        },
        data: datos,
        columns: [
            {
                data: 'id_factura',
                title: '<input type="checkbox" class="checkgeneral" title="Seleccionar Todas las Facturas" onclick="seleccionarTodasLasFacturas(event);">',
                className: 'text-capitalize dt-body-center',
                render: function (data, type, full, meta) {
                    let chequeado='';
                    if(aFacturasSeleccionadas.indexOf(parseInt(data))>=0){
                        chequeado=' checked="true" ';
                    }
                    return '<input type="checkbox" id="check_'+data+'" title="Seleccionar este registro" '+chequeado+' onclick="seleccionarFactura('+data+');">';
                }
            },
            {
                data: 'factura',
                title: 'Factura',
                className: 'text-capitalize'
            },
            {
                data: 'cliente',
                title: 'Cliente',
                className: 'text-capitalize'
            },
            {
                data: 'estado',
                title: 'Estado',
                className: 'text-capitalize dt-body-center'
            },
            {
                data: 'valor_fac',
                title: 'Valor',
                className: 'text-capitalize dt-body-center'
            },
            {
                data: 'fecha_fac',
                title: 'Fecha',
                className: 'text-capitalize dt-body-center'
            },
            {
                data: 'id_factura',
                title: 'Editar',
                className: 'text-capitalize dt-body-center',
                render: function (data, type, full, meta) {
                    return '<a style="cursor:pointer;" onclick="abrirModalFacturas('+data+');" title="Editar este Registro"><i class="fa fa-pencil"></i></a>';
                }
            },
            {
                data: 'id_factura',
                title: 'Eliminar',
                className: 'text-capitalize dt-body-center',
                render: function (data, type, full, meta) {
                    return '<a style="cursor:pointer;" onclick="confirmarEliminarFacturas('+data+');" title="Eliminar este Registro"><i class="fa fa-times  text-danger"></i></a>';
                }
            }            
        ]
    });
}

function cargarTablaClientes(datos){
    if (!parseInt(datos.length)) {
        datos = [];
    }
    let tabla = $('#tabla_clientes').DataTable({
        paging: 'numbers',
        bFilter: false,
        destroy: true,
        select: true,
        dom: 'T<"clear">lfrtip', // Permite cargar la herramienta tableTools
        tableTools: {
            aButtons: [],
            sRowSelect: "single"
        },
        data: datos,
        columns: [{
                data: 'nombre',
                title: 'Nombre del Cliente',
                className: 'text-capitalize'
            },
            {
                data: 'direccion',
                title: 'Dirección',
                className: 'text-capitalize'
            },
            {
                data: 'nume_doc',
                title: 'Número de Documento',
                className: 'text-capitalize dt-body-center'
            },
            {
                data: 'nume_doc',
                title: 'Editar',
                className: 'text-capitalize dt-body-center',
                render: function (data, type, full, meta) {
                    return '<a style="cursor:pointer;" onclick="abrirModalClientes('+data+');" title="Editar este Registro"><i class="fa fa-pencil"></i></a>';
                }
            },
            {
                data: 'nume_doc',
                title: 'Eliminar',
                className: 'text-capitalize dt-body-center',
                render: function (data, type, full, meta) {
                    return '<a style="cursor:pointer;" onclick="confirmarEliminarCliente('+data+');" title="Eliminar este Registro"><i class="fa fa-times  text-danger"></i></a>';
                }
            }
        ]
    });
    indicadorTablaClientes++;
}

function confirmarEliminarFacturas(id_factura){
    alertify.confirm(
        'Esta seguro de eliminar este registro? ',
        function () {
            eliminarFactura(id_factura);
        }
    );
}

function confirmarEliminarCliente(documento){
    alertify.confirm(
        'Esta seguro de eliminar este registro? ',
        function () {
            eliminarCliente(documento);
        }
    );
}

function confirmarEliminarEstadoFactura(codi_estado){
    alertify.confirm(
        'Esta seguro de eliminar este registro? ',
        function () {
            eliminarEstadoFactura(codi_estado);
        }
    );    
}

function eliminarEstadoFactura(codi_estado){
    $.ajax({
        url: rutaPpal,
        data: {
            accion: ELIMINAR_ESTADO_FACTURA,
            codi_estado_aeliminar:codi_estado
        }, 
        type: 'POST',
        dataType: 'json',
        success: function (respuesta) {
            if(respuesta.facturasrelacionadas){
                alertify.error("- Hay facturas que estan relacionadas con este estado, por lo tanto no es posible eliminarlo");
                return;
            }
            alertify.error("- El registro fue eliminado exitosamente");
            cargarTablaEstadosFacturas(respuesta.datos);
        }
    }); 
}

function eliminarFactura(id_factura){
    $.ajax({
        url: rutaPpal,
        data: {
            accion: ELIMINAR_FACTURA,
            id_facturaaeliminar:id_factura
        }, 
        type: 'POST',
        dataType: 'json',
        success: function (respuesta) {
            alertify.error("- El registro fue eliminado exitosamente");
            cargarTablaFacturas(respuesta.datos);
        }
    });   
}

function eliminarCliente(documento){
    $.ajax({
        url: rutaPpal,
        data: {
            accion: ELIMINAR_CLIENTE,
            documentoaeliminar:documento
        }, 
        type: 'POST',
        dataType: 'json',
        success: function (respuesta) {
            if(respuesta.facturasrelacionadas){
                alertify.error("- Hay facturas que estan relacionadas con este cliente, por lo tanto no es posible eliminarlo");
                return;
            }
            alertify.error("- El registro fue eliminado exitosamente");
            cargarTablaClientes(respuesta.datos);
        }
    });   
}

function cargarTablaEstadosFacturas(datos){
    if (!parseInt(datos.length)) {
        datos = [];
    }
    let tabla = $('#tabla_estadosfacturas').DataTable({
        paging: 'numbers',
        bFilter: false,
        destroy: true,
        select: true,
        dom: 'T<"clear">lfrtip', // Permite cargar la herramienta tableTools
        tableTools: {
            aButtons: [],
            sRowSelect: "single"
        },
        data: datos,
        columns: [{
                data: 'descripcion',
                title: 'Descripción del Estado',
                className: 'text-capitalize'
            },
            {
                data: 'codi_estado',
                title: 'Código del Estado',
                className: 'text-capitalize dt-body-center'
            },
            {
                data: 'codi_estado',
                title: 'Editar',
                className: 'text-capitalize dt-body-center',
                render: function (data, type, full, meta) {
                    return '<a style="cursor:pointer;" onclick="abrirModalEstadosFacturas('+data+');" title="Editar este Registro"><i class="fa fa-pencil"></i></a>';
                }
            },
            {
                data: 'codi_estado',
                title: 'Eliminar',
                className: 'text-capitalize dt-body-center',
                render: function (data, type, full, meta) {
                    return '<a style="cursor:pointer;" onclick="confirmarEliminarEstadoFactura('+data+');" title="Eliminar este Registro"><i class="fa fa-times text-danger"></i></a>';
                }
            }
        ]
    });
    indicadorTablaEstadosFacturas++;
}