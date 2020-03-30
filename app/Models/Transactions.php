<?php namespace App\Models;

class Transactions extends Database {
    //PROCESS A TOPUP OR WITHDRAW TRANSACTION
    public function money_transaction($username, $amount, $message)
    {
        try {
            $query = $this->db->query('SELECT (user_id) FROM users WHERE username="'.$username.'"');
            $id = $query->getRow()->user_id;
    
            $query = $this->db->query('INSERT INTO transactions (user_id, tx_type, tx_value, stonk_id, stonk_amount) VALUES ('.$id.',"'.$message.'",'.$amount.', "1", "0")');
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }
    //PROCESS A STONK TRANSACTION
    public function stonk_transaction($username, $moneyamount, $stonkid, $stonkamount, $message)
    {
        try {
            $query = $this->db->query('SELECT (user_id) FROM users WHERE username="'.$username.'"');
            $id = $query->getRow()->user_id;

            $query = $this->db->query('INSERT INTO 
                transactions (user_id, tx_type, tx_value, stonk_id, stonk_amount)
                VALUES ('.$id.', "'.$message.'", '.$moneyamount.', '.$stonkid.', '.$stonkamount.')');
        } catch (\Throwable $th) {
            log_message('error', $th);
            return false;
        }
        return true;
    }
}