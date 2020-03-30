<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Controllers\Account;
use App\Controllers\Error;
use App\Controllers\Pages;
use App\Models\Transactions;
use App\Models\Users;

class Account extends BaseController
{
    //Index redirect
    public function index()
    {
        $error = new Error();
        $pages = new Pages();
        if ($this->isLoggedIn()) {
            $pages->get('dashboard');
        } else {
            $error->setErrorState('danger', 'Not signed in');
            $pages->get('home');
        }
    }
    //Check if user logged in
    public function isLoggedIn()
    {
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
            return true;
        } else {
            return false;
        }
    }
    //Get account balance
    public function getBalance($username)
    {
        $users = new Users();
        //If username null, get balance for current session user
        if ($username == null) {
            $username = $_SESSION['username'];
        }
        return $users->check_balance($username);
    }
    //Get currently logged in username from session
    public function getUsername()
    {
        if (isLoggedIn()) {
            return $_SESSION['username'];
        } else {
            return null;
        }
    }
    //Create user
    public function create()
    {
        $pages = new Pages();
        $users = new Users();
        $error = new Error();
        helper(['form', 'url']);
        $val = $this->validate(
            [
                'username' => 'required|alpha_numeric',
                'password' => 'required|regex_match[^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,16}$]',
                'confirmpassword' => 'required|matches[password]',
            ]
        );
        $transactions = new Transactions();
 
        //Check if user already exists
        if ($users->user_exists($this->request->getVar('username'))) {
            $error->setErrorState('danger', 'User already exists');
            $pages->get('register');
        } else {
            if (!$val) {
                $error->setErrorState('danger', 'Data not valid');
                $pages->get('register');
            } else {
                $username = $this->request->getVar('username');
                $password = password_hash($this->request->getVar('password'), PASSWORD_DEFAULT);
                $users->addUser($username, $password);
                $error->setErrorState('success', 'New user created, please login');
                $transactions->money_transaction($this->request->getVar('username'), 10, "Sign-Up Bonus");
                $pages->get('login');
            }
        }
    }
    //Authenticate user
    public function authenticate()
    {
        $pages = new Pages();
        $error = new Error();
        helper(['form', 'url']);
        $val = $this->validate(
            [
                'username' => 'required|alpha_numeric',
                'password' => 'required|regex_match[^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,16}$]',
            ]
        );
        if (!$val) {
            $error->setErrorState('danger', 'Data not valid');
            $pages->get('login');
        } else {
            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');
            $users = new Users();
            $success = $users->check_credentials($username, $password);
            if ($success) {
                $error->setErrorState('success', 'Authentication successful');
                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = $username;
                $pages->get('dashboard');
            } else {
                $error->setErrorState('danger', 'Could not authenticate');
                unset($_SESSION['logged_in']);
                unset($_SESSION['username']);
                $pages->get('login');
            }
        }
    }
    //Autologin with demo account
    public function demoAutoLogin()
    {
        $pages = new Pages();
        $error = new Error();
        $users = new Users();
        if ($users->user_exists('demouser')) {
            $error->setErrorState('success', 'Demouser logged in');
        } else {
            $users->save(
                [
                    'username' => 'demouser',
                    'password' => password_hash('demouser', PASSWORD_DEFAULT)
                ]
            );
            $error->setErrorState('success', 'Demouser registered and logged in');
        }

        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = 'demouser';
        $pages->get('dashboard');
    }
    //End user session
    public function logout()
    {
        $pages = new Pages();
        $error = new Error();
        unset($_SESSION['logged_in']);
        unset($_SESSION['username']);
        $error->setErrorState('success', 'Logged out');
        $pages->get('home');
    }
    //Delete user
    public function deleteuser()
    {
        $username = $_SESSION['username'];
        $pages = new Pages();
        $error = new Error();
        $users = new Users();
        if ($this->isLoggedIn(true)) {
            $users->removeuser($username);
            unset($_SESSION['logged_in']);
            unset($_SESSION['username']);
            $error->setErrorState('success', 'Your account has been succesfully deleted');
            $pages->get('home');
        } else {
            $error->setErrorState('danger', 'Could not authenticate');
            unset($_SESSION['logged_in']);
            unset($_SESSION['username']);
            $pages->get('login');
        }
    }
}
