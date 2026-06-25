<?php

namespace App\Models;

use CodeIgniter\Model;

class Expense extends Model
{
	protected $table = 'expenses';
	protected $primaryKey = 'id';
	protected $returnType = 'array';
	protected $useTimestamps = true;
	protected $allowedFields = [
		'employee_id','cost_center_id','category','amount','currency','expense_date','approval_status','payment_status','notes','attachment_path'
	];
}

