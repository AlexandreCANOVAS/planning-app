<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('societe.{id}', function (User $user, $id) {
    return true; // Temporairement autoriser tout le monde pour tester
});