<?php namespace App\Models;

class Stonks extends Database {
    //FETCH ALL STONK PROPERTIES
    public function get_stonk_properties()
    {
        try {
            $query = $this->db->query(
                'SELECT * FROM stonks
                INNER JOIN issuers ON stonks.issuer_id = issuers.issuer_id
                WHERE stonk_id > 1'
            );
        } catch (\Throwable $th) {
            return [];
        }
        return $query->getResult();
    }
}