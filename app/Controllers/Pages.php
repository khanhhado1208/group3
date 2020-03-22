<?php namespace App\Controllers;
use App\Controllers\Account;

    class Pages extends BaseController {

    public function index()
    {
        if($this->isLoggedIn()) {
            return $this->get('dashboard');
        } else {
            return $this->get('home');
        }
    }

    public function get($page = 'home')
    {
        //set page title variable and pass it as data 
        $data = ['title' => ucfirst($page)];
        //print header
        echo view('templates/header', $data);
        //print navbar
        $this->generateNavBar();
        //print alerts if any
        $this->generateAlerts();
        //show error if the view doesn't exist
        if ( ! is_file(APPPATH.'/Views/pages/'.$page.'.php'))
        {
            throw new \CodeIgniter\Exceptions\PageNotFoundException($page);
        }
        //check if user logged in and print page content, if not return home
        if($this->isLoggedIn()) {
            //print content from respective view
            $accountController = new Account;
            $data = $accountController->getDynamicData($page);
            echo view('pages/'.$page, $data);
        } else {
            if ($page == 'register' || $page == 'login' || $page == 'createdb') {
                echo view('pages/'.$page);
            } else {
                echo view('pages/home');
            } 
        }
        //print footer
        echo view('templates/footer');
    }
    public function isLoggedIn() {
        $session = \Config\Services::session();
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
            return true;
        }
        else {
            return false;
        }
    }

    public function generateAlerts() {
        $accountController = new Account;
        $state = $accountController->getErrorState('state');
        if($state == ('success' || 'danger')){
            $message = $accountController->getErrorState('message');
            $data = ['state' => $state, 'message' => $message];
            echo view('templates/alert', $data);
        }
    }

    public function generateNavBar() {
        $session = \Config\Services::session();
        if($this->isLoggedIn()) {
            $navItems = '<li class="nav-item active">
                <a class="nav-link" href="/">Dashboard</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="/exchange">Quick trade</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="/topup">Top up</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="/withdraw">Withdraw</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="/profile">My account</a>
            </li>';
        } else {
            $navItems = '<li class="nav-item active">
            <a class="nav-link" href="/">Home</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="/register">Register</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="/login">Login</a>
            </li>';
        }
        $data = ['nav' => $navItems];
        echo view('templates/nav', $data);
    }
}