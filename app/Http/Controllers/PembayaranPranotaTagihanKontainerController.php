<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PembayaranPranotaTagihanKontainerController extends Controller
{
    public function __construct()
    {
        // intentionally left blank
    }

    public function __call($method, $parameters)
    {
        abort(404, 'PembayaranPranotaTagihanKontainer feature has been removed.');
    }
}
