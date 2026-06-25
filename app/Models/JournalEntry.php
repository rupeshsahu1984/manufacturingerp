<?php

namespace App\Models;

use CodeIgniter\Model;

class JournalEntry extends Model
{
	protected $table = 'journal_entries';
	protected $primaryKey = 'id';
	protected $returnType = 'array';
	protected $useTimestamps = true;
	protected $allowedFields = [
		'journal_number','entry_date','description','currency','fx_rate','reference_module','reference_id','status','created_by','approved_by'
	];

	public function lines()
	{
		return (new JournalLine())->where('journal_entry_id', $this->id)->orderBy('line_no','ASC')->findAll();
	}

	public function post(array $lines)
	{
		$db = \Config\Database::connect();
		$db->transStart();
		$id = $this->insert($this->attributes, true);
		$lineNo = 1;
		$totalDebit = 0; $totalCredit = 0;
		$lineModel = new JournalLine();
		foreach ($lines as $line) {
			$line['journal_entry_id'] = $id;
			$line['line_no'] = $lineNo++;
			$totalDebit += (float)(isset($line['debit']) ? $line['debit'] : 0);
			$totalCredit += (float)(isset($line['credit']) ? $line['credit'] : 0);
			$lineModel->insert($line);
		}
		if (round($totalDebit,2) !== round($totalCredit,2)) {
			$db->transRollback();
			throw new \RuntimeException('Unbalanced journal: debit '.$totalDebit.' != credit '.$totalCredit);
		}
		$db->transComplete();
		return $id;
	}
}

