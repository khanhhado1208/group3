<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Controllers\Error;
use App\Controllers\Pages;
use App\Models\Tables;

class Admin extends BaseController
{
    //Index redirect
    public function index()
    {
        $pages = new Pages();
        $pages->get('admin');
    }
    //Create tables in database
    public function setupdb()
    {
        $pages = new Pages();
        $error = new Error();
        $tables = new Tables();
        $success = $tables->initialize_database();
        if ($success) {
            $error->setErrorState('success', 'Tables created');
            $pages->get('home');
        } else {
            $error->setErrorState('danger', 'Tables not created');
            $pages->get('home');
        }
    }
    //Drop tables from database
    public function dropdb()
    {
        $pages = new Pages();
        $error = new Error();
        $tables = new Tables();
        $success = $tables->drop_db_tables();
        if ($success) {
            $error->setErrorState('success', 'Tables removed');
            $pages->get('home');
        } else {
            $error->setErrorState('danger', 'Error while removing tables');
            $pages->get('home');
        }
    }
}
