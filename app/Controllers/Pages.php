<?php namespace App\Controllers;
use App\Controllers\Account;

    class Pages extends BaseController {

    public function index()
    {
        $accountController = new Account;
        if($accountController->isLoggedIn()) {
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
        $accountController = new Account;
        if($accountController->isLoggedIn()) {
            //print content from respective view
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
        $accountController = new Account;
        if($accountController->isLoggedIn()) {
            $balance = $accountController->getBalance(null).' Stonk$';
            $navItems = ['dashboard' => 'Dashboard', 'exchange' => 'Exchange', 'quicktrade' => 'Quick trade', 'topup' => 'Top up', 'withdraw' => 'Withdraw', 'profile' => 'My account'];
            $data = ['navItems' => $navItems, 'balance' => $balance];
        } else {
            $navItems = ['home' => 'Home', 'login' => 'Login', 'register' => 'Register'];
            $data = ['navItems' => $navItems, 'balance' => '0'];
        }
        echo view('templates/nav', $data);
    }
}