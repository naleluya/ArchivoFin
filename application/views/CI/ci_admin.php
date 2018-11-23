<div class="pt-16 border-bottom bd-default bg-light">    <div class="container-fluid">        <div class="row">            <div class="cell-md-6 text-center-md text-left pt-2">                <h3 class="display1">COMPROBANTES DE INGRESO</h3>                <button class="button primary rounded drop-shadow" onclick="Metro.dialog.open('#demoDialog1')" id="add_ci"><span                        class="mif-note-add mif-lg"></span>Adicionar CI                </button>                <button class="button alert square rounded drop-shadow" id="reload"><span class="mif-loop2 mif-lg"></span></button>            </div>        </div>    </div>    <div style="overflow-x:auto;">        <div class="container-fluid pt-0 pl-0 pr-0 pl-5-md pr-5-md">            <table class="table row-border row-hover compact" id="tableCI">                <thead>                <tr>                    <th class="sortable-column sort-asc">CI<br>Gestion</th>                    <th class="sortable-column sort-asc" style="width: 10%;">Nro. Correlativo/<br>Nº Fojas</th>                    <th class="sortable-column sort-asc" style="width: 10%;">Beneficiario</th>                    <th class="sortable-column sort-asc" style="width: 30%;">Descripcion</th>                    <th class="sortable-column sort-asc">Ubicacion</th>                    <th class="sortable-column sort-asc">CUCE</th>                    <th class="sortable-column sort-asc" style="width: 8%;">SISIN</th>                    <th>Archivo</th>                    <th>Accion</th>                </tr>                </thead>                <tbody style="font-size: 13px;">                </tbody>            </table>        </div>    </div>    <div style="text-align: center;"><span style="text-shadow: 1px 1px 1px #3f4448; color: #ff9317; font-size: 18px;">&#174;</span>        <span style="text-shadow: 1px 1px 1px #3f4448; color: #ff9317;">G.A.M.E.A. 2018</span>    </div></div><!--modal Dialog INSERT CI--><div data-role="dialog" id="demoDialog1" style="background-color: #f4eeb2" data-width="800">    <div class="dialog-title" style="font-weight:900;">ADICIONAR CI</div>    <div class="dialog-content">        <form data-role="validator" method="post" action="<?php echo site_url('Admin_control/save_ci')?>">            <div class="row mb-4">                <div class="cell-md-3">                    <label>Fecha</label>                    <input name="ci_fecha_ingreso" data-role="datepicker" data-locale="es-ES">                </div>                <div class="cell-md-3">                    <label>CI</label>                    <input name="ci_nro" type="number" data-validate="required number">                    <span class="invalid_feedback">                        <span class="mif-warning"></span>Ingresar CI                    </span>                </div>                <div class="cell-md-2">                    <label>Gestion</label>                    <select  name="ci_gestion" data-role="select" data-validate="required">                        <option></option>                        <option value="2015">2015</option>                        <option value="2016">2016</option>                        <option value="2017">2017</option>                        <option value="2018">2018</option>                        <option value="2019">2019</option>                        <option value="2020">2020</option>                    </select>                    <span class="invalid_feedback">                      Seleccione Gestion                    </span>                </div>                <div class="cell-md-2">                    <label>Nº Correlativo</label>                    <input name="ci_nro_correlativo" type="number" data-validate="required number">                    <span class="invalid_feedback">                      <span class="mif-warning"></span>Introduzca Nº Correlativo                    </span>                </div>                <div class="cell-md-2">                    <label>Nº Fojas</label>                    <input name="ci_nro_fojas" type="number">                </div>            </div>            <div class="row mb-2">                <div class="cell-md-6">                    <label>Beneficiario</label>                    <input class="text-upper" name="ci_beneficiario" type="text" data-validate="required">                     <span class="invalid_feedback">                        <span class="mif-warning"></span>Introduzca Beneficiario                    </span>                </div>                <div class="cell-md-6">                    <label>Descripcion</label>                    <textarea class="text-upper" name="ci_descripcion" data-validate="required"></textarea>                    <span class="invalid_feedback">                        <span class="mif-warning"></span>Introduzca Descripcion                    </span>                </div>            </div>            <div class="row mb-5">                <div class="cell-md-3">                    <label>Nº Contrato</label>                    <input class="text-upper" name="ci_nro_con" type="text" >                </div>                <div class="cell-md-3">                    <label>Monto Economico</label>                    <input name="ci_monto" id="ci_monto" type="text" data-validate="required number" placeholder="0.00">                    <span class="invalid_feedback">                        <span class="mif-warning"></span>Introduzca Monto Economico                    </span>                </div>                <div class="cell-md-2">                    <label>Estante</label>                    <input class="text-upper" name="ci_estante" type="text" required>                </div>                <div class="cell-md-2">                    <label>Balda</label>                    <select name="ci_balda" data-role="select">                        <option value=""></option>                        <option value="A">A</option>                        <option value="B">B</option>                        <option value="C">C</option>                        <option value="D">D</option>                        <option value="E">E</option>                        <option value="F">F</option>                    </select>                    <div class="invalid_feedback">Please provide a valid zip.</div>                </div>                <div class="cell-md-2">                    <label>Fila</label>                    <select name="ci_fila" data-role="select">                        <option value=""></option>                        <option value="1">1</option>                        <option value="2">2</option>                        <option value="3">3</option>                        <option value="4">4</option>                        <option value="5">5</option>                        <option value="6">6</option>                    </select>                    <div class="invalid_feedback">Please provide a valid zip.</div>                </div>            </div>            <div class="row mb-2">                <div class="cell-md-6">                    <label>SISIN</label>                    <input class="text-upper" name="ci_sisin" type="text">                </div>                <div class="cell-md-6">                    <label>CUCE</label>                    <input class="text-upper" name="ci_cuce" type="text">                </div>            </div>            <div class="row mb-3">                <div class="cell-md-5">                    <label>Objeto de Gasto</label>                    <input class="text-upper" name="ci_obj_gasto" type="text">                </div>                <div class="cell-md-4">                    <label>Partida Presupuestaria</label>                    <input name="ci_partida_pre" type="number">                </div>                <div class="cell-md-3">                    <label>Nº Cheque</label>                    <input name="ci_nro_cheque" type="number">                </div>            </div>    </div>    <div class="dialog-actions">        <button type="submit" class="button primary rounded drop-shadow">Aceptar</button>        </form>        <button class="button secondary js-dialog-close rounded drop-shadow">Cancelar</button>    </div></div><!--modal Dialog EDIT CI--><div data-role="dialog" id="demoEdit" style="background-color: #f4eeb2" data-width="800">    <div class="dialog-title" style="font-weight:900;">ACTUALIZAR CI</div>    <div class="dialog-content">        <form data-role="validator" method="post" action="<?php echo site_url('Admin_control/edit_ci')?>">            <input type="hidden" name="ci_id">            <div class="row mb-4">                <div class="cell-md-3">                    <label>Fecha</label>                    <input name="e_ci_fecha_ingreso" data-role="datepicker" data-locale="es-ES" data-value="" id="e_ci_fecha_ingreso">                </div>                <div class="cell-md-3">                    <label>CI</label>                    <input name="e_ci_nro" type="number" data-validate="required number">                    <span class="invalid_feedback">                        <span class="mif-warning"></span>Ingresar CI                    </span>                </div>                <div class="cell-md-2">                    <label>Gestion</label>                    <select  name="e_ci_gestion"  data-validate="required">                        <option></option>                        <option value="2015">2015</option>                        <option value="2016">2016</option>                        <option value="2017">2017</option>                        <option value="2018">2018</option>                        <option value="2019">2019</option>                        <option value="2020">2020</option>                    </select>                    <span class="invalid_feedback">                      Seleccione Gestion                    </span>                </div>                <div class="cell-md-2">                    <label>Nº Correlativo</label>                    <input name="e_ci_nro_correlativo" type="number" data-validate="required number">                    <span class="invalid_feedback">                      <span class="mif-warning"></span>Introduzca Nº Correlativo                    </span>                </div>                <div class="cell-md-2">                    <label>Nº Fojas</label>                    <input name="e_ci_nro_fojas" type="number">                </div>            </div>            <div class="row mb-2">                <div class="cell-md-6">                    <label>Beneficiario</label>                    <input class="text-upper" name="e_ci_beneficiario" type="text" data-validate="required">                     <span class="invalid_feedback">                        <span class="mif-warning"></span>Introduzca Beneficiario                    </span>                </div>                <div class="cell-md-6">                    <label>Descripcion</label>                    <textarea class="text-upper" name="e_ci_descripcion" data-validate="required"></textarea>                    <span class="invalid_feedback">                        <span class="mif-warning"></span>Introduzca Descripcion                    </span>                </div>            </div>            <div class="row mb-5">                <div class="cell-md-3">                    <label>Nº Contrato</label>                    <input class="text-upper" name="e_ci_nro_con" type="text">                </div>                <div class="cell-md-3">                    <label>Monto Economico</label>                    <input name="e_ci_monto" type="text" data-validate="required number" id="e_ci_monto">                    <span class="invalid_feedback">                        <span class="mif-warning"></span>Introduzca Monto Economico                    </span>                </div>                <div class="cell-md-2">                    <label>Estante</label>                    <input class="text-upper" name="e_ci_estante" type="text" required>                </div>                <div class="cell-md-2">                    <label>Balda</label>                    <select name="e_ci_balda">                        <option value="PENDIENTE"></option>                        <option value="A">A</option>                        <option value="B">B</option>                        <option value="C">C</option>                        <option value="D">D</option>                        <option value="E">E</option>                        <option value="F">F</option>                    </select>                    <div class="invalid_feedback">Please provide a valid zip.</div>                </div>                <div class="cell-md-2">                    <label>Fila</label>                    <select name="e_ci_fila">                        <option value="PENDIENTE"></option>                        <option value="1">1</option>                        <option value="2">2</option>                        <option value="3">3</option>                        <option value="4">4</option>                        <option value="5">5</option>                        <option value="6">6</option>                    </select>                    <div class="invalid_feedback">Please provide a valid zip.</div>                </div>            </div>            <div class="row mb-2">                <div class="cell-md-6">                    <label>SISIN</label>                    <input class="text-upper" name="e_ci_sisin" type="text">                </div>                <div class="cell-md-6">                    <label>CUCE</label>                    <input class="text-upper" name="e_ci_cuce" type="text">                </div>            </div>            <div class="row mb-3">                <div class="cell-md-5">                    <label>Objeto de Gasto</label>                    <input class="text-upper" name="e_ci_obj_gasto" type="text">                </div>                <div class="cell-md-4">                    <label>Partida Presupuestaria</label>                    <input name="e_ci_partida_pre" type="number">                </div>                <div class="cell-md-3">                    <label>Nº Cheque</label>                    <input name="e_ci_nro_cheque" type="number">                </div>            </div>    </div>    <div class="dialog-actions">        <button type="submit" class="button primary rounded drop-shadow">Aceptar</button>        </form>        <button class="button secondary js-dialog-close rounded drop-shadow">Cancelar</button>    </div></div><!--modal Dialog UPLOAD CI--><div data-role="dialog" id="demoUp" style="background-color: #f4eeb2" data-width="500">    <div class="dialog-title" style="font-weight:900;">SUBIR ARCHIVO</div>    <div class="dialog-content">        <form  id="upload" method="post" enctype="multipart/form-data" action="<?php echo site_url("Admin_control/upload_CI");?>">            <!--El name del campo tiene que ser si o si "userfile"-->            <div class="row mb-2">                <div class="cell-md-9">                    <input type="file" data-role="file" name="ci_file" id="ci_file" data-validate="required" data-caption="<span class='mif-folder'></span>">                    <input type="hidden" name="nFile" id="nFile">                    <input type="hidden" name="ci_id">                </div>                <div class="cell-md-3">                    <button type="submit" class="button primary rounded drop-shadow">Enviar Arch.</button>                </div>        </form>        <progress id="prog" max="100" value="0" style="display: none;">        </progress>        <div id="percent" style="font-weight:bold;"></div>    </div>    <div class="dialog-actions">        <button class="button alert js-dialog-close rounded drop-shadow">Cerrar</button>    </div></div></body><style type="text/css">    label{ font-size: 10px; font-weight:900;}    #tableCI_filter{padding-left: 2%;    padding-right: 2%;}    #tableCI_length {padding-left: 2%;        padding-right: 2%;}    #prog {        background-color :  #1a1a1a ;        height :  25px ;        padding :  5px ;        width :  350px ;        margin : 5px 11px;        border-radius :  5px ;        box-shadow :  0  1px  5px  #000  inset ,  0  1px  0  #444 ;    }    .dataTables_filter>label{        font-size: 1.2em;        color: #892a78;    }</style><script>    $(document).ready(function () {        var dataTable = $('#tableCI').DataTable({            "processing": true,            "serverSide": true,            "order": [],            "ajax": {                url: "<?php echo base_url() . 'index.php/Admin_control/list_CI';?>",                type: "POST"            },            "columnDefs": [                {                    "targets": [0,4,8],                    "orderable": false                }            ],            "retrieve": true,            "deferRender": true,            "crollCollapse": true,            "scroller": true,            "language": {                "sProcessing": "Procesando...",                "sLengthMenu": "Mostrar _MENU_ registros",                "sZeroRecords": "No se encontraron resultados",                "sEmptyTable": "Ningún dato disponible en esta tabla",                "sInfo": "Mostrando  _START_ al _END_ del total _TOTAL_",                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",                "sInfoPostFix": "",                "sSearch": "Buscar:",                "sUrl": "",                "sInfoThousands": ",",                "sLoadingRecords": "Cargando...",                "oPaginate": {                    "sFirst": "Primero",                    "sLast": "Último",                    "sNext": ">>",                    "sPrevious": "<<"                },                "oAria": {                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"                }            },            "lengthMenu": [10, 20, 30, 100]        });        $('#ci_monto').number( true, 2,',', '.' );        $('#e_ci_monto').number( true, 2,',', '.' );        $('#reload').click(function () {            location.reload();        });        $('#upload').on('submit',function (e) {            e.preventDefault();            $(this).ajaxSubmit({                beforeSend:function () {                    $('#prog').show();                    $('#prog').attr('value','0');                    rowCI()                },                uploadProgress:function (event,position,total,percentCompelete) {                    $('#prog').attr('value',percentCompelete);                    $('#percent').html(percentCompelete+'%');                },                success: function (data) {                    alertify.alert('SI-ARCH',data);                    window.setTimeout('location.reload()', 1000);                }            });        });    });    function deleteCI(ci_id)    {        $.ajax({            url: "<?php echo site_url('Admin_control/get_ci/')?>" + ci_id,            type: "GET",            dataType: "JSON",            success: function (data) {                alertify.confirm('SI-ARCH', '¿Eliminar <b>CI '+data.ci_nro+'</b>?', function(){                        $.ajax({                            url:  "<?php echo site_url('Admin_control/deleteCI')?>",                            type: 'POST',                            data: {"ci_id": ci_id},                            success: function (respuesta) {                                alertify.alert('SI-ARCH',respuesta);                                window.setTimeout('location.reload()', 1000);                            },                            error: function (xhr, status) {                                alertify.error('Error al Eliminar');                            }                        });                        alertify.success('Ok')                    }                    , function(){                        alertify.error('Cancel')});            },            error: function (jqXHR, textStatus, errorThrown) {                alert('Error al obtener datos de ajax');            }        })    }    function editCI(ci_id)    {        $.ajax({            url: "<?php echo site_url('Admin_control/get_ci/')?>" + ci_id,            type: "GET",            dataType: "JSON",            success: function (data) {                var separador = "-";                var arrayFecha = data.ci_fecha_ingreso.split(separador);                var anio = arrayFecha[0];                var mes = arrayFecha[1];                var dia = arrayFecha[2];                var dateNew = new Date(anio, mes-1 , dia);                $('#e_ci_fecha_ingreso').attr('data-value',dateNew);                $('[name="e_ci_nro"]').val(data.ci_nro);                $('[name="e_ci_gestion"]').val(data.ci_gestion);                $('[name="e_ci_nro_correlativo"]').val(data.ci_correlativo);                $('[name="e_ci_nro_fojas"]').val(data.ci_nro_fojas);                $('[name="e_ci_beneficiario"]').val(data.ci_beneficiario);                $('[name="e_ci_descripcion"]').val(data.ci_descripcion);                $('[name="e_ci_nro_con"]').val(data.ci_nro_con);                var m = data.ci_monto;                var res = m.replace(".", ",");                $('[name="e_ci_monto"]').val(res);                $('[name="e_ci_estante"]').val(data.ci_estante);                $('[name="e_ci_balda"]').val(data.ci_balda);                $('[name="e_ci_fila"]').val(data.ci_fila);                $('[name="e_ci_nro_cheque"]').val(data.ci_nro_cheque);                $('[name="e_ci_sisin"]').val(data.ci_sisin);                $('[name="e_ci_cuce"]').val(data.ci_cuce);                $('[name="e_ci_obj_gasto"]').val(data.ci_obj_gasto);                $('[name="e_ci_partida_pre"]').val(data.ci_partida_pre);                $('[name="ci_id"]').val(data.ci_id);            },            error: function (jqXHR, textStatus, errorThrown) {                alert('Error al obtener datos de ajax');            }        });        Metro.dialog.open('#demoEdit');    }    function  uploadCI(ci_id, ci_nro, ci_gestion) {        var nFile = ci_id+'_CI-'+ci_nro+'_'+ci_gestion;        $('[name="nFile"]').val(nFile );        $('[name="ci_id"]').val(ci_id);        Metro.dialog.open('#demoUp');    }    function  rowCI() {    }    function deletePdfCI(ci_id)    {        $.ajax({            url: "<?php echo site_url('Admin_control/get_ci/')?>" + ci_id,            type: "GET",            dataType: "JSON",            success: function (data) {                alertify.confirm('SI-ARCH', '¿Eliminar <b>el archivo '+data.ci_adjuntar+'</b>?', function(){                        $.ajax({                            url:  "<?php echo site_url('Admin_control/deletePdf_CI')?>",                            type: 'POST',                            data: {"ci_id": ci_id, "ci_adjuntar": data.ci_adjuntar},                            success: function (respuesta) {                                alertify.alert('SI-ARCH',respuesta);                                window.setTimeout('location.reload()', 1000);                            },                            error: function (xhr, status) {                                alertify.error('Error al Eliminar archivo');                            }                        });                        alertify.success('Ok')                    }                    , function(){                        alertify.error('Cancel')});            },            error: function (jqXHR, textStatus, errorThrown) {                alert('Error al obtener datos de ajax');            }        })    }</script></html>