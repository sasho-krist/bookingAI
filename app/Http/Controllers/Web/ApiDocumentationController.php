<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ApiDocumentationController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.api-docs', [
            'baseUrl' => rtrim(url('/api/v1'), '/'),
        ]);
    }
}
