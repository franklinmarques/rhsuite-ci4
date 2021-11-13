<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function command(): string
    {
        $db = db_connect();
        $tables = $db->listTables();
        foreach ($tables as $table) {
            $t = str_replace(' ', '', ucwords(str_replace('_', ' ', $table)));
            echo command('make:entity ' . $t . ' --force');
            echo command('make:model ' . $t . 'Model --return entity --table ' . $table . ' --force');
        }
        return view('welcome_message');
    }

    public function tables(): string
    {
        $db = db_connect();
        $tables = $db->listTables();
        $data = [];
        echo '<pre>';
        $type = [
            'varchar' => 'string',
            'char' => 'string',
            'text' => 'string',
            'tinyint' => 'bool',
            'longtext' => 'string',
            'year' => 'int',
            'enum' => 'string',
        ];
        $dates = [
            'created_at',
            'updated_at',
            'deleted_at',
        ];
        foreach ($tables as $table) {
            $fields = $db->getFieldData($table);
            $row = '[' . chr(10);
            foreach ($fields as $field) {
                if (in_array($field->name, $dates)) {
                    continue;
                }
                $row .= "'$field->name' => '" . ($field->nullable ? '?' : '') . ($type[$field->type] ?? $field->type) . "'," . chr(10);
            }
            $data[$table] = $row . '],' . chr(10);
        }
        print_r($data);
        exit;
        return view('welcome_message');
    }

    public function allowed()
    {
        $db = db_connect();
        $tables = $db->listTables();
        $data = [];
        $dates = [
            'created_at',
            'updated_at',
            'deleted_at',
        ];
        foreach ($tables as $table) {
            $fields = $db->getFieldData($table);
            $row = '[' . chr(10);
            foreach ($fields as $field) {
                if ($field->primary_key == 1) {
                    continue;
                }
                if (in_array($field->name, $dates)) {
                    continue;
                }
                $row .= "'$field->name'," . chr(10);
            }
            $data[$table] = $row . '],' . chr(10);
        }
        echo '<pre>';
        print_r($data);
        exit;
    }

    public function rules()
    {
        $db = db_connect();
        $tables = $db->listTables();
        $data = [];
        $type = [
            'int' => 'integer',
            'decimal' => 'numeric',
            'float' => 'numeric',
            'date' => 'valid_date',
            'datetime' => 'valid_date',
            'timestamp' => 'valid_date',
            'time' => 'valid_time',
            'varchar' => 'string',
            'char' => 'string',
            'text' => 'string',
            'tinyint' => 'integer',
            'smallint' => 'integer',
            'longtext' => 'string',
            'year' => 'int',
            'enum' => 'in_list[]',
            'json' => 'valid_json',
        ];
        $dates = [
            'created_at',
            'updated_at',
            'deleted_at',
        ];
        foreach ($tables as $table) {
            $fields = $db->getFieldData($table);
            $row = '[' . chr(10);
            $rows = $db->query("SELECT *
                                     FROM `INFORMATION_SCHEMA`.`COLUMNS` 
                                     WHERE `TABLE_SCHEMA`='abcbr304_ame' AND `TABLE_NAME`='{$table}'")->getResult();
            $query = [];
            $pk = '';
            foreach ($rows as $ee) {
                if ($ee->COLUMN_KEY == 'PRI') {
                    $pk = $ee->COLUMN_NAME;
                }
                $query[$ee->COLUMN_NAME] = [
                    'name' => $ee->COLUMN_NAME,
                    'key' => $ee->COLUMN_KEY,
                    'extra' => $ee->EXTRA,
                ];
            }
            foreach ($fields as $field) {
                $params = [];
                $queryParam = $query[$field->name];
                if ($field->primary_key == 1 and $queryParam['extra'] == 'auto_increment') {
                    continue;
                }
                if (in_array($field->name, $dates)) {
                    continue;
                }
                if ($field->nullable == false) {
                    $params[] = 'required';
                }
                if ($field->type == 'int' and $queryParam['key'] == 'MUL') {
                    $params[] = 'is_natural_no_zero';
                } elseif (array_key_exists($field->type, $type)) {
                    $params[] = $type[$field->type];
                }
                if ($queryParam['key'] == 'UNI') {
                    $params[] = "is_unique[{$table}.{$field->name},{$pk},{{$pk}}]";
                }
                if ($field->max_length) {
                    if ($field->type == 'tinyint') {
                        $params[] = "exact_length[{$field->max_length}]";
                    } else {
                        $params[] = "max_length[{$field->max_length}]";
                    }
                }
                $row .= "'$field->name' => '" . implode('|', $params) . "'," . chr(10);
            }
            $data[$table] = $row . '],' . chr(10);
        }
        echo '<pre>';
        print_r($data);
        exit;
    }
}
