<?php

namespace App\Services;

use Config\Database;

abstract class AbstractService
{
    protected $empresa;

    protected $DBGroup;
    
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect($this->DBGroup);
        $this->empresa = session('empresa');
    }

    public function select($defaultText, $defaultValue = '')
    {

    }
}
