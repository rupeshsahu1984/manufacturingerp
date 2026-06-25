<?php

namespace App\Models;

use CodeIgniter\Model;

class CostCenter extends Model
{
	protected $table = 'cost_centers';
	protected $primaryKey = 'id';
	protected $returnType = 'array';
	protected $useTimestamps = true;
	protected $allowedFields = [
		'code','name','parent_id','description','budget_amount','currency','is_active'
	];

	public function getHierarchy()
	{
		$rows = $this->orderBy('code','ASC')->findAll();
		$byId = [];
		foreach ($rows as $row) { $row['children'] = []; $byId[$row['id']] = $row; }
		$tree = [];
		foreach ($byId as $id => $row) {
			if (!empty($row['parent_id']) && isset($byId[$row['parent_id']])) {
				$byId[$row['parent_id']]['children'][] = &$byId[$id];
			} else { $tree[] = &$byId[$id]; }
		}
		return $tree;
	}
}

