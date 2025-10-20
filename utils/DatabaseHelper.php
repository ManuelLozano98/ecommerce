<?php

namespace App\Utils;

use App\Config\Database;
use Exception;

class DatabaseHelper
{
    public static function query($sql)
    {
        $con = Database::getConnection();
        $q = $con->query($sql);
        if ($q === false) {
            throw new Exception("MySQL Query Error: " . $con->error);
        }
        $data = [];
        while ($row = $q->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    public static function preparedQuery($sql, $types, ...$params)
    {
        $con = Database::getConnection();
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            throw new Exception("MySQL Prepare Error: " . $con->error);
        }
        if (!$stmt->bind_param($types, ...$params)) {
            throw new Exception("MySQL Bind Param Error: " . $stmt->error);
        }
        if (!$stmt->execute()) {
            throw new Exception("MySQL Execute Error: " . $stmt->error);
        }
        return true;
    }
    public static function getDataPreparedQuery($sql, $types, ...$params)
    {
        $con = Database::getConnection();
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            throw new Exception("MySQL Prepare Error: " . $con->error);
        }
        if (!$stmt->bind_param($types, ...$params)) {
            throw new Exception("MySQL Bind Param Error: " . $stmt->error);
        }

        if (!$stmt->execute()) {
            throw new Exception("MySQL Execute Error: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("MySQL Get Result Error: " . $stmt->error);
        }
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    public static function getLastId()
    {
        $con = Database::getConnection();
        return $con->insert_id !== 0;
    }
    public static function preparedQueryObject($sql, $types, $object, $propertyNames)
    {

        $params = [];
        foreach ($propertyNames as $prop) {
            $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $prop)));
            if (!method_exists($object, $method)) {
                throw new Exception("Method $method does not exist in class " . get_class($object));
            }
            $value = $object->$method();
            if (is_object($value) && method_exists($value, 'getId')) {
                $params[] = $value->getId();
            } else {
                $params[] = $value->$method();
            }
        }
        if (strlen($types) !== count($params)) {
            throw new Exception("The number of types does not match the number of parameters");
        }

        $con = Database::getConnection();
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            throw new Exception("MySQL Prepare Error: " . $con->error);
        }
        if (!$stmt->bind_param($types, ...$params)) {
            throw new Exception("MySQL Bind Param Error: " . $stmt->error);
        }
        if (!$stmt->execute()) {
            throw new Exception("MySQL Execute Error: " . $stmt->error);
        }
        $stmt->close();
        return true;
    }
}
