<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('strava:sync')->hourly();
Schedule::command('withings:sync')->hourly();
