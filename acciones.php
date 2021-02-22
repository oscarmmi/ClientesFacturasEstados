<?php

$BUSCAR_CLIENTES=1; 
$GUARDAR_CLIENTES=2;
$BUSCAR_ESTADOS_FACTURAS=3;
$GUARDAR_ESTADOS_FACTURAS=4;
$ELIMINAR_CLIENTE=5;
$ELIMINAR_ESTADO_FACTURA=6;
$BUSCAR_FACTURAS=7;
$CARGAR_SELECTS_FACTURAS=8;
$GUARDAR_FACTURAS=9;
$ELIMINAR_FACTURA=10;
$CONSULTAR_TOTAL_FACTURAS_SELECCIONADAS=11;

$accion=$_POST['accion'];

switch(intval($accion)){
    case $BUSCAR_CLIENTES:
        buscarClientes();
    break;
    case $GUARDAR_CLIENTES:
        guardarClientes();
    break;    
    case $BUSCAR_ESTADOS_FACTURAS:
        buscarEstadosFacturas();
    break;    
    case $GUARDAR_ESTADOS_FACTURAS:
        guardarEstadosFacturas();
    break;    
    case $ELIMINAR_CLIENTE:
        eliminarCliente();
    break;    
    case $ELIMINAR_ESTADO_FACTURA:
        eliminarEstadoFactura();
    break;    
    case $BUSCAR_FACTURAS:
        buscarFacturas();
    break;    
    case $CARGAR_SELECTS_FACTURAS:
        cargarSelectsFacturas();
    break;    
    case $GUARDAR_FACTURAS:
        guardarFacturas();
    break;    
    case $ELIMINAR_FACTURA:
        eliminarFacturas();
    break;    
    case $CONSULTAR_TOTAL_FACTURAS_SELECCIONADAS:
        consultarValorTotalFacturasSeleccionadas();
    break;
}

function consultarValorTotalFacturasSeleccionadas(){
    $facturasseleccionadas=json_decode($_POST['facturasseleccionadas'], true);
    if(!count($facturasseleccionadas)){
        retornarArraydeDatos(array(
            'nofacturas'=>1 
        ));
        return;
    }
    $sqlFacturas="
        SELECT 
        a.* 
        FROM factura a 
        WHERE 
        a.id_factura IN(".implode(",", $facturasseleccionadas).")
    ";
    $resultadoFacturas=consultar($sqlFacturas);
    if(!count($resultadoFacturas)){
        retornarArraydeDatos(array(
            'errores'=>1 
        ));
        return;
    }
    $total=0;
    foreach($resultadoFacturas as $factura){
        $total+=intval($factura['valor_fac']);
    }
    retornarArraydeDatos(array(
        'datos'=>$total
    ));
}

function cargarSelectsFacturas(){
    $sqlClientes="
        SELECT 
        a.nume_doc as id, 
        a.nombre as descripcion
        FROM clientes a         
    ";
    $resultadoClientes=consultar($sqlClientes);
    $sqlEstados="
        SELECT 
        a.codi_estado as id, 
        a.descripcion 
        FROM estados_factura a         
    ";
    $resultadoEstados=consultar($sqlEstados);
    retornarArraydeDatos(array(
        'clientes'=>$resultadoClientes, 
        'estados'=>$resultadoEstados 
    ));
}

function eliminarEstadoFactura(){
    $codi_estado=$_POST['codi_estado_aeliminar'];
    $sqlFacturas="
        SELECT 
        a.* 
        FROM factura a 
        WHERE 
        a.codi_estado=$codi_estado 
    ";
    $resultado=consultar($sqlFacturas);
    if(count($resultado)){
        retornarArraydeDatos(array(
            'facturasrelacionadas'=>1 
        ));
        return;
    }
    $eliminarEstadosFactura="DELETE FROM estados_factura WHERE codi_estado=$codi_estado; ";
    ejecutarSentencia($eliminarEstadosFactura);
    buscarEstadosFacturas();
}

function eliminarFacturas(){
    $id_factura=$_POST['id_facturaaeliminar'];
    $eliminarFactura="DELETE FROM factura WHERE id_factura=$id_factura; ";
    ejecutarSentencia($eliminarFactura);
    buscarFacturas();
}

