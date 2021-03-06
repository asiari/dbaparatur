<?php
class m_synchronize extends CI_Model{
    function ambil_nip_pegawai_current_db(){
        $queryString = "SELECT nip FROM pegawai";
        $res = $this->db->query($queryString);
        return $res->result_array();
    }
    function ambil_nip_pegawai_other_db(){
        $queryString = "SELECT peg_nip FROM mpegawai WHERE peg_nip IS NOT NULL AND CAST(`peg_nip` AS UNSIGNED) > 0";
        $res = $this->db->query($queryString);
        return $res->result_array();
    }
    function ambil_nama_pegawai_other_db($postnip){
        $q = "SELECT peg_nm FROM mpegawai WHERE peg_nip = '$postnip' AND peg_nip IS NOT NULL AND CAST(`peg_nip` AS UNSIGNED) > 0";
        $ret['rows'] = $this->db->query($q)->row();

        $tmp = $this->db->query($q)->num_rows();
        $ret['num_rows'] = $tmp;

        return $ret;
    }
    function ambil_nama_pegawai_current_db($postnip){
        $q = "SELECT nip FROM pegawai WHERE nip = '$postnip'";
        $ret['rows'] = $this->db->query($q)->row();

        $tmp = $this->db->query($q)->num_rows();
        $ret['num_rows'] = $tmp;

        return $ret;
    }
    function tambahkan_ke_current_db_pegawai($iln, $genpass){
        $query = "INSERT INTO pegawai (nip, password, ref_role) "
                . "VALUES ('$iln', '$genpass', 7)";
        $run = $this->db->query($query);
        if ($run) {
            $this->db->trans_commit();
            return TRUE;
        } else {
            $this->db->trans_rollback();
            return FALSE;
        }
    }
    function get_bawahan_level_1($nip){
         //$queryString = "SELECT nip FROM pegawai WHERE peg_nip IS NOT NULL AND CAST(`peg_nip` AS UNSIGNED) > 0";
        $queryString = "SELECT nip FROM pegawai WHERE pejabat_penilai = '$nip'";
        $res = $this->db->query($queryString);
        $raw_res_nip = $res->result();
        
        $array_raw_res_nip = array();
        foreach ($raw_res_nip as $rrn):
            array_push($array_raw_res_nip, $rrn->nip);
        endforeach;
        $concat_raw_res_nip = implode("','",$array_raw_res_nip);
        $object_bawahan_level_1 = $this->ambil_nama_pegawai_other_db_Get_Bawahan($concat_raw_res_nip);
        $this->load->database('default',TRUE);
        return $object_bawahan_level_1;
    }
    function ambil_nama_pegawai_other_db_Get_Bawahan($concat_raw_res_nip){
        $q = "SELECT peg_nm, peg_nip FROM mpegawai WHERE peg_nip IN ('$concat_raw_res_nip')";
        $returnresults = $this->db->query($q)->result();
        return $returnresults;
    }
}
