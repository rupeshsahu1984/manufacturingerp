<?php

namespace App\Models;

use CodeIgniter\Model;

class TaxDefinition extends Model
{
	protected $table = 'tax_definitions';
	protected $primaryKey = 'id';
	protected $returnType = 'array';
	protected $useTimestamps = true;
	protected $allowedFields = [
		'tax_code','tax_name','tax_type','rate','jurisdiction','is_active'
	];
}

