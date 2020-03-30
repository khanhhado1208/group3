<?php namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class Database extends Model {
    protected $db;

    //MODEL CONSTRUCTOR
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
}