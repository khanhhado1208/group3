<?php
namespace App\Models;

class Users extends Database
{
    protected $table = 'users';
    public $allowedFields = ['username', 'password', 'avatar', 'disabled'];

    public function addUser($username, $password)
    {
        $this->save(
            [
                'username' => $username,
                'password' => $password,
                'avatar' => 'img/default.jpg',
                'disabled' => false
            ]
        );
    }

    //CHECK IF USER DATABASE ENTRY EXISTS
    public function user_exists($username)
    {
        $results = $this->db->table('users')->select('1')->where('username', $username)->get()->getResult();
        if (count($results) > 0) {
            return true;
        } else {
            return false;
        }
    }
    //CHECK USER CREDENTIALS ON LOGIN
    public function check_credentials($username, $password)
    {
        $query = $this->db->table('users')->select('*')->where('username', $username)->get();
        if (count($query->getResult()) > 0) {
            $results = $query->getRow();
            if (password_verify($password, $results->password) == true && $results->disabled == false) {
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
            $query = $this->db->table('users')->select('user_id')->where('username', $username)->get();
            $id = $query->getRow()->user_id;

            $query = $this->db->table('transactions')->selectSum('tx_value', 'balance')->where('user_id', $id)->get();
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
            $query = $this->db->table('users')->select('user_id')->where('username', $username)->get();
            $id = $query->getRow()->user_id;

            $query = $this->db->table('transactions')->select('*')->join('stonks', 'transactions.stonk_id = stonks.stonk_id', 'inner')->where('user_id', $id)->orderBy('tx_date', 'ASC')->get();
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
            $query = $this->db->table('users')->select('user_id')->where('username', $username)->get();
            $id = $query->getRow()->user_id;
            $query = $this->db->table('transactions')->select('transactions.stonk_id, stonk_name')->selectSum('stonk_amount')
                ->join('stonks', 'transactions.stonk_id = stonks.stonk_id', 'inner')->where('user_id', $id)->where('transactions.stonk_id > 1')
                ->groupBy('stonk_id')->get();
            $user_stonks = $query->getResult();
        } catch (\Throwable $th) {
            return [];
        }
        return $user_stonks;
    }
    //GET USER AVATAR
    function getavatar($username)
    {
        $avatar = "img/default.jpg";
        try {
            $query = $this->db->table('users')->select('avatar')->where('username', $username)->get();
            $avatar = $query->getRow()->avatar;
        } catch (\Throwable $th) {
            return "img/default.jpg";
        }
        return $avatar;
    }
    //SET USER AVATAR
    function setavatar($username, $file)
    {
        try {
            $query = $this->db->table('users')->select('user_id')->where('username', $username)->get();
            $id = $query->getRow()->user_id;
            $query = $this->db->table('users')->set('avatar', $file)->where('user_id', $id)->update();
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }
    //DELETE USER
    function removeuser($username)
    {
        try {
            $query = $this->db->table('users')->select('user_id')->where('username', $username)->get();
            $id = $query->getRow()->user_id;
            $query = $this->db->table('transactions')->delete(['user_id' => $id]);
            $query = $this->db->table('users')->delete(['username' => $username]);
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }
    //DELETE USER ACTIVITY
    function deleteinfo($username)
    {
        try {
            $query = $this->db->table('users')->select('user_id')->where('username', $username)->get();
            $id = $query->getRow()->user_id;
            $query = $this->db->table('transactions')->delete(['user_id' => $id]);
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }
    //DISABLE ACCOUNT
    function disable($username)
    {
        try {
            $query = $this->db->table('users')->select('user_id')->where('username', $username)->get();
            $id = $query->getRow()->user_id;
            $query = $this->db->table('users')->set('disabled', true)->where('user_id', $id)->update();
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }
}