function eliminarCliente(){
    $documento=$_POST['documentoaeliminar'];
    $sqlFacturas="
        SELECT 
        a.* 
        FROM factura a 
        WHERE 
        a.nume_doc=$documento 
    ";
    $resultado=consultar($sqlFacturas);
    if(count($resultado)){
        retornarArraydeDatos(array(
            'facturasrelacionadas'=>1 
        ));
        return;
    }
    $eliminarCliente="DELETE FROM clientes WHERE nume_doc=$documento; ";
    ejecutarSentencia($eliminarCliente);
    buscarClientes();
}

function buscarEstadosFacturas(){
    $filtroDocumento="";
    if(isset($_POST['codi_estado'])){
        $filtroDocumento=" AND a.codi_estado=".$_POST['codi_estado'];
    }
    $sqlClientes="
        SELECT 
        a.*
        FROM estados_factura a   
        WHERE 
        1 
        $filtroDocumento       
    ";
    $resultado=consultar($sqlClientes);
    retornarArraydeDatos(array(
        'datos'=>$resultado
    ));
}

function buscarFacturas(){
    $filtroIdfactura="";
    if(isset($_POST['id_factura'])){
        $filtroIdfactura=" AND a.id_factura=".$_POST['id_factura'];
    }
    $sqlFacturas="
        SELECT 
        a.id_factura, 
        a.factura, 
        b.nume_doc, 
        b.nombre as cliente, 
        c.codi_estado, 
        c.descripcion as estado, 
        a.valor_fac,         
        CAST(a.fecha_fac AS DATE) AS fecha_fac 
        FROM factura a 
        JOIN clientes b ON(b.nume_doc=a.nume_doc) 
        JOIN estados_factura c ON(c.codi_estado=a.codi_estado) 
        WHERE 
        1 
        $filtroIdfactura        
    ";
    $resultado=consultar($sqlFacturas);
    retornarArraydeDatos(array(
        'datos'=>$resultado
    ));
}

function buscarClientes(){
    $filtroDocumento="";
    if(isset($_POST['documento'])){
        $filtroDocumento=" AND a.nume_doc=".$_POST['documento'];
    }
    $sqlClientes="
        SELECT 
        a.*
        FROM clientes a   
        WHERE 
        1 
        $filtroDocumento       
    ";
    $resultado=consultar($sqlClientes);
    retornarArraydeDatos(array(
        'datos'=>$resultado
    ));
}

function validarEstadoFactura($datos){
    $respuesta=array();
    $respuesta['datos']=array();
    $respuesta['errores']=array();
    $aCliente=array();    
    if(!isset($datos['descripcion']) || $datos['descripcion']==''){
        $respuesta['errores'][]="- Debe ingresar una descripcion del estado";
    }else{
        $filtroCodigoAnterior="";  
        if(isset($datos['codi_estado'])){
            $filtroCodigoAnterior=" AND a.codi_estado!='$datos[codi_estado]' ";
        }
        $sqlDocumento="
            SELECT 
            a.* 
            FROM estados_factura a 
            WHERE 
            a.descripcion='$datos[descripcion]'  
            $filtroCodigoAnterior 
            LIMIT 1 
        ";
        $resultado=consultar($sqlDocumento);
        if(count($resultado)){
            $respuesta['errores'][]="- La descripcion del estado debe ser unica, la ingresada ya existe en la base de datos";
        }else{
            $respuesta['datos']['descripcion']=$datos['descripcion'];
        }   
    }        
    return $respuesta;
}

