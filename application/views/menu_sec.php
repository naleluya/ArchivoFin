<div class="pos-fixed fixed-top app-bar-wrapper z-top">    <header class="container pos-relative app-bar-expand-md" data-role="appbar">        <a href="#" class="brand no-hover fg-white-hover order-1"><span class="mif-books fg-yellow mif-3x"></span></a>        <div class="app-bar-item d-none d-flex-md order-2">            <h2 style="color: #fdf9a8">SI-ARCH</h2>        </div>        <ul class="app-bar-menu order-4 order-md-3">            <li><a href="<?php echo site_url('Sec_control/')?>" class="fg-yellow-hover"                   data-role="hint"                   data-hint-text="Direccion Tesoro Municipal"                   data-hint-position="bottom"                >DTM</a></li>            <li><a href="<?php echo site_url('Sec_control/ci_index')?>" class="fg-yellow-hover"                   data-role="hint"                   data-hint-text="Comprobantes de Ingreso"                   data-hint-position="bottom"                >CI</a></li>            <li><a href="<?php echo site_url('Sec_control/cd_index')?>" class="fg-yellow-hover"                   data-role="hint"                   data-hint-text="Comprobantes de Descargo"                   data-hint-position="bottom"                >CD</a></li>            <li><a href="<?php echo site_url('Sec_control/cdaf_index')?>" class="fg-yellow-hover"                   data-role="hint"                   data-hint-text="Consejo de Direccion<br>Administrativa Financiera"                   data-hint-position="bottom"                >CDAF</a></li>            <li><a href="<?php echo site_url('Sec_control/pdp_index')?>" class="fg-yellow-hover"                   data-role="hint"                   data-hint-text="Programa de<br>Drenaje Pluvial"                   data-hint-position="bottom"                >PDP</a></li>            <li><a href="<?php echo site_url('Sec_control/pmruc_index')?>" class="fg-yellow-hover"                   data-role="hint"                   data-hint-text="Programa de Reordenamiento<br>Urbano de la Ceja"                   data-hint-position="bottom"                >PMRUC</a></li>            <li><a href="<?php echo site_url('Sec_control/prestamo_index')?>" class="fg-yellow-hover">PRESTAMOS</a></li>            <li><a href="<?php echo site_url('Sec_control/historial_index')?>" class="fg-yellow-hover">HISTORIAL</a></li>        </ul>        <div class="app-bar-container ml-auto order-3 order-md-4">            <a href="#" class="app-bar-item" onclick="Metro.dialog.open('#Dialog')"><span style="color:#ff5b62;font-weight:900;"><?php echo  $count->pre_cantidad; ?></span><span class="mif-bell"></span></a>            <div class="app-bar-container">                <a class="app-bar-item dropdown-toggle marker-light pl-1 pr-5" href="#">                    <span class="mif-user mif-2x"></span>                </a>                <ul class="v-menu place-right" data-role="dropdown">                    <li class="text-center"><b><?php echo $usu_nombres.' '.$usu_paterno;?></b></li>                    <li class="divider"></li>                    <li><a href="#" class="app-bar-item" onclick="Metro.dialog.open('#Dialog_Perfil')"><span class="mif-user  mif-lg"></span>&nbsp;&nbsp; Perfil de Usuario</a></li>                    <li class="divider"></li>                    <li><a class="app-bar-item" href="<?php echo base_url();?>index.php/Login_control/close_session"><span class="mif-switch  mif-lg"></span>&nbsp;&nbsp; Salir</a></li>                </ul>            </div>        </div>    </header>    <div class="dialog" data-role="dialog" id="Dialog"data-width="800">        <div class="dialog-title"><b>ARCHIVOS PENDIENTES DE ENTREGA</b></div>        <div class="dialog-content" style="overflow-y: scroll;height: 300px;">            <table class="table row-border table-border row-hover">                <thead>                    <tr>                        <th>#</th>                        <th style="width: 35%">Funcionario</th>                        <th>Nro. CITE</th>                        <th>Fec. ingreso</th>                        <th>Fec. devolucion</th>                        <th>Dias<br>Mora</th>                    </tr>                </thead>                <tbody>                <?php                $i=1;                foreach ($mora as $m)                {?>                    <tr style="font-size: 12px">                        <td><?php echo $i;?></td>                        <td><span class="mif-user mif-2x"></span> <?php echo $m->pre_solicitante;?></td>                        <td><?php echo $m->pre_cite_af;?></td>                        <td><span class="mif-calendar mif-2x"></span> <?php echo $m->pre_fecha_prestamo;?></td>                        <td><span class="mif-calendar mif-2x"></span> <?php echo $m->pre_fecha_devolucion;?></td>                        <td><span style="color:#ff5b62;font-weight:900; font-size: 1.5em"><span class="mif-alarm fg-red"></span> <?php echo $m->pre_diferencia;?></span></td>                    </tr>                 <?php $i++;                }?>                </tbody>            </table>        </div>    </div>    <div class="dialog" data-role="dialog" id="Dialog_Perfil"data-width="400">        <div class="dialog-title"><b>DATOS DE USUARIO</b></div>        <div class="dialog-content" style="overflow-y: scroll;height: 300px;">            <div class="card" style="background-color: #dbdbdb">                <div class="card-header">                    <div class="avatar">                        <?php                        if ($this->session->userdata('usu_genero') == 'F')                        {                            echo '<img src="'.base_url().'assets/svg/mujer.svg">';                        }else {                            echo '<img src="'.base_url().'assets/svg/hombre.svg">';                        }                        ?>                    </div>                    <div class="name">                        <b>USUARIO:</b> <?php echo $this->session->userdata('hab_nombreusuario');                        ?><br>                        <b>ROL: </b><?php echo $this->session->userdata('rol_nombre'); ?><br>                    </div>                </div>                <div class="card-content p-2">                    <b>NOMBRES:</b> <?php echo $this->session->userdata('usu_nombres');?><br>                    <b>APELLIDO PATERNO: </b><?php echo $this->session->userdata('usu_paterno');?><br>                    <b>APELLIDO MATERNO: </b><?php echo $this->session->userdata('usu_materno');?><br>                    <b>C.I.: </b><?php echo $this->session->userdata('usu_ci');?>                    <?php                    if ($this->session->userdata('dep_id')==1)                        echo ('LP');                    elseif ($this->session->userdata('dep_id')==2)                        echo ('CB');                    elseif ($this->session->userdata('dep_id')==3)                        echo ('SC');                    elseif ($this->session->userdata('dep_id')==4)                        echo ('OR');                    elseif ($this->session->userdata('dep_id')==5)                        echo ('CH');                    elseif ($this->session->userdata('dep_id')==6)                        echo ('BN');                    elseif ($this->session->userdata('dep_id')==7)                        echo ('PT');                    elseif ($this->session->userdata('dep_id')==8)                        echo ('TJ');                    elseif ($this->session->userdata('dep_id')==9)                        echo ('PA');                    else                        echo ('OTRO');                    ?><br><br>                    <b>ESTADO: </b><?php                    if($this->session->userdata('hab_estado') == true)                        echo ('HABILITADO');                    else                        echo ('DESHABILITADO');                    ?><br>                    <b>FECHA DE HABILITACION: </b><?php                                                    $pg_date = $this->session->userdata('hab_fecha');                                                    $date_obj = date_create_from_format('Y-m-d', $pg_date);                                                    $date = date_format($date_obj, 'd-m-Y');                                                    echo $date;                                                    ?>                </div>            </div>        </div>    </div></div>