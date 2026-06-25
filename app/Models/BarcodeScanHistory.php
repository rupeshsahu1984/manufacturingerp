<?php

namespace App\Models;

use CodeIgniter\Model;

class BarcodeScanHistory extends Model
{
    protected $table = 'barcode_scan_history';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'barcode_id',
        'scan_date',
        'scan_location',
        'scan_user_id',
        'scan_type',
        'scan_status',
        'device_info',
        'gps_coordinates',
        'ip_address',
        'user_agent',
        'session_id',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'barcode_id' => 'required',
        'scan_date' => 'required|valid_date',
        'scan_user_id' => 'required|integer',
        'scan_type' => 'required|in_list[in,out,transfer,count,verification]',
        'scan_status' => 'required|in_list[success,failed,duplicate,invalid]'
    ];

    protected $validationMessages = [
        'barcode_id' => [
            'required' => 'Barcode ID is required'
        ],
        'scan_user_id' => [
            'required' => 'Scan user is required',
            'integer' => 'Invalid scan user ID'
        ],
        'scan_type' => [
            'required' => 'Scan type is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function barcode()
    {
        return $this->belongsTo('App\Models\BarcodeTracking', 'barcode_id', 'id');
    }

    public function scanUser()
    {
        return $this->belongsTo('App\Models\User', 'scan_user_id', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('barcode_scan_history.*, barcode_tracking.barcode_id, barcode_tracking.rfid_tag, items.item_code, items.item_name, users.username as scan_user_name')
                        ->join('barcode_tracking', 'barcode_tracking.id = barcode_scan_history.barcode_id')
                        ->join('items', 'items.id = barcode_tracking.item_id')
                        ->join('users', 'users.id = barcode_scan_history.scan_user_id');

        if ($id) {
            return $builder->where('barcode_scan_history.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByBarcode($barcodeId)
    {
        return $this->where('barcode_id', $barcodeId)
                    ->orderBy('scan_date', 'DESC')
                    ->findAll();
    }

    public function getByUser($userId)
    {
        return $this->where('scan_user_id', $userId)
                    ->orderBy('scan_date', 'DESC')
                    ->findAll();
    }

    public function getByScanType($scanType)
    {
        return $this->where('scan_type', $scanType)
                    ->orderBy('scan_date', 'DESC')
                    ->findAll();
    }

    public function getByScanStatus($scanStatus)
    {
        return $this->where('scan_status', $scanStatus)
                    ->orderBy('scan_date', 'DESC')
                    ->findAll();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->where('scan_date >=', $startDate)
                    ->where('scan_date <=', $endDate)
                    ->orderBy('scan_date', 'DESC')
                    ->findAll();
    }

    public function getByLocation($scanLocation)
    {
        return $this->where('scan_location', $scanLocation)
                    ->orderBy('scan_date', 'DESC')
                    ->findAll();
    }

    public function getScanStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('scan_type, scan_status, COUNT(*) as count')
                        ->groupBy('scan_type, scan_status');
        
        if ($startDate) {
            $builder->where('scan_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('scan_date <=', $endDate);
        }

        return $builder->findAll();
    }

    public function getScanAnalytics($startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(scan_date) as date, COUNT(*) as scan_count, COUNT(DISTINCT barcode_id) as unique_barcodes_scanned, COUNT(DISTINCT scan_user_id) as unique_users')
                        ->groupBy('DATE(scan_date)');
        
        if ($startDate) {
            $builder->where('scan_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('scan_date <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function getUserScanStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('users.username, COUNT(*) as scan_count, COUNT(DISTINCT barcode_id) as unique_barcodes')
                        ->join('users', 'users.id = barcode_scan_history.scan_user_id')
                        ->groupBy('users.id, users.username');
        
        if ($startDate) {
            $builder->where('scan_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('scan_date <=', $endDate);
        }

        return $builder->orderBy('scan_count', 'DESC')->findAll();
    }

    public function getLocationScanStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('scan_location, COUNT(*) as scan_count, COUNT(DISTINCT barcode_id) as unique_barcodes')
                        ->where('scan_location IS NOT NULL')
                        ->groupBy('scan_location');
        
        if ($startDate) {
            $builder->where('scan_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('scan_date <=', $endDate);
        }

        return $builder->orderBy('scan_count', 'DESC')->findAll();
    }

    public function getDeviceScanStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('device_info, COUNT(*) as scan_count')
                        ->where('device_info IS NOT NULL')
                        ->groupBy('device_info');
        
        if ($startDate) {
            $builder->where('scan_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('scan_date <=', $endDate);
        }

        return $builder->orderBy('scan_count', 'DESC')->findAll();
    }

    public function getScanTrends($startDate = null, $endDate = null, $groupBy = 'day')
    {
        $dateFormat = $groupBy == 'hour' ? 'DATE_FORMAT(scan_date, "%Y-%m-%d %H:00:00")' : 'DATE(scan_date)';
        
        $builder = $this->select("{$dateFormat} as period, COUNT(*) as scan_count, COUNT(DISTINCT barcode_id) as unique_barcodes")
                        ->groupBy('period');
        
        if ($startDate) {
            $builder->where('scan_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('scan_date <=', $endDate);
        }

        return $builder->orderBy('period', 'ASC')->findAll();
    }

    public function getFailedScans($startDate = null, $endDate = null)
    {
        $builder = $this->select('barcode_scan_history.*, barcode_tracking.barcode_id, items.item_code, items.item_name, users.username as scan_user_name')
                        ->join('barcode_tracking', 'barcode_tracking.id = barcode_scan_history.barcode_id')
                        ->join('items', 'items.id = barcode_tracking.item_id')
                        ->join('users', 'users.id = barcode_scan_history.scan_user_id')
                        ->where('scan_status !=', 'success');
        
        if ($startDate) {
            $builder->where('scan_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('scan_date <=', $endDate);
        }

        return $builder->orderBy('scan_date', 'DESC')->findAll();
    }

    public function getDuplicateScans($startDate = null, $endDate = null)
    {
        $builder = $this->select('barcode_id, scan_user_id, COUNT(*) as scan_count, MIN(scan_date) as first_scan, MAX(scan_date) as last_scan')
                        ->groupBy('barcode_id, scan_user_id')
                        ->having('scan_count >', 1);
        
        if ($startDate) {
            $builder->where('scan_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('scan_date <=', $endDate);
        }

        return $builder->orderBy('scan_count', 'DESC')->findAll();
    }

    public function getScanPerformance($startDate = null, $endDate = null)
    {
        $builder = $this->select('COUNT(*) as total_scans, COUNT(CASE WHEN scan_status = "success" THEN 1 END) as successful_scans, COUNT(CASE WHEN scan_status != "success" THEN 1 END) as failed_scans, COUNT(DISTINCT barcode_id) as unique_barcodes, COUNT(DISTINCT scan_user_id) as unique_users');
        
        if ($startDate) {
            $builder->where('scan_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('scan_date <=', $endDate);
        }

        $result = $builder->first();
        
        if ($result && $result['total_scans'] > 0) {
            $result['success_rate'] = round(($result['successful_scans'] / $result['total_scans']) * 100, 2);
            $result['failure_rate'] = round(($result['failed_scans'] / $result['total_scans']) * 100, 2);
        } else {
            $result['success_rate'] = 0;
            $result['failure_rate'] = 0;
        }

        return $result;
    }

    public function getScanTypes()
    {
        return [
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'transfer' => 'Stock Transfer',
            'count' => 'Stock Count',
            'verification' => 'Verification'
        ];
    }

    public function getScanStatuses()
    {
        return [
            'success' => 'Success',
            'failed' => 'Failed',
            'duplicate' => 'Duplicate',
            'invalid' => 'Invalid'
        ];
    }

    public function getScanHistoryByItem($itemId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('barcode_scan_history.*, barcode_tracking.barcode_id, users.username as scan_user_name')
                        ->join('barcode_tracking', 'barcode_tracking.id = barcode_scan_history.barcode_id')
                        ->join('users', 'users.id = barcode_scan_history.scan_user_id')
                        ->where('barcode_tracking.item_id', $itemId);
        
        if ($startDate) {
            $builder->where('scan_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('scan_date <=', $endDate);
        }

        return $builder->orderBy('scan_date', 'DESC')->findAll();
    }

    public function getScanHistoryByWarehouse($warehouseId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('barcode_scan_history.*, barcode_tracking.barcode_id, items.item_code, items.item_name, users.username as scan_user_name')
                        ->join('barcode_tracking', 'barcode_tracking.id = barcode_scan_history.barcode_id')
                        ->join('items', 'items.id = barcode_tracking.item_id')
                        ->join('users', 'users.id = barcode_scan_history.scan_user_id')
                        ->where('barcode_tracking.warehouse_id', $warehouseId);
        
        if ($startDate) {
            $builder->where('scan_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('scan_date <=', $endDate);
        }

        return $builder->orderBy('scan_date', 'DESC')->findAll();
    }

    public function getRecentScans($limit = 50)
    {
        return $this->select('barcode_scan_history.*, barcode_tracking.barcode_id, items.item_code, items.item_name, users.username as scan_user_name')
                    ->join('barcode_tracking', 'barcode_tracking.id = barcode_scan_history.barcode_id')
                    ->join('items', 'items.id = barcode_tracking.item_id')
                    ->join('users', 'users.id = barcode_scan_history.scan_user_id')
                    ->orderBy('scan_date', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function getScanHistoryBySession($sessionId)
    {
        return $this->where('session_id', $sessionId)
                    ->orderBy('scan_date', 'ASC')
                    ->findAll();
    }

    public function getScanHistoryByIP($ipAddress, $startDate = null, $endDate = null)
    {
        $builder = $this->where('ip_address', $ipAddress);
        
        if ($startDate) {
            $builder->where('scan_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('scan_date <=', $endDate);
        }

        return $builder->orderBy('scan_date', 'DESC')->findAll();
    }
}
