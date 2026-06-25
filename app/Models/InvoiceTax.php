<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceTax extends Model
{
	protected $table = 'invoice_taxes';
	protected $primaryKey = 'id';
	protected $returnType = 'array';
	protected $useTimestamps = true;
	protected $allowedFields = [
		'module','invoice_id','tax_id','tax_rate','tax_amount','status'
	];
}

