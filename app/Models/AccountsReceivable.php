<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountsReceivable extends Model
{
	protected $table = 'accounts_receivable';
	protected $primaryKey = 'id';
	protected $returnType = 'array';
	protected $useTimestamps = true;
	protected $allowedFields = [
		'customer_id','invoice_no','invoice_date','due_date','amount','currency','status','reference_id'
	];

	public function getAging($asOfDate = null)
	{
		$asOf = $asOfDate ?: date('Y-m-d');
		$rows = $this->findAll();
		$aging = ['current'=>0,'30'=>0,'60'=>0,'90'=>0,'120+'=>0];
		foreach ($rows as $r) {
			$diff = (new \DateTime(isset($r['due_date']) ? $r['due_date'] : $r['invoice_date']))->diff(new \DateTime($asOf))->days;
			$bal = (float)$r['amount'];
			if ($diff <= 0) $aging['current'] += $bal; elseif ($diff <= 30) $aging['30'] += $bal; elseif ($diff <= 60) $aging['60'] += $bal; elseif ($diff <= 90) $aging['90'] += $bal; else $aging['120+'] += $bal;
		}
		return $aging;
	}
}

