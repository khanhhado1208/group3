<?php namespace App\Models;

class Stonks extends Database {
    //FETCH ALL STONK PROPERTIES
    public function get_stonk_properties()
    {
        return $this->db->table('stonks')->select('*')->join('issuers', 'stonks.issuer_id = issuers.issuer_id', 'inner')->where('stonk_id > 1')->get()->getResult();
    }
}