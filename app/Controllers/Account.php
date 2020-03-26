<?php namespace App\Controllers;
 
use CodeIgniter\Controller;
use App\Models\UsersModel;
use App\Controllers\Pages;
 
class Account extends BaseController
{
    //GET DYNAMIC DATA FOR VIEWS
    public function getDynamicData($page = '') {
        $model = new UsersModel();
        $data = [];

        if($this->isLoggedIn()) {
            $username = $_SESSION['username'];
            $data['username'] = $username;
            $history = $model->check_transaction_history($username);
            $data['history'] = $history;
            $balance = $this->getBalance($username);
            $data['balance'] = $balance;
            $userstonks = $model->get_user_stonks($username);
            $data['userstonks'] = $userstonks;
            $stonkproperties = $model->get_stonk_properties();
            $data['stonkproperties'] = $stonkproperties;
        }

        return $data;
    } 

    //GET USER ACCOUNT BALANCE
    public function getBalance($username) {
        $model = new UsersModel();
        //If username null, get balance for current session user
        if ($username == null){
            $username = $_SESSION['username'];
        }
        return $model->check_balance($username);
    }
    public function getUsername() {
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

    //QUICK TRADE
    public function quicktrade() {
        $pageController = new Pages;
        $model = new UsersModel();

        if($this->isLoggedIn()) {
            $username = $_SESSION['username'];
            $stonkid = $this->request->getVar('stonkid');
            $amount = $this->request->getVar('amount');
            $value = $this->request->getVar('value');

            if ($this->request->getVar('operation') == "buy") {
                $current_funds = $model->check_balance($username);
                if ($current_funds < $value) {
                    $this->setErrorState('danger', 'Insufficient funds');
                    $pageController->get('home');
                } else {
                    $success = $model->stonk_transaction($username, -$value, $stonkid, $amount, "Stonk Purchase");
                    if ($success) {
                        $this->setErrorState('success', 'Stonks Purchased');
                        $pageController->get('home');
                    } else {
                        $this->setErrorState('danger', 'Unable to Purchase Stonks');
                        $pageController->get('home');
                    }
                }
            } else {
                $user_stonks = $model->get_user_stonks($username);
                $user_has_stonks = false;
                foreach ($user_stonks as $stonk_row) {
                    if ($stonk_row->stonk_id == $stonkid && $stonk_row->stonk_amount >= $amount) {
                        $user_has_stonks = true;
                    }
                }
                if ($user_has_stonks) {
                    $success = $model->stonk_transaction($username, $value, $stonkid, -$amount, "Stonk Sold");
                    if ($success) {
                        $this->setErrorState('success', 'Stonks Sold');
                        $pageController->get('home');
                    } else {
                        $this->setErrorState('danger', 'Unable to Sell Stonks');
                        $pageController->get('home');
                    }
                } else {
                    $this->setErrorState('danger', 'Insufficient Stonks');
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
    public function create(){  
        $pageController = new Pages;
        helper(['form', 'url']);
        $val = $this->validate([
            'username' => 'required|alpha_numeric',
            'password' => 'required|regex_match[^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,16}$]',
            'confirmpassword' => 'required|matches[password]',
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
                $model->money_transaction($this->request->getVar('username'), 10, "Sign-Up Bonus");
                $pageController->get('login');
            }
        }

    }

    //AUTHENTICATE USER
    public function authenticate(){
        $pageController = new Pages;
        helper(['form', 'url']);
        $val = $this->validate([
            'username' => 'required|alpha_numeric',
            'password' => 'required|regex_match[^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,16}$]',
        ]);
        if (!$val)
            {
                $this->setErrorState('danger', 'Data not valid');
                $pageController->get('login');
    
            }
            else
            { 
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
        
    }

    //test account for demo/testing purposes
    public function demoAutoLogin(){
        $pageController = new Pages;
        $model = new UsersModel;
        if ($model->user_exists('demouser')){
            $this->setErrorState('success', 'Demouser logged in');
        } else{
            $model->save([
                'username' => 'demouser',
                'password'  => password_hash('demouser', PASSWORD_DEFAULT)
            ]);
            $this->setErrorState('success', 'Demouser registered and logged in');
        }

        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = 'demouser';
        $pageController->get('dashboard');
    }

    //LOGOUT USER
    public function logout(){
        $pageController = new Pages;
        unset($_SESSION['logged_in']);
        unset($_SESSION['username']);
        $this->setErrorState('success', 'Logged out');
        $pageController->get('home');
    }

    //ACCOUNT CONTROLLER ERROR STATE HANDLING FUNCTIONS AND VARIABLES
    public $alertMessage;
    public $alertState;

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