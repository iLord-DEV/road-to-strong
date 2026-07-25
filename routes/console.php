<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('strava:sync')->everySixHours();
Schedule::command('withings:sync')->everySixHours();
