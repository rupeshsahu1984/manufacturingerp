<?php

namespace App\Models;

use CodeIgniter\Model;

class JournalLine extends Model
{
	protected $table = 'journal_lines';
	protected $primaryKey = 'id';
	protected $returnType = 'array';
	protected $useTimestamps = true;
	protected $allowedFields = [
		'journal_entry_id','line_no','account_id','cost_center_id','description','debit','credit','currency'
	];
}

