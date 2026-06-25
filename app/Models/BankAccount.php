<?php

namespace App\Models;

use CodeIgniter\Model;

class BankAccount extends Model
{
	protected $table = 'bank_accounts';
	protected $primaryKey = 'id';
	protected $returnType = 'array';
	protected $useTimestamps = true;
	protected $allowedFields = [
		'account_name','bank_name','account_number','ifsc','currency','coa_account_id','is_active'
	];
}

