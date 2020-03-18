<?php namespace App\Controllers;

    class Pages extends BaseController {

    public function index()
    {
        $session = \Config\Services::session();
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
            return $this->get('dashboard');
        } else {
            return $this->get('home');
          }
        
    }
    public function get($page = 'home')
    {
        $session = \Config\Services::session();
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
            
        } else {
            if ($page == ('register' || 'login' || 'success' || 'createdb' || 'dbcreated' || 'dbcreatefailed' || 'loginsuccess' || 'loginfailure' || 'failadd')) {

            } else {
                $page = 'home';
            }
        }
        if ( ! is_file(APPPATH.'/Views/pages/'.$page.'.php'))
        {
            throw new \CodeIgniter\Exceptions\PageNotFoundException($page);
        }

        $data = ['title' => ucfirst($page), 'nav' => $this->generateNavItems()];
        
        echo view('templates/header', $data);
        echo view('templates/nav', $data);
        echo view('pages/'.$page, $data);
        echo view('templates/footer', $data);
    }
    public function generateNavItems() {
        $session = \Config\Services::session();
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
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
            return $navItems;
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
            return $navItems;
        }
             
    }
}