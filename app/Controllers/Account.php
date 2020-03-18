<?php namespace App\Controllers;
 
use CodeIgniter\Controller;
use App\Models\UsersModel;
use App\Controllers\Pages;
 
class Account extends Controller
{
    //ACCOUNT PANEL
    public function index() {
        $session = \Config\Services::session();
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
            return redirect()->to('/dashboard'); 
        } else {
            return redirect()->to('/'); 
          }
    }

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
            return redirect()->to('/failadd'); 
        } else {
            if (!$val)
            {
                return redirect()->to('/register'); 
    
            }
            else
            { 
        
                $model->save([
                    'username' => $this->request->getVar('username'),
                    'password'  => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
                ]);
                $data['title'] = ucfirst('success');
                return redirect()->to('/success'); 
            }
        }

    }

    //AUTHENTICATE USER
    public function authenticate(){
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        $model = new UsersModel();
        $success = $model->check_credentials($username, $password);
        if ($success){
            return redirect()->to('/loginsuccess'); 
        } else {
            return redirect()->to('/loginfailure'); 
        }
    }

    //LOGOUT USER
    public function logout(){
        $session = \Config\Services::session();
        unset($_SESSION['logged_in']);
        unset($_SESSION['username']);
        return redirect()->to('/'); 
    }
}