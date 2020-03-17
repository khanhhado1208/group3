<?php namespace App\Models;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
 
class UsersModel extends Model
{
    
    protected $table = 'users';
 
    protected $allowedFields = ['username', 'password'];

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

    function check_credentials($username, $password) {
        $db = \Config\Database::connect();
        $query = $db->query('SELECT 1 FROM users WHERE (username="'.$username.'" AND password="'.$password.'")');
        $results = $query->getResult();
        if (count($results) > 0){
            return true;
        }
        else{
            return false;
        }
    }

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