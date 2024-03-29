<?php
/**
 *
 * Classe pdo_lnk.
 *
 * classe héritant de mysqli
 *
 */


namespace Kat\MicroORM;

use PDO;
use Exception;

class PdoLink extends PDO
{
    /**
     * @brief Execute un SELECT sur une base MYSQL est retourné le nombre de résultats et un tableau contenant ceci.
     *
     * @param string $query la requête MYSQL a exécuter
     * @param string $index nom de la colonne à utiliser comme index pour $tab_resultat
     * @param mixed $tab_resultat tableau dans lequel le résultat de la requete doit être stocké
     * @return le nombre de résultats du SELECT.
     * @throws Exception
     */
    function executer_select($query, $index = '', &$tab_resultat)
    {
        $tab_resultat = [];

        if (!$result = $this->query($query)) {
            $error_mysql = $this->errorInfo();
            throw new Exception($error_mysql[1] . " " . $error_mysql[2] . " >> " . $query);
        }

        $nb_row = 0;

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if (isset($row[$index]) && $row[$index]) {
                $tab_resultat[$row[$index]] = $row;
            } else {
                $tab_resultat[] = $row;
            }
            $nb_row++;
        }

        $result->closeCursor();

        return $nb_row;
    }

    /**
     * @throws Exception
     */
    function exec_params($sql, $params)
    {
        $stm = $this->prepare($sql);
        $result = 0;
        if ($stm && $stm->execute($params)) {
            $result = $stm->rowCount();
            while ($stm->fetch(PDO::FETCH_ASSOC)) {
            }
        } else {
            $error_mysql = $stm->errorInfo();
            throw new Exception($error_mysql[1] . " " . $error_mysql[2] . " >> " . $sql);
        }
        return $result;
    }

}