function validarDatosFactura($datos){
    $respuesta=array();
    $respuesta['datos']=array();
    $respuesta['errores']=array();
    $aCliente=array();    
    if(!isset($datos['factura']) || $datos['factura']==''){
        $respuesta['errores'][]="- Debe ingresar un número de factura";
    }else{
        $respuesta['datos']['factura']=$datos['factura'];
    }
    if(!isset($datos['nume_doc']) || $datos['nume_doc']==''){
        $respuesta['errores'][]="- Debe ingresar el Cliente";
    }else{
        $sqlCliente="
            SELECT 
            a.*
            FROM clientes a 
            WHERE 
            a.nume_doc=$datos[nume_doc]
        ";
        $resultado=consultar($sqlCliente);
        if(!count($resultado)){
            $respuesta['errores'][]="- El Cliente ingresado no es valido";
        }else{
            $respuesta['datos']['nume_doc']=$datos['nume_doc'];
        }        
    }
    if(!isset($datos['codi_estado']) || $datos['codi_estado']==''){
        $respuesta['errores'][]="- Debe ingresar un Estado";
    }else{
        $sqlEstado="
            SELECT 
            a.*
            FROM estados_factura a 
            WHERE 
            a.codi_estado=$datos[codi_estado]
        ";
        $resultado=consultar($sqlEstado);
        if(!count($resultado)){
            $respuesta['errores'][]="- El estado ingresado no es valido";
        }else{
            $respuesta['datos']['codi_estado']=$datos['codi_estado'];
        }        
    }
    if(!isset($datos['valor_fac']) || $datos['valor_fac']==''){
        $respuesta['errores'][]="- Debe ingresar un valor para la factura";
    }else if(!is_numeric($datos['valor_fac'])){        
        $respuesta['errores'][]="- El valor de la factura no es numerico";        
    }else if($datos['valor_fac']<=0){        
        $respuesta['errores'][]="- El valor de la factura no es valido";        
    }else{
        $respuesta['datos']['valor_fac']=$datos['valor_fac'];
    }
    if(!isset($datos['fecha_fac']) || $datos['fecha_fac']==''){
        $respuesta['errores'][]="- Debe ingresar una fecha para la factura";
    }else{
        $respuesta['datos']['fecha_fac']=$datos['fecha_fac'];
    }
    return $respuesta;
}

function validarDatosCliente($datos){
    $respuesta=array();
    $respuesta['datos']=array();
    $respuesta['errores']=array();
    $aCliente=array();    
    if(!isset($datos['nombre']) || $datos['nombre']==''){
        $respuesta['errores'][]="- Debe ingresar el nombre del Cliente";
    }else{
        $respuesta['datos']['nombre']=$datos['nombre'];
    }
    if(!isset($datos['direccion']) || $datos['direccion']==''){
        $respuesta['errores'][]="- Debe ingresar la dirección del Cliente";
    }else{
        $respuesta['datos']['direccion']=$datos['direccion'];
    }
    if(!isset($datos['documento']) || $datos['documento']==''){
        $respuesta['errores'][]="- Debe ingresar el numero de documento del Cliente";
    }else{      
        $filtroDocumentoAnterior="";  
        if(isset($datos['documentoanterior'])){
            $filtroDocumentoAnterior=" AND a.nume_doc!='$datos[documentoanterior]' ";
        }
        $sqlDocumento="
            SELECT 
            a.* 
            FROM clientes a 
            WHERE 
            a.nume_doc='$datos[documento]' 
            $filtroDocumentoAnterior 
            LIMIT 1 
        ";
        $resultado=consultar($sqlDocumento);
        if(count($resultado)){
            $respuesta['errores'][]="- El numero de documento del Cliente debe ser unico, el ingresado ya existe en la base de datos";
        }else{
            $respuesta['datos']['documento']=$datos['documento'];
        }        
    }    
    return $respuesta;
}

function guardarEstadosFacturas(){
    $datos=json_decode($_POST['datos'], true);    
    $aRespuesta=validarEstadoFactura($datos);    
    if(count($aRespuesta['errores'])){
        retornarArraydeDatos(array(
            'errores'=>$aRespuesta['errores']
        ));
        return;
    }
    $aDatos=$aRespuesta['datos'];
    $guardarEstadoFactura="INSERT INTO estados_factura(descripcion) VALUES('$aDatos[descripcion]');";
    if(isset($datos['codi_estado'])){
        $guardarEstadoFactura=" UPDATE estados_factura SET descripcion='$aDatos[descripcion]' WHERE codi_estado=$datos[codi_estado];";
    }
    ejecutarSentencia($guardarEstadoFactura);
    buscarEstadosFacturas();
}

