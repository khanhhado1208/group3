<?php namespace App\Models;

class Users extends Database {
    protected $table = 'users';
 
    protected $allowedFields = ['username', 'password'];

    //CHECK IF USER DATABASE ENTRY EXISTS
    public function user_exists($username)
    {
        $query = $this->db->query('SELECT 1 FROM users WHERE username="'.$username.'"');
        $results = $query->getResult();

        if (count($results) > 0) {
            return true;
        } else {
            return false;
        }
    }
    //CHECK USER CREDENTIALS ON LOGIN
    public function check_credentials($username, $password)
    {
        $query = $this->db->query('SELECT * FROM users WHERE (username="'.$username.'")');
        if (count($query->getResult()) > 0) {
            $results = $query->getRow();
            if (password_verify($password, $results->password) == true) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    //CHECK USER BALANCE FROM TRANSACTION HISTORY
    public function check_balance($username)
    {
        try {
            $query = $this->db->query('SELECT (user_id) FROM users WHERE username="'.$username.'"');
            $id = $query->getRow()->user_id;

            $query = $this->db->query('SELECT SUM(tx_value) AS balance FROM transactions WHERE user_id = '.$id.'');
            $balance = $query->getRow()->balance;
        } catch (\Throwable $th) {
            return 0;
        }
        return $balance;
    }
    //CHECK USER TRANSACTION HISTORY
    public function check_transaction_history($username)
    {
        try {
            $query = $this->db->query('SELECT (user_id) FROM users WHERE username="'.$username.'"');
            $id = $query->getRow()->user_id;

            $query = $this->db->query(
                'SELECT * FROM transactions 
                INNER JOIN stonks ON transactions.stonk_id = stonks.stonk_id
                WHERE user_id = '.$id.' ORDER BY tx_date ASC'
            );
            $history = $query->getResult();
        } catch (\Throwable $th) {
            return [];
        }
        return $history;
    }
    //FETCH ALL STONKS OWNED BY USER
    public function get_user_stonks($username)
    {
        try {
            $query = $this->db->query('SELECT (user_id) FROM users WHERE username="'.$username.'"');
            $id = $query->getRow()->user_id;

            $query = $this->db->query(
                'SELECT transactions.stonk_id, stonk_name, SUM(stonk_amount) AS stonk_amount
                FROM transactions INNER JOIN stonks ON transactions.stonk_id = stonks.stonk_id
                WHERE user_id = '.$id.' AND transactions.stonk_id > 1
                GROUP BY stonk_id'
            );

            $user_stonks = $query->getResult();
        } catch (\Throwable $th) {
            return [];
        }
        return $user_stonks;
    }
    //DELETE USER
    function removeuser($username) {
        try{     
            $query = $this->db->query('SELECT (user_id) FROM users WHERE username = "'.$username.'" ');
            $id = $query->getRow()->user_id;
            $query = $this->db->query('DELETE FROM transactions WHERE user_id = "'.$id.'" ');
            $query = $this->db->query('DELETE FROM users WHERE username = "'.$username.'" ');
        } catch(\Throwable $th) {     
            return false;
        }
            return true;
    }
}