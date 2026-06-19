<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

abstract class Controller
{
    protected function noChangesResponse(string $message = 'Tidak ada perubahan data.'): RedirectResponse
    {
        return back()->with('info', $message);
    }
}
