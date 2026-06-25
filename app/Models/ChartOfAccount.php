<?php

namespace App\Models;

use CodeIgniter\Model;

class ChartOfAccount extends Model
{
	protected $table = 'chart_of_accounts';
	protected $primaryKey = 'id';
	protected $returnType = 'array';
	protected $useTimestamps = true;
	protected $allowedFields = [
		'account_code','account_name','account_type','parent_account_id','currency','status','cost_center_id'
	];

	public function getHierarchy()
	{
		$accounts = $this->orderBy('account_code','ASC')->findAll();
		$byId = [];
		foreach ($accounts as $acc) { $acc['children'] = []; $byId[$acc['id']] = $acc; }
		$tree = [];
		foreach ($byId as $id => $acc) {
			if (!empty($acc['parent_account_id']) && isset($byId[$acc['parent_account_id']])) {
				$byId[$acc['parent_account_id']]['children'][] = &$byId[$id];
			} else {
				$tree[] = &$byId[$id];
			}
		}
		return $tree;
	}
}

