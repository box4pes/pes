<?php

/*
 * Copyright (C) 2019 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Database\Handler;

/**
 * Objekt User obsahuje informace o uživateli pro připojení k databázi, t.j. uživatelské jméno a heslo.
 *
 * Interface User neobsahuje settery ani gettery. Settery neobsahuje, protože vše je nutno nastavit při instancování objektu,
 * pozdější nastavení User name, pass nemůže mít vliv na již provedené připojení k databázi. Třída neobsahuje gettery z bezpečnostních důvodů.
 *
 * @author pes2704
 */
interface AccountInterface {

}
