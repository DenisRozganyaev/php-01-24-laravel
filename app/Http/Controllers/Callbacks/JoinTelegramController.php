<?php

namespace App\Http\Controllers\Callbacks;

use App\Http\Controllers\Controller;
use Azate\LaravelTelegramLoginAuth\TelegramLoginAuth;
use Illuminate\Http\Request;

class JoinTelegramController extends Controller
{
    public function __invoke(TelegramLoginAuth $telegramLoginAuth, Request $request)
    {
        $data = $telegramLoginAuth->validate($request);

        if (!$data) {
            notify()->warning('Oops, smth went wrong with telegram connection');
            return redirect()->route('admin.dashboard');
        }

        auth()->user()->update([
            'telegram_id' => $data->getId()
        ]);

        notify()->success('You\'re added to our telegram bot');

        return redirect()->route('admin.dashboard');
    }
}
