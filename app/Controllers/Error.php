<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Controllers\Pages;

class Error extends BaseController
{
    //Index redirect
    public function index()
    {
        $pages = new Pages();
        $pages->get('home');
    }
    public $alertMessage;
    public $alertState;
    public function setErrorState($state, $message)
    {
        global $alertMessage, $alertState;
        $alertState = $state;
        $alertMessage = $message;
    }
    public function getErrorState($property)
    {
        global $alertMessage, $alertState;
        if ($property == 'state') {
            return $alertState;
        } elseif ($property == 'message') {
            return $alertMessage;
        }
    }
}
