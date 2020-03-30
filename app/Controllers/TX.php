<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Controllers\Account;
use App\Controllers\Error;
use App\Controllers\Pages;
use App\Models\Transactions;
use App\Models\Users;

class TX extends BaseController
{
    //Index redirect
    public function index()
    {
        $pages = new Pages();
        $pages->get('home');
    }
    //Create new topup transaction
    public function topup()
    {
        $pages = new Pages();
        $error = new Error();
        $account = new Account();
        $transactions = new Transactions();

        if ($account->isLoggedIn()) {
            $username = $_SESSION['username'];
            $amount = $this->request->getVar('amount');
            $success = $transactions->money_transaction($username, $amount, "Deposit");
            if ($success) {
                $error->setErrorState('success', 'Account balance updated');
                $pages->get('home');
            } else {
                $error->setErrorState('danger', 'Unable to deposit funds');
                $pages->get('home');
            }
        } else {
            $error->setErrorState('danger', 'Not signed in');
            $pages->get('home');
        }
    }

    //Create new withdraw transaction
    public function withdraw()
    {
        $pages = new Pages();
        $error = new Error();
        $account = new Account();
        $transactions = new Transactions();
        $users = new Users();

        if ($account->isLoggedIn()) {
            $username = $_SESSION['username'];
            $amount = $this->request->getVar('amount');
            $current_funds = $users->check_balance($username);
            if (!is_numeric($amount) || $amount < 0) {
                $error->setErrorState('danger', 'Enter a valid amount');
                $pages->get('withdraw');
            } else if ($current_funds < $amount) {
                $error->setErrorState('danger', 'Insufficient funds');
                $pages->get('home');
            } else {
                $success = $transactions->money_transaction($username, -$amount, "Withdrawal");
                if ($success) {
                    $error->setErrorState('success', 'Account balance updated');
                    $pages->get('home');
                } else {
                    $error->setErrorState('danger', 'Unable to deposit funds');
                    $pages->get('home');
                }
            }
        } else {
            $error->setErrorState('danger', 'Not signed in');
            $pages->get('home');
        }
    }
    //Create new stonk trade transaction
    public function quicktrade()
    {
        $pages = new Pages();
        $error = new Error();
        $account = new Account();
        $transactions = new Transactions();
        $users = new Users();

        if ($account->isLoggedIn()) {
            $username = $_SESSION['username'];
            $stonkid = $this->request->getVar('stonkid');
            $amount = $this->request->getVar('amount');
            $value = $this->request->getVar('value');

            if (!is_null($stonkid) && !is_null($amount) && !is_null($value)) {
                $pricenow = $pages->getData()['pricenow'][$stonkid];
                if ($value != $amount * $pricenow) {
                    $error->setErrorState('danger', 'Stonk Price Mismatch, Please Try Again');
                    $pages->get('home');
                    return;
                }
            }

            if ($this->request->getVar('operation') == "buy") {
                $current_funds = $users->check_balance($username);
                if ($current_funds < $value) {
                    $error->setErrorState('danger', 'Insufficient funds');
                    $pages->get('home');
                } else {
                    $success = $transactions->stonk_transaction($username, -$value, $stonkid, $amount, "Stonk Purchase");
                    if ($success) {
                        $error->setErrorState('success', 'Stonks Purchased');
                        $pages->get('home');
                    } else {
                        $error->setErrorState('danger', 'Unable to Purchase Stonks');
                        $pages->get('home');
                    }
                }
            } else if ($this->request->getVar('operation') == "sell") {
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
                        $error->setErrorState('success', 'Stonks Sold');
                        $pages->get('home');
                    } else {
                        $error->setErrorState('danger', 'Unable to Sell Stonks');
                        $pages->get('home');
                    }
                } else {
                    $error->setErrorState('danger', 'Insufficient Stonks');
                    $pages->get('home');
                }
            } else {
                $error->setErrorState('danger', 'Unable to process transaction');
                $pages->get('home');
            }
        } else {
            $error->setErrorState('danger', 'Not signed in');
            $pages->get('home');
        }
    }
}
