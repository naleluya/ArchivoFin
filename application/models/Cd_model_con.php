<?phpdefined('BASEPATH') OR exit('No direct script access allowed');class Cd_model_con extends CI_Model{    var $table = 'cd';    var $select_column = array('cd_id','cd_nro', 'cd_correlativo', 'cd_beneficiario','cd_estante', 'cd_descripcion', 'cd_cuce','cd_sisin','cd_adjuntar');    var $order_column =  array(null,'cd_nro', 'cd_correlativo', 'cd_beneficiario',null, 'cd_descripcion', 'cd_cuce','cd_sisin',null);    public function __construct()    {        parent::__construct();        $this->load->database();    }    function make_query()    {        $this->db->select($this->select_column);        $this->db->from($this->table);        if (isset($_POST["search"]["value"])) {            $this->db->like("cd_nro", $_POST["search"]["value"]);            $this->db->or_like("cd_correlativo", $_POST["search"]["value"]);            $this->db->or_like("cd_beneficiario", $_POST["search"]["value"]);            $this->db->or_like("cd_estante", $_POST["search"]["value"]);            $this->db->or_like("cd_descripcion", $_POST["search"]["value"]);            $this->db->or_like("cd_cuce", $_POST["search"]["value"]);            $this->db->or_like("cd_sisin", $_POST["search"]["value"]);        }        if (isset($_POST["order"])) {            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);        } else {            $this->db->order_by("cd_id", "DESC");        }    }    function make_dataTables()    {        $this->make_query();        if ($_POST["length"] != -1) {            $this->db->limit($_POST["length"], $_POST["start"]);        }        $query = $this->db->get();        return $query->result();    }    function get_filtered_data()    {        $this->make_query();        $query = $this->db->get();        return $query->num_rows();    }    function get_all_data()    {        $this->db->select($this->select_column);        $this->db->from($this->table);        return $this->db->count_all_results();    }    function list_cdComplement($cd_id)    {        $this->db->where('cd_id',$cd_id);        $query=$this->db->get($this->table);        return $query->row();    }    public function delete_CD($id)    {        $this->db->where('cd_id',$id);        $this->db->delete('cd');        if ($this->db->affected_rows()) {            return true;        } else {            return falses;        }    }    public function saveCD($data)    {        $this->db->insert('cd', $data);        if ($this->db->affected_rows())            return true;        else            return false;    }    public function getCD($id)    {        $this->db->from('cd');        $this->db->where('cd_id', $id);        $query = $this->db->get();        return $query->row();    }    public function updateCD($id,$data)    {        $this->db->where('cd_id', $id);        $this->db->update('cd', $data);        if ($this->db->affected_rows() > 0)            return true;        else            return false;    }    public function uploadCD($data, $cd_id){        $this->db->where('cd_id', $cd_id);        $this->db->update('cd', $data);        if ($this->db->affected_rows() > 0)            return true;        else            return false;    }    public function deletePDF_CD($cd_id, $data)    {        $this->db->where('cd_id', $cd_id);        $this->db->update('cd', $data);        if ($this->db->affected_rows() > 0)            return true;        else            return false;    }    public function verificar_CD_Nro($cd_nro, $cd_gestion)    {        $this->db->where('cd_nro', $cd_nro);        $this->db->where('cd_gestion', $cd_gestion);        $this->db->get('cd');        if ($this->db->affected_rows() > 0)            return false;        else            return true;    }    public function verificar_CD_Nro_Update($cd_nro, $cd_gestion)    {        $this->db->where('cd_nro', $cd_nro);        $this->db->where('cd_gestion', $cd_gestion);        $this->db->get('cd');        if ($this->db->affected_rows() > 1)            return false;        else            return true;    }}