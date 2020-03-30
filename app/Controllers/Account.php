<?php namespace App\Controllers;
 
use CodeIgniter\Controller;
use App\Models\Users;
use App\Models\Stonks;
use App\Models\Transactions;
use App\Models\Tables;
use App\Controllers\Pages;
use DateTime;
use DateInterval;
 
class Account extends BaseController
{
    //GET DYNAMIC DATA FOR VIEWS
    public function getDynamicData($page = '') {
        $users = new Users();
        $stonks = new Stonks();
        $data = [];

        if($this->isLoggedIn()) {
            $username = $_SESSION['username'];
            $data['username'] = $username;
            $history = $users->check_transaction_history($username);
            $data['history'] = $history;
            $balance = $this->getBalance($username);
            $data['balance'] = $balance;
            $userstonks = $users->get_user_stonks($username);
            $data['userstonks'] = $userstonks;
            $stonkproperties = $stonks->get_stonk_properties();
            $data['stonkproperties'] = $stonkproperties;
            $pricenow = $this->generatePriceData('now');
            $data['pricenow'] = $pricenow;
            if($page == 'exchange') {
                $hourlydata = $this->generatePriceData('lasthour');
                $dailydata = $this->generatePriceData('lastday');
                $weeklydata = $this->generatePriceData('lastweek');
                $data['hourlydata'] = $hourlydata;
                $data['dailydata'] = $dailydata;
                $data['weeklydata'] = $weeklydata;
            }
        }

        return $data;
    } 
    public function generatePriceData($period){
        $stonks = new Stonks();
        if($period == 'lasthour') {
            $hourarr = [];
            for ($i = 0; $i < count($stonks->get_stonk_properties()); $i++) {
                $date = new DateTime();
                $pricearr = [];
                for ($a = 0; $a < 60; $a++) {
                    mt_srand($date->format("dHi") * 3.14);
                    $gen = mt_rand(0, 10);
                    $vol = $stonks->get_stonk_properties()[$i]->volatility;
                    $base = $stonks->get_stonk_properties()[$i]->base;
                    $change = 2 * $vol * $gen;
                    if($change > $vol) {
                        $change -= (2 * $vol);
                    }
                    $price = $base + $change;
                    $date->sub(new DateInterval('PT1M'));
                    array_push($pricearr, $price);
                }
                array_push($hourarr, array_reverse($pricearr));
            }
            return $hourarr;
        } elseif ($period == 'lastday') {
            $dayarr = [];
            for ($i = 0; $i < count($stonks->get_stonk_properties()); $i++) {
                $date = new DateTime();
                $pricearr = [];
                for ($a = 0; $a < 24; $a++) {
                    mt_srand($date->format("dHi") * 3.14);
                    $gen = mt_rand(0, 10);
                    $vol = $stonks->get_stonk_properties()[$i]->volatility;
                    $base = $stonks->get_stonk_properties()[$i]->base;
                    $change = 2 * $vol * $gen;
                    if($change > $vol) {
                        $change -= (2 * $vol);
                    }
                    $price = $base + $change;
                    $date->sub(new DateInterval('PT1H'));
                    array_push($pricearr, $price);
                }
                array_push($dayarr, array_reverse($pricearr));
            }
            return $dayarr;
        } elseif ($period == 'lastweek') {
            $weekarr = [];
            for ($i = 0; $i < count($stonks->get_stonk_properties()); $i++) {
                $date = new DateTime();
                $pricearr = [];
                for ($a = 0; $a < 7; $a++) {
                    mt_srand($date->format("dHi") * 3.14);
                    $gen = mt_rand(0, 10);
                    $vol = $stonks->get_stonk_properties()[$i]->volatility;
                    $base = $stonks->get_stonk_properties()[$i]->base;
                    $change = 2 * $vol * $gen;
                    if($change > $vol) {
                        $change -= (2 * $vol);
                    }
                    $price = $base + $change;
                    $date->sub(new DateInterval('P1D'));
                    array_push($pricearr, $price);
                }
                array_push($weekarr, array_reverse($pricearr));
            }
            return $weekarr;
        } else {
            $date = new DateTime();
            $date_now = $date->format("dHi");
            $pricearr = [];
            for ($i = 0; $i < count($stonks->get_stonk_properties()); $i++) {
                mt_srand($date_now * 3.14);
                $gen = mt_rand(0, 10);
                $vol = $stonks->get_stonk_properties()[$i]->volatility;
                $base = $stonks->get_stonk_properties()[$i]->base;
                $change = 2 * $vol * $gen;
                if($change > $vol) {
                    $change -= (2 * $vol);
                }
                $price = $base + $change;
                array_push($pricearr, $price);
            }
            return $pricearr;
        }
    }


