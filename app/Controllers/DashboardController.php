<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index()
    {
        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, private')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody(view('dashboard/index'));
    }
}
