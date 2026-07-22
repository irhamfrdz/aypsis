<?php $l = file("storage/logs/laravel.log"); echo implode("", array_slice($l, -50));
