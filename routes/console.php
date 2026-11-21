<?php
use Illuminate\Support\Facades\Artisan;
Artisan::command('barz:about', function () { $this->info('Barz REST API is ready.'); })->purpose('Display a short status about Barz API');
