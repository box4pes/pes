<?php

######### TIMEZONE, LOCALE #################################
date_default_timezone_set("Europe/Prague");
if($bootstrapLogger) {
    $bootstrapLogger->info("SetCzechZone: Date default timezone set: date_default_timezone_set(\"Europe/Prague\");");
}
//On Windows, setlocale(LC_ALL, '') sets the locale names from the system's regional/language settings (accessible via Control Panel).
setlocale(LC_ALL, '');   //default je "Czech_Czechia.1250"
if($bootstrapLogger) {
    $bootstrapLogger->info("SetCzechZone: Set locale: ".print_r(setlocale(LC_ALL, "0"), TRUE));
}