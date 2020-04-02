<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Controllers\Pages;
use App\Models\Stonks;
use DateInterval;
use DateTime;

class Prices extends BaseController
{
    //Index redirect
    public function index()
    {
        $pages = new Pages();
        $pages->get('home');
    }
    public function generatePriceData($period)
    {
        $stonks = new Stonks();
        if ($period == 'lasthour') {
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
                    if ($change > $vol) {
                        $change -= (2 * $vol);
                    }
                    $price = $base + $change;
                    $date->sub(new DateInterval('PT1M'));
                    array_push($pricearr, $price);
                }
                $hourarr[$stonks->get_stonk_properties()[$i]->stonk_id] = array_reverse($pricearr);
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
                    if ($change > $vol) {
                        $change -= (2 * $vol);
                    }
                    $price = $base + $change;
                    $date->sub(new DateInterval('PT1H'));
                    array_push($pricearr, $price);
                }
                $dayarr[$stonks->get_stonk_properties()[$i]->stonk_id] = array_reverse($pricearr);
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
                    if ($change > $vol) {
                        $change -= (2 * $vol);
                    }
                    $price = $base + $change;
                    $date->sub(new DateInterval('P1D'));
                    array_push($pricearr, $price);
                }
                $weekarr[$stonks->get_stonk_properties()[$i]->stonk_id] = $pricearr;
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
                if ($change > $vol) {
                    $change -= (2 * $vol);
                }
                $price = $base + $change;
                $pricearr[$stonks->get_stonk_properties()[$i]->stonk_id] = $price;
            }
            return $pricearr;
        }
    }
}
