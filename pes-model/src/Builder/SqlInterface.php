<?php
namespace Pes\Model\Builder;


/**
 *
 * @author pes2704
 */
interface SqlInterface {

    /**
     * Vytvoří řetězec ze slova SELECT a podle hodnoty parametru $fields:
     * - pole fields má jen jeden prvek a hodnotou tohoto prvku je string *, pak za slovo SELECT přidá znak *
     * - v ostatních případech za slovo SELECT přidá řetězec z jmen v poli oddělených čárkou, jednoltlivá jména formátuje jako SQL identifikátory, t.j. obalí je zpětnými apostrofy (backticks)
     *
     * @param array $fields
     * @return string
     */
    public function select(array $fields = []): string;
    public function from(string $name): string;
    public function where(string $condition = ""): string;

    /**
     * Vytvoří řetězec z jmen sloupců.
     * Jednotlivá jména sloupců formátuje jako SQL identifikátory, t.j. obalí je zpětnými apostrofy (backticks) a zřetězí je se znakem čárka jako oddělovačem.
     *
     * @param array $cols Pole jmen sloupců
     * @return string
     */
    public function columns(array $cols): string;
    public function values(array $names): string;
    public function set(array $setTouples): string;

    /**
     * Z pole jmen vytvoří pole výrazů sloupec = :placeholder. Sloupec je jméno použité jako identifikátor a placeholder je pojmenovaný placeholder pro prepared statement.
     * Pokud je zadán parametr placeholder prefix, jsou placeholdery prefixovány. To je potřebné pro případ, že je nutné vytvořit sql s opakujícími se (duplicitními)
     * jmény sloupců. Pojmenované placeholdery se totiž v SQL výrazu nesmí opakovat. Opakovaným voláním metody bez prefixu a s různými prefixy lze předejít duplicitám.
     *
     * @param array $names
     * @param string $placeholderPrefix
     * @return array
     */
    public function touples(array $names, $placeholderPrefix=''): array;

    public function and(...$conditions): string;
    public function or(...$conditions): string;
}
