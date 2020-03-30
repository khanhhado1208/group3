<?php
namespace App\Models;

class Transactions extends Database
{
    //PROCESS A TOPUP OR WITHDRAW TRANSACTION
    public function money_transaction($username, $amount, $message)
    {
        try {
            $query = $this->db->table('users')->select('user_id')->where('username', $username)->get();
            $id = $query->getRow()->user_id;
            $data = [
                'user_id' => $id,
                'tx_type' => $message,
                'tx_value' => $amount,
                'stonk_id' => '1',
                'stonk_amount' => '0'
            ];
            $query = $this->db->table('transactions')->insert($data);
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }
    //PROCESS A STONK TRANSACTION
    public function stonk_transaction($username, $moneyamount, $stonkid, $stonkamount, $message)
    {
        try {
            $query = $this->db->table('users')->select('user_id')->where('username', $username)->get();
            $id = $query->getRow()->user_id;
            $data = [
                'user_id' => $id,
                'tx_type' => $message,
                'tx_value' => $moneyamount,
                'stonk_id' => $stonkid,
                'stonk_amount' => $stonkamount
            ];
            $query = $this->db->table('transactions')->insert($data);
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }
}
