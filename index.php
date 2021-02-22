<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturas SYC</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <!-- CSS -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <!-- Default theme -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
    <!-- Semantic UI theme -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/semantic.min.css" />
    <!-- Bootstrap theme -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js" integrity="sha256-TQq84xX6vkwR0Qs1qH5ADkP+MvH0W+9E7TdHJsoIQiM=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.js" integrity="sha256-nZaxPHA2uAaquixjSDX19TmIlbRNCOrf5HO1oHl5p70=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.css" integrity="sha256-IvM9nJf/b5l2RoebiFno92E5ONttVyaEEsdemDC6iQA=" crossorigin="anonymous" />


    <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css" />

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <script src="funciones.js"></script>
</head>
<body>
    <div class="container">
        <div class="row top-buffer">
            <div class="col-lg-4">
                <h4 class="text-success">Facturas</h4>
            </div>
        </div>
        <div class="row top-buffer" id="pestannas">
            <ul class="nav nav-pills">
                <li>
                    <a data-toggle="pill" class="lipestannas" id="liclientes" href="#clientes" title="Haga click para recargar los datos de esta pestaña">Clientes</a>
                </li>
                <li>
                    <a data-toggle="pill" class="lipestannas" id="lifacturas" href="#facturas" title="Haga click para recargar los datos de esta pestaña">Facturas</a>
                </li>
                <li>
                    <a data-toggle="pill" class="lipestannas" id="liestadosfacturas" href="#estadosfacturas" title="Haga click para recargar los datos de esta pestaña">Estados Facturas</a>
                </li>
            </ul>            
            <br/>
            <div class="tab-content">
                <div id="clientes" class="tabcategorias tab-pane fade" >                
                    <div class="row top-buffer">
                        <div class="col-lg-4">
                            <button id="btnMostrarModalCliente" class="btn btn-success mt-3">Agregar Cliente</button>
                        </div>
                    </div>
                    <br/>
                    <div class="row top-buffer">
                        <div class="col-lg-12">
                            <table id="tabla_clientes" class="table table-bordered table-striped display"></table>
                        </div>
                    </div>
                </div>
                <div id="facturas" class="tabcategorias tab-pane fade" >
                    <div class="row top-buffer">
                        <div class="col-lg-2">
                            <button id="btnMostrarModalFacturas" class="btn btn-success mt-3">Agregar Factura</button>
                        </div>
                        <div class="col-lg-4">
                            <button id="btnConsultarValorTotales" class="btn btn-success mt-3">Total Facturas Seleccionadas</button>
                        </div>
                    </div>            
                    <br/>
                    <div class="row top-buffer">
                        <div class="col-lg-12">
                        <table id="tabla_facturas" class="table table-bordered table-striped display"></table>
                        </div>
                    </div>
                </div>
                <div id="estadosfacturas" class="tabcategorias tab-pane fade" >
                    <div class="row top-buffer">
                        <div class="col-lg-4">
                            <button id="btnMostrarModalEstadosClientes" class="btn btn-success mt-3">Agregar Estado Factura</button>
                        </div>
                    </div>            
                    <br/>
                    <div class="row top-buffer">
                        <div class="col-lg-12">
                            <table id="tabla_estadosfacturas" class="table table-bordered table-striped display"></table>
                        </div>
                    </div>
                </div>
            </div>  
        </div>              
    </div>
    <div class="modal" id="modalFacturas">
        <div class="modal-dialog modal-medium">
            <div class="modal-content" style="overflow: visible;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Agregar/Editar Factura</h4>
                </div>
                <div class="modal-body" style="font-size: 9pt;">
                    <input type="hidden" id="facturas_id_factura" class="facturas">
                    <div class="row container-fluid">
                        <div class="row top-buffer">
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3" style="cursor:help;" title="Número de la Factura">Factura</span>
                                    <input type="text" class="form-control facturas" id="facturas_factura" aria-describedby="basic-addon3">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon input-fix-sm">
                                        <div class="input-group-fix-width" style="cursor:help;" title="Nombre del Cliente">
                                            Cliente
                                        </div>
                                    </span>
                                    <select class="form-control facturas" id="facturas_nume_doc"></select>
                                </div>
                            </div>                            
                        </div>
                        <br />
                        <div class="row top-buffer">
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon input-fix-sm">
                                        <div class="input-group-fix-width" style="cursor:help;" title="Estado de la Factura">
                                            Estado
                                        </div>
                                    </span>
                                    <select class="form-control facturas" id="facturas_codi_estado"></select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3" style="cursor:help;" title="Valor de la Factura">Valor</span>
                                    <input type="text" class="form-control facturas" id="facturas_valor_fac" aria-describedby="basic-addon3">
                                </div>
                            </div>
                        </div>
                        <br />
                        <div class="row top-buffer">
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3" style="cursor:help;" title="Fecha de la Factura">Fecha</span>
                                    <input type="date" class="form-control facturas" id="facturas_fecha_fac" aria-describedby="basic-addon3">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnGuardarFactura" class="btn btn-primary btn-sm">
                        Guardar</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="modalClientes">
        <div class="modal-dialog modal-medium">
            <div class="modal-content" style="overflow: visible;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Agregar/Editar Cliente</h4>
                </div>
                <div class="modal-body" style="font-size: 9pt;">
                    <input type="hidden" id="clientes_documentoanterior" class="datoscliente">
                    <div class="row container-fluid">
                        <div class="row top-buffer">
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3" style="cursor:help;" title="Nombre del Cliente">Nombre</span>
                                    <input type="text" class="form-control datoscliente" id="clientes_nombres" aria-describedby="basic-addon3">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3" style="cursor:help;" title="Dirección del Cliente">Dirección</span>
                                    <input type="text" class="form-control datoscliente" id="clientes_direccion" aria-describedby="basic-addon3">
                                </div>
                            </div>
                        </div>
                        <br />
                        <div class="row top-buffer">
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3" style="cursor:help;" title="Número de Documento del Cliente">#Doc</span>
                                    <input type="text" class="form-control datoscliente" id="clientes_numerodocumento" aria-describedby="basic-addon3">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnGuardarCliente" class="btn btn-primary btn-sm">
                        Guardar</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="modalEstadosFacturas">
        <div class="modal-dialog modal-medium">
            <div class="modal-content" style="overflow: visible;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Agregar/Editar Estado de la Factura</h4>
                </div>
                <div class="modal-body" style="font-size: 9pt;">
                    <input type="hidden" id="estadosfacturas_codi_estado" class="estadosfacturas">
                    <div class="row container-fluid">
                        <div class="row top-buffer">
                            <div class="col-lg-12">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3" style="cursor:help;" title="Descripción del Estado">Descripción</span>
                                    <input type="text" class="form-control estadosfacturas" id="estadosfacturas_descripcion" aria-describedby="basic-addon3">
                                </div>
                            </div>                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnGuardarEstadoFactura" class="btn btn-primary btn-sm">
                        Guardar</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>