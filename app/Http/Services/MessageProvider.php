<?php

namespace App\Http\Services;

use Illuminate\Http\Request;

interface MessageProvider
{
    public function sendMessage(Request $request);
}
