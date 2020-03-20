<?php namespace App\Controllers;
 
use CodeIgniter\Controller;
use App\Models\UsersModel;
use App\Controllers\Pages;
 
class Account extends Controller
{
    //ACCOUNT PANEL
    public function index() {
        $pageHandler = new Pages;
        $session = \Config\Services::session();
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
            $pageHandler->get('dashboard');
        } else {
            $this->setErrorState('error', 'Not signed in');
            $pageHandler->get('home');
          }
    }

    //CREATE USERS DATABASE
    public function setupdb(){
        $pageHandler = new Pages;
        $model = new UsersModel();
        $success = $model->initialize_database();
        if ($success){
            $this->setErrorState('success', 'Table was created');
            $pageHandler->get('home');
        } else {
            $this->setErrorState('error', 'Table not created');
            $pageHandler->get('home');
        }
    }

    //CREATE USER
    public function create()
    {  
    $pageHandler = new Pages;
    helper(['form', 'url']);
        $val = $this->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
 
        $model = new UsersModel();
 
        //CHECK IF USER EXIST
        if ($model->user_exists($this->request->getVar('username'))) {
            $this->setErrorState('error', 'User already exists');
            $pageHandler->get('register');
        } else {
            if (!$val)
            {
                $this->setErrorState('error', 'Data not valid');
                $pageHandler->get('register');
    
            }
            else
            { 
        
                $model->save([
                    'username' => $this->request->getVar('username'),
                    'password'  => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
                ]);
                $this->setErrorState('success', 'New user created, please login');
                $pageHandler->get('login');
            }
        }

    }

    //AUTHENTICATE USER
    public function authenticate(){
        $pageHandler = new Pages;
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        $model = new UsersModel();
        $success = $model->check_credentials($username, $password);
        if ($success){
            $this->setErrorState('success', 'Authentication successful');
            $pageHandler->get('dashboard');
        } else {
            $this->setErrorState('error', 'Could not authenticate');
            $pageHandler->get('login');
        }
    }

    //LOGOUT USER
    public function logout(){
        $pageHandler = new Pages;
        $session = \Config\Services::session();
        unset($_SESSION['logged_in']);
        unset($_SESSION['username']);
        $this->setErrorState('success', 'Logged out');
        $pageHandler->get('home');
    }

    public $alertMessage;
    public $alertState;
    //ACCOUNT CONTROLLER ERROR STATE HANDLING
    public function setErrorState($state, $message){
        global $alertMessage, $alertState;
        $alertState = $state;
        $alertMessage = $message;
    }

    public function getErrorState($property){
        global $alertMessage, $alertState;
        if ($property == 'state') {
            return $alertState;
        } elseif ($property == 'message') {
            return $alertMessage;
        }
    }
}