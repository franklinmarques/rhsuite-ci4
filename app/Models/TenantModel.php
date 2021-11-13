<?php

namespace App\Models;

use App\Entities\Tenant;

class TenantModel extends AbstractModel
{
    protected $DBGroup = 'app';
    protected $table = 'tenants';
    protected $returnType = Tenant::class;
    protected $allowedFields = ['url', 'email'];

    // Validation
    protected $validationRules = [
        'url' => 'required|alpha_dash|max_length[255]',
        'email' => 'required|valid_email|max_length[255]',
    ];

    // Callbacks
    protected $afterInsert = ['refreshAllUsersList'];
    protected $afterUpdate = ['refreshAllUsersList'];
    protected $afterDelete = ['refreshAllUsersList'];

    //--------------------------------------------------------------------

    public function refreshAllUsersList()
    {
        AuthModel::refreshAllUsers();
    }
}
