<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Pes\Model\Builder;

/**
 * Description of Sql
 *
 * @author pes2704
 */
class Sql implements SqlInterface {

    public function columns(array $cols): string {
        $columns = [];
        foreach ($cols as $col) {
            $columns[] = $this->identificator($col);
        }
        return implode(", ", $columns);
    }

    public function select(array $fields = []): string {
        if (count($fields)!==1 OR $fields[0]!='*') {
            $select = $this->columns($fields);
        } else {
            $select = '*';
        }
        return " SELECT $select ";
    }

    public function from(string $name): string {
        return " FROM ".$this->identificator($name)." ";
    }

    public function where(string $condition = ""): string {
        return $condition ? " WHERE ".$condition." " : "";
    }

    /**
     * Obalí jméno zpětnými apostrofy (backtics)
     * @param type $name
     * @return string
     */
    private function identificator($name) {
    // https://stackoverflow.com/questions/16476744/exploding-a-string-using-a-regular-expression

    // if you have more than one delimiter like quotes (that are the same for open and close), you can write your pattern like this, using a capture group:
    // explanation:
//    (['#@°])   one character in the class is captured in group 1
//    .*?        any character zero or more time in lazy mode
//    \1         group 1 content
        preg_match_all('~([\'"`]).*?\1|\([^)]+\)|[^ ]+~', $name, $match);
        $keys = [
            ',',
            'AS',
            'JOIN',
            'LEFT',
            'RIGHT',
            'INNER',
            'OUTER'
        ];

        $terms = $match[0];
        $parenthesis = $match[1];   // znaky nalezené jako ohraničení (captured in group 1)
        $id = [];
        foreach ($terms as $key=>$term) {
            if ($parenthesis[$key]=="" AND (!in_array($term, $keys))) {
                    $trn = trim($term);
                    $id[] = "`".trim($trn, "`")."`";
            } else {
                $id[] = $term;
            }
        }
        return implode(" ", $id);
    }

    /**
     *
     * @param array $conditions Jedno nebo více asociativních polí.
     * @return string
     */
    public function and(...$conditions): string {
        $merged = $this->merge($conditions);
        return $merged ? implode(" AND ", $merged) : "";
    }

    /**
     *
     * @param array $conditions Jedno nebo více asociativních polí.
     * @return string
     */
    public function or(...$conditions): string {
        $merged = $this->merge($conditions);
        return $merged ? "(".implode(" OR ", $merged).")" : "";  // závorky pro prioritu OR před případnými AND spojujícími jednotlivé OR výrazy
    }

    private function merge($conditions): array {
        $merged = [];
        if ($conditions) {
            foreach ($conditions as $condition) {
                if ($condition) {
                    if (is_array($condition)) {
                        $merged = array_merge_recursive($merged, $condition);
                    } else {
                        $merged = array_merge_recursive($merged, [$condition]);
                    }
                }
            }
        }
        return $merged;
    }

    public function values(array $names): string {
        return ':'.implode(', :', $names);
    }

    public function set(array $setTouples): string {
        return implode(", ", $setTouples);
    }

    /**
     * {@inheritdoc}
     *
     * @param array $names
     * @param string $placeholderPrefix
     * @return array
     */
    public function touples(array $names, $placeholderPrefix=''): array {
        $touples = [];
        foreach ($names as $name) {
            $touples[] = $this->identificator($name) . " = :".$placeholderPrefix.$name;
        }
        return $touples;
    }
}
