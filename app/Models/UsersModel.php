<?php namespace App\Models;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
 
class UsersModel extends Model
{
    
    protected $table = 'users';
 
    protected $allowedFields = ['username', 'password'];

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

    //CREATE USERS TABLE IN DATABASE
    function initialize_database() {
        $db = \Config\Database::connect();
        try {
            $query = $db->query('CREATE TABLE users(
                id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
                username varchar(100) NOT NULL,
                password varchar(100) NOT NULL) DEFAULT CHARSET=utf8');
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

}