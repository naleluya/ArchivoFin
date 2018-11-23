<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_control extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->model('Prestamo_model');
        $this->load->model('Pdp_model');
        $this->load->model('Cdaf_model');
        $this->load->model('Cd_model');
        $this->load->model('Ci_modelo');
        $this->load->model('Dtm_model');
        $this->load->model('Pmruc_model');
        $this->load->model('Login_model');
        $this->load->model('User_model');
        $this->load->helper(array('html', 'url', 'form'));
        $this->load->library('session');
        $this->load->library('upload');
    }

    ////////DTM///////////////
    public function index()
    {
        $data_session = $this->session->all_userdata();
        if (isset($data_session)) {
            if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                $this->load->view('head', $data_session);
                date_default_timezone_set('America/La_Paz');
                $time = time();
                $fecha_actual = date("Y-m-d", $time);
                $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                $this->load->view('menu_admin',$pre);
                $this->load->view('DTM/dtm_admin');
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        } else {
            redirect('/Login_control/close_session', 'refresh');
        }
    }
    public function list_DTM()
    {
        $fetch_data = $this->Dtm_model->make_dataTables();
        $data = array();
        foreach ($fetch_data as $row) {
            $subarray = array();
            $rowComplement = $this->Dtm_model->list_dtmComplement($row->dtm_id);
            $subarray[] = '<span style="border-bottom:2px black solid;"><span style="font-size: 12px"><b>DTM</b>: <span style="background-color: #FFFF66">' . $row->dtm_nro . '</span></span></span><br><b>Gestion:</b> ' . $rowComplement->dtm_gestion;
                $pg_date = $rowComplement->dtm_fecha_ingreso;
                $date_obj = date_create_from_format('Y-m-d', $pg_date);
                $date = date_format($date_obj, 'd-m-Y');
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>Nro. Correlativo:</b> ' . $row->dtm_correlativo . '</span><br><b>Nº Fojas:</b> ' . $rowComplement->dtm_nro_fojas . '<br><span  data-role="hint"
                data-hint-text="Fecha de Ingreso" data-hint-position="right"><span class="mif-calendar fg-darkBlue mif-lg"></span> ' . $date . '</span>';
            $monto = number_format($rowComplement->dtm_monto, 2, ',', '.');
            $subarray[] = '<span  data-role="hint" data-hint-text="Beneficiario" data-hint-position="right"><span style="border-bottom:2px black solid;"><span class="mif-user-secret fg-darkGreen mif-lg"></span> ' .
                $row->dtm_beneficiario . '</span></span><br><span  data-role="hint" data-hint-text="Monto" data-hint-position="right"><b>Bs. </b> ' . $monto . '</span><br><b>Nº Cheque: </b> ' . $rowComplement->dtm_nro_cheque.
                '<br><span ><b>Nro. Contrato:</b> ' . $rowComplement->dtm_nro_con .'</span>';
            $subarray[] = '<span><b>Por:</b> ' . $row->dtm_descripcion .'</span>';
            $subarray[] = '<ul data-role="listview" data-view-icons="icons-medium">
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="ESTANTE" data-icon="' . base_url() . 'assets/svg/estante-lleno.svg" data-caption="' . $row->dtm_estante . '" style="border-bottom:2px black solid;"></li>
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="BALDA"
                                data-icon="' . base_url() . 'assets/svg/balda.svg" data-caption="' . $rowComplement->dtm_balda . '"></li>
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="FILA"
                                data-icon="' . base_url() . 'assets/svg/fila.svg" data-caption="' . $rowComplement->dtm_fila . '"></li>
                        </ul>';
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>CUCE: </b>' . $row->dtm_cuce . '</span><br><b>Cite/Nro:</b> ' . $rowComplement->dtm_orden_cite . '<br><b>ORDEN DE </b>' . $rowComplement->dtm_orden_tipo .
                '<br><b>BD: </b> ' . $rowComplement->dtm_bd;
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>SISIN: </b> ' . $row->dtm_sisin . '</span><br><b>Part. Pptaria.: </b> ' . $rowComplement->dtm_partida_pre . '<br><b>Objeto de gasto: </b> ' . $rowComplement->dtm_obj_gasto;
            if ($row->dtm_adjuntar != '') {
                $subarray[] = '<a class="file_upload" href="'.base_url().'assets/uploads/DTM/'. $row->dtm_adjuntar.'" target="_blank"><span class="mif-file-pdf fg-red mif-5x"></span></a><br>
                                <button class="button alert rounded mini drop-shadow" onclick="deletePdfDTM('. $row->dtm_id .')"><b>X</b> Elimniar PDF</button>';
            }
            else {
                $subarray[] = '<button  class="upload_file rounded drop-shadow" onclick="uploadDTM('. $row->dtm_id .','. $rowComplement->dtm_nro .','. $rowComplement->dtm_gestion .')"><span class="mif-upload mif-4x"></span></button>';
            }
            $subarray[] = '<button  onclick="editDTM('. $row->dtm_id .')" class="button success cycle drop-shadow"><span class="mif-pencil"></span></button>
                         <button onclick="deleteDTM('. $row->dtm_id .')" class="button alert cycle drop-shadow"><span class="mif-cross"></span></button>';
            $data[] = $subarray;
        }
        $output = array(
            "draw" => intval($_POST["draw"]),
            "recordsTotal" => $this->Dtm_model->get_all_data(),
            "recordsFiltered" => $this->Dtm_model->get_filtered_data(),
            "data" => $data
        );
        echo json_encode($output);
    }
    public function deleteDTM()
    {
        if ($this->input->is_ajax_request()) {
            $dtm_id = trim($this->input->post("dtm_id"));
            if ($this->Dtm_model->delete_DTM($dtm_id) == true)
                echo "DTM Eliminado";
            else
                echo "DTM que Desea Eliminar NO EXISTE";

        } else
            show_404();
    }
    public function save_dtm()
    {
        $dtm_fecha_ingreso = strip_tags(trim($this->input->post('dtm_fecha_ingreso')));
        $dtm_nro = strip_tags(trim($this->input->post('dtm_nro')));
        $dtm_gestion = strip_tags(trim($this->input->post('dtm_gestion')));
        $dtm_nro_correlativo = strip_tags(trim($this->input->post('dtm_nro_correlativo')));

        $dtm_nro_fojas = strip_tags(trim($this->input->post('dtm_nro_fojas')));
        $dtm_nro_fojas = ('' == $dtm_nro_fojas) ? null : $dtm_nro_fojas;

        $dtm_beneficiario = strip_tags(trim(strtoupper($this->input->post('dtm_beneficiario'))));
        $dtm_descripcion = strip_tags(trim(strtoupper($this->input->post('dtm_descripcion'))));
        $dtm_nro_con = strip_tags(trim(strtoupper($this->input->post('dtm_nro_con'))));

        $dtm_monto = strip_tags(trim($this->input->post('dtm_monto')));
        $dtm_monto = str_replace(".", "", $dtm_monto);
        $dtm_monto = str_replace(",", ".", $dtm_monto);
        $dtm_monto = (double)$dtm_monto;

        $dtm_estante = strip_tags(trim(strtoupper($this->input->post('dtm_estante'))));
        $dtm_estante = ('' == $dtm_estante) ? 'PENDIENTE' : $dtm_estante;

        $dtm_balda = strip_tags(trim($this->input->post('dtm_balda')));
        $dtm_balda = ('' == $dtm_balda) ? 'PENDIENTE' : $dtm_balda;

        $dtm_fila = strip_tags(trim($this->input->post('dtm_fila')));
        $dtm_fila = ('' == $dtm_fila) ? 'PENDIENTE' : $dtm_fila;

        $dtm_orden_tipo = strip_tags(trim($this->input->post('dtm_orden_tipo')));
        $dtm_orden_tipo = ('' == $dtm_orden_tipo) ? null : $dtm_orden_tipo;

        $dtm_orden_cite = strip_tags(trim(strtoupper($this->input->post('dtm_orden_cite'))));
        $dtm_orden_cite = ('' == $dtm_orden_cite) ? null : $dtm_orden_cite;

        $dtm_nro_cheque = strip_tags(trim($this->input->post('dtm_nro_cheque')));
        $dtm_nro_cheque = ('' == $dtm_nro_cheque) ? null : $dtm_nro_cheque;

        $dtm_sisin = strip_tags(trim(strtoupper($this->input->post('dtm_sisin'))));
        $dtm_sisin = ('' == $dtm_sisin) ? null : $dtm_sisin;

        $dtm_cuce = strip_tags(trim(strtoupper($this->input->post('dtm_cuce'))));
        $dtm_cuce = ('' == $dtm_cuce) ? null : $dtm_cuce;

        $dtm_obj_gasto = strip_tags(trim(strtoupper($this->input->post('dtm_obj_gasto'))));
        $dtm_obj_gasto = ('' == $dtm_obj_gasto) ? null : $dtm_obj_gasto;

        $dtm_bd = strip_tags(trim($this->input->post('dtm_bd')));
        $dtm_bd = ('' == $dtm_bd) ? null : $dtm_bd;

        $dtm_partida_pre = strip_tags(trim($this->input->post('dtm_partida_pre')));
        $dtm_partida_pre = ('' == $dtm_partida_pre) ? null : $dtm_partida_pre;

        if ($this->Dtm_model->verificar_DTM_Nro($dtm_nro, $dtm_gestion)){
                $data = array(
                    'dtm_fecha_ingreso' => $dtm_fecha_ingreso,
                    'dtm_nro' => $dtm_nro,
                    'dtm_gestion' => $dtm_gestion,
                    'dtm_correlativo' => $dtm_nro_correlativo,
                    'dtm_nro_fojas' => $dtm_nro_fojas,
                    'dtm_beneficiario' => $dtm_beneficiario,
                    'dtm_descripcion' => $dtm_descripcion,
                    'dtm_nro_con' => $dtm_nro_con,
                    'dtm_monto' => $dtm_monto,
                    'dtm_estante' => $dtm_estante,
                    'dtm_balda' => $dtm_balda,
                    'dtm_fila' => $dtm_fila,
                    'dtm_orden_tipo' => $dtm_orden_tipo,
                    'dtm_orden_cite' => $dtm_orden_cite,
                    'dtm_nro_cheque' => $dtm_nro_cheque,
                    'dtm_sisin' => $dtm_sisin,
                    'dtm_cuce' => $dtm_cuce,
                    'dtm_obj_gasto' => $dtm_obj_gasto,
                    'dtm_bd' => $dtm_bd,
                    'dtm_partida_pre' => $dtm_partida_pre
                );
            if ($this->Dtm_model->saveDTM($data))
            {redirect("Admin_control/");}
            else
            {echo "Existe los Datos del DTM que desea Registrar";}
        } else {
            $data_session = $this->session->all_userdata();
            if (isset($data_session)) {
                if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                    $this->load->view('head', $data_session);
                    date_default_timezone_set('America/La_Paz');
                    $time = time();
                    $fecha_actual = date("Y-m-d", $time);
                    $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                    $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                    $this->load->view('menu_admin',$pre);
                    $this->load->view('ERROR/error_admin');
                } else {
                    redirect('/Login_control/close_session', 'refresh');
                }
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        }
    }
    public function get_dtm($dtm_id)
    {
        $data = $this->Dtm_model->getDTM($dtm_id);
        echo json_encode($data);
    }
    public function edit_dtm()
    {
        $dtm_fecha_ingreso = strip_tags(trim($this->input->post('e_dtm_fecha_ingreso')));
        $dtm_nro = strip_tags(trim($this->input->post('e_dtm_nro')));
        $dtm_gestion = strip_tags(trim($this->input->post('e_dtm_gestion')));
        $dtm_nro_correlativo = strip_tags(trim($this->input->post('e_dtm_nro_correlativo')));

        $dtm_nro_fojas = strip_tags(trim($this->input->post('e_dtm_nro_fojas')));
        $dtm_nro_fojas = ('' == $dtm_nro_fojas) ? null : $dtm_nro_fojas;

        $dtm_beneficiario = strip_tags(trim(strtoupper($this->input->post('e_dtm_beneficiario'))));
        $dtm_descripcion = strip_tags(trim(strtoupper($this->input->post('e_dtm_descripcion'))));
        $dtm_nro_con = strip_tags(trim(strtoupper($this->input->post('e_dtm_nro_con'))));
        $dtm_nro_con = ('' == $dtm_nro_con) ? null : $dtm_nro_con;

        $dtm_monto = strip_tags(trim($this->input->post('e_dtm_monto')));
        $dtm_monto = str_replace(".", "", $dtm_monto);
        $dtm_monto = str_replace(",", ".", $dtm_monto);
        $dtm_monto = (double)$dtm_monto;

        $dtm_estante = strip_tags(trim(strtoupper($this->input->post('e_dtm_estante'))));
        $dtm_estante = ('' == $dtm_estante) ? 'PENDIENTE' : $dtm_estante;

        $dtm_balda = strip_tags(trim($this->input->post('e_dtm_balda')));
        $dtm_balda = ('' == $dtm_balda) ? 'PENDIENTE' : $dtm_balda;

        $dtm_fila = strip_tags(trim($this->input->post('e_dtm_fila')));
        $dtm_fila = ('' == $dtm_fila) ? 'PENDIENTE' : $dtm_fila;

        $dtm_orden_tipo = strip_tags(trim($this->input->post('e_dtm_orden_tipo')));
        $dtm_orden_tipo = ('' == $dtm_orden_tipo) ? null : $dtm_orden_tipo;

        $dtm_orden_cite = strip_tags(trim(strtoupper($this->input->post('e_dtm_orden_cite'))));
        $dtm_orden_cite = ('' == $dtm_orden_cite) ? null : $dtm_orden_cite;

        $dtm_nro_cheque = strip_tags(trim($this->input->post('e_dtm_nro_cheque')));
        $dtm_nro_cheque = ('' == $dtm_nro_cheque) ? null : $dtm_nro_cheque;

        $dtm_sisin = strip_tags(trim(strtoupper($this->input->post('e_dtm_sisin'))));
        $dtm_sisin = ('' == $dtm_sisin) ? null : $dtm_sisin;

        $dtm_cuce = strip_tags(trim(strtoupper($this->input->post('e_dtm_cuce'))));
        $dtm_cuce = ('' == $dtm_cuce) ? '' : $dtm_cuce;

        $dtm_obj_gasto = strip_tags(trim(strtoupper($this->input->post('e_dtm_obj_gasto'))));
        $dtm_obj_gasto = ('' == $dtm_obj_gasto) ? null : $dtm_obj_gasto;

        $dtm_bd = strip_tags(trim($this->input->post('e_dtm_bd')));
        $dtm_bd = ('' == $dtm_bd) ? null : $dtm_bd;

        $dtm_partida_pre = strip_tags(trim($this->input->post('e_dtm_partida_pre')));
        $dtm_partida_pre = ('' == $dtm_partida_pre) ? null : $dtm_partida_pre;

        $dtm_id = strip_tags(trim($this->input->post('dtm_id')));

        if ($this->Dtm_model->verificar_DTM_Nro_Update($dtm_nro, $dtm_gestion)){
            $data = array(
                'dtm_fecha_ingreso' => $dtm_fecha_ingreso,
                'dtm_nro' => $dtm_nro,
                'dtm_gestion' => $dtm_gestion,
                'dtm_correlativo' => $dtm_nro_correlativo,
                'dtm_nro_fojas' => $dtm_nro_fojas,
                'dtm_beneficiario' => $dtm_beneficiario,
                'dtm_descripcion' => $dtm_descripcion,
                'dtm_nro_con' => $dtm_nro_con,
                'dtm_monto' => $dtm_monto,
                'dtm_estante' => $dtm_estante,
                'dtm_balda' => $dtm_balda,
                'dtm_fila' => $dtm_fila,
                'dtm_orden_tipo' => $dtm_orden_tipo,
                'dtm_orden_cite' => $dtm_orden_cite,
                'dtm_nro_cheque' => $dtm_nro_cheque,
                'dtm_sisin' => $dtm_sisin,
                'dtm_cuce' => $dtm_cuce,
                'dtm_obj_gasto' => $dtm_obj_gasto,
                'dtm_bd' => $dtm_bd,
                'dtm_partida_pre' => $dtm_partida_pre
            );
            if ($this->Dtm_model->updateDTM($dtm_id, $data))
                redirect("Admin_control/");
            else
                echo "ya existen los Datos del DTM que desea Registrar";
        } else {
            $data_session = $this->session->all_userdata();
            if (isset($data_session)) {
                if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                    $this->load->view('head', $data_session);
                    date_default_timezone_set('America/La_Paz');
                    $time = time();
                    $fecha_actual = date("Y-m-d", $time);
                    $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                    $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                    $this->load->view('menu_admin',$pre);
                    $this->load->view('ERROR/error_admin');
                } else {
                    redirect('/Login_control/close_session', 'refresh');
                }
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        }
    }
    public function upload_DTM()
    {

        $nFile = htmlspecialchars($_POST['nFile']);
        $dtm_id = htmlspecialchars($_POST['dtm_id']);
        if (!empty($_FILES['dtm_file'] )) {
           if ($_FILES['dtm_file']['type'] == 'application/pdf') {
                if (isset($_FILES['dtm_file'])) {
                    $nombreArchivo = $nFile.".pdf";
                    $destination = './assets/uploads/DTM/' . $nombreArchivo;
                    move_uploaded_file($_FILES['dtm_file']['tmp_name'], $destination);
                    $data = array('dtm_adjuntar' => $nombreArchivo);
                    $this->Dtm_model->uploadDTM($data, $dtm_id);
                    echo "<b>Archivo guardado correctamente</b>";
                }
            }else
            {
                echo "<b>El formato del archivo no está permitido.</b>";
            }
        } else {
            echo "Debe cargar un archivo PDF";
        }
    }
    public function deletePdf_DTM()
    {

        $dtm_id = trim($this->input->post("dtm_id"));
        $dtm_adj = trim($this->input->post("dtm_adjuntar"));

        $data = array('dtm_adjuntar' => null);
        if ($this->Dtm_model->deletePDF_DTM($dtm_id, $data) == true) {
            unlink('./assets/uploads/DTM/'.$dtm_adj);
            echo "Archivo DTM Eliminado";

        }
        else
            echo "El archivo DTM que Desea Eliminar NO EXISTE";
    }

    ////////CI///////////////
    public function ci_index()
    {
        $data_session = $this->session->all_userdata();
        if (isset($data_session)) {
            if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                $this->load->view('head', $data_session);
                date_default_timezone_set('America/La_Paz');
                $time = time();
                $fecha_actual = date("Y-m-d", $time);
                $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                $this->load->view('menu_admin',$pre);
                $this->load->view('CI/ci_admin');
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        } else {
            redirect('/Login_control/close_session', 'refresh');
        }
    }
    public function list_CI()
    {
        $fetch_data = $this->Ci_modelo->make_dataTables();
        $data = array();
        foreach ($fetch_data as $row) {
            $subarray = array();
            $rowComplement = $this->Ci_modelo->list_ciComplement($row->ci_id);
            $subarray[] = '<span style="border-bottom:2px black solid;"><span style="font-size: 12px"><b>CI</b>: <span style="background-color: #FFFF66">' . $row->ci_nro . '</span></span></span><br><b>Gestion:</b> ' . $rowComplement->ci_gestion;
            $pg_date = $rowComplement->ci_fecha_ingreso;
            $date_obj = date_create_from_format('Y-m-d', $pg_date);
            $date = date_format($date_obj, 'd-m-Y');
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>Nro. Correlativo:</b> ' . $row->ci_correlativo . '</span><br><b>Nº Fojas:</b> ' . $rowComplement->ci_nro_fojas . '<br><span  data-role="hint"
            data-hint-text="Fecha de Ingreso" data-hint-position="right"><span class="mif-calendar fg-darkBlue mif-lg"></span> ' . $date . '</span>';
            $monto = number_format($rowComplement->ci_monto, 2, ',', '.');
            $subarray[] = '<span style="border-bottom:2px black solid;"><span class="mif-user-secret fg-darkGreen mif-lg"></span> ' . $row->ci_beneficiario . '</span><br><span  data-role="hint" data-hint-text="Monto" data-hint-position="left"><b>Bs. </b> ' . $monto . '</span><br><b>Nº Cheque: </b> ' . $rowComplement->ci_nro_cheque .
                '<br><b>Nro. Contrato:</b> ' . $rowComplement->ci_nro_con . '</span>';
            $subarray[] = '<b>Por:</b> ' . $row->ci_descripcion;
            $subarray[] = '<ul data-role="listview" data-view-icons="icons-medium">
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="ESTANTE" data-icon="' . base_url() . 'assets/svg/estante-lleno.svg" data-caption="' . $row->ci_estante . '" style="border-bottom:2px black solid;"></li>
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="BALDA"
                                data-icon="' . base_url() . 'assets/svg/balda.svg" data-caption="' . $rowComplement->ci_balda . '"></li>
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="FILA"
                                data-icon="' . base_url() . 'assets/svg/fila.svg" data-caption="' . $rowComplement->ci_fila . '"></li>
                        </ul>';
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>CUCE: </b></span>' . $row->ci_cuce . '<br><b>Obj. de Gasto: </b>' . $rowComplement->ci_obj_gasto;
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>SISIN: </b> ' . $row->ci_sisin . '</span><br><b>Part. Pptria.: </b> ' . $rowComplement->ci_partida_pre;
            if ($row->ci_adjuntar != '')
                $subarray[] = '<a class="file_upload" href="'.base_url().'assets/uploads/CI/'. $row->ci_adjuntar.'" target="_blank"><span class="mif-file-pdf fg-red mif-5x"></span></a><br>
                                <button class="button alert rounded mini drop-shadow" onclick="deletePdfCI('. $row->ci_id .')"><b>X</b> Eliminar PDF</button>';
            else
                $subarray[] = '<button  class="upload_file rounded drop-shadow" onclick="uploadCI('. $row->ci_id .','. $rowComplement->ci_nro .','. $rowComplement->ci_gestion .')"><span class="mif-upload mif-4x"></span></button>';
            $subarray[] = '<button  onclick="editCI(' . $row->ci_id . ')" class="button success cycle drop-shadow"><span class="mif-pencil"></span></button>
                         <button onclick="deleteCI(' . $row->ci_id . ')" class="button alert cycle drop-shadow"><span class="mif-cross"></span></button>';
            $data[] = $subarray;
        }
        $output = array(
            "draw" => intval($_POST["draw"]),
            "recordsTotal" => $this->Ci_modelo->get_all_data(),
            "recordsFiltered" => $this->Ci_modelo->get_filtered_data(),
            "data" => $data
        );
        echo json_encode($output);
    }
    public function deleteCI()
    {
        if ($this->input->is_ajax_request()) {
            $ci_id = trim($this->input->post("ci_id"));
            if ($this->Ci_modelo->delete_CI($ci_id) == true)
                echo "CI Eliminado";
            else
                echo "CI que Desea Eliminar NO EXISTE";

        } else
            show_404();
    }
    public function save_ci()
    {
        $ci_fecha_ingreso = strip_tags(trim($this->input->post('ci_fecha_ingreso')));
        $ci_nro = strip_tags(trim($this->input->post('ci_nro')));
        $ci_gestion = strip_tags(trim($this->input->post('ci_gestion')));
        $ci_nro_correlativo = strip_tags(trim($this->input->post('ci_nro_correlativo')));

        $ci_nro_fojas = strip_tags(trim($this->input->post('ci_nro_fojas')));
        $ci_nro_fojas = ('' == $ci_nro_fojas) ? null : $ci_nro_fojas;

        $ci_beneficiario = strip_tags(trim(strtoupper($this->input->post('ci_beneficiario'))));
        $ci_descripcion = strip_tags(trim(strtoupper($this->input->post('ci_descripcion'))));

        $ci_nro_con = strip_tags(trim(strtoupper($this->input->post('ci_nro_con'))));
        $ci_nro_con = ('' == $ci_nro_con) ? null : $ci_nro_con;

        $ci_monto = strip_tags(trim($this->input->post('ci_monto')));
        $ci_monto = str_replace(".", "", $ci_monto);
        $ci_monto = str_replace(",", ".", $ci_monto);
        $ci_monto = (double)$ci_monto;

        $ci_estante = strip_tags(trim(strtoupper($this->input->post('ci_estante'))));
        $ci_estante = ('' == $ci_estante) ? 'PENDIENTE' : $ci_estante;

        $ci_balda = strip_tags(trim($this->input->post('ci_balda')));
        $ci_balda = ('' == $ci_balda) ? 'PENDIENTE' : $ci_balda;

        $ci_fila = strip_tags(trim($this->input->post('ci_fila')));
        $ci_fila = ('' == $ci_fila) ? 'PENDIENTE' : $ci_fila;

        $ci_nro_cheque = strip_tags(trim($this->input->post('ci_nro_cheque')));
        $ci_nro_cheque = ('' == $ci_nro_cheque) ? null : $ci_nro_cheque;

        $ci_sisin = strip_tags(trim(strtoupper($this->input->post('ci_sisin'))));
        $ci_sisin = ('' == $ci_sisin) ? null : $ci_sisin;

        $ci_cuce = strip_tags(trim(strtoupper($this->input->post('ci_cuce'))));
        $ci_cuce = ('' == $ci_cuce) ? null : $ci_cuce;

        $ci_obj_gasto = strip_tags(trim(strtoupper($this->input->post('ci_obj_gasto'))));
        $ci_obj_gasto = ('' == $ci_obj_gasto) ? null : $ci_obj_gasto;

        $ci_partida_pre = strip_tags(trim(strtoupper($this->input->post('ci_partida_pre'))));
        $ci_partida_pre = ('' == $ci_partida_pre) ? null : $ci_partida_pre;

        if ($this->Ci_modelo->verificar_CI_Nro($ci_nro, $ci_gestion)){
            $data = array(
                'ci_fecha_ingreso' => $ci_fecha_ingreso,
                'ci_nro' => $ci_nro,
                'ci_gestion' => $ci_gestion,
                'ci_correlativo' => $ci_nro_correlativo,
                'ci_nro_fojas' => $ci_nro_fojas,
                'ci_beneficiario' => $ci_beneficiario,
                'ci_descripcion' => $ci_descripcion,
                'ci_nro_con' => $ci_nro_con,
                'ci_monto' => $ci_monto,
                'ci_estante' => $ci_estante,
                'ci_balda' => $ci_balda,
                'ci_fila' => $ci_fila,
                'ci_nro_cheque' => $ci_nro_cheque,
                'ci_sisin' => $ci_sisin,
                'ci_cuce' => $ci_cuce,
                'ci_obj_gasto' => $ci_obj_gasto,
                'ci_partida_pre' => $ci_partida_pre
            );
            if ($this->Ci_modelo->saveCI($data))
                redirect("Admin_control/ci_index");
            else
                echo "Existe los Datos del CI que desea Registrar";
        } else {
            $data_session = $this->session->all_userdata();
            if (isset($data_session)) {
                if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                    $this->load->view('head', $data_session);
                    date_default_timezone_set('America/La_Paz');
                    $time = time();
                    $fecha_actual = date("Y-m-d", $time);
                    $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                    $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                    $this->load->view('menu_admin',$pre);
                    $this->load->view('ERROR/error_admin');
                } else {
                    redirect('/Login_control/close_session', 'refresh');
                }
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        }
    }
    public function get_ci($ci_id)
    {
        $data = $this->Ci_modelo->getCI($ci_id);
        echo json_encode($data);
    }
    public function edit_ci()
    {
        $ci_fecha_ingreso = strip_tags(trim($this->input->post('e_ci_fecha_ingreso')));
        $ci_nro = strip_tags(trim($this->input->post('e_ci_nro')));
        $ci_gestion = strip_tags(trim($this->input->post('e_ci_gestion')));
        $ci_nro_correlativo = strip_tags(trim($this->input->post('e_ci_nro_correlativo')));

        $ci_nro_fojas = strip_tags(trim($this->input->post('e_ci_nro_fojas')));
        $ci_nro_fojas = ('' == $ci_nro_fojas) ? null : $ci_nro_fojas;

        $ci_beneficiario = strip_tags(trim(strtoupper($this->input->post('e_ci_beneficiario'))));
        $ci_descripcion = strip_tags(trim(strtoupper($this->input->post('e_ci_descripcion'))));

        $ci_nro_con = strip_tags(trim(strtoupper($this->input->post('e_ci_nro_con'))));
        $ci_nro_con = ('' == $ci_nro_con) ? null : $ci_nro_con;

        $ci_monto = strip_tags(trim($this->input->post('e_ci_monto')));
        $ci_monto = str_replace(".", "", $ci_monto);
        $ci_monto = str_replace(",", ".", $ci_monto);
        $ci_monto = (double)$ci_monto;

        $ci_estante = strip_tags(trim(strtoupper($this->input->post('e_ci_estante'))));
        $ci_estante = ('' == $ci_estante) ? 'PENDIENTE' : $ci_estante;

        $ci_balda = strip_tags(trim($this->input->post('e_ci_balda')));
        $ci_balda = ('' == $ci_balda) ? 'PENDIENTE' : $ci_balda;

        $ci_fila = strip_tags(trim($this->input->post('e_ci_fila')));
        $ci_fila = ('' == $ci_fila) ? 'PENDIENTE' : $ci_fila;

        $ci_nro_cheque = strip_tags(trim($this->input->post('e_ci_nro_cheque')));
        $ci_nro_cheque = ('' == $ci_nro_cheque) ? null : $ci_nro_cheque;

        $ci_sisin = strip_tags(trim(strtoupper($this->input->post('e_ci_sisin'))));
        $ci_sisin = ('' == $ci_sisin) ? null : $ci_sisin;

        $ci_cuce = strip_tags(trim(strtoupper($this->input->post('e_ci_cuce'))));
        $ci_cuce = ('' == $ci_cuce) ? null : $ci_cuce;

        $ci_obj_gasto = strip_tags(trim(strtoupper($this->input->post('e_ci_obj_gasto'))));
        $ci_obj_gasto = ('' == $ci_obj_gasto) ? null : $ci_obj_gasto;

        $ci_partida_pre = strip_tags(trim(strtoupper($this->input->post('e_ci_partida_pre'))));
        $ci_partida_pre = ('' == $ci_partida_pre) ? null : $ci_partida_pre;

        $ci_id = strip_tags(trim($this->input->post('ci_id')));

        if ($this->Ci_modelo->verificar_CI_Nro_Update($ci_nro, $ci_gestion)){
            $data = array(
                'ci_fecha_ingreso' => $ci_fecha_ingreso,
                'ci_nro' => $ci_nro,
                'ci_gestion' => $ci_gestion,
                'ci_correlativo' => $ci_nro_correlativo,
                'ci_nro_fojas' => $ci_nro_fojas,
                'ci_beneficiario' => $ci_beneficiario,
                'ci_descripcion' => $ci_descripcion,
                'ci_nro_con' => $ci_nro_con,
                'ci_monto' => $ci_monto,
                'ci_estante' => $ci_estante,
                'ci_balda' => $ci_balda,
                'ci_fila' => $ci_fila,
                'ci_nro_cheque' => $ci_nro_cheque,
                'ci_sisin' => $ci_sisin,
                'ci_cuce' => $ci_cuce,
                'ci_obj_gasto' => $ci_obj_gasto,
                'ci_partida_pre' => $ci_partida_pre
            );
            if ($this->Ci_modelo->updateCI($ci_id, $data))
                redirect("Admin_control/ci_index");
            else
                echo "Existe los Datos del CI que desea Registrar";
        } else {
            $data_session = $this->session->all_userdata();
            if (isset($data_session)) {
                if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                    $this->load->view('head', $data_session);
                    date_default_timezone_set('America/La_Paz');
                    $time = time();
                    $fecha_actual = date("Y-m-d", $time);
                    $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                    $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                    $this->load->view('menu_admin',$pre);
                    $this->load->view('ERROR/error_admin');
                } else {
                    redirect('/Login_control/close_session', 'refresh');
                }
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        }
    }
    public function upload_CI()
    {

        $nFile = htmlspecialchars($_POST['nFile']);
        $ci_id = htmlspecialchars($_POST['ci_id']);
        if (!empty($_FILES['ci_file'] )) {
            if ($_FILES['ci_file']['type'] == 'application/pdf') {
                if (isset($_FILES['ci_file'])) {
                    $nombreArchivo = $nFile.".pdf";
                    $destination = './assets/uploads/CI/' . $nombreArchivo;
                    move_uploaded_file($_FILES['ci_file']['tmp_name'], $destination);
                    $data = array('ci_adjuntar' => $nombreArchivo);
                    $this->Ci_modelo->uploadCI($data, $ci_id);
                    echo "<b>Archivo guardado correctamente</b>";
                }
            }else
            {
                echo "<b>El formato del archivo no está permitido.</b>";
            }
        } else {
            echo "Debe cargar un archivo PDF";
        }
    }
    public function deletePdf_CI()
    {

        $ci_id = trim($this->input->post("ci_id"));
        $ci_adj = trim($this->input->post("ci_adjuntar"));

        $data = array('ci_adjuntar' => null);
        if ($this->Ci_modelo->deletePDF_CI($ci_id, $data) == true) {
            unlink('./assets/uploads/CI/'.$ci_adj);
            echo "Archivo CI Eliminado";

        }
        else
            echo "El archivo CI que Desea Eliminar NO EXISTE";
    }

    ////////CD///////////////
    public function cd_index()
    {
        $data_session = $this->session->all_userdata();
        if (isset($data_session)) {
            if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                $this->load->view('head', $data_session);
                date_default_timezone_set('America/La_Paz');
                $time = time();
                $fecha_actual = date("Y-m-d", $time);
                $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                $this->load->view('menu_admin',$pre);
                $this->load->view('CD/cd_admin');
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        } else {
            redirect('/Login_control/close_session', 'refresh');
        }
    }
    public function list_CD()
    {
        $fetch_data = $this->Cd_model->make_dataTables();
        $data = array();
        foreach ($fetch_data as $row) {
            $subarray = array();
            $rowComplement = $this->Cd_model->list_cdComplement($row->cd_id);
            $subarray[] = '<span style="border-bottom:2px black solid;"><span style="font-size: 12px"><b>CD</b>: <span style="background-color: #FFFF66">' . $row->cd_nro . '</span></span></span><br><b>Gestion:</b> ' . $rowComplement->cd_gestion;
            $pg_date = $rowComplement->cd_fecha_ingreso;
            $date_obj = date_create_from_format('Y-m-d', $pg_date);
            $date = date_format($date_obj, 'd-m-Y');
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>Nro. Correlativo:</b> ' . $row->cd_correlativo . '</span><br><b>Nº Fojas:</b> ' . $rowComplement->cd_nro_fojas . '<br><span  data-role="hint"
            data-hint-text="Fecha de Ingreso" data-hint-position="right"><span class="mif-calendar fg-darkBlue mif-lg"></span> ' . $date . '</span>';
            $monto = number_format($rowComplement->cd_monto, 2, ',', '.');
            $subarray[] = '<span style="border-bottom:2px black solid;"><span class="mif-user-secret fg-darkGreen mif-lg"><span  data-role="hint" data-hint-text="Beneficiario" data-hint-position="right"></span></span> ' . $row->cd_beneficiario . '</span></span><br><span  data-role="hint" data-hint-text="Monto" data-hint-position="left"><b>Bs. </b> ' . $monto . '</span><br><b>Nº Cheque: </b> ' . $rowComplement->cd_nro_cheque
                .'<br><b>Nro. Contrato:</b> ' . $rowComplement->cd_nro_con;
            $subarray[] = '<b>Por:</b> ' . $row->cd_descripcion;
            $subarray[] = '<ul data-role="listview" data-view-icons="icons-medium">
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="ESTANTE" data-icon="' . base_url() . 'assets/svg/estante-lleno.svg" data-caption="' . $row->cd_estante . '" style="border-bottom:2px black solid;"></li>
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="BALDA"
                                data-icon="' . base_url() . 'assets/svg/balda.svg" data-caption="' . $rowComplement->cd_balda . '"></li>
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="FILA"
                                data-icon="' . base_url() . 'assets/svg/fila.svg" data-caption="' . $rowComplement->cd_fila . '"></li>
                        </ul>';
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>CUCE: </b>' . $row->cd_cuce . '</span><br><b>Cite/Nro:</b> ' . $rowComplement->cd_orden_cite . '<br><b>ORDEN DE </b>' . $rowComplement->cd_orden_tipo .
                '<br><b>BD: </b> ' . $rowComplement->cd_bd;
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>SISIN: </b> ' . $row->cd_sisin . '</span><br><b>Part. Pptaria.: </b> ' . $rowComplement->cd_partida_pre . '<br><b>Objeto de gasto: </b> ' . $rowComplement->cd_obj_gasto;
            if ($row->cd_adjuntar != '')
                $subarray[] = '<a class="file_upload" href="'.base_url().'assets/uploads/CD/'. $row->cd_adjuntar.'" target="_blank"><span class="mif-file-pdf fg-red mif-5x"></span></a><br>
                                <button class="button alert rounded mini drop-shadow" onclick="deletePdfCD('. $row->cd_id .')"><b>X</b> Elimniar PDF</button>';
            else
                $subarray[] = '<button  class="upload_file rounded drop-shadow" onclick="uploadCD('. $row->cd_id .','. $rowComplement->cd_nro .','. $rowComplement->cd_gestion .')"><span class="mif-upload mif-4x"></span></button>';
            $subarray[] = '<button  onclick="editCD(' . $row->cd_id . ')" class="button success cycle drop-shadow"><span class="mif-pencil"></span></button>
                         <button onclick="deleteCD(' . $row->cd_id . ')" class="button alert cycle drop-shadow"><span class="mif-cross"></span></button>';
            $data[] = $subarray;
        }
        $output = array(
            "draw" => intval($_POST["draw"]),
            "recordsTotal" => $this->Cd_model->get_all_data(),
            "recordsFiltered" => $this->Cd_model->get_filtered_data(),
            "data" => $data
        );
        echo json_encode($output);
    }
    public function deleteCD()
    {
        if ($this->input->is_ajax_request()) {
            $cd_id = trim($this->input->post("cd_id"));
            if ($this->Cd_model->delete_CD($cd_id) == true)
                echo "CD Eliminado";
            else
                echo "CD que Desea Eliminar NO EXISTE";

        } else
            show_404();
    }
    public function save_cd()
    {
        $cd_fecha_ingreso = strip_tags(trim($this->input->post('cd_fecha_ingreso')));
        $cd_nro = strip_tags(trim($this->input->post('cd_nro')));
        $cd_gestion = strip_tags(trim($this->input->post('cd_gestion')));
        $cd_nro_correlativo = strip_tags(trim($this->input->post('cd_nro_correlativo')));

        $cd_nro_fojas = strip_tags(trim($this->input->post('cd_nro_fojas')));
        $cd_nro_fojas = ('' == $cd_nro_fojas) ? null : $cd_nro_fojas;

        $cd_beneficiario = strip_tags(trim(strtoupper($this->input->post('cd_beneficiario'))));
        $cd_descripcion = strip_tags(trim(strtoupper($this->input->post('cd_descripcion'))));

        $cd_nro_con = strip_tags(trim(strtoupper($this->input->post('cd_nro_con'))));
        $cd_nro_con = ('' == $cd_nro_con) ? null : $cd_nro_con;

        $cd_monto = strip_tags(trim($this->input->post('cd_monto')));
        $cd_monto = str_replace(".", "", $cd_monto);
        $cd_monto = str_replace(",", ".", $cd_monto);
        $cd_monto = (double)$cd_monto;

        $cd_estante = strip_tags(trim(strtoupper($this->input->post('cd_estante'))));
        $cd_estante = ('' == $cd_estante) ? 'PENDIENTE' : $cd_estante;

        $cd_balda = strip_tags(trim($this->input->post('cd_balda')));
        $cd_balda = ('' == $cd_balda) ? 'PENDIENTE' : $cd_balda;

        $cd_fila = strip_tags(trim($this->input->post('cd_fila')));
        $cd_fila = ('' == $cd_fila) ? 'PENDIENTE' : $cd_fila;

        $cd_orden_tipo = strip_tags(trim($this->input->post('cd_orden_tipo')));
        $cd_orden_tipo = ('' == $cd_orden_tipo) ? null : $cd_orden_tipo;

        $cd_orden_cite = strip_tags(trim(strtoupper($this->input->post('cd_orden_cite'))));
        $cd_orden_cite = ('' == $cd_orden_cite) ? null : $cd_orden_cite;

        $cd_nro_cheque = strip_tags(trim($this->input->post('cd_nro_cheque')));
        $cd_nro_cheque = ('' == $cd_nro_cheque) ? null : $cd_nro_cheque;

        $cd_sisin = strip_tags(trim(strtoupper($this->input->post('cd_sisin'))));
        $cd_sisin = ('' == $cd_sisin) ? null : $cd_sisin;

        $cd_cuce = strip_tags(trim(strtoupper($this->input->post('cd_cuce'))));
        $cd_cuce = ('' == $cd_cuce) ? null : $cd_cuce;

        $cd_obj_gasto = strip_tags(trim(strtoupper($this->input->post('cd_obj_gasto'))));
        $cd_obj_gasto = ('' == $cd_obj_gasto) ? null : $cd_obj_gasto;

        $cd_bd = strip_tags(trim($this->input->post('cd_bd')));
        $cd_bd = ('' == $cd_bd) ? null : $cd_bd;

        $cd_partida_pre = strip_tags(trim(strtoupper($this->input->post('cd_partida_pre'))));
        $cd_partida_pre = ('' == $cd_partida_pre) ? null : $cd_partida_pre;

        if ($this->Cd_model->verificar_CD_Nro($cd_nro, $cd_gestion)){
            $data = array(
                'cd_fecha_ingreso' => $cd_fecha_ingreso,
                'cd_nro' => $cd_nro,
                'cd_gestion' => $cd_gestion,
                'cd_correlativo' => $cd_nro_correlativo,
                'cd_nro_fojas' => $cd_nro_fojas,
                'cd_beneficiario' => $cd_beneficiario,
                'cd_descripcion' => $cd_descripcion,
                'cd_nro_con' => $cd_nro_con,
                'cd_monto' => $cd_monto,
                'cd_estante' => $cd_estante,
                'cd_balda' => $cd_balda,
                'cd_fila' => $cd_fila,
                'cd_orden_tipo' => $cd_orden_tipo,
                'cd_orden_cite' => $cd_orden_cite,
                'cd_nro_cheque' => $cd_nro_cheque,
                'cd_sisin' => $cd_sisin,
                'cd_cuce' => $cd_cuce,
                'cd_obj_gasto' => $cd_obj_gasto,
                'cd_bd' => $cd_bd,
                'cd_partida_pre' => $cd_partida_pre
            );
            if ($this->Cd_model->saveCD($data))
                redirect("Admin_control/cd_index");
            else
                echo "Existe los Datos del CD que desea Registrar";
        } else {
            $data_session = $this->session->all_userdata();
            if (isset($data_session)) {
                if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                    $this->load->view('head', $data_session);
                    date_default_timezone_set('America/La_Paz');
                    $time = time();
                    $fecha_actual = date("Y-m-d", $time);
                    $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                    $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                    $this->load->view('menu_admin',$pre);
                    $this->load->view('ERROR/error_admin');
                } else {
                    redirect('/Login_control/close_session', 'refresh');
                }
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        }
    }
    public function get_cd($cd_id)
    {
        $data = $this->Cd_model->getCD($cd_id);
        echo json_encode($data);
    }
    public function edit_cd()
    {
        $cd_fecha_ingreso = strip_tags(trim($this->input->post('e_cd_fecha_ingreso')));
        $cd_nro = strip_tags(trim($this->input->post('e_cd_nro')));
        $cd_gestion = strip_tags(trim($this->input->post('e_cd_gestion')));
        $cd_nro_correlativo = strip_tags(trim($this->input->post('e_cd_nro_correlativo')));

        $cd_nro_fojas = strip_tags(trim($this->input->post('e_cd_nro_fojas')));
        $cd_nro_fojas = ('' == $cd_nro_fojas) ? null : $cd_nro_fojas;

        $cd_beneficiario = strip_tags(trim(strtoupper($this->input->post('e_cd_beneficiario'))));
        $cd_descripcion = strip_tags(trim(strtoupper($this->input->post('e_cd_descripcion'))));

        $cd_nro_con = strip_tags(trim(strtoupper($this->input->post('e_cd_nro_con'))));
        $cd_nro_con = ('' == $cd_nro_con) ? null : $cd_nro_con;

        $cd_monto = strip_tags(trim($this->input->post('e_cd_monto')));
        $cd_monto = str_replace(".", "", $cd_monto);
        $cd_monto = str_replace(",", ".", $cd_monto);
        $cd_monto = (double)$cd_monto;

        $cd_estante = strip_tags(trim(strtoupper($this->input->post('e_cd_estante'))));
        $cd_estante = ('' == $cd_estante) ? 'PENDIENTE' : $cd_estante;

        $cd_balda = strip_tags(trim($this->input->post('e_cd_balda')));
        $cd_balda = ('' == $cd_balda) ? 'PENDIENTE' : $cd_balda;

        $cd_fila = strip_tags(trim($this->input->post('e_cd_fila')));
        $cd_fila = ('' == $cd_fila) ? 'PENDIENTE' : $cd_fila;

        $cd_orden_tipo = strip_tags(trim($this->input->post('e_cd_orden_tipo')));
        $cd_orden_tipo = ('' == $cd_orden_tipo) ? null : $cd_orden_tipo;

        $cd_orden_cite = strip_tags(trim(strtoupper($this->input->post('e_cd_orden_cite'))));
        $cd_orden_cite = ('' == $cd_orden_cite) ? null : $cd_orden_cite;

        $cd_nro_cheque = strip_tags(trim($this->input->post('e_cd_nro_cheque')));
        $cd_nro_cheque = ('' == $cd_nro_cheque) ? null : $cd_nro_cheque;

        $cd_sisin = strip_tags(trim(strtoupper($this->input->post('e_cd_sisin'))));
        $cd_sisin = ('' == $cd_sisin) ? null : $cd_sisin;

        $cd_cuce = strip_tags(trim(strtoupper($this->input->post('e_cd_cuce'))));
        $cd_cuce = ('' == $cd_cuce) ? null : $cd_cuce;

        $cd_obj_gasto = strip_tags(trim(strtoupper($this->input->post('e_cd_obj_gasto'))));
        $cd_obj_gasto = ('' == $cd_obj_gasto) ? null : $cd_obj_gasto;

        $cd_bd = strip_tags(trim($this->input->post('e_cd_bd')));
        $cd_bd = ('' == $cd_bd) ? null : $cd_bd;

        $cd_partida_pre = strip_tags(trim(strtoupper($this->input->post('e_cd_partida_pre'))));
        $cd_partida_pre = ('' == $cd_partida_pre) ? null : $cd_partida_pre;

        $cd_id = strip_tags(trim($this->input->post('cd_id')));

        if ($this->Cd_model->verificar_CD_Nro_Update($cd_nro, $cd_gestion)){
            $data = array(
                'cd_fecha_ingreso' => $cd_fecha_ingreso,
                'cd_nro' => $cd_nro,
                'cd_gestion' => $cd_gestion,
                'cd_correlativo' => $cd_nro_correlativo,
                'cd_nro_fojas' => $cd_nro_fojas,
                'cd_beneficiario' => $cd_beneficiario,
                'cd_descripcion' => $cd_descripcion,
                'cd_nro_con' => $cd_nro_con,
                'cd_monto' => $cd_monto,
                'cd_estante' => $cd_estante,
                'cd_balda' => $cd_balda,
                'cd_fila' => $cd_fila,
                'cd_orden_tipo' => $cd_orden_tipo,
                'cd_orden_cite' => $cd_orden_cite,
                'cd_nro_cheque' => $cd_nro_cheque,
                'cd_sisin' => $cd_sisin,
                'cd_cuce' => $cd_cuce,
                'cd_obj_gasto' => $cd_obj_gasto,
                'cd_bd' => $cd_bd,
                'cd_partida_pre' => $cd_partida_pre
            );
            if ($this->Cd_model->updateCD($cd_id, $data))
                redirect("Admin_control/cd_index");
            else
                echo "Existe los Datos del CIDque desea Registrar";
        } else {
            $data_session = $this->session->all_userdata();
            if (isset($data_session)) {
                if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                    $this->load->view('head', $data_session);
                    date_default_timezone_set('America/La_Paz');
                    $time = time();
                    $fecha_actual = date("Y-m-d", $time);
                    $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                    $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                    $this->load->view('menu_admin',$pre);
                    $this->load->view('ERROR/error_admin');
                } else {
                    redirect('/Login_control/close_session', 'refresh');
                }
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        }
    }
    public function upload_CD()
    {
        $nFile = htmlspecialchars($_POST['nFile']);
        $cd_id = htmlspecialchars($_POST['cd_id']);
        if (!empty($_FILES['cd_file'] )) {
            if ($_FILES['cd_file']['type'] == 'application/pdf') {
                if (isset($_FILES['cd_file'])) {
                    $nombreArchivo = $nFile.".pdf";
                    $destination = './assets/uploads/CD/' . $nombreArchivo;
                    move_uploaded_file($_FILES['cd_file']['tmp_name'], $destination);
                    $data = array('cd_adjuntar' => $nombreArchivo);
                    $this->Cd_model->uploadCD($data, $cd_id);
                    echo "<b>Archivo guardado correctamente</b>";
                }
            }else
            {
                echo "<b>El formato del archivo no está permitido.</b>";
            }
        } else {
            echo "Debe cargar un archivo PDF";
        }
    }
    public function deletePdf_CD()
    {

        $cd_id = trim($this->input->post("cd_id"));
        $cd_adj = trim($this->input->post("cd_adjuntar"));

        $data = array('cd_adjuntar' => null);
        if ($this->Cd_model->deletePDF_CD($cd_id, $data) == true) {
            unlink('./assets/uploads/CD/'.$cd_adj);
            echo "Archivo CD Eliminado";

        }
        else
            echo "El archivo CD que Desea Eliminar NO EXISTE";
    }

    ////////CDAF///////////////
    public function cdaf_index()
    {
        $data_session = $this->session->all_userdata();
        if (isset($data_session)) {
            if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                $this->load->view('head', $data_session);
                date_default_timezone_set('America/La_Paz');
                $time = time();
                $fecha_actual = date("Y-m-d", $time);
                $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                $this->load->view('menu_admin',$pre);
                $this->load->view('CDAF/cdaf_admin');
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        } else {
            redirect('/Login_control/close_session', 'refresh');
        }
    }
    public function list_CDAF()
    {
        $fetch_data = $this->Cdaf_model->make_dataTables();
        $data = array();
        foreach ($fetch_data as $row) {
            $subarray = array();
            $rowComplement = $this->Cdaf_model->list_cdafComplement($row->cdaf_id);
            $subarray[] = '<span style="border-bottom:2px black solid;"><span style="font-size: 12px"><b>CDAF</b>: <span style="background-color: #FFFF66">' . $row->cdaf_nro . '</span></span></span><br><b>Gestion:</b> ' . $rowComplement->cdaf_gestion;
                $pg_date = $rowComplement->cdaf_fecha_ingreso;
                $date_obj = date_create_from_format('Y-m-d', $pg_date);
                $date = date_format($date_obj, 'd-m-Y');
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>Nro. Correlativo:</b> ' . $row->cdaf_correlativo . '</span><br><b>Nº Fojas:</b> ' . $rowComplement->cdaf_nro_fojas . '<br><span  data-role="hint"
            data-hint-text="Fecha de Ingreso" data-hint-position="right"><span class="mif-calendar fg-darkBlue mif-lg"></span> ' . $date . '</span>';
            $monto = number_format($rowComplement->cdaf_monto, 2, ',', '.');
            $subarray[] = '<span style="border-bottom:2px black solid;"><span class="mif-user-secret fg-darkGreen mif-lg"></span> ' . $row->cdaf_beneficiario . '</span><br><span  data-role="hint" data-hint-text="Monto" data-hint-position="left"><b>Bs. </b> ' . $monto . '</span><br><b>Nº Cheque: </b> ' . $rowComplement->cdaf_nro_cheque
                .'<br><b>Nro. Contrato:</b> ' . $rowComplement->cdaf_nro_con;
            $subarray[] = '<b>Por:</b> ' . $row->cdaf_descripcion;
            $subarray[] = '<ul data-role="listview" data-view-icons="icons-medium">
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="ESTANTE" data-icon="' . base_url() . 'assets/svg/estante-lleno.svg" data-caption="' . $row->cdaf_estante . '" style="border-bottom:2px black solid;"></li>
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="BALDA"
                                data-icon="' . base_url() . 'assets/svg/balda.svg" data-caption="' . $rowComplement->cdaf_balda . '"></li>
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="FILA"
                                data-icon="' . base_url() . 'assets/svg/fila.svg" data-caption="' . $rowComplement->cdaf_fila . '"></li>
                        </ul>';
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>CUCE: </b>' . $row->cdaf_cuce . '</span><br><b>Cite/Nro:</b> ' . $rowComplement->cdaf_orden_cite . '<br><b>ORDEN DE </b>' . $rowComplement->cdaf_orden_tipo .
                '<br><b>BD: </b> ' . $rowComplement->cdaf_bd;
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>SISIN: </b> ' . $row->cdaf_sisin . '</span><br><b>Part. Pptaria.: </b> ' . $rowComplement->cdaf_partida_pre . '<br><b>Objeto de gasto: </b> ' . $rowComplement->cdaf_obj_gasto;
            if ($row->cdaf_adjuntar != '') {
                $subarray[] = '<a class="file_upload" href="'.base_url().'assets/uploads/CDAF/'. $row->cdaf_adjuntar.'" target="_blank"><span class="mif-file-pdf fg-red mif-5x"></span></a><br>
                                <button class="button alert rounded mini drop-shadow" onclick="deletePdfCDAF('. $row->cdaf_id .')"><b>X</b> Elimniar PDF</button>';
            }
            else {
                $subarray[] = '<button  class="upload_file rounded drop-shadow" onclick="uploadCDAF('. $row->cdaf_id .','. $rowComplement->cdaf_nro .','. $rowComplement->cdaf_gestion .')"><span class="mif-upload mif-4x"></span></button>';
            }
            $subarray[] = '<button onclick="editCDAF(' . $row->cdaf_id . ')" class="button success cycle drop-shadow"><span class="mif-pencil"></span></button>
                          <button onclick="deleteCDAF(' . $row->cdaf_id . ')" class="button alert cycle drop-shadow"><span class="mif-cross"></span></button>';

            $data[] = $subarray;
        }
        $output = array(
            "draw" => intval($_POST["draw"]),
            "recordsTotal" => $this->Cdaf_model->get_all_data(),
            "recordsFiltered" => $this->Cdaf_model->get_filtered_data(),
            "data" => $data
        );
        echo json_encode($output);
    }
    public function deleteCDAF()
    {
        if ($this->input->is_ajax_request()) {
            $cdaf_id = trim($this->input->post("cdaf_id"));
            if ($this->Cdaf_model->delete_CDAF($cdaf_id) == true)
                echo "CDAF Eliminado";
            else
                echo "CDAF que Desea Eliminar NO EXISTE";

        } else
            show_404();
    }
    public function save_cdaf()
    {
        $cdaf_fecha_ingreso = strip_tags(trim($this->input->post('cdaf_fecha_ingreso')));
        $cdaf_nro = strip_tags(trim($this->input->post('cdaf_nro')));
        $cdaf_gestion = strip_tags(trim($this->input->post('cdaf_gestion')));
        $cdaf_nro_correlativo = strip_tags(trim($this->input->post('cdaf_nro_correlativo')));

        $cdaf_nro_fojas = strip_tags(trim($this->input->post('cdaf_nro_fojas')));
        $cdaf_nro_fojas = ('' == $cdaf_nro_fojas) ? null : $cdaf_nro_fojas;

        $cdaf_beneficiario = strip_tags(trim(strtoupper($this->input->post('cdaf_beneficiario'))));
        $cdaf_descripcion = strip_tags(trim(strtoupper($this->input->post('cdaf_descripcion'))));

        $cdaf_nro_con = strip_tags(trim(strtoupper($this->input->post('cdaf_nro_con'))));
        $cdaf_nro_con = ('' == $cdaf_nro_con) ? null : $cdaf_nro_con;

        $cdaf_monto = strip_tags(trim($this->input->post('cdaf_monto')));
        $cdaf_monto = str_replace(".", "", $cdaf_monto);
        $cdaf_monto = str_replace(",", ".", $cdaf_monto);
        $cdaf_monto = (double)$cdaf_monto;

        $cdaf_estante = strip_tags(trim(strtoupper($this->input->post('cdaf_estante'))));
        $cdaf_estante = ('' == $cdaf_estante) ? 'PENDIENTE' : $cdaf_estante;

        $cdaf_balda = strip_tags(trim($this->input->post('cdaf_balda')));
        $cdaf_balda = ('' == $cdaf_balda) ? 'PENDIENTE' : $cdaf_balda;

        $cdaf_fila = strip_tags(trim($this->input->post('cdaf_fila')));
        $cdaf_fila = ('' == $cdaf_fila) ? 'PENDIENTE' : $cdaf_fila;

        $cdaf_orden_tipo = strip_tags(trim($this->input->post('cdaf_orden_tipo')));
        $cdaf_orden_tipo = ('' == $cdaf_orden_tipo) ? null : $cdaf_orden_tipo;

        $cdaf_orden_cite = strip_tags(trim(strtoupper($this->input->post('cdaf_orden_cite'))));
        $cdaf_orden_cite = ('' == $cdaf_orden_cite) ? null : $cdaf_orden_cite;

        $cdaf_nro_cheque = strip_tags(trim($this->input->post('cdaf_nro_cheque')));
        $cdaf_nro_cheque = ('' == $cdaf_nro_cheque) ? null : $cdaf_nro_cheque;

        $cdaf_sisin = strip_tags(trim(strtoupper($this->input->post('cdaf_sisin'))));
        $cdaf_sisin = ('' == $cdaf_sisin) ? null : $cdaf_sisin;

        $cdaf_cuce = strip_tags(trim(strtoupper($this->input->post('cdaf_cuce'))));
        $cdaf_cuce = ('' == $cdaf_cuce) ? null : $cdaf_cuce;

        $cdaf_obj_gasto = strip_tags(trim(strtoupper($this->input->post('cdaf_obj_gasto'))));
        $cdaf_obj_gasto = ('' == $cdaf_obj_gasto) ? null : $cdaf_obj_gasto;

        $cdaf_bd = strip_tags(trim($this->input->post('cdaf_bd')));
        $cdaf_bd = ('' == $cdaf_bd) ? null : $cdaf_bd;

        $cdaf_partida_pre = strip_tags(trim($this->input->post('cdaf_partida_pre')));
        $cdaf_partida_pre = ('' == $cdaf_partida_pre) ? null : $cdaf_partida_pre;

        if ($this->Cdaf_model->verificar_CDAF_Nro($cdaf_nro, $cdaf_gestion)){
            $data = array(
                'cdaf_fecha_ingreso' => $cdaf_fecha_ingreso,
                'cdaf_nro' => $cdaf_nro,
                'cdaf_gestion' => $cdaf_gestion,
                'cdaf_correlativo' => $cdaf_nro_correlativo,
                'cdaf_nro_fojas' => $cdaf_nro_fojas,
                'cdaf_beneficiario' => $cdaf_beneficiario,
                'cdaf_descripcion' => $cdaf_descripcion,
                'cdaf_nro_con' => $cdaf_nro_con,
                'cdaf_monto' => $cdaf_monto,
                'cdaf_estante' => $cdaf_estante,
                'cdaf_balda' => $cdaf_balda,
                'cdaf_fila' => $cdaf_fila,
                'cdaf_orden_tipo' => $cdaf_orden_tipo,
                'cdaf_orden_cite' => $cdaf_orden_cite,
                'cdaf_nro_cheque' => $cdaf_nro_cheque,
                'cdaf_sisin' => $cdaf_sisin,
                'cdaf_cuce' => $cdaf_cuce,
                'cdaf_obj_gasto' => $cdaf_obj_gasto,
                'cdaf_bd' => $cdaf_bd,
                'cdaf_partida_pre' => $cdaf_partida_pre
            );
            if ($this->Cdaf_model->saveCDAF($data))
                redirect("Admin_control/cdaf_index");
            else
                echo "Existe los Datos del CDAF que desea Registrar";
        } else {
            $data_session = $this->session->all_userdata();
            if (isset($data_session)) {
                if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                    $this->load->view('head', $data_session);
                    date_default_timezone_set('America/La_Paz');
                    $time = time();
                    $fecha_actual = date("Y-m-d", $time);
                    $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                    $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                    $this->load->view('menu_admin',$pre);
                    $this->load->view('ERROR/error_admin');
                } else {
                    redirect('/Login_control/close_session', 'refresh');
                }
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        }
    }
    public function get_cdaf($cdaf_id)
    {
        $data = $this->Cdaf_model->getCDAF($cdaf_id);
        echo json_encode($data);
    }
    public function edit_cdaf()
    {
        $cdaf_fecha_ingreso = strip_tags(trim($this->input->post('e_cdaf_fecha_ingreso')));
        $cdaf_nro = strip_tags(trim($this->input->post('e_cdaf_nro')));
        $cdaf_gestion = strip_tags(trim($this->input->post('e_cdaf_gestion')));
        $cdaf_nro_correlativo = strip_tags(trim($this->input->post('e_cdaf_nro_correlativo')));

        $cdaf_nro_fojas = strip_tags(trim($this->input->post('e_cdaf_nro_fojas')));
        $cdaf_nro_fojas = ('' == $cdaf_nro_fojas) ? null : $cdaf_nro_fojas;

        $cdaf_beneficiario = strip_tags(trim(strtoupper($this->input->post('e_cdaf_beneficiario'))));
        $cdaf_descripcion = strip_tags(trim(strtoupper($this->input->post('e_cdaf_descripcion'))));

        $cdaf_nro_con = strip_tags(trim(strtoupper($this->input->post('e_cdaf_nro_con'))));
        $cdaf_nro_con = ('' == $cdaf_nro_con) ? null : $cdaf_nro_con;

        $cdaf_monto = strip_tags(trim($this->input->post('e_cdaf_monto')));
        $cdaf_monto = str_replace(".", "", $cdaf_monto);
        $cdaf_monto = str_replace(",", ".", $cdaf_monto);
        $cdaf_monto = (double)$cdaf_monto;

        $cdaf_estante = strip_tags(trim(strtoupper($this->input->post('e_cdaf_estante'))));
        $cdaf_estante = ('' == $cdaf_estante) ? 'PENDIENTE' : $cdaf_estante;

        $cdaf_balda = strip_tags(trim($this->input->post('e_cdaf_balda')));
        $cdaf_balda = ('' == $cdaf_balda) ? 'PENDIENTE' : $cdaf_balda;

        $cdaf_fila = strip_tags(trim($this->input->post('e_cdaf_fila')));
        $cdaf_fila = ('' == $cdaf_fila) ? 'PENDIENTE' : $cdaf_fila;

        $cdaf_orden_tipo = strip_tags(trim($this->input->post('e_cdaf_orden_tipo')));
        $cdaf_orden_tipo = ('' == $cdaf_orden_tipo) ? null : $cdaf_orden_tipo;

        $cdaf_orden_cite = strip_tags(trim(strtoupper($this->input->post('e_cdaf_orden_cite'))));
        $cdaf_orden_cite = ('' == $cdaf_orden_cite) ? null : $cdaf_orden_cite;

        $cdaf_nro_cheque = strip_tags(trim($this->input->post('e_cdaf_nro_cheque')));
        $cdaf_nro_cheque = ('' == $cdaf_nro_cheque) ? null : $cdaf_nro_cheque;

        $cdaf_sisin = strip_tags(trim(strtoupper($this->input->post('e_cdaf_sisin'))));
        $cdaf_sisin = ('' == $cdaf_sisin) ? null : $cdaf_sisin;

        $cdaf_cuce = strip_tags(trim(strtoupper($this->input->post('e_cdaf_cuce'))));
        $cdaf_cuce = ('' == $cdaf_cuce) ? null : $cdaf_cuce;

        $cdaf_obj_gasto = strip_tags(trim(strtoupper($this->input->post('e_cdaf_obj_gasto'))));
        $cdaf_obj_gasto = ('' == $cdaf_obj_gasto) ? null : $cdaf_obj_gasto;

        $cdaf_bd = strip_tags(trim($this->input->post('e_cdaf_bd')));
        $cdaf_bd = ('' == $cdaf_bd) ? null : $cdaf_bd;

        $cdaf_partida_pre = strip_tags(trim($this->input->post('e_cdaf_partida_pre')));
        $cdaf_partida_pre = ('' == $cdaf_partida_pre) ? null : $cdaf_partida_pre;

        $cdaf_id = strip_tags(trim($this->input->post('cdaf_id')));

        if ($this->Cdaf_model->verificar_CDAF_Nro_Update($cdaf_nro, $cdaf_gestion)){
            $data = array(
                'cdaf_fecha_ingreso' => $cdaf_fecha_ingreso,
                'cdaf_nro' => $cdaf_nro,
                'cdaf_gestion' => $cdaf_gestion,
                'cdaf_correlativo' => $cdaf_nro_correlativo,
                'cdaf_nro_fojas' => $cdaf_nro_fojas,
                'cdaf_beneficiario' => $cdaf_beneficiario,
                'cdaf_descripcion' => $cdaf_descripcion,
                'cdaf_nro_con' => $cdaf_nro_con,
                'cdaf_monto' => $cdaf_monto,
                'cdaf_estante' => $cdaf_estante,
                'cdaf_balda' => $cdaf_balda,
                'cdaf_fila' => $cdaf_fila,
                'cdaf_orden_tipo' => $cdaf_orden_tipo,
                'cdaf_orden_cite' => $cdaf_orden_cite,
                'cdaf_nro_cheque' => $cdaf_nro_cheque,
                'cdaf_sisin' => $cdaf_sisin,
                'cdaf_cuce' => $cdaf_cuce,
                'cdaf_obj_gasto' => $cdaf_obj_gasto,
                'cdaf_bd' => $cdaf_bd,
                'cdaf_partida_pre' => $cdaf_partida_pre
            );
            if ($this->Cdaf_model->updateCDAF($cdaf_id, $data))
                redirect("Admin_control/cdaf_index");
            else
                echo "Existe los Datos del CDAF que desea Registrar";
        } else {
            $data_session = $this->session->all_userdata();
            if (isset($data_session)) {
                if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                    $this->load->view('head', $data_session);
                    date_default_timezone_set('America/La_Paz');
                    $time = time();
                    $fecha_actual = date("Y-m-d", $time);
                    $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                    $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                    $this->load->view('menu_admin',$pre);
                    $this->load->view('ERROR/error_admin');
                } else {
                    redirect('/Login_control/close_session', 'refresh');
                }
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        }
    }
    public function upload_CDAF()
    {
        $nFile = htmlspecialchars($_POST['nFile']);
        $cdaf_id = htmlspecialchars($_POST['cdaf_id']);
        if (!empty($_FILES['cdaf_file'] )) {
            if ($_FILES['cdaf_file']['type'] == 'application/pdf') {
                if (isset($_FILES['cdaf_file'])) {
                    $nombreArchivo = $nFile.".pdf";
                    $destination = './assets/uploads/CDAF/' . $nombreArchivo;
                    move_uploaded_file($_FILES['cdaf_file']['tmp_name'], $destination);
                    $data = array('cdaf_adjuntar' => $nombreArchivo);
                    $this->Cdaf_model->uploadCDAF($data, $cdaf_id);
                    echo "<b>Archivo guardado correctamente</b>";
                }
            }else
            {
                echo "<b>El formato del archivo no está permitido.</b>";
            }
        } else {
            echo "Debe cargar un archivo PDF";
        }
    }
    public function deletePdf_CDAF()
    {

        $cdaf_id = trim($this->input->post("cdaf_id"));
        $cdaf_adj = trim($this->input->post("cdaf_adjuntar"));

        $data = array('cdaf_adjuntar' => null);
        if ($this->Cdaf_model->deletePDF_CDAF($cdaf_id, $data) == true) {
            unlink('./assets/uploads/CDAF/'.$cdaf_adj);
            echo "Archivo CDAF Eliminado";

        }
        else
            echo "El archivo CDAF que Desea Eliminar NO EXISTE";
    }

    ////////PDP///////////////
    public function pdp_index()
    {
        $data_session = $this->session->all_userdata();
        if (isset($data_session)) {
            if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                $this->load->view('head', $data_session);
                date_default_timezone_set('America/La_Paz');
                $time = time();
                $fecha_actual = date("Y-m-d", $time);
                $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                $this->load->view('menu_admin',$pre);
                $this->load->view('PDP/pdp_admin');
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        } else {
            redirect('/Login_control/close_session', 'refresh');
        }
    }
    public function list_PDP()
    {
        $fetch_data = $this->Pdp_model->make_dataTables();
        $data = array();
        foreach ($fetch_data as $row) {
            $subarray = array();
            $rowComplement = $this->Pdp_model->list_pdpComplement($row->pdp_id);
            $subarray[] = '<span style="border-bottom:2px black solid;"><span style="font-size: 12px"><b>PDP</b>: <span style="background-color: #FFFF66">' . $row->pdp_nro . '</span></span></span><br><b>Gestion:</b> ' . $rowComplement->pdp_gestion;
            $pg_date = $rowComplement->pdp_fecha_ingreso;
            $date_obj = date_create_from_format('Y-m-d', $pg_date);
            $date = date_format($date_obj, 'd-m-Y');
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>Nro. Correlativo:</b> ' . $row->pdp_correlativo . '</span><br><b>Nº Fojas:</b> ' . $rowComplement->pdp_nro_fojas . '<br><span  data-role="hint"
            data-hint-text="Fecha de Ingreso" data-hint-position="right"><span class="mif-calendar fg-darkBlue mif-lg"></span> ' . $date . '</span>';
            $monto = number_format($rowComplement->pdp_monto, 2, ',', '.');
            $subarray[] = '<span style="border-bottom:2px black solid;"><span class="mif-user-secret fg-darkGreen mif-lg"></span> ' . $row->pdp_beneficiario . '</span><br><span  data-role="hint" data-hint-text="Monto" data-hint-position="left"><b>Bs. </b> ' . $monto . '</span><br><b>Nº Cheque: </b> ' . $rowComplement->pdp_nro_cheque
                .'<br><b>Nro. Contrato:</b> ' . $rowComplement->pdp_nro_con;
            $subarray[] = '<br><b>Por:</b> ' . $row->pdp_descripcion;
            $subarray[] = '<ul data-role="listview" data-view-icons="icons-medium">
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="ESTANTE" data-icon="' . base_url() . 'assets/svg/estante-lleno.svg" data-caption="' . $row->pdp_estante . '" style="border-bottom:2px black solid;"></li>
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="BALDA"
                                data-icon="' . base_url() . 'assets/svg/balda.svg" data-caption="' . $rowComplement->pdp_balda . '"></li>
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="FILA"
                                data-icon="' . base_url() . 'assets/svg/fila.svg" data-caption="' . $rowComplement->pdp_fila . '"></li>
                        </ul>';
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>CUCE: </b>' . $row->pdp_cuce . '</span><br><b>Cite/Nro:</b> ' . $rowComplement->pdp_orden_cite . '<br><b>ORDEN DE </b>' . $rowComplement->pdp_orden_tipo .
                '<br><b>BD: </b> ' . $rowComplement->pdp_bd;
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>SISIN: </b> ' . $row->pdp_sisin . '</span><br><b>Part. Pptaria.: </b> ' . $rowComplement->pdp_partida_pre . '<br><b>Objeto de gasto: </b> ' . $rowComplement->pdp_obj_gasto;
            if ($row->pdp_adjuntar != '') {
                $subarray[] = '<a class="file_upload" href="'.base_url().'assets/uploads/PDP/'. $row->pdp_adjuntar.'" target="_blank"><span class="mif-file-pdf fg-red mif-5x"></span></a><br>
                                <button class="button alert rounded mini drop-shadow" onclick="deletePdfPDP('. $row->pdp_id .')"><b>X</b> Elimniar PDF</button>';
            }
            else {
                $subarray[] = '<button  class="upload_file rounded drop-shadow" onclick="uploadPDP('. $row->pdp_id .','. $rowComplement->pdp_nro .','. $rowComplement->pdp_gestion .')"><span class="mif-upload mif-4x"></span></button>';
            }
            $subarray[] = '<button onclick="editPDP(' . $row->pdp_id . ')" class="button success cycle drop-shadow"><span class="mif-pencil"></span></button>
                          <button onclick="deletePDP(' . $row->pdp_id . ')" class="button alert cycle drop-shadow"><span class="mif-cross"></span></button>';
            //$subarray[] = $row->usu_id;

            $data[] = $subarray;
        }
        $output = array(
            "draw" => intval($_POST["draw"]),
            "recordsTotal" => $this->Pdp_model->get_all_data(),
            "recordsFiltered" => $this->Pdp_model->get_filtered_data(),
            "data" => $data
        );
        echo json_encode($output);
    }
    public function deletePDP()
    {
        if ($this->input->is_ajax_request()) {
            $pdp_id = trim($this->input->post("pdp_id"));
            if ($this->Pdp_model->delete_PDP($pdp_id) == true)
                echo "PDP Eliminado";
            else
                echo "PDP que Desea Eliminar NO EXISTE";

        } else
            show_404();
    }
    public function save_pdp()
    {
        $pdp_fecha_ingreso = strip_tags(trim($this->input->post('pdp_fecha_ingreso')));
        $pdp_nro = strip_tags(trim($this->input->post('pdp_nro')));
        $pdp_gestion = strip_tags(trim($this->input->post('pdp_gestion')));
        $pdp_nro_correlativo = strip_tags(trim($this->input->post('pdp_nro_correlativo')));

        $pdp_nro_fojas = strip_tags(trim($this->input->post('pdp_nro_fojas')));
        $pdp_nro_fojas = ('' == $pdp_nro_fojas) ? null : $pdp_nro_fojas;

        $pdp_beneficiario = strip_tags(trim(strtoupper($this->input->post('pdp_beneficiario'))));
        $pdp_descripcion = strip_tags(trim(strtoupper($this->input->post('pdp_descripcion'))));

        $pdp_nro_con = strip_tags(trim(strtoupper($this->input->post('pdp_nro_con'))));
        $pdp_nro_con = ('' == $pdp_nro_con) ? null : $pdp_nro_con;

        $pdp_monto = strip_tags(trim($this->input->post('pdp_monto')));
        $pdp_monto = str_replace(".", "", $pdp_monto);
        $pdp_monto = str_replace(",", ".", $pdp_monto);
        $pdp_monto = (double)$pdp_monto;

        $pdp_estante = strip_tags(trim(strtoupper($this->input->post('pdp_estante'))));
        $pdp_estante = ('' == $pdp_estante) ? 'PENDIENTE' : $pdp_estante;

        $pdp_balda = strip_tags(trim($this->input->post('pdp_balda')));
        $pdp_balda = ('' == $pdp_balda) ? 'PENDIENTE' : $pdp_balda;

        $pdp_fila = strip_tags(trim($this->input->post('pdp_fila')));
        $pdp_fila = ('' == $pdp_fila) ? 'PENDIENTE' : $pdp_fila;

        $pdp_orden_tipo = strip_tags(trim($this->input->post('pdp_orden_tipo')));
        $pdp_orden_tipo = ('' == $pdp_orden_tipo) ? null : $pdp_orden_tipo;

        $pdp_orden_cite = strip_tags(trim(strtoupper($this->input->post('pdp_orden_cite'))));
        $pdp_orden_cite = ('' == $pdp_orden_cite) ? null : $pdp_orden_cite;

        $pdp_nro_cheque = strip_tags(trim($this->input->post('pdp_nro_cheque')));
        $pdp_nro_cheque = ('' == $pdp_nro_cheque) ? null : $pdp_nro_cheque;

        $pdp_sisin = strip_tags(trim(strtoupper($this->input->post('pdp_sisin'))));
        $pdp_sisin = ('' == $pdp_sisin) ? null : $pdp_sisin;

        $pdp_cuce = strip_tags(trim(strtoupper($this->input->post('pdp_cuce'))));
        $pdp_cuce = ('' == $pdp_cuce) ? null : $pdp_cuce;

        $pdp_obj_gasto = strip_tags(trim(strtoupper($this->input->post('pdp_obj_gasto'))));
        $pdp_obj_gasto = ('' == $pdp_obj_gasto) ? null : $pdp_obj_gasto;

        $pdp_bd = strip_tags(trim($this->input->post('pdp_bd')));
        $pdp_bd = ('' == $pdp_bd) ? null : $pdp_bd;

        $pdp_partida_pre = strip_tags(trim($this->input->post('pdp_partida_pre')));
        $pdp_partida_pre = ('' == $pdp_partida_pre) ? null : $pdp_partida_pre;

        if ($this->Pdp_model->verificar_PDP_Nro($pdp_nro, $pdp_gestion)){
            $data = array(
                'pdp_fecha_ingreso' => $pdp_fecha_ingreso,
                'pdp_nro' => $pdp_nro,
                'pdp_gestion' => $pdp_gestion,
                'pdp_correlativo' => $pdp_nro_correlativo,
                'pdp_nro_fojas' => $pdp_nro_fojas,
                'pdp_beneficiario' => $pdp_beneficiario,
                'pdp_descripcion' => $pdp_descripcion,
                'pdp_nro_con' => $pdp_nro_con,
                'pdp_monto' => $pdp_monto,
                'pdp_estante' => $pdp_estante,
                'pdp_balda' => $pdp_balda,
                'pdp_fila' => $pdp_fila,
                'pdp_orden_tipo' => $pdp_orden_tipo,
                'pdp_orden_cite' => $pdp_orden_cite,
                'pdp_nro_cheque' => $pdp_nro_cheque,
                'pdp_sisin' => $pdp_sisin,
                'pdp_cuce' => $pdp_cuce,
                'pdp_obj_gasto' => $pdp_obj_gasto,
                'pdp_bd' => $pdp_bd,
                'pdp_partida_pre' => $pdp_partida_pre
            );
            if ($this->Pdp_model->savePDP($data))
                redirect("Admin_control/pdp_index");
            else
                echo "Existe los Datos del PDP que desea Registrar";
        } else {
            $data_session = $this->session->all_userdata();
            if (isset($data_session)) {
                if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                    $this->load->view('head', $data_session);
                    date_default_timezone_set('America/La_Paz');
                    $time = time();
                    $fecha_actual = date("Y-m-d", $time);
                    $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                    $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                    $this->load->view('menu_admin',$pre);
                    $this->load->view('ERROR/error_admin');
                } else {
                    redirect('/Login_control/close_session', 'refresh');
                }
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        }
    }
    public function get_pdp($pdp_id)
    {
        $data = $this->Pdp_model->getPDP($pdp_id);
        echo json_encode($data);
    }
    public function edit_pdp()
    {
        $pdp_fecha_ingreso = strip_tags(trim($this->input->post('e_pdp_fecha_ingreso')));
        $pdp_nro = strip_tags(trim($this->input->post('e_pdp_nro')));
        $pdp_gestion = strip_tags(trim($this->input->post('e_pdp_gestion')));
        $pdp_nro_correlativo = strip_tags(trim($this->input->post('e_pdp_nro_correlativo')));

        $pdp_nro_fojas = strip_tags(trim($this->input->post('e_pdp_nro_fojas')));
        $pdp_nro_fojas = ('' == $pdp_nro_fojas) ? 0 : $pdp_nro_fojas;

        $pdp_beneficiario = strip_tags(trim(strtoupper($this->input->post('e_pdp_beneficiario'))));
        $pdp_descripcion = strip_tags(trim(strtoupper($this->input->post('e_pdp_descripcion'))));

        $pdp_nro_con = strip_tags(trim(strtoupper($this->input->post('e_pdp_nro_con'))));
        $pdp_nro_con = ('' == $pdp_nro_con) ? null : $pdp_nro_con;

        $pdp_monto = strip_tags(trim($this->input->post('e_pdp_monto')));
        $pdp_monto = str_replace(".", "", $pdp_monto);
        $pdp_monto = str_replace(",", ".", $pdp_monto);
        $pdp_monto = (double)$pdp_monto;

        $pdp_estante = strip_tags(trim(strtoupper($this->input->post('e_pdp_estante'))));
        $pdp_estante = ('' == $pdp_estante) ? 'PENDIENTE' : $pdp_estante;

        $pdp_balda = strip_tags(trim($this->input->post('e_pdp_balda')));
        $pdp_balda = ('' == $pdp_balda) ? 'PENDIENTE' : $pdp_balda;

        $pdp_fila = strip_tags(trim($this->input->post('e_pdp_fila')));
        $pdp_fila = ('' == $pdp_fila) ? 'PENDIENTE' : $pdp_fila;

        $pdp_orden_tipo = strip_tags(trim($this->input->post('e_pdp_orden_tipo')));
        $pdp_orden_tipo = ('' == $pdp_orden_tipo) ? null : $pdp_orden_tipo;

        $pdp_orden_cite = strip_tags(trim(strtoupper($this->input->post('e_pdp_orden_cite'))));
        $pdp_orden_cite = ('' == $pdp_orden_cite) ? null : $pdp_orden_cite;

        $pdp_nro_cheque = strip_tags(trim($this->input->post('e_pdp_nro_cheque')));
        $pdp_nro_cheque = ('' == $pdp_nro_cheque) ? null : $pdp_nro_cheque;

        $pdp_sisin = strip_tags(trim(strtoupper($this->input->post('e_pdp_sisin'))));
        $pdp_sisin = ('' == $pdp_sisin) ? null : $pdp_sisin;

        $pdp_cuce = strip_tags(trim(strtoupper($this->input->post('e_pdp_cuce'))));
        $pdp_cuce = ('' == $pdp_cuce) ? null : $pdp_cuce;

        $pdp_obj_gasto = strip_tags(trim(strtoupper($this->input->post('e_pdp_obj_gasto'))));
        $pdp_obj_gasto = ('' == $pdp_obj_gasto) ? null : $pdp_obj_gasto;

        $pdp_bd = strip_tags(trim($this->input->post('e_pdp_bd')));
        $pdp_bd = ('' == $pdp_bd) ? null : $pdp_bd;

        $pdp_partida_pre = strip_tags(trim($this->input->post('e_pdp_partida_pre')));
        $pdp_partida_pre = ('' == $pdp_partida_pre) ? null : $pdp_partida_pre;

        $pdp_id = strip_tags(trim($this->input->post('pdp_id')));

        if ($this->Pdp_model->verificar_PDP_Nro_Update($pdp_nro, $pdp_gestion)){
            $data = array(
                'pdp_fecha_ingreso' => $pdp_fecha_ingreso,
                'pdp_nro' => $pdp_nro,
                'pdp_gestion' => $pdp_gestion,
                'pdp_correlativo' => $pdp_nro_correlativo,
                'pdp_nro_fojas' => $pdp_nro_fojas,
                'pdp_beneficiario' => $pdp_beneficiario,
                'pdp_descripcion' => $pdp_descripcion,
                'pdp_nro_con' => $pdp_nro_con,
                'pdp_monto' => $pdp_monto,
                'pdp_estante' => $pdp_estante,
                'pdp_balda' => $pdp_balda,
                'pdp_fila' => $pdp_fila,
                'pdp_orden_tipo' => $pdp_orden_tipo,
                'pdp_orden_cite' => $pdp_orden_cite,
                'pdp_nro_cheque' => $pdp_nro_cheque,
                'pdp_sisin' => $pdp_sisin,
                'pdp_cuce' => $pdp_cuce,
                'pdp_obj_gasto' => $pdp_obj_gasto,
                'pdp_bd' => $pdp_bd,
                'pdp_partida_pre' => $pdp_partida_pre
            );
            if ($this->Pdp_model->updatePDP($pdp_id, $data))
                redirect("Admin_control/pdp_index");
            else
                echo "Existe los Datos del PDP que desea Registrar";
        } else {
            $data_session = $this->session->all_userdata();
            if (isset($data_session)) {
                if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                    $this->load->view('head', $data_session);
                    date_default_timezone_set('America/La_Paz');
                    $time = time();
                    $fecha_actual = date("Y-m-d", $time);
                    $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                    $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                    $this->load->view('menu_admin',$pre);
                    $this->load->view('ERROR/error_admin');
                } else {
                    redirect('/Login_control/close_session', 'refresh');
                }
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        }
    }
    public function upload_PDP()
    {

        $nFile = htmlspecialchars($_POST['nFile']);
        $pdp_id = htmlspecialchars($_POST['pdp_id']);
        if (!empty($_FILES['pdp_file'] )) {
            if ($_FILES['pdp_file']['type'] == 'application/pdf') {
                if (isset($_FILES['pdp_file'])) {
                    $nombreArchivo = $nFile.".pdf";
                    $destination = './assets/uploads/PDP/' . $nombreArchivo;
                    move_uploaded_file($_FILES['pdp_file']['tmp_name'], $destination);
                    $data = array('pdp_adjuntar' => $nombreArchivo);
                    $this->Pdp_model->uploadPDP($data, $pdp_id);
                    echo "<b>Archivo guardado correctamente</b>";
                }
            }else
            {
                echo "<b>El formato del archivo no está permitido.</b>";
            }
        } else {
            echo "Debe cargar un archivo PDF";
        }
    }
    public function deletePdf_PDP()
    {

        $pdp_id = trim($this->input->post("pdp_id"));
        $pdp_adj = trim($this->input->post("pdp_adjuntar"));

        $data = array('pdp_adjuntar' => null);
        if ($this->Pdp_model->deletePDF_PDP($pdp_id, $data) == true) {
            unlink('./assets/uploads/PDP/'.$pdp_adj);
            echo "Archivo PDP Eliminado";

        }
        else
            echo "El archivo PDP que Desea Eliminar NO EXISTE";
    }

    ////////PMRUC///////////////
    public function pmruc_index()
    {
        $data_session = $this->session->all_userdata();
        if (isset($data_session)) {
            if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                $this->load->view('head', $data_session);
                date_default_timezone_set('America/La_Paz');
                $time = time();
                $fecha_actual = date("Y-m-d", $time);
                $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                $this->load->view('menu_admin',$pre);
                $this->load->view('PMRUC/pmruc_admin');
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        } else {
            redirect('/Login_control/close_session', 'refresh');
        }
    }
    public function list_PMRUC()
    {
        $fetch_data = $this->Pmruc_model->make_dataTables();
        $data = array();
        foreach ($fetch_data as $row) {
            $subarray = array();
            $rowComplement = $this->Pmruc_model->list_pmrucComplement($row->pmruc_id);
            $subarray[] = '<span style="border-bottom:2px black solid;"><span style="font-size: 12px"><b>PMRUC</b>: <span style="background-color: #FFFF66">' . $row->pmruc_nro . '</span></span></span><br><b>Gestion:</b> ' . $rowComplement->pmruc_gestion;
            $pg_date = $rowComplement->pmruc_fecha_ingreso;
            $date_obj = date_create_from_format('Y-m-d', $pg_date);
            $date = date_format($date_obj, 'd-m-Y');
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>Nro. Correlativo:</b> ' . $row->pmruc_correlativo . '</span><br><b>Nº Fojas:</b> ' . $rowComplement->pmruc_nro_fojas . '<br><span  data-role="hint"
            data-hint-text="Fecha de Ingreso" data-hint-position="right"><span class="mif-calendar fg-darkBlue mif-lg"></span> ' . $date . '</span>';
            $monto = number_format($rowComplement->pmruc_monto, 2, ',', '.');
            $subarray[] = '<span style="border-bottom:2px black solid;"><span class="mif-user-secret fg-darkGreen mif-lg"></span> ' . $row->pmruc_beneficiario . '</span><br><span  data-role="hint" data-hint-text="Monto" data-hint-position="left"><b>Bs. </b> ' . $monto . '</span><br><b>Nº Cheque: </b> ' . $rowComplement->pmruc_nro_cheque
                .'<br><b>Nro. Contrato:</b> ' . $rowComplement->pmruc_nro_con;
            $subarray[] = '<br><b>Por:</b> ' . $row->pmruc_descripcion;
            $subarray[] = '<ul data-role="listview" data-view-icons="icons-medium">
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="ESTANTE" data-icon="' . base_url() . 'assets/svg/estante-lleno.svg" data-caption="' . $row->pmruc_estante . '" style="border-bottom:2px black solid;"></li>
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="BALDA"
                                data-icon="' . base_url() . 'assets/svg/balda.svg" data-caption="' . $rowComplement->pmruc_balda . '"></li>
                            <li data-role="hint"
                                data-hint-position="left"
                                data-hint-text="FILA"
                                data-icon="' . base_url() . 'assets/svg/fila.svg" data-caption="' . $rowComplement->pmruc_fila . '"></li>
                        </ul>';
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>CUCE: </b>' . $row->pmruc_cuce . '</span><br><b>Cite/Nro:</b> ' . $rowComplement->pmruc_orden_cite . '<br><b>ORDEN DE </b>' . $rowComplement->pmruc_orden_tipo .
                '<br><b>BD: </b> ' . $rowComplement->pmruc_bd;
            $subarray[] = '<span style="border-bottom:2px black solid;"><b>SISIN: </b> ' . $row->pmruc_sisin . '</span><br><b>Part. Pptaria.: </b> ' . $rowComplement->pmruc_partida_pre . '<br><b>Objeto de gasto: </b> ' . $rowComplement->pmruc_obj_gasto;
            if ($row->pmruc_adjuntar != '') {
                $subarray[] = '<a class="file_upload" href="'.base_url().'assets/uploads/PMRUC/'. $row->pmruc_adjuntar.'" target="_blank"><span class="mif-file-pdf fg-red mif-5x"></span></a><br>
                                <button class="button alert rounded mini drop-shadow" onclick="deletePdfPMRUC('. $row->pmruc_id .')"><b>X</b> Elimniar PDF</button>';
            }
            else {
                $subarray[] = '<button  class="upload_file rounded drop-shadow" onclick="uploadPMRUC('. $row->pmruc_id .','. $rowComplement->pmruc_nro .','. $rowComplement->pmruc_gestion .')"><span class="mif-upload mif-4x"></span></button>';
            }
            $subarray[] = '<button onclick="editPMRUC(' . $row->pmruc_id . ')" class="button success cycle drop-shadow"><span class="mif-pencil"></span></button>
                          <button onclick="deletePMRUC(' . $row->pmruc_id . ')" class="button alert cycle drop-shadow"><span class="mif-cross"></span></button>';
            //$subarray[] = $row->usu_id;

            $data[] = $subarray;
        }
        $output = array(
            "draw" => intval($_POST["draw"]),
            "recordsTotal" => $this->Pmruc_model->get_all_data(),
            "recordsFiltered" => $this->Pmruc_model->get_filtered_data(),
            "data" => $data
        );
        echo json_encode($output);
    }
    public function deletePMRUC()
    {
        if ($this->input->is_ajax_request()) {
            $pmruc_id = trim($this->input->post("pmruc_id"));
            if ($this->Pmruc_model->delete_PMRUC($pmruc_id) == true)
                echo "PMRUC Eliminado";
            else
                echo "PMRUC que Desea Eliminar NO EXISTE";

        } else
            show_404();
    }
    public function save_pmruc()
    {
        $pmruc_fecha_ingreso = strip_tags(trim($this->input->post('pmruc_fecha_ingreso')));
        $pmruc_nro = strip_tags(trim($this->input->post('pmruc_nro')));
        $pmruc_gestion = strip_tags(trim($this->input->post('pmruc_gestion')));
        $pmruc_nro_correlativo = strip_tags(trim($this->input->post('pmruc_nro_correlativo')));

        $pmruc_nro_fojas = strip_tags(trim($this->input->post('pmruc_nro_fojas')));
        $pmruc_nro_fojas = ('' == $pmruc_nro_fojas) ? null : $pmruc_nro_fojas;

        $pmruc_beneficiario = strip_tags(trim(strtoupper($this->input->post('pmruc_beneficiario'))));
        $pmruc_descripcion = strip_tags(trim(strtoupper($this->input->post('pmruc_descripcion'))));

        $pmruc_nro_con = strip_tags(trim(strtoupper($this->input->post('pmruc_nro_con'))));
        $pmruc_nro_con = ('' == $pmruc_nro_con) ? null : $pmruc_nro_con;

        $pmruc_monto = strip_tags(trim($this->input->post('pmruc_monto')));
        $pmruc_monto = str_replace(".", "", $pmruc_monto);
        $pmruc_monto = str_replace(",", ".", $pmruc_monto);
        $pmruc_monto = (double)$pmruc_monto;

        $pmruc_estante = strip_tags(trim(strtoupper($this->input->post('pmruc_estante'))));
        $pmruc_estante = ('' == $pmruc_estante) ? 'PENDIENTE' : $pmruc_estante;

        $pmruc_balda = strip_tags(trim($this->input->post('pmruc_balda')));
        $pmruc_balda = ('' == $pmruc_balda) ? 'PENDIENTE' : $pmruc_balda;

        $pmruc_fila = strip_tags(trim($this->input->post('pmruc_fila')));
        $pmruc_fila = ('' == $pmruc_fila) ? 'PENDIENTE' : $pmruc_fila;

        $pmruc_orden_tipo = strip_tags(trim($this->input->post('pmruc_orden_tipo')));
        $pmruc_orden_tipo = ('' == $pmruc_orden_tipo) ? null : $pmruc_orden_tipo;

        $pmruc_orden_cite = strip_tags(trim(strtoupper($this->input->post('pmruc_orden_cite'))));
        $pmruc_orden_cite = ('' == $pmruc_orden_cite) ? null : $pmruc_orden_cite;

        $pmruc_nro_cheque = strip_tags(trim($this->input->post('pmruc_nro_cheque')));
        $pmruc_nro_cheque = ('' == $pmruc_nro_cheque) ? null : $pmruc_nro_cheque;

        $pmruc_sisin = strip_tags(trim(strtoupper($this->input->post('pmruc_sisin'))));
        $pmruc_sisin = ('' == $pmruc_sisin) ? null : $pmruc_sisin;

        $pmruc_cuce = strip_tags(trim(strtoupper($this->input->post('pmruc_cuce'))));
        $pmruc_cuce = ('' == $pmruc_cuce) ? null : $pmruc_cuce;

        $pmruc_obj_gasto = strip_tags(trim(strtoupper($this->input->post('pmruc_obj_gasto'))));
        $pmruc_obj_gasto = ('' == $pmruc_obj_gasto) ? null : $pmruc_obj_gasto;

        $pmruc_bd = strip_tags(trim($this->input->post('pmruc_bd')));
        $pmruc_bd = ('' == $pmruc_bd) ? null : $pmruc_bd;

        $pmruc_partida_pre = strip_tags(trim($this->input->post('pmruc_partida_pre')));
        $pmruc_partida_pre = ('' == $pmruc_partida_pre) ? null : $pmruc_partida_pre;

        if ($this->Pmruc_model->verificar_PMRUC_Nro($pmruc_nro, $pmruc_gestion)){
            $data = array(
                'pmruc_fecha_ingreso' => $pmruc_fecha_ingreso,
                'pmruc_nro' => $pmruc_nro,
                'pmruc_gestion' => $pmruc_gestion,
                'pmruc_correlativo' => $pmruc_nro_correlativo,
                'pmruc_nro_fojas' => $pmruc_nro_fojas,
                'pmruc_beneficiario' => $pmruc_beneficiario,
                'pmruc_descripcion' => $pmruc_descripcion,
                'pmruc_nro_con' => $pmruc_nro_con,
                'pmruc_monto' => $pmruc_monto,
                'pmruc_estante' => $pmruc_estante,
                'pmruc_balda' => $pmruc_balda,
                'pmruc_fila' => $pmruc_fila,
                'pmruc_orden_tipo' => $pmruc_orden_tipo,
                'pmruc_orden_cite' => $pmruc_orden_cite,
                'pmruc_nro_cheque' => $pmruc_nro_cheque,
                'pmruc_sisin' => $pmruc_sisin,
                'pmruc_cuce' => $pmruc_cuce,
                'pmruc_obj_gasto' => $pmruc_obj_gasto,
                'pmruc_bd' => $pmruc_bd,
                'pmruc_partida_pre' => $pmruc_partida_pre
            );
            if ($this->Pmruc_model->savePMRUC($data))
                redirect("Admin_control/pmruc_index");
            else
                echo "Existe los Datos del PMRUC que desea Registrar";
        } else {
            $data_session = $this->session->all_userdata();
            if (isset($data_session)) {
                if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                    $this->load->view('head', $data_session);
                    date_default_timezone_set('America/La_Paz');
                    $time = time();
                    $fecha_actual = date("Y-m-d", $time);
                    $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                    $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                    $this->load->view('menu_admin',$pre);
                    $this->load->view('ERROR/error_admin');
                } else {
                    redirect('/Login_control/close_session', 'refresh');
                }
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        }
    }
    public function get_pmruc($pmruc_id)
    {
        $data = $this->Pmruc_model->getPMRUC($pmruc_id);
        echo json_encode($data);
    }
    public function edit_pmruc()
    {
        $pmruc_fecha_ingreso = strip_tags(trim($this->input->post('e_pmruc_fecha_ingreso')));
        $pmruc_nro = strip_tags(trim($this->input->post('e_pmruc_nro')));
        $pmruc_gestion = strip_tags(trim($this->input->post('e_pmruc_gestion')));
        $pmruc_nro_correlativo = strip_tags(trim($this->input->post('e_pmruc_nro_correlativo')));

        $pmruc_nro_fojas = strip_tags(trim($this->input->post('e_pmruc_nro_fojas')));
        $pmruc_nro_fojas = ('' == $pmruc_nro_fojas) ? 0 : $pmruc_nro_fojas;

        $pmruc_beneficiario = strip_tags(trim(strtoupper($this->input->post('e_pmruc_beneficiario'))));
        $pmruc_descripcion = strip_tags(trim(strtoupper($this->input->post('e_pmruc_descripcion'))));

        $pmruc_nro_con = strip_tags(trim(strtoupper($this->input->post('e_pmruc_nro_con'))));
        $pmruc_nro_con = ('' == $pmruc_nro_con) ? null : $pmruc_nro_con;

        $pmruc_monto = strip_tags(trim($this->input->post('e_pmruc_monto')));
        $pmruc_monto = str_replace(".", "", $pmruc_monto);
        $pmruc_monto = str_replace(",", ".", $pmruc_monto);
        $pmruc_monto = (double)$pmruc_monto;

        $pmruc_estante = strip_tags(trim(strtoupper($this->input->post('e_pmruc_estante'))));
        $pmruc_estante = ('' == $pmruc_estante) ? 'PENDIENTE' : $pmruc_estante;

        $pmruc_balda = strip_tags(trim($this->input->post('e_pmruc_balda')));
        $pmruc_balda = ('' == $pmruc_balda) ? 'PENDIENTE' : $pmruc_balda;

        $pmruc_fila = strip_tags(trim($this->input->post('e_pmruc_fila')));
        $pmruc_fila = ('' == $pmruc_fila) ? 'PENDIENTE' : $pmruc_fila;

        $pmruc_orden_tipo = strip_tags(trim($this->input->post('e_pmruc_orden_tipo')));
        $pmruc_orden_tipo = ('' == $pmruc_orden_tipo) ? null : $pmruc_orden_tipo;

        $pmruc_orden_cite = strip_tags(trim(strtoupper($this->input->post('e_pmruc_orden_cite'))));
        $pmruc_orden_cite = ('' == $pmruc_orden_cite) ? null : $pmruc_orden_cite;

        $pmruc_nro_cheque = strip_tags(trim($this->input->post('e_pmruc_nro_cheque')));
        $pmruc_nro_cheque = ('' == $pmruc_nro_cheque) ? null : $pmruc_nro_cheque;

        $pmruc_sisin = strip_tags(trim(strtoupper($this->input->post('e_pmruc_sisin'))));
        $pmruc_sisin = ('' == $pmruc_sisin) ? null : $pmruc_sisin;

        $pmruc_cuce = strip_tags(trim(strtoupper($this->input->post('e_pmruc_cuce'))));
        $pmruc_cuce = ('' == $pmruc_cuce) ? null : $pmruc_cuce;

        $pmruc_obj_gasto = strip_tags(trim(strtoupper($this->input->post('e_pmruc_obj_gasto'))));
        $pmruc_obj_gasto = ('' == $pmruc_obj_gasto) ? null : $pmruc_obj_gasto;

        $pmruc_bd = strip_tags(trim($this->input->post('e_pmruc_bd')));
        $pmruc_bd = ('' == $pmruc_bd) ? null : $pmruc_bd;

        $pmruc_partida_pre = strip_tags(trim($this->input->post('e_pmruc_partida_pre')));
        $pmruc_partida_pre = ('' == $pmruc_partida_pre) ? null : $pmruc_partida_pre;

        $pmruc_id = strip_tags(trim($this->input->post('pmruc_id')));

        if ($this->Pmruc_model->verificar_PMRUC_Nro_Update($pmruc_nro, $pmruc_gestion)){
            $data = array(
                'pmruc_fecha_ingreso' => $pmruc_fecha_ingreso,
                'pmruc_nro' => $pmruc_nro,
                'pmruc_gestion' => $pmruc_gestion,
                'pmruc_correlativo' => $pmruc_nro_correlativo,
                'pmruc_nro_fojas' => $pmruc_nro_fojas,
                'pmruc_beneficiario' => $pmruc_beneficiario,
                'pmruc_descripcion' => $pmruc_descripcion,
                'pmruc_nro_con' => $pmruc_nro_con,
                'pmruc_monto' => $pmruc_monto,
                'pmruc_estante' => $pmruc_estante,
                'pmruc_balda' => $pmruc_balda,
                'pmruc_fila' => $pmruc_fila,
                'pmruc_orden_tipo' => $pmruc_orden_tipo,
                'pmruc_orden_cite' => $pmruc_orden_cite,
                'pmruc_nro_cheque' => $pmruc_nro_cheque,
                'pmruc_sisin' => $pmruc_sisin,
                'pmruc_cuce' => $pmruc_cuce,
                'pmruc_obj_gasto' => $pmruc_obj_gasto,
                'pmruc_bd' => $pmruc_bd,
                'pmruc_partida_pre' => $pmruc_partida_pre
            );
            if ($this->Pmruc_model->updatePMRUC($pmruc_id, $data))
                redirect("Admin_control/pmruc_index");
            else
                echo "Existe los Datos del PMRUC que desea Registrar";
        } else {
            $data_session = $this->session->all_userdata();
            if (isset($data_session)) {
                if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                    $this->load->view('head', $data_session);
                    date_default_timezone_set('America/La_Paz');
                    $time = time();
                    $fecha_actual = date("Y-m-d", $time);
                    $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                    $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                    $this->load->view('menu_admin',$pre);
                    $this->load->view('ERROR/error_admin');
                } else {
                    redirect('/Login_control/close_session', 'refresh');
                }
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        }
    }
    public function upload_PMRUC()
    {
        $nFile = htmlspecialchars($_POST['nFile']);
        $pmruc_id = htmlspecialchars($_POST['pmruc_id']);
        if (!empty($_FILES['pmruc_file'] )) {
            if ($_FILES['pmruc_file']['type'] == 'application/pdf') {
                if (isset($_FILES['pmruc_file'])) {
                    $nombreArchivo = $nFile.".pdf";
                    $destination = './assets/uploads/PMRUC/' . $nombreArchivo;
                    move_uploaded_file($_FILES['pmruc_file']['tmp_name'], $destination);
                    $data = array('pmruc_adjuntar' => $nombreArchivo);
                    $this->Pmruc_model->uploadPMRUC($data, $pmruc_id);
                    echo "<b>Archivo guardado correctamente</b>";
                }
            }else
            {
                echo "<b>El formato del archivo no está permitido.</b>";
            }
        } else {
            echo "Debe cargar un archivo PDF";
        }
    }
    public function deletePdf_PMRUC()
    {

        $pmruc_id = trim($this->input->post("pmruc_id"));
        $pmruc_adj = trim($this->input->post("pmruc_adjuntar"));

        $data = array('pmruc_adjuntar' => null);
        if ($this->Pmruc_model->deletePDF_PMRUC($pmruc_id, $data) == true) {
            unlink('./assets/uploads/PMRUC/'.$pmruc_adj);
            echo "Archivo PMRUC Eliminado";
        }
        else
            echo "El archivo PMRUC que Desea Eliminar NO EXISTE";
    }

    ////////PRESTAMO///////////////
    public function prestamo_index()
    {
        $data_session = $this->session->all_userdata();
        if (isset($data_session)) {
            if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                $data['gestion']=$this->Prestamo_model->list_gestion();
                $data['admin']=$this->Prestamo_model->list_adminHab();
                $data['prestamos']=$this->Prestamo_model->list_prestamos();
                date_default_timezone_set('America/La_Paz');
                $time = time();
                $fecha_actual = date("Y-m-d", $time);
                $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                $this->load->view('head', $data_session);
                $this->load->view('menu_admin',$pre);
                $this->load->view('PRESTAMO/prestamo_admin',$data);
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        } else {
            redirect('/Login_control/close_session', 'refresh');
        }
    }
    public function show_queries()
    {
        $bus_name_file = strip_tags(trim($this->input->post('bus_name_file')));
        $bus_nro_file = strip_tags(trim($this->input->post('bus_nro_file')));
        $bus_year_file = strip_tags(trim($this->input->post('bus_year_file')));
        $q=$this->Prestamo_model->search_queries($bus_name_file,$bus_nro_file,$bus_year_file);
        echo json_encode($q);
    }
    public function save_prestamo()
    {
        $data_session = $this->session->all_userdata();
        $doc_name = $this->input->post('doc_name');
        $doc_nro = $this->input->post('doc_nro');
        $doc_gestion = $this->input->post('doc_gestion');
        if ($doc_name!=NULL and $doc_nro!=NULL and $doc_gestion!=NULL)
        {
            $pre_solicitante = strip_tags(trim(strtoupper($this->input->post('pre_solicitante'))));
            $pre_cite_solicitud = strip_tags(trim(strtoupper($this->input->post('pre_cite_solicitud'))));
            $pre_dependencia = strip_tags(trim(strtoupper($this->input->post('pre_dependencia'))));
            $pre_fecha_prestamo = strip_tags(trim($this->input->post('pre_fecha_prestamo')));
            $pre_fecha_devolucion = strip_tags(trim($this->input->post('pre_fecha_devolucion')));
            $pre_cite_af = strip_tags(trim(strtoupper($this->input->post('pre_cite_af'))));
            $pre_autorizado = strip_tags(trim(strtoupper($this->input->post('pre_autorizado'))));
            $pre_observaciones = strip_tags(trim(strtoupper($this->input->post('pre_observaciones'))));
            $data_prestamo = array(
                'pre_solicitante' => $pre_solicitante,
                'pre_cite_solicitud' => $pre_cite_solicitud,
                'pre_dependencia' => $pre_dependencia,
                'pre_fecha_prestamo' => $pre_fecha_prestamo,
                'pre_fecha_devolucion' => $pre_fecha_devolucion,
                'pre_estado'=>true,
                'pre_cite_af' => $pre_cite_af,
                'usu_id_rol'=>$data_session['usu_id'],
                'pre_autorizado' => $pre_autorizado,
                'pre_observaciones' => $pre_observaciones,
                'pre_fecha_real_dev'=>null
            );
            $this->Prestamo_model->savePrestamo($data_prestamo);
            $pre_id=$this->db->insert_id();

            for ($i=0;$i<count($doc_name);$i++)
            {  $data_doc = array(
                'doc_name' => $doc_name[$i],
                'doc_nro' => $doc_nro[$i],
                'doc_gestion' => $doc_gestion[$i],
                'pre_id' => $pre_id
            );
                $this->Prestamo_model->saveDocumento($data_doc);
            }
            /////REPORT PDF//////
            $this->load->library('ciqrcode');
            $this->load->library('Pdf');
            $pdf = new Pdf('P', 'mm', 'LETTER', true, 'UTF-8', false);
            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('UNIDAD DE SISTEMAS');
            $pdf->SetTitle('REPORTE DE PRESTAMO');
            $pdf->SetKeywords('reporte PDF, Reporte de prestamo');
            // set header and footer fonts
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT,40, PDF_MARGIN_RIGHT,60);
            $pdf->SetPrintHeader(false);
            $pdf->SetFooterMargin(20);
            // set font
            $pdf->SetFont('helvetica', '', 11);
            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->AddPage();
            date_default_timezone_set('America/La_Paz');
            $time = time();
            $txt=$data_session['usu_nombres'].'|'.$data_session['usu_paterno'].'|'.$data_session['usu_materno'].' '.$data_session['usu_ci'].'|'
                .$data_session['rol_nombre'].'|'.$pre_solicitante.'|'.$pre_dependencia;
            $txt.='|'.date("d-m-Y (H:i:s)", $time);
            $params['data'] = $txt;
            $params['level'] = 'H';
            $params['size'] = 10;
            $params['savename'] = FCPATH.'assets/qr/'.$pre_id.'.png';
            $this->ciqrcode->generate($params);
            $html_head='
                <table>
                  <tr>
                     <td rowspan="4" style="width: 40%; font-size:8px;">
                        <b>GOBIERNO AUTONOMO MUNICIPAL DE EL ALTO<br></b>
                        <b>  SECRETARIA MUNICIPAL DE ADMINISTRACION Y FINANZAS<br></b>
                        <b>  DIRECCION DE TESORO MUNICIPAL<br></b>
                        <b>  UNIDAD DE PRESUPUESTO<br></b>
                        <b>  AREA DE ARCHIVO FINANCIERO</b>
                     </td>
                     <td style="width: 22%;"></td>
                     <td style="width: 22%;"></td>
                     <td rowspan="4" style="width: 15%; font-size:8px;">
                       <img style="float:left;width:100px;height:80px;" src="' . base_url() . 'assets/qr/'.$pre_id.'.png">
                     </td>
                  </tr>
                </table>
                <div style="text-align:center;font-size:14pt; line-height:1;"><b>ACTA DE PRESTAMO</b><br>
                <span style="font-size:9pt;"><b>CITE:</b>'.$pre_cite_af.'</span>
                </div>';
            $pdf->writeHTML($html_head, true, false, true, false, '');
            $date =date("d/m/Y");   ;


            $html_cabecera='
                <br>
                <p style="text-indent: 30px;text-align:justify;font-family:Verdana;font-style: italic;">
                    En la ciudad de El Alto, <b>'.$date.'</b> en oficinas del Área de Archivo Financiero del Gobierno Autónomo  
                    Municipal de El Alto, en presencia de: <b>'.$pre_solicitante.'</b>  que corresponde al CITE: <b>'.$pre_cite_solicitud.'</b>
                    de la <b>'.$pre_dependencia.'</b> se procede al préstamo de la siguiente documentación, que se detalla a continuación:

                </p><br>';
            $pdf->writeHTML($html_cabecera, true, false, true, false, '');

            $documentos=$this->Prestamo_model->list_doc($pre_id);

            $html = '<style>
                            table {
                                border-collapse: separate;
                                border-spacing:  3px 3px;                                
                            }
                            table, th, td {
                            border: 1px solid black;
                            font-size: 10pt;
                            text-align: center;
                            padding-top: 5pt;
                            padding-bottom: 5pt;
                            }
                      </style>
                <table>
                  <thead>
                      <tr style="background-color:lightgrey;">
                         <th style="width: 10%"><b>DTM</b></th>
                         <th style="width: 13%"><b>Nº ARCHIVO</b></th>
                         <th style="width: 13%"><b>Nº FOJAS</b></th>
                         <th style="width: 54%"><b>DESCRIPCION</b></th>
                         <th style="width: 10%"><b>GESTION</b></th>
                      </tr>
                  <tbody>';
            foreach ($documentos as $doc)
            {
                $detalles_doc=$this->Prestamo_model->list_archivos_decrip($doc->doc_name,$doc->doc_nro,$doc->doc_gestion);
                $detalles_foj=$this->Prestamo_model->list_archivos_fojas($doc->doc_name,$doc->doc_nro,$doc->doc_gestion);
                $html.='<tr>';
                $html.='<td>'.$doc->doc_name.'</td>';
                $html.='<td>'.$doc->doc_nro.'</td>';
                foreach ($detalles_foj as $foj)
                {
                    $html.='<td>'.$foj.'</td>';
                }
                foreach ($detalles_doc as $det)
                {
                    $html.='<td style="text-align:center;font-family:Verdana;font-style:normal;font-size:6pt;">'.$det.'</td>';
                }
                $html.='<td>'.$doc->doc_gestion.'</td>';
                $html.='</tr>';
            }
            $pg_date = $pre_fecha_prestamo;
            $date_obj = date_create_from_format('Y-m-d', $pg_date);
            $pre_fecha_prestamo = date_format($date_obj, 'd/m/Y');
            $pg_date = $pre_fecha_devolucion;
            $date_obj = date_create_from_format('Y-m-d', $pg_date);
            $pre_fecha_devolucion = date_format($date_obj, 'd/m/Y');
            $row_admin=$this->Prestamo_model->row_adminHab($pre_autorizado);
            $html.=
                '</tbody>
                </table>
                <br>
                <p style="text-indent: 30px;text-align:justify;font-family:Verdana;font-style: italic;">
                    La documentación en préstamo deberá ser devuelta en un plazo de <b>5 días hábiles</b>, computables a
                     partir de la suscripción de la presente Acta y sea en estricta sujeción a lo establecido por el 
                     Art. 84 del Decreto Supremo Nº 27113 – Reglamento a la Ley Nº 234 del Procedimiento Administrativo.
                </p>    
                <p style="text-indent: 30px;text-align:justify;font-family:Verdana;font-style: italic;">
                La negativa o vulneración a este aspecto será sancionado de acuerdo al Reglamento Interno del 
                Gobierno Autónomo Municipal de El Alto.
                </p><br>
               
                     <table style=" padding-top: 4pt;  padding-bottom: 4pt;">
                          <thead>
                              <tr style="background-color:lightgrey;">
                                 <th width="25%"><b>FECHA DE PRESTAMO</b></th>
                                 <th width="27%"><b>FECHA DE DEVOLUCION</b></th>
                                 <th width="48%"><b>OBSERVACIONES</b></th>
                              </tr>
                           </thead>   
                          <tbody>
                              <tr>
                                <td width="25%"><img src="'.base_url().'assets/svg/calendario.svg" width="15px" heigth="9px"><span style="font-size:11pt;"> '.$pre_fecha_prestamo.'</span></td>
                                <td width="27%"><img src="'.base_url().'assets/svg/calendario.svg" width="15px" heigth="9px"><span style="font-size:11pt;"> '.$pre_fecha_devolucion.'</span></td>
                                <td width="48%" style="font-size:6pt;">'.$pre_observaciones.'</td>
                              </tr>
                          </tbody>
                     </table>
                     <br><br>
                     
                     <table style="border: 0px none white;border-collapse: separate; padding-top: 80pt">
                        <tr>
                            <td style="text-align:center;font-family:Verdana;font-style:normal;font-size:8pt;">
                                
                                <b>Entregue Conforme</b><br>
                                '.$data_session['usu_nombres'].' '.$data_session['usu_paterno'].' '.$data_session['usu_materno'].'<br>
                                <b>'.$data_session['rol_nombre'].'</b>
                            </td>
                            <td style="text-align:center;font-family:Verdana;font-style:normal;font-size:8pt;">
                                    
                                    <b>Recibi Conforme</b><br>
                                    ..................................................<br>
                                    ..................................................<br>
                                    Cel:.................................
                            </td>
                            <td style="text-align:center;font-family:Verdana;font-style:normal;font-size:8pt;">
                                    
                                    <b>Autorizado Conforme</b><br>
                                    '.$row_admin->usu_nombres.' '.$row_admin->usu_paterno.' '.$row_admin->usu_materno.'<br>
                                    <b>RESPONSABLE DE ARCHIVO FINANCIERO<br>Corp:</b>69960407
                            </td>
                         </tr>
                     </table>
               <p style="text-align:justify;font-family:Verdana;font-style: italic;font-size:8pt;">
                <b>IMPORTANTE:</b> En caso de deterioro o perdida de la documentación, la responsabilidad recae en 
                Servidor(a) Publico que solicito el préstamo, sin perjuicio de las sanciones establecidas por normativa legal vigente. 
                </p>';
            // output the HTML content
            $pdf->writeHTML($html, true, false, true, false, '');
            //Close and output PDF document

            $pdf->Output('doc_prestamo.pdf', 'I');
        }
        else
        {
            redirect("Admin_control/prestamo_index");
        }
    }
    public function ver_archivo($pre_id){
        $data = $this->Prestamo_model->ver_Archivo($pre_id);
        echo json_encode($data);
    }
    public function ver_detalle_archivo(){
        $doc_name = $this->input->post('doc_name');
        $doc_nro = $this->input->post('doc_nro');
        $doc_gestion = $this->input->post('doc_gestion');

        $data = $this->Prestamo_model->ver_Detalle_Archivo($doc_name, $doc_nro, $doc_gestion);
        echo json_encode($data);
    }
    public function finalizar_Prestamo(){
        $pre_id = strip_tags(trim($this->input->post('pre_id')));
        $usu_fin = strip_tags(trim($this->input->post('usu_fin')));

        date_default_timezone_set('America/La_Paz');
        $time = time();
        $fecha_actual = date("Y-m-d", $time);

        $data = array(
            'pre_estado' => false,
            'pre_id_rol_fin' => $usu_fin,
            'pre_fecha_real_dev' => $fecha_actual
        );

            if ($this->Prestamo_model->finalizarPrestamo($pre_id,$data) == true)
                echo "Prestamo Concluido";
            else
                echo "ERROR DE Finalizar Prestamo";
    }

    ////////HISTORIAL///////////////
    public function historial_index()
    {
        $data_session = $this->session->all_userdata();
        if (isset($data_session)) {
            if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                $data['gestion']=$this->Prestamo_model->list_gestion();
                $data['admin']=$this->Prestamo_model->list_adminHab();
                $data['prestamos']=$this->Prestamo_model->list_prestamos_historial();
                $this->load->view('head', $data_session);
                date_default_timezone_set('America/La_Paz');
                $time = time();
                $fecha_actual = date("Y-m-d", $time);
                $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                $this->load->view('menu_admin',$pre);
                $this->load->view('HISTORIAL/historial_admin', $data);
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        } else {
            redirect('/Login_control/close_session', 'refresh');
        }
    }
    public function reporte_pdf(){
        $pre_id = trim($this->input->post('p_pre_id'));
        $pre_solicitante = strip_tags(trim(strtoupper($this->input->post('p-pre_solicitante'))));
        $pre_cite_solicitud = strip_tags(trim(strtoupper($this->input->post('p_pre_cite_solicitud'))));
        $pre_dependencia = strip_tags(trim(strtoupper($this->input->post('p_pre_dependencia'))));
        $pre_fecha_prestamo = strip_tags(trim($this->input->post('p_pre_fecha_prestamo')));
        $pre_fecha_devolucion = strip_tags(trim($this->input->post('p_pre_fecha_devolucion')));
        $pre_cite_af = strip_tags(trim(strtoupper($this->input->post('p_pre_cite_af'))));
        $pre_autorizado = strip_tags(trim(strtoupper($this->input->post('p_pre_autorizado'))));
        $pre_observaciones = strip_tags(trim(strtoupper($this->input->post('p_pre_observaciones'))));
        /////REPORT PDF//////
        $data_session = $this->session->all_userdata();
        $this->load->library('ciqrcode');
        $this->load->library('Pdf');
        $pdf = new Pdf('P', 'mm', 'LETTER', true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('UNIDAD DE SISTEMAS');
        $pdf->SetTitle('REPORTE DE PRESTAMO');
        $pdf->SetKeywords('reporte PDF, Reporte de prestamo');
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT,40, PDF_MARGIN_RIGHT,60);
        $pdf->SetPrintHeader(false);
        $pdf->SetFooterMargin(20);
        // set font
        $pdf->SetFont('helvetica', '', 11);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->AddPage();
        date_default_timezone_set('America/La_Paz');
        $time = time();
        $txt=$data_session['usu_nombres'].'|'.$data_session['usu_paterno'].'|'.$data_session['usu_materno'].' '.$data_session['usu_ci'].'|'
            .$data_session['rol_nombre'].'|'.$pre_solicitante.'|'.$pre_dependencia;
        $txt.='|'.date("d-m-Y (H:i:s)", $time);
        $params['data'] = $txt;
        $params['level'] = 'H';
        $params['size'] = 10;
        $params['savename'] = FCPATH.'assets/qr/'.$pre_id.'.png';
        $this->ciqrcode->generate($params);
        $html_head='
                <table>
                  <tr>
                     <td rowspan="4" style="width: 40%; font-size:8px;">
                        <b>GOBIERNO AUTONOMO MUNICIPAL DE EL ALTO<br></b>
                        <b>  SECRETARIA MUNICIPAL DE ADMINISTRACION Y FINANZAS<br></b>
                        <b>  DIRECCION DE TESORO MUNICIPAL<br></b>
                        <b>  UNIDAD DE PRESUPUESTO<br></b>
                        <b>  AREA DE ARCHIVO FINANCIERO</b>
                     </td>
                     <td style="width: 22%;"></td>
                     <td style="width: 22%;"></td>
                     <td rowspan="4" style="width: 15%; font-size:8px;">
                       <img style="float:left;width:100px;height:80px;" src="' . base_url() . 'assets/qr/'.$pre_id.'.png">
                     </td>
                  </tr>
                </table>
                <div style="text-align:center;font-size:14pt; line-height:1;"><b>ACTA DE PRESTAMO</b><br>
                <span style="font-size:9pt;"><b>CITE:</b>'.$pre_cite_af.'</span>
                </div>';
        $pdf->writeHTML($html_head, true, false, true, false, '');
        $date =date("d/m/Y");   ;


        $html_cabecera='
                <br>
                <p style="text-indent: 30px;text-align:justify;font-family:Verdana;font-style: italic;">
                    En la ciudad de El Alto, <b>'.$date.'</b> en oficinas del Área de Archivo Financiero del Gobierno Autónomo  
                    Municipal de El Alto, en presencia de: <b>'.$pre_solicitante.'</b>  que corresponde al CITE: <b>'.$pre_cite_solicitud.'</b>
                    de la <b>'.$pre_dependencia.'</b> se procede al préstamo de la siguiente documentación, que se detalla a continuación:

                </p><br>';
        $pdf->writeHTML($html_cabecera, true, false, true, false, '');

        $documentos=$this->Prestamo_model->list_doc($pre_id);

        $html = '<style>
                            table {
                                border-collapse: separate;
                                border-spacing:  3px 3px;                                
                            }
                            table, th, td {
                            border: 1px solid black;
                            font-size: 10pt;
                            text-align: center;
                            padding-top: 5pt;
                            padding-bottom: 5pt;
                            }
                      </style>
                <table>
                  <thead>
                      <tr style="background-color:lightgrey;">
                         <th style="width: 10%"><b>ARCHIVO</b></th>
                         <th style="width: 13%"><b>Nº ARCHIVO</b></th>
                         <th style="width: 13%"><b>Nº FOJAS</b></th>
                         <th style="width: 54%"><b>DESCRIPCION</b></th>
                         <th style="width: 10%"><b>GESTION</b></th>
                      </tr>
                  <tbody>';
        foreach ($documentos as $doc)
        {
            $detalles_doc=$this->Prestamo_model->list_archivos_decrip($doc->doc_name,$doc->doc_nro,$doc->doc_gestion);
            $detalles_foj=$this->Prestamo_model->list_archivos_fojas($doc->doc_name,$doc->doc_nro,$doc->doc_gestion);
            $html.='<tr>';
            $html.='<td>'.$doc->doc_name.'</td>';
            $html.='<td>'.$doc->doc_nro.'</td>';
            foreach ($detalles_foj as $foj)
            {
                $html.='<td>'.$foj.'</td>';
            }
            foreach ($detalles_doc as $det)
            {
                $html.='<td style="text-align:center;font-family:Verdana;font-style:normal;font-size:6pt;">'.$det.'</td>';
            }
            $html.='<td>'.$doc->doc_gestion.'</td>';
            $html.='</tr>';
        }
        $pg_date = $pre_fecha_prestamo;
        $date_obj = date_create_from_format('Y-m-d', $pg_date);
        $pre_fecha_prestamo = date_format($date_obj, 'd/m/Y');
        $pg_date = $pre_fecha_devolucion;
        $date_obj = date_create_from_format('Y-m-d', $pg_date);
        $pre_fecha_devolucion = date_format($date_obj, 'd/m/Y');
        $row_admin=$this->Prestamo_model->row_adminHab($pre_autorizado);
        $html.=
            '</tbody>
                </table>
                <br>
                <p style="text-indent: 30px;text-align:justify;font-family:Verdana;font-style: italic;">
                    La documentación en préstamo deberá ser devuelta en un plazo de <b>5 días hábiles</b>, computables a
                     partir de la suscripción de la presente Acta y sea en estricta sujeción a lo establecido por el 
                     Art. 84 del Decreto Supremo Nº 27113 – Reglamento a la Ley Nº 234 del Procedimiento Administrativo.
                </p>    
                <p style="text-indent: 30px;text-align:justify;font-family:Verdana;font-style: italic;">
                La negativa o vulneración a este aspecto será sancionado de acuerdo al Reglamento Interno del 
                Gobierno Autónomo Municipal de El Alto.
                </p><br>
               
                     <table style=" padding-top: 4pt;  padding-bottom: 4pt;">
                          <thead>
                              <tr style="background-color:lightgrey;">
                                 <th width="25%"><b>FECHA DE PRESTAMO</b></th>
                                 <th width="27%"><b>FECHA DE DEVOLUCION</b></th>
                                 <th width="48%"><b>OBSERVACIONES</b></th>
                              </tr>
                           </thead>   
                          <tbody>
                              <tr>
                                <td width="25%"><img src="'.base_url().'assets/svg/calendario.svg" width="15px" heigth="9px"><span style="font-size:11pt;"> '.$pre_fecha_prestamo.'</span></td>
                                <td width="27%"><img src="'.base_url().'assets/svg/calendario.svg" width="15px" heigth="9px"><span style="font-size:11pt;"> '.$pre_fecha_devolucion.'</span></td>
                                <td width="48%" style="font-size:6pt;">'.$pre_observaciones.'</td>
                              </tr>
                          </tbody>
                     </table>
                     <br><br>
                     
                     <table style="border: 0px none white;border-collapse: separate; padding-top: 80pt">
                        <tr>
                            <td style="text-align:center;font-family:Verdana;font-style:normal;font-size:8pt;">
                                
                                <b>Entregue Conforme</b><br>
                                '.$data_session['usu_nombres'].' '.$data_session['usu_paterno'].' '.$data_session['usu_materno'].'<br>
                                <b>'.$data_session['rol_nombre'].'</b>
                            </td>
                            <td style="text-align:center;font-family:Verdana;font-style:normal;font-size:8pt;">
                                    
                                    <b>Recibi Conforme</b><br>
                                    ..................................................<br>
                                    ..................................................<br>
                                    Cel:.................................
                            </td>
                            <td style="text-align:center;font-family:Verdana;font-style:normal;font-size:8pt;">
                                    
                                    <b>Autorizado Conforme</b><br>
                                    '.$row_admin->usu_nombres.' '.$row_admin->usu_paterno.' '.$row_admin->usu_materno.'<br>
                                    <b>RESPONSABLE DE ARCHIVO FINANCIERO<br>Corp:</b>69960407
                            </td>
                         </tr>
                     </table>
               <p style="text-align:justify;font-family:Verdana;font-style: italic;font-size:8pt;">
                <b>IMPORTANTE:</b> En caso de deterioro o perdida de la documentación, la responsabilidad recae en 
                Servidor(a) Publico que solicito el préstamo, sin perjuicio de las sanciones establecidas por normativa legal vigente. 
                </p>';
        // output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        //Close and output PDF document

        $pdf->Output('doc_prestamo.pdf', 'I');
    }

    ////////USERS///////////////
    public function users_index()
    {
        $data_session = $this->session->all_userdata();
        if (isset($data_session)) {
            if ($data_session['rol_nombre'] == 'ADMINISTRADOR' and $data_session['hab_estado'] == true) {
                $this->load->view('head', $data_session);
                date_default_timezone_set('America/La_Paz');
                $time = time();
                $fecha_actual = date("Y-m-d", $time);
                $pre['count']=$this->Prestamo_model->cant_Prestamos($fecha_actual);
                $pre['mora']=$this->Prestamo_model->list_prestamos_mora($fecha_actual);
                $this->load->view('menu_admin', $pre);
                $data['usuario']=$this->User_model->get_all_data_user();
                $this->load->view('USERS/users_admin',$data);
            } else {
                redirect('/Login_control/close_session', 'refresh');
            }
        } else {
            redirect('/Login_control/close_session', 'refresh');
        }
    }
    public function add_user()
    {
        $usu_paterno = strip_tags(trim(strtoupper($this->input->post('usu_paterno'))));
        $usu_paterno = ('' == $usu_paterno) ? null : $usu_paterno;

        $usu_materno = strip_tags(trim(strtoupper($this->input->post('usu_materno'))));
        $usu_materno = ('' == $usu_materno) ? null :$usu_materno;

        $usu_nombres = strip_tags(trim(strtoupper($this->input->post('usu_nombres'))));
        $usu_nombres = ('' == $usu_nombres) ? null : $usu_nombres;

        $usu_ci = strip_tags(trim($this->input->post('usu_ci')));
        $usu_ci = ('' == $usu_ci) ? null : $usu_ci;

        $dep_id = strip_tags(trim($this->input->post('usu_emitido')));
        $dep_id = ('' == $dep_id) ? null : $dep_id;

        $usu_genero = strip_tags(trim($this->input->post('usu_genero')));
        $usu_genero = ('' == $usu_genero) ? null : $usu_genero;

        $hab_nombreusuario = strip_tags(trim(strtolower($this->input->post('usu_nombreusu'))));
        $hab_nombreusuario = ('' == $hab_nombreusuario) ? null : $hab_nombreusuario;

        $hab_password = strip_tags(trim($this->input->post('usu_pass')));
        $hab_password = ('' == $hab_password) ? null : $hab_password;

        $hab_estado = 'TRUE';

        $hab_fecha = strip_tags(trim($this->input->post('usu_creacion')));
        $hab_fecha = ('' == $hab_fecha) ?  null : $hab_fecha;

        $rol_id = strip_tags(trim($this->input->post('usu_rol')));
        $rol_id = ('' == $rol_id) ? null : $rol_id;

        $data_u = array(
            'usu_paterno' => $usu_paterno,
            'usu_materno' => $usu_materno,
            'usu_nombres' => $usu_nombres,
            'usu_ci' => $usu_ci,
            'dep_id' => $dep_id,
            'usu_genero' => $usu_genero
        );



        /*$this->User_model->Add_User($data_u);
        $this->User_model->Add_User_H($data_h);*/
        if ($this->User_model->Add_User($data_u)) {
            $usu_id=$this->db->insert_id();
            $data_h = array(
                'hab_nombreusuario' => $hab_nombreusuario,
                'hab_password' => $hab_password,
                'hab_estado' => $hab_estado,
                'hab_fecha' => $hab_fecha,
                'rol_id' => $rol_id,
                'usu_id' => $usu_id
            );
            if ($this->User_model->Add_User_H($data_h)){
                redirect("Admin_control/users_index");
            }
            else
                echo "Existe los Datos del DTM que desea Registrar";
        }
        else
            echo "Existe los Datos del DTM que desea Registrar";
    }
    public function get_user($usu_id)
    {
        $data = $this->User_model->GetUser($usu_id);
        echo json_encode($data);
    }
    public function edit_user()
    {
        $usu_id = strip_tags(trim($this->input->post('usu_id')));

        $hab_nombreusuario = strip_tags(trim(strtolower($this->input->post('usu_nombreusu'))));
        $hab_nombreusuario = ('' == $hab_nombreusuario) ? null : $hab_nombreusuario;

        $hab_password = strip_tags(trim($this->input->post('usu_pass')));
        $hab_password = ('' == $hab_password) ? null : $hab_password;

        $hab_estado = $this->input->post('hab_estado');
        if ($hab_estado=='habilitado')
            $hab_estado=true;
        else
            $hab_estado=false;

        $data = array(
            'hab_nombreusuario' => $hab_nombreusuario,
            'hab_password' => $hab_password,
            'hab_estado' => $hab_estado
        );        
	if($this->User_model->Update_User($usu_id,$data)){
            redirect("Admin_control/users_index");
        }else{
            echo "No existen los datos que desea registrar";
        }
    }

    /* Desarrollado por:
        Lic. Mark Erik Copa
        Ing. Nelson Erwin Aleluya
        G.A.M.E.A.
        2018 */
}