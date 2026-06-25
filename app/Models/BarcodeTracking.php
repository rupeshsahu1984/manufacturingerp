<?php

namespace App\Models;

use CodeIgniter\Model;

class BarcodeTracking extends Model
{
    protected $table = 'barcode_tracking';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'barcode_id',
        'rfid_tag',
        'item_id',
        'batch_id',
        'warehouse_id',
        'location_id',
        'barcode_type',
        'barcode_format',
        'barcode_data',
        'print_date',
        'print_count',
        'scan_date',
        'scan_location',
        'scan_user_id',
        'scan_type',
        'scan_status',
        'last_scan_date',
        'scan_history_count',
        'is_active',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'barcode_id' => 'required|is_unique[barcode_tracking.barcode_id,id,{id}]',
        'item_id' => 'required|integer',
        'warehouse_id' => 'required|integer',
        'barcode_type' => 'required|in_list[1d,2d,qr,rfid]',
        'barcode_format' => 'required|in_list[code128,code39,ean13,ean8,upc,datamatrix,qrcode,rfid]',
        'barcode_data' => 'required',
        'scan_type' => 'required|in_list[in,out,transfer,count,verification]',
        'scan_status' => 'required|in_list[success,failed,duplicate,invalid]'
    ];

    protected $validationMessages = [
        'barcode_id' => [
            'required' => 'Barcode ID is required',
            'is_unique' => 'Barcode ID must be unique'
        ],
        'item_id' => [
            'required' => 'Item is required',
            'integer' => 'Invalid item ID'
        ],
        'barcode_type' => [
            'required' => 'Barcode type is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id', 'id');
    }

    public function batch()
    {
        return $this->belongsTo('App\Models\BatchTracking', 'batch_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse', 'warehouse_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\WarehouseLocation', 'location_id', 'id');
    }

    public function scanUser()
    {
        return $this->belongsTo('App\Models\User', 'scan_user_id', 'id');
    }

    public function scanHistory()
    {
        return $this->hasMany('App\Models\BarcodeScanHistory', 'barcode_id', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('barcode_tracking.*, items.item_code, items.item_name, batch_tracking.batch_number, warehouses.warehouse_name, warehouse_locations.location_name, users.username as scan_user_name')
                        ->join('items', 'items.id = barcode_tracking.item_id')
                        ->join('batch_tracking', 'batch_tracking.id = barcode_tracking.batch_id', 'left')
                        ->join('warehouses', 'warehouses.id = barcode_tracking.warehouse_id')
                        ->join('warehouse_locations', 'warehouse_locations.id = barcode_tracking.location_id', 'left')
                        ->join('users', 'users.id = barcode_tracking.scan_user_id', 'left');

        if ($id) {
            return $builder->where('barcode_tracking.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByBarcode($barcodeId)
    {
        return $this->where('barcode_id', $barcodeId)->first();
    }

    public function getByRFID($rfidTag)
    {
        return $this->where('rfid_tag', $rfidTag)->first();
    }

    public function getByItem($itemId, $warehouseId = null)
    {
        $builder = $this->where('item_id', $itemId);
        
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    public function getByWarehouse($warehouseId)
    {
        return $this->where('warehouse_id', $warehouseId)
                    ->where('is_active', 1)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getByLocation($locationId)
    {
        return $this->where('location_id', $locationId)
                    ->where('is_active', 1)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getActiveBarcodes($itemId = null, $warehouseId = null)
    {
        $builder = $this->where('is_active', 1);
        
        if ($itemId) {
            $builder->where('item_id', $itemId);
        }
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    public function getBarcodeStats($itemId = null, $warehouseId = null)
    {
        $builder = $this->select('COUNT(*) as total_barcodes, COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_barcodes, COUNT(CASE WHEN scan_date IS NOT NULL THEN 1 END) as scanned_barcodes');
        
        if ($itemId) {
            $builder->where('item_id', $itemId);
        }
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }

        return $builder->first();
    }

    public function getScanStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('scan_type, scan_status, COUNT(*) as count')
                        ->where('scan_date IS NOT NULL')
                        ->groupBy('scan_type, scan_status');
        
        if ($startDate) {
            $builder->where('scan_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('scan_date <=', $endDate);
        }

        return $builder->findAll();
    }

    public function generateBarcode($itemId, $batchId = null, $prefix = null)
    {
        $item = model('Item')->find($itemId);
        if (!$item) {
            return false;
        }

        if (!$prefix) {
            $prefix = 'BC';
        }

        $date = date('Ymd');
        $itemCode = $item['item_code'];
        $batchCode = $batchId ? 'B' . $batchId : '';
        
        $lastBarcode = $this->where('barcode_id LIKE', $prefix . $date . $itemCode . $batchCode . '%')
                            ->orderBy('barcode_id', 'DESC')
                            ->first();

        if ($lastBarcode) {
            $lastNumber = intval(substr($lastBarcode['barcode_id'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . $itemCode . $batchCode . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function generateRFID($itemId, $batchId = null)
    {
        $item = model('Item')->find($itemId);
        if (!$item) {
            return false;
        }

        $prefix = 'RFID';
        $date = date('Ymd');
        $itemCode = $item['item_code'];
        $batchCode = $batchId ? 'B' . $batchId : '';
        
        $lastRFID = $this->where('rfid_tag LIKE', $prefix . $date . $itemCode . $batchCode . '%')
                         ->orderBy('rfid_tag', 'DESC')
                         ->first();

        if ($lastRFID) {
            $lastNumber = intval(substr($lastRFID['rfid_tag'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . $itemCode . $batchCode . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function createBarcode($data)
    {
        $barcodeData = [
            'barcode_id' => isset($data['barcode_id']) ? $data['barcode_id'] : $this->generateBarcode($data['item_id'], isset($data['batch_id']) ? $data['batch_id'] : null),
            'rfid_tag' => isset($data['rfid_tag']) ? $data['rfid_tag'] : $this->generateRFID($data['item_id'], isset($data['batch_id']) ? $data['batch_id'] : null),
            'item_id' => $data['item_id'],
            'batch_id' => isset($data['batch_id']) ? $data['batch_id'] : null,
            'warehouse_id' => $data['warehouse_id'],
            'location_id' => isset($data['location_id']) ? $data['location_id'] : null,
            'barcode_type' => isset($data['barcode_type']) ? $data['barcode_type'] : '2d',
            'barcode_format' => isset($data['barcode_format']) ? $data['barcode_format'] : 'qrcode',
            'barcode_data' => isset($data['barcode_data']) ? $data['barcode_data'] : $data['barcode_id'],
            'print_date' => isset($data['print_date']) ? $data['print_date'] : date('Y-m-d H:i:s'),
            'print_count' => 1,
            'is_active' => 1,
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        return $this->insert($barcodeData);
    }

    public function scanBarcode($barcodeId, $scanData)
    {
        $barcode = $this->getByBarcode($barcodeId);
        if (!$barcode) {
            return false;
        }

        $scanHistoryData = [
            'barcode_id' => $barcodeId,
            'scan_date' => date('Y-m-d H:i:s'),
            'scan_location' => isset($scanData['scan_location']) ? $scanData['scan_location'] : null,
            'scan_user_id' => isset($scanData['scan_user_id']) ? $scanData['scan_user_id'] : session()->get('user_id'),
            'scan_type' => isset($scanData['scan_type']) ? $scanData['scan_type'] : 'verification',
            'scan_status' => isset($scanData['scan_status']) ? $scanData['scan_status'] : 'success',
            'device_info' => isset($scanData['device_info']) ? $scanData['device_info'] : null,
            'gps_coordinates' => isset($scanData['gps_coordinates']) ? $scanData['gps_coordinates'] : null,
            'notes' => isset($scanData['notes']) ? $scanData['notes'] : ''
        ];

        // Insert scan history
        model('BarcodeScanHistory')->insert($scanHistoryData);

        // Update barcode tracking
        $updateData = [
            'scan_date' => date('Y-m-d H:i:s'),
            'scan_location' => isset($scanData['scan_location']) ? $scanData['scan_location'] : null,
            'scan_user_id' => isset($scanData['scan_user_id']) ? $scanData['scan_user_id'] : session()->get('user_id'),
            'scan_type' => isset($scanData['scan_type']) ? $scanData['scan_type'] : 'verification',
            'scan_status' => isset($scanData['scan_status']) ? $scanData['scan_status'] : 'success',
            'last_scan_date' => date('Y-m-d H:i:s'),
            'scan_history_count' => $barcode['scan_history_count'] + 1
        ];

        return $this->update($barcode['id'], $updateData);
    }

    public function printBarcode($barcodeId)
    {
        $barcode = $this->find($barcodeId);
        if (!$barcode) {
            return false;
        }

        $updateData = [
            'print_date' => date('Y-m-d H:i:s'),
            'print_count' => $barcode['print_count'] + 1
        ];

        return $this->update($barcodeId, $updateData);
    }

    public function deactivateBarcode($barcodeId, $reason = '')
    {
        return $this->update($barcodeId, [
            'is_active' => 0,
            'notes' => $reason ? $reason : 'Barcode deactivated'
        ]);
    }

    public function reactivateBarcode($barcodeId)
    {
        return $this->update($barcodeId, [
            'is_active' => 1,
            'notes' => 'Barcode reactivated'
        ]);
    }

    public function getBarcodeTypes()
    {
        return [
            '1d' => '1D Barcode',
            '2d' => '2D Barcode',
            'qr' => 'QR Code',
            'rfid' => 'RFID Tag'
        ];
    }

    public function getBarcodeFormats()
    {
        return [
            'code128' => 'Code 128',
            'code39' => 'Code 39',
            'ean13' => 'EAN-13',
            'ean8' => 'EAN-8',
            'upc' => 'UPC',
            'datamatrix' => 'Data Matrix',
            'qrcode' => 'QR Code',
            'rfid' => 'RFID'
        ];
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

    public function getBarcodeAnalytics($itemId = null, $warehouseId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(created_at) as date, COUNT(*) as barcode_count, COUNT(CASE WHEN scan_date IS NOT NULL THEN 1 END) as scanned_count')
                        ->groupBy('DATE(created_at)');
        
        if ($itemId) {
            $builder->where('item_id', $itemId);
        }
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }
        if ($startDate) {
            $builder->where('created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('created_at <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function getScanAnalytics($startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(scan_date) as date, COUNT(*) as scan_count, COUNT(DISTINCT barcode_id) as unique_barcodes_scanned')
                        ->where('scan_date IS NOT NULL')
                        ->groupBy('DATE(scan_date)');
        
        if ($startDate) {
            $builder->where('scan_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('scan_date <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function getBarcodeByData($barcodeData)
    {
        return $this->where('barcode_data', $barcodeData)
                    ->where('is_active', 1)
                    ->first();
    }

    public function getBarcodesForPrinting($itemId = null, $warehouseId = null, $limit = 100)
    {
        $builder = $this->where('is_active', 1);
        
        if ($itemId) {
            $builder->where('item_id', $itemId);
        }
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }

        return $builder->orderBy('created_at', 'ASC')
                      ->limit($limit)
                      ->findAll();
    }

    public function bulkPrintBarcodes($barcodeIds)
    {
        $barcodes = $this->whereIn('id', $barcodeIds)->findAll();
        $printResults = [];

        foreach ($barcodes as $barcode) {
            $printResult = $this->printBarcode($barcode['id']);
            $printResults[] = [
                'barcode_id' => $barcode['barcode_id'],
                'success' => $printResult,
                'print_date' => date('Y-m-d H:i:s')
            ];
        }

        return $printResults;
    }
}
