<?php

namespace App\Models;

use CodeIgniter\Model;

class BankReconciliation extends Model
{
	protected $table = 'bank_reconciliations';
	protected $primaryKey = 'id';
	protected $returnType = 'array';
	protected $useTimestamps = true;
	protected $allowedFields = [
		'bank_account_id','statement_date','system_balance','bank_balance','difference','status'
	];
}

