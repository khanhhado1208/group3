<?php namespace App\Controllers;
 
use CodeIgniter\Controller;
use App\Models\UsersModel;
 
class Account extends Controller
{

    //CREATE USERS DATABASE
    public function setupdb(){
        $model = new UsersModel();
        $success = $model->initialize_database();
        $data['title'] = ucfirst('create database');
        echo view('templates/header', $data);
        echo view('templates/nav', $data);
        if ($success){
            echo view('pages/dbcreated');
        } else {
            echo view('pages/dbcreatefailed');
        }
        echo view('templates/footer', $data);
    }

    //CREATE USER
    public function create()
    {  
 
    helper(['form', 'url']);
        $val = $this->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
 
        $model = new UsersModel();
 
        //CHECK IF USER EXIST
        if ($model->user_exists($this->request->getVar('username'))) {
            $data['title'] = ucfirst('failadd');
            echo view('templates/header', $data);
            echo view('templates/nav', $data);
            echo view('pages/failadd');
            echo view('templates/footer', $data);
        } else {
            if (!$val)
            {
                $data['title'] = ucfirst('register');
                echo view('templates/header', $data);
                echo view('templates/nav', $data);
                echo view('pages/register', [
                    'validation' => $this->validator
                ]);
                echo view('templates/footer', $data);
    
            }
            else
            { 
        
                $model->save([
                    'username' => $this->request->getVar('username'),
                    'password'  => $this->request->getVar('password'),
                ]);
                $data['title'] = ucfirst('success');
                echo view('templates/header', $data);
                echo view('templates/nav', $data);
                echo view('pages/success');
                echo view('templates/footer', $data);
            }
        }

    }
    public function authenticate(){
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        $data['title'] = ucfirst('login');
        echo view('templates/header', $data);
        echo view('templates/nav', $data);
        $model = new UsersModel();
        $success = $model->check_credentials($username, $password);
        if ($success){
            echo view('pages/loginsuccess');
        } else {
            echo view('pages/loginfailure');
        }
        echo view('templates/footer', $data);
    }
}