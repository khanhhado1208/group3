<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Controllers\Account;
use App\Controllers\Error;
use App\Controllers\Prices;
use App\Models\Stonks;
use App\Models\Users;

class Pages extends BaseController
{
    public function index()
    {
        $account = new Account();
        if ($account->isLoggedIn()) {
            return $this->get('dashboard');
        } else {
            return $this->get('home');
        }
    }

    public function get($page = 'home')
    {
        $account = new Account();
        //set page title variable and pass it as data 
        $data = ['title' => ucfirst($page)];
        //print header
        echo view('templates/header', $data);
        //print navbar
        $this->generateNavBar();
        //print alerts if any
        $this->generateAlerts();
        //show error if the view doesn't exist
        if (!is_file(APPPATH . '/Views/pages/' . $page . '.php')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException($page);
        }
        //check if user logged in and print page content, if not return home
        if ($account->isLoggedIn()) {
            //print content from respective view
            $data = $this->getData($page);
            echo view('pages/' . $page, $data);
        } else {
            if ($page == 'register' || $page == 'login' || $page == 'admin') {
                echo view('pages/' . $page);
            } else {
                echo view('pages/home');
            }
        }
        //print footer
        echo view('templates/footer');
    }

    public function generateAlerts()
    {
        $account = new Account();
        $error = new Error();
        $state = $error->getErrorState('state');
        if ($state == ('success' || 'danger')) {
            $message = $error->getErrorState('message');
            $data = ['state' => $state, 'message' => $message];
            echo view('templates/alert', $data);
        }
    }

    public function generateNavBar()
    {
        $account = new Account();
        if ($account->isLoggedIn()) {
            $balance = $account->getBalance(null) . ' Stonk$';
            $navItems = [
                'dashboard' => 'Dashboard', 'exchange' => 'Stonk exchange', 'support' => 'Support',
                'profile' => 'My account'
            ];
            $data = ['navItems' => $navItems, 'balance' => $balance];
        } else {
            $navItems = ['home' => 'Home', 'login' => 'Login', 'register' => 'Register'];
            $data = ['navItems' => $navItems, 'balance' => '-1'];
        }
        echo view('templates/nav', $data);
    }

    //Get data for views
    public function getData($page = '')
    {
        $users = new Users();
        $stonks = new Stonks();
        $account = new Account();
        $prices = new Prices();
        $data = [];

        if ($account->isLoggedIn()) {
            $username = $_SESSION['username'];
            $data['username'] = $username;
            $history = $users->check_transaction_history($username);
            $data['history'] = $history;
            $balance = $account->getBalance($username);
            $data['balance'] = $balance;
            $userstonks = $users->get_user_stonks($username);
            $data['userstonks'] = $userstonks;
            $stonkproperties = $stonks->get_stonk_properties();
            $data['stonkproperties'] = $stonkproperties;
            $pricenow = $prices->generatePriceData('now');
            $data['pricenow'] = $pricenow;
            if ($page == 'exchange') {
                $hourlydata = $prices->generatePriceData('lasthour');
                $dailydata = $prices->generatePriceData('lastday');
                $weeklydata = $prices->generatePriceData('lastweek');
                $data['hourlydata'] = $hourlydata;
                $data['dailydata'] = $dailydata;
                $data['weeklydata'] = $weeklydata;
            }
        }

        return $data;
    }
}
