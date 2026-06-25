<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerCommunication extends Model
{
    protected $table = 'customer_communications';
    protected $primaryKey = 'id';
    protected $allowedFields = ['customer_id', 'contact_id', 'communication_type', 'communication_date', 'subject', 'notes', 'created_by', 'created_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;
}
