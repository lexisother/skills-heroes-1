<?php

namespace App\Controllers;

class HealthController extends Controller
{
    public function getHealth()
    {
        echo '{"ok": true}';
    }
}
