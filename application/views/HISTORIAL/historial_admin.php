<div class="pt-16 border-bottom bd-default bg-light">    <div class="container-fluid">        <ul class="tabs-expand-md" data-role="tabs">            <li><a href="#_target_1">Archivos</a></li>        </ul>            <div id="_target_1">                <div class="container-fluid">                    <div class="row">                        <div class="cell-md-6 text-center-md text-left pt-2">                            <h4 class="display1">HISTORIAL DE ARCHIVOS</h4>                        </div>                    </div>                </div>                <div class="container">                    <div style="overflow-y: scroll; overflow-x: scroll; height: 560px; ">                        <table class="table row-border table-border row-hover" id="MiTabla">                            <thead style="background-color: #d8d8d8; ">                            <tr>                                <th style="font-size: 15px;">#</th>                                <th style="font-size: 15px;">FUNCIONARIO</th>                                <th style="font-size: 15px;">FECHA<br>PRESTAMO</th>                                <th style="font-size: 15px;">FECHA<br>DEVOLUCION</th>                                <th style="font-size: 15px;">ARCHIVOS</th>                                <th style="font-size: 15px;">ESTADO DE<br>PRESTAMO</th>                                <th style="font-size: 15px;">FECHA REAL<br>DEVOLUCION</th>                                <th style="font-size: 15px;">DIAS DE<br>MORA</th>                                <th style="font-size: 15px;">PDF</th>                            </tr>                            </thead>                            <tbody style="font-size: 13px;">                            <?php                            $i = 1;                            foreach ($prestamos as $pre)                            {?>                             <tr>                                 <td><?php echo  $i;?></td>                                 <td><span class="mif-user mif-2x"></span> <?php echo  $pre->pre_solicitante;?></td>                                 <td><span class="mif-calendar mif-2x"></span>                                     <?php                                     $pg_date = $pre->pre_fecha_prestamo;                                     $date_obj = date_create_from_format('Y-m-d', $pg_date);                                     $date = date_format($date_obj,'d-m-Y');                                     echo $date;                                     ?>                                 </td>                                 <td><span class="mif-calendar mif-2x"></span>                                     <?php                                     $pg_date2 = $pre->pre_fecha_devolucion;                                     $date_obj2 = date_create_from_format('Y-m-d', $pg_date2);                                     $date2 = date_format($date_obj2,'d-m-Y');                                     echo  $date2;                                     ?>                                 </td>                                 <td style="width:40%;">                                    <?php                                        $documentos = $this->Prestamo_model->ver_Archivo($pre->pre_id);                                        foreach ($documentos as $doc)                                        {                                            $detalle = $this->Prestamo_model->ver_Detalle_Archivo($doc->doc_name, $doc->doc_nro, $doc->doc_gestion);?>                                            <div style="border: 2px solid gray; border-radius: 2px; margin-bottom: 4px; background-color: #e7e7e7; font-size: 11px;">                                                <b>Archivo: </b><?php echo $doc->doc_name ?>                                                <b> Nro: </b><?php echo $doc->doc_nro ?>                                                <b> Beneficiario: </b><?php echo $detalle->beneficiario ?>                                                <b> Monto: </b>Bs. <?php echo $detalle->monto ?>                                                <b> Descripción: </b><?php echo $detalle->descripcion ?>                                            </div>                                            <?php                                        }                                    ?>                                 </td>                                 <?php                                 if ($pre->pre_estado === 't') {?>                                     <td style="color: red;"><span class="mif-upload2 mif-2x fg-red"></span> En Prestamo</td>                                     <td style="color: red;"><span class="mif-calendar mif-2x fg-red"></span> En Prestamo</td>                                     <td><span class="mif-checkmark mif-2x fg-red"></span><?php                                         $date1 = new DateTime($pre->pre_fecha_real_dev);                                         $date2 = new DateTime($pre->pre_fecha_devolucion);                                         $diferencia = $date2->diff($date1);                                         echo $diferencia->format('%a días');                                         ?></td>                                 <?php }                                 else { ?>                                     <td><span class="mif-checkmark mif-2x fg-green"></span> Conluido</button></td>                                     <td><span class="mif-calendar mif-2x"></span>                                         <?php                                         $pg_date3 = $pre->pre_fecha_real_dev;                                         $date_obj3 = date_create_from_format('Y-m-d', $pg_date3);                                         $date3 = date_format($date_obj3,'d-m-Y');                                         echo $date3;                                         ?>                                     </td>                                     <td> Concluido</td>                                     <?php                                 }?>                                 <?php                                 $i++;                                 ?>                                 <td><form method="POST" action="<?php site_url('Admin_control/reporte_pdf')?>">                                         <input type="hidden" name="p_pre_id" value="<?php echo $pre->pre_id?>">                                         <input type="hidden" name="p_pre_solicitante" value="<?php echo $pre->pre_solicitante?>">                                         <input type="hidden" name="p_pre_dependencia" value="<?php echo $pre->pre_dependencia?>">                                         <input type="hidden" name="p_pre_cite_af" value="<?php echo $pre->pre_cite_af?>">                                         <input type="hidden" name="p_pre_cite_solicitud" value="<?php echo $pre->pre_cite_solicitud?>">                                         <input type="hidden" name="p_pre_fecha_prestamo" value="<?php echo $pre->pre_fecha_prestamo?>">                                         <input type="hidden" name="p_pre_fecha_devolucion" value="<?php echo $pre->pre_fecha_devolucion?>">                                         <input type="hidden" name="p_pre_autorizado" value="<?php echo $pre->pre_autorizado?>">                                         <input type="hidden" name="p_pre_observaciones" value="<?php echo $pre->pre_observaciones?>">                                         <button type="submit" class="mif-file-pdf mif-3x fg-red"></button>                                     </form></td>                            <?php }                            ?>                             </tr>                            </tbody>                        </table>                    </div>                </div>            </div>        </div>    </div>    <div style="text-align: center;"><span style="text-shadow: 1px 1px 1px #3f4448; color: #ff9317; font-size: 18px;">&#174;</span>        <span style="text-shadow: 1px 1px 1px #3f4448; color: #ff9317;">G.A.M.E.A. 2018</span>    </div></div></body><script>    /////////////ORDENAR TABLA///////////////    $(document).ready(function()        {            $("#MiTabla").tablesorter();        }    );//////////Ver Archivo////////</script><style type="text/css"></style><!--Desarrollado por:            Lic. Mark Erik Copa            Ing. Nelson Erwin Aleluya            G.A.M.E.A.            2018--></html>