    //GET USER ACCOUNT BALANCE
    public function getBalance($username) {
        $users = new Users();
        //If username null, get balance for current session user
        if ($username == null){
            $username = $_SESSION['username'];
        }
        return $users->check_balance($username);
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
        $transactions = new Transactions();

        if($this->isLoggedIn()) {
            $username = $_SESSION['username'];
            $amount = $this->request->getVar('amount');
            $success = $transactions->money_transaction($username, $amount, "Deposit");
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
        $transactions = new Transactions();
        $users = new Users();

        if($this->isLoggedIn()) {
            $username = $_SESSION['username'];
            $amount = $this->request->getVar('amount');
            $current_funds = $users->check_balance($username);
            if(!is_numeric($amount) || $amount < 0) {
                $this->setErrorState('danger', 'Enter a valid amount');
                $pageController->get('withdraw');
            } else if ($current_funds < $amount) {
                $this->setErrorState('danger', 'Insufficient funds');
                $pageController->get('home');
            } else {
                $success = $transactions->money_transaction($username, -$amount, "Withdrawal");
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
        $transactions = new Transactions();
        $users = new Users();

        if($this->isLoggedIn()) {
            $username = $_SESSION['username'];
            $stonkid = $this->request->getVar('stonkid');
            $amount = $this->request->getVar('amount');
            $value = $this->request->getVar('value');

            //The index must be moved by two to re-sync with actual stonk_id in database
            $pricenow = $this->getDynamicData()['pricenow'][$stonkid - 2];
            if ($value != $amount * $pricenow) {
                $this->setErrorState('danger', 'Stonk Price Mismatch, Please Try Again');
                $pageController->get('home');
                return;
            }

            if ($this->request->getVar('operation') == "buy") {
                $current_funds = $users->check_balance($username);
                if ($current_funds < $value) {
                    $this->setErrorState('danger', 'Insufficient funds');
                    $pageController->get('home');
                } else {
                    $success = $transactions->stonk_transaction($username, -$value, $stonkid, $amount, "Stonk Purchase");
                    if ($success) {
                        $this->setErrorState('success', 'Stonks Purchased');
                        $pageController->get('home');
                    } else {
                        $this->setErrorState('danger', 'Unable to Purchase Stonks');
                        $pageController->get('home');
                    }
                }
            } else {
                $user_stonks = $users->get_user_stonks($username);
                $user_has_stonks = false;
                foreach ($user_stonks as $stonk_row) {
                    if ($stonk_row->stonk_id == $stonkid && $stonk_row->stonk_amount >= $amount) {
                        $user_has_stonks = true;
                    }
                }
                if ($user_has_stonks) {
                    $success = $transactions->stonk_transaction($username, $value, $stonkid, -$amount, "Stonk Sold");
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
        $tables = new Tables();
        $success = $tables->initialize_database();
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
        $tables = new Tables();
        $success = $tables->drop_db_tables();
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
 
        $users = new Users();
        $transactions = new Transactions();
 
        //CHECK IF USER EXIST
        if ($users->user_exists($this->request->getVar('username'))) {
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
        
                $users->save([
                    'username' => $this->request->getVar('username'),
                    'password'  => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
                ]);
                $this->setErrorState('success', 'New user created, please login');
                $transactions->money_transaction($this->request->getVar('username'), 10, "Sign-Up Bonus");
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
                $users = new Users();
                $success = $users->check_credentials($username, $password);
                if ($success){
                    $this->setErrorState('success', 'Authentication successful');
                    $_SESSION['logged_in'] = true;
                    $_SESSION['username'] = $username;
                    $pageController->get('dashboard');
                } else {
                    $this->setErrorState('danger', 'Could not authenticate');
                    unset($_SESSION['logged_in']);
                    unset($_SESSION['username']);
                    $pageController->get('login');
                }
            }
        
    }

    //test account for demo/testing purposes
    public function demoAutoLogin(){
        $pageController = new Pages;
        $users = new Users;
        if ($users->user_exists('demouser')){
            $this->setErrorState('success', 'Demouser logged in');
        } else{
            $users->save([
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

    //DELETE USER
    public function deleteuser(){
        $username = $_SESSION['username']; 
        $pageController = new Pages;
        $users = new Users();
        if($this->isLoggedIn(true)){
            $users->removeuser($username);
            unset($_SESSION['logged_in']);
            unset($_SESSION['username']);
            $this->setErrorState('success', 'Your account has been succesfully deleted');
            $pageController->get('home');
        }
        else{
            $this->setErrorState('danger', 'Could not authenticate');
            unset($_SESSION['logged_in']);
            unset($_SESSION['username']);
            $pageController->get('login');
        }
    }
}