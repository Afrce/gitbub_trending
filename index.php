<?php
require_once "vendor/autoload.php";

use App\DayTending;
$day = ['daily','weekly','monthly'];
foreach ($day as $value){
    $dayData = new DayTending($value);
    $dayData->run();
}
