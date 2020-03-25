<?php namespace App\Models;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
 
class UsersModel extends Model
{
    
    protected $table = 'users';
 
    protected $allowedFields = ['username', 'password'];


    //PROCESS A TOPUP OR WITHDRAW TRANSACTION
    function money_transaction($username, $amount, $message) {
        $db = \Config\Database::connect();

        try {
            $query = $db->query('SELECT (user_id) FROM users WHERE username="'.$username.'"');
            $id = $query->getRow()->user_id;
    
            $query = $db->query('INSERT INTO transactions (user_id, tx_type, tx_value, stonk_id, stonk_amount) VALUES ('.$id.',"'.$message.'",'.$amount.', "1", "0")');
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }

    //CHECK USER BALANCE FROM TRANSACTION HISTORY
    function check_balance($username) {
        $db = \Config\Database::connect();

        try {
            $query = $db->query('SELECT (user_id) FROM users WHERE username="'.$username.'"');
            $id = $query->getRow()->user_id;

            $query = $db->query('SELECT SUM(tx_value) AS balance FROM transactions WHERE user_id = '.$id.'');
            $balance = $query->getRow()->balance;
        } catch (\Throwable $th) {
            return 0;
        }
        return $balance;
    }

    //CHECK USER TRANSACTION HISTORY
    function check_transaction_history($username) {
        $db = \Config\Database::connect();

        try {
            $query = $db->query('SELECT (user_id) FROM users WHERE username="'.$username.'"');
            $id = $query->getRow()->user_id;

            $query = $db->query('SELECT * FROM transactions 
                INNER JOIN stonks ON transactions.stonk_id = stonks.stonk_id
                WHERE user_id = '.$id.' ORDER BY tx_date ASC'
            );
            $history = $query->getResult();
        } catch (\Throwable $th) {
            return null;
        }
        return $history;
    }

    //CHECK IF USER DATABASE ENTRY EXISTS
    function user_exists($username) {
        $db = \Config\Database::connect();

        $query = $db->query('SELECT 1 FROM users WHERE username="'.$username.'"');
        $results = $query->getResult();

        if (count($results) > 0){
            return true;
        }
        else{
            return false;
        }
    }

    //CHECK USER CREDENTIALS ON LOGIN
    function check_credentials($username, $password) {
        $db = \Config\Database::connect();
        $session = \Config\Services::session();
        $query = $db->query('SELECT * FROM users WHERE (username="'.$username.'")');
        if (count($query->getResult()) > 0){
            $results = $query->getRow();
            if (password_verify($password, $results->password) == true){
                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = $username;
                return true;
            } else {
                unset($_SESSION['logged_in']);
                unset($_SESSION['username']);
                return false;
            }
        }
        else{
            unset($_SESSION['logged_in']);
            unset($_SESSION['username']);
            return false;
        }
    }

    //FETCH ALL STONK NAMES
    function get_stonk_names() {
        $db = \Config\Database::connect();

        $query = $db->query('SELECT stonk_name FROM stonks WHERE stonk_id > 1');
        $results = $query->getResult();

        $stonk_names = [];

        foreach($results as $row) {
            array_push($stonk_names, $row->stonk_name);
        }

        return $stonk_names;
    }

    //FETCH STONK PROPERTIES
    function get_stonk_properties($id) {
        $db = \Config\Database::connect();

        $query = $db->query('SELECT * FROM stonks
            INNER JOIN issuers ON stonks.issuer_id = issuers.issuer_id
            WHERE stonk_id ='.$id.''
        );

        return $query->getRow();
    }

    //CREATE TABLES AND ADD DEFAULT STOCKS IN DATABASE
    function initialize_database() {
        $db = \Config\Database::connect();

        try {

            //Create tables
            $query = $db->query('CREATE TABLE `users` (
                `user_id` INT NOT NULL AUTO_INCREMENT UNIQUE,
                `username` varchar(255) NOT NULL UNIQUE,
                `password` varchar(255) NOT NULL UNIQUE,
                `create_date` TIMESTAMP NOT NULL,
                PRIMARY KEY (`user_id`)
            )');

            $query = $db->query('CREATE TABLE `issuers` (
                `issuer_id` INT NOT NULL AUTO_INCREMENT UNIQUE,
                `issuer_name` varchar(255) NOT NULL UNIQUE,
                `issuer_desc` TEXT NOT NULL,
                `sponsored` BOOLEAN NOT NULL,
                PRIMARY KEY (`issuer_id`)
            )');

            $query = $db->query('CREATE TABLE `stonks` (
                `stonk_id` INT NOT NULL AUTO_INCREMENT,
                `stonk_name` varchar(255) NOT NULL,
                `issuer_id` INT NOT NULL,
                `stonk_desc` TEXT NOT NULL,
                `stonk_tradable` BOOLEAN NOT NULL,
                PRIMARY KEY (`stonk_id`),
                FOREIGN KEY (`issuer_id`) REFERENCES issuers(issuer_id)
            )');

            $query = $db->query('CREATE TABLE `transactions` (
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
            $query = $db->query('INSERT INTO issuers
                (issuer_id, issuer_name, issuer_desc, sponsored) VALUES
                (1, "Default Issuer", "Default Issuer", false)'
            );

            $query = $db->query('INSERT INTO stonks
                (stonk_name, issuer_id, stonk_desc, stonk_tradable) VALUES
                ("Currency Transaction", 1, "Currency Transaction", false)'
            );

            //Add default issuer and a few stonks
            $default_issuer_names = ['ACME Company LTD', 'Worldwide Trading Co', 'United Refineries'];
            $default_issuer_desc = 'A vibrant and well-respected company in their field.';

            $query = $db->query('INSERT INTO issuers
                (issuer_id, issuer_name, issuer_desc, sponsored) VALUES
                (2, "'.$default_issuer_names[array_rand($default_issuer_names)].'", "'.$default_issuer_desc.'", false)'
            );

            for ($i = rand(1, 5); $i > 0; $i--) {
                $query = $db->query('INSERT INTO stonks 
                    (stonk_name, issuer_id, stonk_desc, stonk_tradable) VALUES
                    ("Stock", 2, "Stock", true)'
                );
            }
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }

    function drop_db_tables() {
        $db = \Config\Database::connect();
        try {
            $query = $db->query('DROP TABLE transactions');
            $query = $db->query('DROP TABLE stonks;');
            $query = $db->query('DROP TABLE issuers');
            $query = $db->query('DROP TABLE users');
        } catch(\Throwable $th) {
            return false;
        }
        return true;
    }

}