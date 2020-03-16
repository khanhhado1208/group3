<?php namespace App\Controllers;

    class Pages extends BaseController {

    public function index()
    {
        return $this->get('home');
    }
    public function get($page = 'home')
    {
        if ( ! is_file(APPPATH.'/Views/pages/'.$page.'.php'))
        {
            throw new \CodeIgniter\Exceptions\PageNotFoundException($page);
        }

        $data['title'] = ucfirst($page);

        echo view('templates/header', $data);
        echo view('templates/nav', $data);
        echo view('pages/'.$page, $data);
        echo view('templates/footer', $data);
    }
}