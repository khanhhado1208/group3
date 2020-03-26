<?php namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $db;

    protected $table = 'users';
 
    protected $allowedFields = ['username', 'password'];

    //MODEL CONSTRUCTOR
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

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
        $session = \Config\Services::session();
        $query = $this->db->query('SELECT * FROM users WHERE (username="'.$username.'")');
        if (count($query->getResult()) > 0) {
            $results = $query->getRow();
            if (password_verify($password, $results->password) == true) {
                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = $username;
                return true;
            } else {
                unset($_SESSION['logged_in']);
                unset($_SESSION['username']);
                return false;
            }
        } else {
            unset($_SESSION['logged_in']);
            unset($_SESSION['username']);
            return false;
        }
    }

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

    //CREATE TABLES AND ADD DEFAULT STOCKS IN DATABASE
    public function initialize_database()
    {
        try {

            //Create tables
            $query = $this->db->query('CREATE TABLE `users` (
                `user_id` INT NOT NULL AUTO_INCREMENT UNIQUE,
                `username` varchar(255) NOT NULL UNIQUE,
                `password` varchar(255) NOT NULL UNIQUE,
                `create_date` TIMESTAMP NOT NULL,
                PRIMARY KEY (`user_id`)
            )');

            $query = $this->db->query('CREATE TABLE `issuers` (
                `issuer_id` INT NOT NULL AUTO_INCREMENT UNIQUE,
                `issuer_name` varchar(255) NOT NULL UNIQUE,
                `issuer_desc` TEXT NOT NULL,
                `sponsored` BOOLEAN NOT NULL,
                PRIMARY KEY (`issuer_id`)
            )');

            $query = $this->db->query('CREATE TABLE `stonks` (
                `stonk_id` INT NOT NULL AUTO_INCREMENT,
                `stonk_name` varchar(255) NOT NULL,
                `issuer_id` INT NOT NULL,
                `stonk_desc` TEXT NOT NULL,
                `stonk_tradable` BOOLEAN NOT NULL,
                PRIMARY KEY (`stonk_id`),
                FOREIGN KEY (`issuer_id`) REFERENCES issuers(issuer_id)
            )');

            $query = $this->db->query('CREATE TABLE `transactions` (
                `tx_id` INT NOT NULL AUTO_INCREMENT UNIQUE,
                `user_id` INT NOT NULL ,
                `tx_value` INT NOT NULL,
                `tx_type` varchar(255),
                `stonk_id` INT NOT NULL,
                `stonk_amount` INT NOT NULL,
                `tx_date` TIMESTAMP NOT NULL,
                PRIMARY KEY (`tx_id`),
                FOREIGN KEY (`user_id`) REFERENCES users(user_id),
                FOREIGN KEY (`stonk_id`) REFERENCES stonks(stonk_id)
            )');

            //Add default issuer and dummy stonk for money transactions
            $query = $this->db->query(
                'INSERT INTO issuers
                (issuer_id, issuer_name, issuer_desc, sponsored) VALUES
                (1, "Default Issuer", "Default Issuer", false)'
            );

            $query = $this->db->query(
                'INSERT INTO stonks
                (stonk_name, issuer_id, stonk_desc, stonk_tradable) VALUES
                ("", 1, "", false)'
            );

            //Add default issuer and a few stonks
            $default_issuer_names = ['ACME Company LTD', 'Worldwide Trading Co', 'United Refineries'];
            $default_issuer_desc = 'A vibrant and well-respected company in their field.';

            $query = $this->db->query(
                'INSERT INTO issuers
                (issuer_id, issuer_name, issuer_desc, sponsored) VALUES
                (2, "'.$default_issuer_names[array_rand($default_issuer_names)].'", "'.$default_issuer_desc.'", false)'
            );

            for ($i = rand(1, 5); $i > 0; $i--) {
                $query = $this->db->query(
                    'INSERT INTO stonks 
                    (stonk_name, issuer_id, stonk_desc, stonk_tradable) VALUES
                    ("Stock No. '.$i.'", 2, "Stock", true)'
                );
            }
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }

    public function drop_db_tables()
    {
        try {
            $query = $this->db->query('DROP TABLE transactions');
            $query = $this->db->query('DROP TABLE stonks;');
            $query = $this->db->query('DROP TABLE issuers');
            $query = $this->db->query('DROP TABLE users');
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }
}
