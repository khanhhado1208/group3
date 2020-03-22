<?php namespace App\Controllers;
 
use CodeIgniter\Controller;
use App\Models\UsersModel;
use App\Controllers\Pages;
 
class Account extends Controller
{
    //GET DYNAMIC DATA FOR VIEWS
    public function getDynamicData($page = '') {
        $model = new UsersModel();
        $session = \Config\Services::session();
        $data = [];

        if($this->isLoggedIn()) {
            $username = $_SESSION['username'];
            $data['username'] = $username;
            $history = $model->check_transaction_history($username);
            $data['history'] = $history;
            $balance = $this->getBalance($username);
            $data['balance'] = $balance;
        }

        return $data;
    } 

    public function getBalance($username) {
        $model = new UsersModel();
        //If username null, get balance for current session user
        if ($username == null){
            $session = \Config\Services::session();
            $username = $_SESSION['username'];
        }
        return $model->check_balance($username);
    }
    public function getUsername() {
        $session = \Config\Services::session();
        return $_SESSION['username'];
    }
    //ACCOUNT PANEL
    public function index() {
        $pageController = new Pages;
        if($this->isLoggedIn()) {
            $pageController->get('dashboard');
        } else {
            $this->setErrorState('danger', 'Not signed in');
            $pageController->get('home');
          }
    }

    //CHECK IF USER IS LOGGED IN
    public function isLoggedIn() {
        $session = \Config\Services::session();
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
            return true;
        }
        else {
            return false;
        }
    }
    
    //TOP UP
    public function topup() {
        $pageController = new Pages;
        $model = new UsersModel();

        if($this->isLoggedIn()) {
            $username = $_SESSION['username'];
            $amount = $this->request->getVar('amount');
            $success = $model->money_transaction($username, $amount, "Deposit");
            if ($success) {
                $this->setErrorState('success', 'Account balance updated');
                $pageController->get('home');
            } else {
                $this->setErrorState('danger', 'Unable to deposit funds');
                $pageController->get('home');
            }
        } else {
            $this->setErrorState('danger', 'Not signed in');
            $pageController->get('home');
        }
    }

    //WITHDRAW
    public function withdraw() {
        $pageController = new Pages;
        $model = new UsersModel();

        if($this->isLoggedIn()) {
            $username = $_SESSION['username'];
            $amount = $this->request->getVar('amount');
            $current_funds = $model->check_balance($username);
            if ($current_funds < $amount) {
                $this->setErrorState('danger', 'Insufficient funds');
                $pageController->get('home');
            } else {
                $success = $model->money_transaction($username, -$amount, "Withdrawal");
                if ($success) {
                    $this->setErrorState('success', 'Account balance updated');
                    $pageController->get('home');
                } else {
                    $this->setErrorState('danger', 'Unable to deposit funds');
                    $pageController->get('home');
                }
            }
        } else {
            $this->setErrorState('danger', 'Not signed in');
            $pageController->get('home');
        }

    }

    //TRANSACTION HISTORY
    public function history() {
        $pageController = new Pages;
        $model = new UsersModel();

        if($this->isLoggedIn()) {
            $pageController->get('history');
        } else {
            $this->setErrorState('danger', 'Not signed in');
            $pageController->get('home');
        }
    }

    //CREATE DATABASE TABLES
    public function setupdb(){
        $pageController = new Pages;
        $model = new UsersModel();
        $success = $model->initialize_database();
        if ($success){
            $this->setErrorState('success', 'Tables created');
            $pageController->get('home');
        } else {
            $this->setErrorState('danger', 'Tables not created');
            $pageController->get('home');
        }
    }

    //REMOVE DATABASE TABLES
    public function dropdb(){
        $pageController = new Pages;
        $model = new UsersModel();
        $success = $model->drop_db_tables();
        if ($success){
            $this->setErrorState('success', 'Tables removed');
            $pageController->get('home');
        } else {
            $this->setErrorState('danger', 'Error while removing tables');
            $pageController->get('home');
        }
    }

    //CREATE USER
    public function create()
    {  
    $pageController = new Pages;
    helper(['form', 'url']);
        $val = $this->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
 
        $model = new UsersModel();
 
        //CHECK IF USER EXIST
        if ($model->user_exists($this->request->getVar('username'))) {
            $this->setErrorState('danger', 'User already exists');
            $pageController->get('register');
        } else {
            if (!$val)
            {
                $this->setErrorState('danger', 'Data not valid');
                $pageController->get('register');
    
            }
            else
            { 
        
                $model->save([
                    'username' => $this->request->getVar('username'),
                    'password'  => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
                ]);
                $this->setErrorState('success', 'New user created, please login');
                $pageController->get('login');
            }
        }

    }

    //AUTHENTICATE USER
    public function authenticate(){
        $pageController = new Pages;
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        $model = new UsersModel();
        $success = $model->check_credentials($username, $password);
        if ($success){
            $this->setErrorState('success', 'Authentication successful');
            $pageController->get('dashboard');
        } else {
            $this->setErrorState('danger', 'Could not authenticate');
            $pageController->get('login');
        }
    }

    //LOGOUT USER
    public function logout(){
        $pageController = new Pages;
        $session = \Config\Services::session();
        unset($_SESSION['logged_in']);
        unset($_SESSION['username']);
        $this->setErrorState('success', 'Logged out');
        $pageController->get('home');
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