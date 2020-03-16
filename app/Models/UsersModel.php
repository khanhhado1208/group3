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

}