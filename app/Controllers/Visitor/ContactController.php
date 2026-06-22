<?php
namespace App\Controllers\Visitor;

use App\Core\Controller;

class ContactController extends Controller
{
    public function index(): void
    {
        $this->render('visitor/contact', [
            'title' => 'Contact - Restaurant RYOHA'
        ]);
    }
}