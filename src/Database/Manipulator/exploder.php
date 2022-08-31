<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

function my_explode($str, $separator, $leftBracket, $rightBracket)
{
    $ret = array();
    $left_parenthesis = 0;
    $right_parenthesis = 0;
    $opened_paretnhesis = false;
    $pos = 0;
    for($i=0;$i<strlen($str);$i++)
    {
        $c = $str[$i];
        $opened_paretnhesis = $left_parenthesis>$right_parenthesis;
        if($c == $separator && !$opened_paretnhesis) {
            $ret[] = substr($str, $pos, $i-$pos);
            $pos = $i+1;
        } elseif($opened_paretnhesis && $c == $rightBracket) {
            $right_parenthesis++;
        } elseif($c == $leftBracket) {
            $left_parenthesis++;
        }
    }
    if($pos > 0) $ret[] = substr($str, $pos);

    return $ret;
}
    // MySQL umožňuje escapovat apostrof i takto: \'
    // funkce převede na standardní SQL escapování '' a pak volá my_explode
    function mysql_explode($str, $separator, $leftBracket, $rightBracket) {
        $str = str_replace("\'", "''", $str);
        return my_explode($str, $separator, $leftBracket, $rightBracket);
    }

$str = "My|Hello (sir|maam).|Hi there!";
var_dump(my_explode($str, '|', '(', ')'));
$sql = "INSERT INTO `registrace_osob` (casova_znacka, datum_prichodu_do_ceske_republiky, stupen_vzdelani, oblast_studia, vase_profese, o_jakou_praci_mate_zajem, mate_zajem_o_nejake_vzdelavani_kurzy, jakymi_jazyky_mluvite, jmeno, prijmeni, mesto_kde_se_bydlite_v_ceske_republice, potrebujete_chuvu_pro_deti_do_6_let_abyste_mohli_pracovat, telefon, emailova_adresa, datum_narozeni, poznamka_existuje_neco_co_jsme_se_nezeptali_a_meli_bychom_to_ved, potrebujete_neco_jineho_nez_praci_nebo_kurzy_prosim_pis, kdy_muzete_jit_na_kurz, chci_jit_na_kurzy, souhlasim_s_registraci_mych_udaju_pro_komunikaci_s_projektem_rek, Col_21, identifikator) VALUES ('2022/03/30 6:11:43 odp. GMT+2', '2022-03-07', 'вища школа', 'Вища Освіта', 'Учитель', 'робочий, на виробництві, ручний;Мене цікавить будь-яка робота, незалежно від сфери освіти чи професії', 'чеський;англійська;Основи роботи персонального комп’ютера;знання ринку праці в Чехії', 'український;російський', 'Мариана', 'Шелевер', 'Пльзень', 'Ні', '420777128264', 'marianshel01@gmail.com', '28.06.2002', '', '', '', '', 'ANO', '', 'Мариана Шелевер 420777128264 marianshel01@gmail.com');";
$sql .= "INSERT INTO `registrace_osob` (casova_znacka, datum_prichodu_do_ceske_republiky, stupen_vzdelani, oblast_studia, vase_profese, o_jakou_praci_mate_zajem, mate_zajem_o_nejake_vzdelavani_kurzy, jakymi_jazyky_mluvite, jmeno, prijmeni, mesto_kde_se_bydlite_v_ceske_republice, potrebujete_chuvu_pro_deti_do_6_let_abyste_mohli_pracovat, telefon, emailova_adresa, datum_narozeni, poznamka_existuje_neco_co_jsme_se_nezeptali_a_meli_bychom_to_ved, potrebujete_neco_jineho_nez_praci_nebo_kurzy_prosim_pis, kdy_muzete_jit_na_kurz, chci_jit_na_kurzy, souhlasim_s_registraci_mych_udaju_pro_komunikaci_s_projektem_rek, Col_21, identifikator) VALUES ('2022/03/30 6:11:43 odp. GMT+2', '2022-03-07', 'вища школа', 'Вища Освіта', 'Учитель', 'робочий, на виробництві, ручний;Мене цікавить будь-яка робота, незалежно від сфери освіти чи професії', 'чеський;англійська;Основи роботи персонального комп’ютера;знання ринку праці в Чехії', 'український;російський', 'Мариана', 'Шелевер', 'Пльзень', 'Ні', '420777128264', 'marianshel01@gmail.com', '28.06.2002', '', '', '', '', 'ANO', '', 'Мариана Шелевер 420777128264 marianshel01@gmail.com');";
$sql .= "INSERT INTO `registrace_osob` (casova_znacka, datum_prichodu_do_ceske_republiky, stupen_vzdelani, oblast_studia, vase_profese, o_jakou_praci_mate_zajem, mate_zajem_o_nejake_vzdelavani_kurzy, jakymi_jazyky_mluvite, jmeno, prijmeni, mesto_kde_se_bydlite_v_ceske_republice, potrebujete_chuvu_pro_deti_do_6_let_abyste_mohli_pracovat, telefon, emailova_adresa, datum_narozeni, poznamka_existuje_neco_co_jsme_se_nezeptali_a_meli_bychom_to_ved, potrebujete_neco_jineho_nez_praci_nebo_kurzy_prosim_pis, kdy_muzete_jit_na_kurz, chci_jit_na_kurzy, souhlasim_s_registraci_mych_udaju_pro_komunikaci_s_projektem_rek, Col_21, identifikator) VALUES ('2022/03/30 6:11:43 odp. GMT+2', '2022-03-07', 'вища школа', 'Вища Освіта', 'Учитель', 'робочий, на виробництві, ручний;Мене цікавить будь-яка робота, незалежно від сфери освіти чи професії', 'чеський;англійська;Основи роботи персонального комп’ютера;знання ринку праці в Чехії', 'український;російський', 'Мариана', 'Шелевер', 'Пльзень', 'Ні', '420777128264', 'marianshel01@gmail.com', '28.06.2002', '', '', '', '', 'ANO', '', 'Мариана Шелевер 420777128264 marianshel01@gmail.com');";
var_dump(my_explode($sql, ';', "'", "'"));
$sql = "INSERT INTO `registrace_osob` (casova_znacka, datum_prichodu_do_ceske_republiky, stupen_vzdelani, oblast_studia, vase_profese, o_jakou_praci_mate_zajem, mate_zajem_o_nejake_vzdelavani_kurzy, jakymi_jazyky_mluvite, jmeno, prijmeni, mesto_kde_se_bydlite_v_ceske_republice, potrebujete_chuvu_pro_deti_do_6_let_abyste_mohli_pracovat, telefon, emailova_adresa, datum_narozeni, poznamka_existuje_neco_co_jsme_se_nezeptali_a_meli_bychom_to_ved, potrebujete_neco_jineho_nez_praci_nebo_kurzy_prosim_pis, kdy_muzete_jit_na_kurz, chci_jit_na_kurzy, souhlasim_s_registraci_mych_udaju_pro_komunikaci_s_projektem_rek, Col_21, identifikator) VALUES ('2022/03/30 6:11:43 odp. GMT+2', '2022-03-07', 'вища \'школа', 'Вища \'Освіта\'', 'Учитель', 'робочий, на виробництві, ручний;Мене цікавить будь-яка робота, незалежно від сфери освіти чи професії', 'чеський;англійська;Основи роботи персонального комп’ютера;знання ринку праці в Чехії', 'український;російський', 'Мариана', 'Шелевер', 'Пльзень', 'Ні', '420777128264', 'marianshel01@gmail.com', '28.06.2002', '', '', '', '', 'ANO', '', 'Мариана Шелевер 420777128264 marianshel01@gmail.com');";
var_dump(mysql_explode($sql, ';', "'", "'"));
