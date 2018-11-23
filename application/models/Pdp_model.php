<?phpdefined('BASEPATH') OR exit('No direct script access allowed');class Pdp_model extends CI_Model{    var $table = 'pdp';    var $select_column = array('pdp_id','pdp_nro', 'pdp_correlativo', 'pdp_beneficiario','pdp_estante', 'pdp_descripcion', 'pdp_cuce','pdp_sisin','pdp_adjuntar');    var $order_column =  array(null,'pdp_nro', 'pdp_correlativo', 'pdp_beneficiario',null, 'pdp_descripcion', 'pdp_cuce','pdp_sisin',null);    public function __construct()    {        parent::__construct();        $this->load->database();    }    function make_query()    {        $this->db->select($this->select_column);        $this->db->from($this->table);        if (isset($_POST["search"]["value"])) {            $this->db->like("pdp_nro", $_POST["search"]["value"]);            $this->db->or_like("pdp_correlativo", $_POST["search"]["value"]);            $this->db->or_like("pdp_beneficiario", $_POST["search"]["value"]);            $this->db->or_like("pdp_descripcion", $_POST["search"]["value"]);            $this->db->or_like("pdp_cuce", $_POST["search"]["value"]);            $this->db->or_like("pdp_sisin", $_POST["search"]["value"]);        }        if (isset($_POST["order"])) {            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);        } else {            $this->db->order_by("pdp_id", "DESC");        }    }    function make_dataTables()    {        $this->make_query();        if ($_POST["length"] != -1) {            $this->db->limit($_POST["length"], $_POST["start"]);        }        $query = $this->db->get();        return $query->result();    }    function get_filtered_data()    {        $this->make_query();        $query = $this->db->get();        return $query->num_rows();    }    function get_all_data()    {        $this->db->select($this->select_column);        $this->db->from($this->table);        return $this->db->count_all_results();    }    function list_pdpComplement($pdp_id)    {        $this->db->where('pdp_id',$pdp_id);        $query=$this->db->get($this->table);        return $query->row();    }    public function delete_PDP($id)    {        $this->db->where('pdp_id',$id);        $this->db->delete('pdp');        if ($this->db->affected_rows()) {            return true;        } else {            return falses;        }    }    public function savePDP($data)    {        $this->db->insert('pdp', $data);        if ($this->db->affected_rows())            return true;        else            return false;    }    public function getPDP($id)    {        $this->db->from('pdp');        $this->db->where('pdp_id', $id);        $query = $this->db->get();        return $query->row();    }    public function updatePDP($id,$data)    {        $this->db->where('pdp_id', $id);        $this->db->update('pdp', $data);        if ($this->db->affected_rows() > 0)            return true;        else            return false;    }    public function uploadPDP($data, $pdp_id){        $this->db->where('pdp_id', $pdp_id);        $this->db->update('pdp', $data);        if ($this->db->affected_rows() > 0)            return true;        else            return false;    }    public function deletePDF_PDP($pdp_id, $data)    {        $this->db->where('pdp_id', $pdp_id);        $this->db->update('pdp', $data);        if ($this->db->affected_rows() > 0)            return true;        else            return false;    }    public function verificar_PDP_Nro($pdp_nro, $pdp_gestion)    {        $this->db->where('pdp_nro', $pdp_nro);        $this->db->where('pdp_gestion', $pdp_gestion);        $this->db->get('pdp');        if ($this->db->affected_rows() > 0)            return false;        else            return true;    }    public function verificar_PDP_Nro_Update($pdp_nro, $pdp_gestion)    {        $this->db->where('pdp_nro', $pdp_nro);        $this->db->where('pdp_gestion', $pdp_gestion);        $this->db->get('pdp');        if ($this->db->affected_rows() > 1)            return false;        else            return true;    }}