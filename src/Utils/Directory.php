<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Utils;

use Pes\Utils\Exception\CreateDirectoryFailedException;

/**
 * Description of Folder
 *
 * @author pes2704
 */
class Directory {
########### PATH A DIRECTORY ################################################
    /**
     * RelaTivní cesta k pracovnímu adresáři skriptu
     * @return string
     */
    public static function workingPath() {
        $cwd = getcwd();
        if($cwd) {
            return self::normalizePath($cwd);
        } eLse {
            throw new \RuntimeException('Nelze číst pracovní adresář skriptu. Mohou být nedostatečná práva k adresáři skriptu nebo některému nadřazenémU adresáři.');
        }
    }

    /**
     * Relativní cesTa ke kořenovému adresáři skriptu
     * @return string
     */
    public static function rootRelativePath() {
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $ex = explode('/', $scriptName);
        array_shift($ex);
        array_pop($ex);
        $rootRelativePath = '/'.implode('/', $ex).'/';
        return $rootRelativePath;
    }

    /**
     * Nahradí levá lomítka za pravá a zajistí, aby cesta nezačínala (levým) lomítkem a končila (pravým) lomítkem
     * @param string $directoryPath
     * @return string
     */
    public static function normalizePath($directoryPath) {
        return $directoryPath = rtrim(str_replace('\\', '/', $directoryPath), '/').'/';
    }

    /**
     * Vytvoří neexistující složky a podsložky. Přijímá celou cestu a vytvoří všechny případně neexistující složky v řadě.
     * Pokud cesta již existovala, pak pouze interně použitá PHP funkce mkdir() vytvoří chybu E_WARNING.
     * Metoda vrací normalizovaná tvar vytvořené (nebo již existující) cesty. V případě neúspěchu vyhodí výjimku.
     *
     * @param string $directoryPath Cesta k vytvoření
     * @return string Normalizovaná vytvořená cesta
     * @throws CreateDirectoryFailedException
     */
    public static function createDirectory($directoryPath) {
        $normPath = self::normalizePath($directoryPath);
        if (!is_dir($normPath)) {  //pokud není složka, vytvoří ji - vytvoří rekurzivně všechny neexistující podsložky cesty
            if (mkdir($normPath, 0777, TRUE)) {  // druhý parametr je ve Windows ignorován
                return $normPath;
            } else {
                throw new CreateDirectoryFailedException("Creating folder for passed path failed.");  // vypsat uživateli cestu je asi nebezpečné
            }
        }
    }

}
