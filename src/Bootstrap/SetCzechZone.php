<?php

######### TIMEZONE, LOCALE #################################
date_default_timezone_set("Europe/Prague");
if($bootstrapLogger) {
    $bootstrapLogger->info("SetCzechZone: Date default timezone set: date_default_timezone_set(\"Europe/Prague\");");
}
//On Windows, setlocale(LC_ALL, '') sets the locale names from the system's regional/language settings (accessible via Control Panel).
// nepoužitekné: setlocale(LC_ALL, '');   //default je "Czech_Czechia.1250"
$localeCode = ['cs-CZ.UTF8', 'cs_CZ.UTF8', 'cs-CZ.UTF8', 'cs_CZ.UTF8'];  // zdá se, že varianta cd-CZ je windows, cs_CZ je Linux, UTF8, utf8, UTF-8 a utf-8 by měly být záměnšnné
$locale = setlocale(LC_ALL, $localeCode);
if($bootstrapLogger) {
    $bootstrapLogger->info("SetCzechZone: Set locale: ".print_r(setlocale(LC_ALL, "0"), TRUE));
}