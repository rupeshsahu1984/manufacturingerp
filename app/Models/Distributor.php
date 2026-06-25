<?php

namespace App\Models;

use CodeIgniter\Model;

class Distributor extends Model
{
    protected $table = 'distributors';
    protected $primaryKey = 'id';
    protected $allowedFields = ['distributor_name', 'distributor_code', 'contact_person', 'email', 'phone', 'address', 'status', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
