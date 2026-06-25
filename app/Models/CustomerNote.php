<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerNote extends Model
{
    protected $table = 'customer_notes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['customer_id', 'note', 'created_by', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
