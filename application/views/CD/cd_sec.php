<div class="pt-16 border-bottom bd-default bg-light">    <div class="container-fluid">        <div class="row">            <div class="cell-md-6 text-center-md text-left pt-2">                <h3 class="display1">COMPROBANTES DE DESCARGO</h3>            </div>        </div>    </div>    <div style="overflow-x:auto;">        <div class="container-fluid pt-0 pl-0 pr-0 pl-5-md pr-5-md">            <table class="table row-border row-hover compact" id="tableCD">                <thead>                <tr>                    <th class="sortable-column sort-asc">CD<br>Gestion</th>                    <th class="sortable-column sort-asc" style="width: 10%">Nro. Correlativo/<br>Nº Fojas</th>                    <th class="sortable-column sort-asc">Beneficiario</th>                    <th class="sortable-column sort-asc" style="width: 25%">Descripcion</th>                    <th class="sortable-column sort-asc">Ubicacion</th>                    <th class="sortable-column sort-asc">CUCE</th>                    <th class="sortable-column sort-asc" style="width: 8%">SISIN</th>                    <th style="width: 5%">Archivo</th>                    <th style="width: 8%">Accion</th>                </tr>                </thead>                <tbody style="font-size: 13px;">                </tbody>            </table>        </div>    </div>    <div style="text-align: center;"><span style="text-shadow: 1px 1px 1px #3f4448; color: #ff9317; font-size: 18px;">&#174;</span>        <span style="text-shadow: 1px 1px 1px #3f4448; color: #ff9317;">G.A.M.E.A. 2018</span>    </div></div></body><style type="text/css">    label{ font-size: 10px; font-weight:900;}    #tableCD_filter{padding-left: 2%;    padding-right: 2%;}    #tableCD_length {padding-left: 2%;        padding-right: 2%;}    .dataTables_filter>label{        font-size: 1.2em;        color: #892a78;    }</style><script>    $(document).ready(function () {        var dataTable = $('#tableCD').DataTable({            "processing": true,            "serverSide": true,            "order": [],            "ajax": {                url: "<?php echo base_url() . 'index.php/Sec_control/list_CD';?>",                type: "POST"            },            "columnDefs": [                {                    "targets": [3,7,8],                    "orderable": false                }            ],            "retrieve": true,            "deferRender": true,            "crollCollapse": true,            "scroller": true,            "language": {                "sProcessing": "Procesando...",                "sLengthMenu": "Mostrar _MENU_ registros",                "sZeroRecords": "No se encontraron resultados",                "sEmptyTable": "Ningún dato disponible en esta tabla",                "sInfo": "Mostrando  _START_ al _END_ del total _TOTAL_",                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",                "sInfoPostFix": "",                "sSearch": "Buscar:",                "sUrl": "",                "sInfoThousands": ",",                "sLoadingRecords": "Cargando...",                "oPaginate": {                    "sFirst": "Primero",                    "sLast": "Último",                    "sNext": ">>",                    "sPrevious": "<<"                },                "oAria": {                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"                }            },            "lengthMenu": [10, 20, 30, 100]        });        $('#cd_monto').number( true, 2,',', '.' );        $('#e_cd_monto').number( true, 2,',', '.' );        $('#reload').click(function () {            location.reload();        });    });</script></html>