function validarFacturaNoRepetidaxCliente($aDatos, $datos){
    $aErrores=array();
    $filtroRegistro="";
    if(isset($datos['id_factura'])){
        $filtroRegistro=" AND a.id_factura!=$datos[id_factura] ";
    }
    $sqlRepetida="
        SELECT 
        a.* 
        FROM factura a 
        WHERE 
        a.factura='$aDatos[factura]'  
        AND a.nume_doc=$aDatos[nume_doc] 
        $filtroRegistro 
    ";    
    $resultado=consultar($sqlRepetida);
    if(count($resultado)){
        $aErrores[]="- El numero de factura ingresado ya se encuentra registrada para el mismo cliente ";
    }
    return $aErrores;
}

function guardarFacturas(){
    $datos=json_decode($_POST['datos'], true);    
    $aRespuesta=validarDatosFactura($datos);    
    if(count($aRespuesta['errores'])){
        retornarArraydeDatos(array(
            'errores'=>$aRespuesta['errores']
        ));
        return;
    }    
    $aDatos=$aRespuesta['datos'];
    $aErrores=validarFacturaNoRepetidaxCliente($aDatos, $datos);
    if(count($aErrores)){
        retornarArraydeDatos(array(
            'errores'=>$aRespuesta['errores']
        ));
        return;
    }
    $guardarFactura="INSERT INTO factura(factura, nume_doc, codi_estado, valor_fac, fecha_fac) VALUES('$aDatos[factura]', $aDatos[nume_doc], $aDatos[codi_estado], $aDatos[valor_fac], '$aDatos[fecha_fac]');";
    if(isset($datos['id_factura'])){
        $guardarFactura=" UPDATE factura SET factura='$aDatos[factura]', nume_doc=$aDatos[nume_doc], codi_estado=$aDatos[codi_estado], valor_fac=$aDatos[valor_fac], fecha_fac='$aDatos[fecha_fac]' WHERE id_factura=$datos[id_factura];";
    }
    ejecutarSentencia($guardarFactura);
    buscarFacturas();
}

function guardarClientes(){
    $datos=json_decode($_POST['datos'], true);    
    $aRespuesta=validarDatosCliente($datos);    
    if(count($aRespuesta['errores'])){
        retornarArraydeDatos(array(
            'errores'=>$aRespuesta['errores']
        ));
        return;
    }
    $aDatos=$aRespuesta['datos'];
    $guardarCliente="INSERT INTO clientes(nume_doc, nombre, direccion) VALUES($aDatos[documento], '$aDatos[nombre]', '$aDatos[direccion]');";
    if(isset($datos['documentoanterior'])){
        $guardarCliente=" UPDATE clientes SET nume_doc=$aDatos[documento], nombre='$aDatos[nombre]', direccion='$aDatos[direccion]' WHERE nume_doc=$datos[documentoanterior];";
    }
    ejecutarSentencia($guardarCliente);
    buscarClientes();
}

function ejecutarSentencia($sql){
    $conexion=conexion();
    mysqli_query( $conexion, $sql ) or die ( "Algo ha ido mal en la consulta a la base de datos");    
}

function retornarArraydeDatos($aDatos){
    echo json_encode($aDatos, true);
}

function consultar($sql){
    $conexion=conexion();
    $resultado = mysqli_query( $conexion, $sql ) or die ( "Algo ha ido mal en la consulta a la base de datos");
    $datosF=array();
    $campos=array();
    $camposT=array();
    while ($registros = mysqli_fetch_array( $resultado )){
        if(!count($campos)){
            $camposT=array_keys($registros);    
            foreach($camposT as $campo){
                if(!is_numeric($campo)){
                    $campos[]=$campo;
                }                
            }
        }
        $datosR=array();
        foreach($campos as $campo){
            $datosR[$campo]=$registros[$campo];
        }
        $datosF[]=$datosR;        
    }
    return $datosF;
}

function conexion(){
    $usuario = "root";
    $contrasena = "";  // en mi caso tengo contraseña pero en casa caso introducidla aquí.
    $servidor = "localhost";
    $basededatos = "clientesfacturasestados";
    $conexion = mysqli_connect( $servidor, $usuario, $contrasena ) or die ("No se ha podido conectar al servidor de Base de datos");
    $db = mysqli_select_db( $conexion, $basededatos ) or die ( "Upps! Pues va a ser que no se ha podido conectar a la base de datos" );
    return $conexion;
}

?>