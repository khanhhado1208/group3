<?php namespace App\Controllers;
 
use CodeIgniter\Controller;
use App\Models\UsersModel;
 
class Account extends Controller
{
    //ACCOUNT PANEL
    public function index() {
        $session = \Config\Services::session();
        $data['title'] = ucfirst('account');
        echo view('templates/header', $data);
        echo view('templates/nav', $data);
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
            $data['username'] = $_SESSION['username'];
            echo view('pages/account', $data);
        } else {
            echo "You are not currently logged in";
          }

        echo view('templates/footer', $data);
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
                    'password'  => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
                ]);
                $data['title'] = ucfirst('success');
                echo view('templates/header', $data);
                echo view('templates/nav', $data);
                echo view('pages/success');
                echo view('templates/footer', $data);
            }
        }

    }

    //AUTHENTICATE USER
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

    //LOGOUT USER
    public function logout(){
        $session = \Config\Services::session();
        unset($_SESSION['logged_in']);
        unset($_SESSION['username']);
        return redirect()->to('/'); 
    }
}