<?php

namespace App\Utils;

use App\Utils\DatabaseHelper;

class PaginationHelper
{

    public static function getTotalRecords($tableName)
    {
        $query = "SELECT COUNT(*) AS records FROM $tableName";
        $data = DatabaseHelper::query($query);
        return (int) $data[0]["records"];
    }


    public static function getPagination(int $start, int $length, string $search = '', string $orderBy = 'id', string $orderDir = 'DESC', string $tableName, array $columns)
    {

        $data = self::buildQuery($search, $columns);

        $whereClause = $data["where"];
        $types = $data["types"];
        $params = $data["params"];

        $query = "SELECT * FROM $tableName $whereClause ORDER BY $orderBy $orderDir LIMIT ?, ?";


        $params[] = $start;
        $params[] = $length;
        $types .= 'ii';


        return DatabaseHelper::getDataPreparedQuery($query, $types, ...$params);
    }



    public static function getFilteredCount(string $search, string $tableName, array $columns)
    {

        $data = self::buildQuery($search, $columns);

        $whereClause = $data["where"];
        $types = $data["types"];
        $params = $data["params"];

        $query = "SELECT COUNT(*) AS records FROM $tableName $whereClause";

        if (count($params) < 1) {
            $result = DatabaseHelper::query($query);
            return isset($result[0]['records']) ? (int) $result[0]['records'] : 0;
        }

        $result = DatabaseHelper::getDataPreparedQuery($query, $types, ...$params);
        return isset($result[0]['records']) ? (int) $result[0]['records'] : 0;
    }


    public static function make(array $params, string $tableName, array $validColumns)
    {
        $validDirections = ['asc', 'desc'];

        $defaultOrderDir = "desc"; // Show most recent items first
        $start = (int)($params['start'] ?? 0);
        $length = (int)($params['length'] ?? 10); // 10 items per page as default
        $search = $params['search']['value'] ?? '';
        $orderColumnIndex = $params['order'][0]['column'] ?? 0; // The index of the column the user wants to sort by


        if (!isset($params['order'])) {
            $params['order'][0]['dir'] = 'desc';
        }

        $orderDir = $params['order'][0]['dir'] === $defaultOrderDir ? 'asc' : 'desc'; // Show most recent items first by default otherwise show oldest items

        $orderBy = $validColumns[$orderColumnIndex] ? $validColumns[$orderColumnIndex] : $validColumns[0]; // Sort by the selected column in the table or column 0 which in most cases is ID
        $orderDir = in_array(strtolower($orderDir), $validDirections) ? strtoupper($orderDir) : 'ASC';

        return self::getPagination($start, $length, $search, $orderBy, $orderDir, $tableName, $validColumns);
    }

    private static function buildQuery($search, $columns)
    {
        $terms = explode(' ', strtolower(trim($search)));
        $whereParts = [];
        $params = [];
        $types = '';


        foreach ($terms as $term) {
            $termConditions = [];
            foreach ($columns as $column) {
                if ($column === "active") {
                    if (str_contains("yes", $term) || str_contains("ye", $term) || str_contains("y", $term)) {
                        $term = 1;
                    }
                    if (str_contains("no", $term) || str_contains("n", $term)) {
                        $term = 0;
                    }
                }
                $termConditions[] = "LOWER($column) LIKE ?";
                $params[] = '%' . $term . '%';
                $types .= 's';
            }

            $whereParts[] = '(' . implode(' OR ', $termConditions) . ')';
        }

        $whereClause = '';
        if (!empty($whereParts)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereParts);
        }
        return ["where" => $whereClause, "types" => $types, "params" => $params];
    }
}
