<?php

/**
 * Sada funkcí MysqlFunctions, které zčásti nahrazují sadu PHP mysql_XXXXX funkcí, které byly v PHP7 odstraněny. 
 */

use Pes\Database\MysqlReplace\HandlerFactoryStatic;


/**
 * Funkce získá vytvoření připojení k databázi (handler) z interně použité třídy HandlerFactoryStatic, 
 * volá PDO metodu query s parametrem funkce. PDO->query($sql) vrací v případě úspěchu PDOStatement a tento PDOStatement
 * je vrácen touto funkcí. Starý kód používá pro získání resultsetu volání MySQL_Fetch_Array($vysledek), kde $vysledek 
 * je návratová hodnota volání MySQL_Query($sql). Tato funkce tedy narozdíl od původní PHP4 funkce MySQL_Query() vrací PDOStatement, 
 * ale návratová hodnota je vždy parametrem volání funkce MySQL_Fetch_Array() a ta PDOStetement přijímá. Starý k=d tak nikdy nezjistí, 
 * že MySQL_Query() vrací něco zcela jiného než původní PHP4 funkce MySQL_Query().
 * Funkce také ukládá poslední použitý PDOStatement do globální proměnné, ze které je pak statement dostupný pro volání funkce mysql_num_rows().
 * 
 * @param string $sql
 * @return \PDOStatement
 */
function MySQL_Query($sql) {  
    
    /**
     * @var \PDOStatement 
     */
    global $lastPDOStatement;
    
    $dbh = HandlerFactoryStatic::get();
    $lastPDOStatement = $dbh->query($sql);
    return $lastPDOStatement;
}

/**
 * Funkce přijímá PDOStatement vytvořený funkcí MySQL_Query(). Interně pak volá PDOStatement->fetch(\PDO::FETCH_ASSOC); a vrací tak
 * asociatovní pole s jední řádkem výsledku.
 * 
 * @param \PDOStatement $statement
 * @return array
 */
function MySQL_Fetch_Array(\PDOStatement $statement) {
    return $statement->fetch(\PDO::FETCH_ASSOC);
}

/**
 * Funkce pouze nastaví parametry pro budoucí připojení. Pro skutečné připojení s použitím PDO je nutné znát jméno databáze a to při volání 
 * mysql_connect() ještě neznám. Jmeno databáze bude až parametrem mysql_select_db().
 * Metoda napodobuje mysql_connect. Nevrací však resource jako mysql_connect, v případě úspěchu vrací TRUE. Většinou starému kódu
 * stačí, že mysql_connect vrací hodnotu vyhodnocovanou jako TRUE (např. mysql_connect($serverdb, $userdb, $passdb) or die ("Nelze navázat spojení s databazí");
 * 
 * PDO se nepřipojuje k databázovému stroji, ale ke konkrétní databázi (s právy ke konkrétní databázi).
 * @param type $dbHost
 * @param type $user
 * @param type $password
 * @return bool
 */
function mysql_connect($dbHost, $user, $password) {
    HandlerFactoryStatic::preSetConnection($dbHost, $user, $password);
    return HandlerFactoryStatic::isPresetted();   // nevrací resource jako mysql_connect, vrací alespoň TRUE
}

/**
 * Funkce provede skutečné připojení k databázi. Při volání této metody získám jméno databáze, tedy poslední potřebný údaj pro připojení s použitím PDO. 
 * Vytvořené přopojení k databázi (handler) si pamatuje interně použitá třída HandlerFactoryStatic, ze které je pak možno handler získávat 
 * při následných voláních (např. MySQL_Query).
 * 
 * @param type $dbName
 * @return type
 */
function mysql_select_db($dbName) {
    return HandlerFactoryStatic::setDb($dbName);
}

/**
 * Vymaže databázové připojení (nadler) zapamatovaní v interně použití třídě HandlerFactoryStatic
 * @param type $connect Jen dummy parametr, funkce ho nevyužívá.
 */
function MySQL_CLOSE($connect=NULL) {
    HandlerFactoryStatic::reset();
}

/**
 * Funkce použije poslední použitý PDOStatement uložená do globální proměnné při volání funkce MySQL_Query() 
 * a z něj zíká počet dotčených řádek metodou PDOStatement->rowCount();
 * 
 * @global type $lastPDOStatement
 * @return type
 */
function mysql_num_rows() {
    
    /**
     * @var \PDOStatement 
     */
    global $lastPDOStatement;
        
    return $lastPDOStatement->rowCount();
}