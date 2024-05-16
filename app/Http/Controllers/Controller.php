<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function notifyErrors(): void
    {
        $errors = \Session::get('errors')?->getBag('default')?->all(':message');

        if (! empty($errors)) {
            foreach ($errors as $message) {
                notify()->error($message);
            }
        }
    }
}
