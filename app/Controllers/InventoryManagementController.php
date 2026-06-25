<?php

namespace App\Controllers;

use App\Models\Warehouse;
use App\Models\Item;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Employee;

class InventoryManagementController extends BaseController
{
    protected $warehouseModel;
    protected $itemModel;
    protected $stockModel;
    protected $transferModel;
    protected $categoryModel;
    protected $supplierModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->warehouseModel = new Warehouse();
        $this->itemModel = new Item();
        $this->stockModel = new Stock();
        $this->transferModel = new StockTransfer();
        $this->categoryModel = new Category();
        $this->supplierModel = new Supplier();
        $this->employeeModel = new Employee();
    }

    public function index()
    {
        // Dashboard view with inventory summary
        $data = [
            'title' => 'Inventory Management Dashboard',
            'warehouse_stats' => $this->warehouseModel->getWarehouseStats(),
            'item_stats' => $this->itemModel->getItemStats(),
            'stock_stats' => $this->stockModel->getStockStats(),
            'transfer_stats' => $this->transferModel->getTransferStats(),
            'low_stock_items' => $this->itemModel->getLowStockItems(),
            'recent_transfers' => $this->transferModel->getTransferWithDetails()
        ];
        
        return view('inventory/dashboard', $data);
    }

    // ==================== WAREHOUSE MANAGEMENT ====================
    
    public function warehouses()
    {
        $filters = [
            'type' => $this->request->getGet('type'),
            'status' => $this->request->getGet('status'),
            'city' => $this->request->getGet('city')
        ];

        $data = [
            'title' => 'Warehouse Management',
            'warehouses' => $this->warehouseModel->searchWarehouses('', $filters),
            'warehouse_types' => [
                'head_office' => 'Head Office',
                'factory' => 'Factory',
                'branch' => 'Branch',
                'distribution_center' => 'Distribution Center',
                'retail_store' => 'Retail Store'
            ],
            'filters' => $filters
        ];
        
        return view('inventory/warehouses/index', $data);
    }

    public function warehouseCreate()
    {
        $data = [
            'title' => 'Create New Warehouse',
            'warehouse_types' => [
                'head_office' => 'Head Office',
                'factory' => 'Factory',
                'branch' => 'Branch',
                'distribution_center' => 'Distribution Center',
                'retail_store' => 'Retail Store'
            ],
            'capacity_units' => [
                'sqft' => 'Square Feet',
                'sqm' => 'Square Meters',
                'pallets' => 'Pallets',
                'units' => 'Units',
                'kg' => 'Kilograms',
                'liters' => 'Liters'
            ],
            'employees' => $this->employeeModel->findAll()
        ];
        
        return view('inventory/warehouses/create', $data);
    }

    public function warehouseStore()
    {
        $rules = [
            'warehouse_name' => 'required|min_length[3]',
            'warehouse_type' => 'required',
            'address' => 'required|min_length[10]',
            'city' => 'required',
            'state' => 'required',
            'pincode' => 'required',
            'contact_person' => 'required',
            'phone' => 'required',
            'email' => 'required|valid_email',
            'capacity_total' => 'required|numeric|greater_than[0]',
            'capacity_unit' => 'required',
            'in_charge_id' => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $warehouseData = [
            'warehouse_name' => $this->request->getPost('warehouse_name'),
            'warehouse_type' => $this->request->getPost('warehouse_type'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'pincode' => $this->request->getPost('pincode'),
            'country' => $this->request->getPost('country') ? $this->request->getPost('country') : 'India',
            'contact_person' => $this->request->getPost('contact_person'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'capacity_total' => $this->request->getPost('capacity_total'),
            'capacity_unit' => $this->request->getPost('capacity_unit'),
            'in_charge_id' => $this->request->getPost('in_charge_id'),
            'description' => $this->request->getPost('description'),
            'status' => 'active'
        ];

        if ($this->warehouseModel->insert($warehouseData)) {
            return redirect()->to('/inventory/warehouses')->with('success', 'Warehouse created successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create warehouse.');
        }
    }

    public function warehouseEdit($id)
    {
        $warehouse = $this->warehouseModel->find($id);
        if (!$warehouse) {
            return redirect()->to('/inventory/warehouses')->with('error', 'Warehouse not found.');
        }

        $data = [
            'title' => 'Edit Warehouse',
            'warehouse' => $warehouse,
            'warehouse_types' => [
                'head_office' => 'Head Office',
                'factory' => 'Factory',
                'branch' => 'Branch',
                'distribution_center' => 'Distribution Center',
                'retail_store' => 'Retail Store'
            ],
            'capacity_units' => [
                'sqft' => 'Square Feet',
                'sqm' => 'Square Meters',
                'pallets' => 'Pallets',
                'units' => 'Units',
                'kg' => 'Kilograms',
                'liters' => 'Liters'
            ],
            'employees' => $this->employeeModel->findAll()
        ];
        
        return view('inventory/warehouses/edit', $data);
    }

    public function warehouseUpdate($id)
    {
        $rules = [
            'warehouse_name' => 'required|min_length[3]',
            'warehouse_type' => 'required',
            'address' => 'required|min_length[10]',
            'city' => 'required',
            'state' => 'required',
            'pincode' => 'required',
            'contact_person' => 'required',
            'phone' => 'required',
            'email' => 'required|valid_email',
            'capacity_total' => 'required|numeric|greater_than[0]',
            'capacity_unit' => 'required',
            'in_charge_id' => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $warehouseData = [
            'warehouse_name' => $this->request->getPost('warehouse_name'),
            'warehouse_type' => $this->request->getPost('warehouse_type'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'pincode' => $this->request->getPost('pincode'),
            'country' => $this->request->getPost('country') ? $this->request->getPost('country') : 'India',
            'contact_person' => $this->request->getPost('contact_person'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'capacity_total' => $this->request->getPost('capacity_total'),
            'capacity_unit' => $this->request->getPost('capacity_unit'),
            'in_charge_id' => $this->request->getPost('in_charge_id'),
            'description' => $this->request->getPost('description')
        ];

        if ($this->warehouseModel->update($id, $warehouseData)) {
            return redirect()->to('/inventory/warehouses')->with('success', 'Warehouse updated successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update warehouse.');
        }
    }

    public function warehouseView($id)
    {
        $warehouse = $this->warehouseModel->getWarehouseWithCapacity($id);
        if (!$warehouse) {
            return redirect()->to('/inventory/warehouses')->with('error', 'Warehouse not found.');
        }

        $data = [
            'title' => 'Warehouse Details',
            'warehouse' => $warehouse,
            'stock_items' => $this->stockModel->getStockByWarehouse($id),
            'transfers' => $this->transferModel->getTransfersByWarehouse($id)
        ];
        
        return view('inventory/warehouses/view', $data);
    }

    // ==================== ITEM MASTER ====================
    
    public function items()
    {
        $filters = [
            'type' => $this->request->getGet('type'),
            'category_id' => $this->request->getGet('category_id'),
            'status' => $this->request->getGet('status'),
            'supplier_id' => $this->request->getGet('supplier_id')
        ];

        $data = [
            'title' => 'Item Master',
            'items' => [],
            'categories' => [],
            'suppliers' => [],
            'item_types' => [
                'raw_material' => 'Raw Material',
                'semi_finished' => 'Semi-Finished',
                'finished_goods' => 'Finished Goods',
                'consumables' => 'Consumables',
                'spare_parts' => 'Spare Parts',
                'packaging' => 'Packaging',
                'waste' => 'Waste'
            ],
            'filters' => $filters
        ];

        try {
            $data['items'] = $this->itemModel->searchItems('', $filters);
            $data['categories'] = $this->categoryModel->findAll();
            $data['suppliers'] = $this->supplierModel->where('status', 'active')->findAll();
        } catch (\Throwable $e) {
            log_message('error', 'InventoryManagementController::items: ' . $e->getMessage());
            try {
                $data['items'] = $this->itemModel->orderBy('item_name', 'ASC')->findAll();
            } catch (\Throwable $e2) {
                log_message('error', 'InventoryManagementController::items fallback: ' . $e2->getMessage());
            }
            try {
                $data['categories'] = $this->categoryModel->findAll();
                $data['suppliers'] = $this->supplierModel->where('status', 'active')->findAll();
            } catch (\Throwable $e3) {
                log_message('error', 'InventoryManagementController::items categories/suppliers: ' . $e3->getMessage());
            }
            $data['items_load_error'] = 'Item list used a simplified query. Check items/categories/suppliers schema matches the app (e.g. uom, category_id).';
        }

        $data['categories'] = array_values(array_filter(
            is_array($data['categories'] ?? null) ? $data['categories'] : [],
            static fn ($row) => is_array($row)
        ));
        $data['suppliers'] = array_values(array_filter(
            is_array($data['suppliers'] ?? null) ? $data['suppliers'] : [],
            static fn ($row) => is_array($row)
        ));

        return view('inventory/items/index', $data);
    }

    public function itemCreate()
    {
        $data = [
            'title' => 'Create New Item',
            'categories' => $this->categoryModel->findAll(),
            'suppliers' => $this->supplierModel->where('status', 'active')->findAll(),
            'item_types' => [
                'raw_material' => 'Raw Material',
                'semi_finished' => 'Semi-Finished',
                'finished_goods' => 'Finished Goods',
                'consumables' => 'Consumables',
                'spare_parts' => 'Spare Parts',
                'packaging' => 'Packaging',
                'waste' => 'Waste'
            ],
            'uom_options' => [
                'kg' => 'Kilogram (kg)',
                'g' => 'Gram (g)',
                'l' => 'Liter (l)',
                'ml' => 'Milliliter (ml)',
                'pcs' => 'Pieces (pcs)',
                'm' => 'Meter (m)',
                'cm' => 'Centimeter (cm)',
                'mm' => 'Millimeter (mm)',
                'sqft' => 'Square Feet (sqft)',
                'sqm' => 'Square Meter (sqm)',
                'box' => 'Box',
                'pack' => 'Pack',
                'roll' => 'Roll',
                'set' => 'Set'
            ]
        ];
        
        return view('inventory/items/create', $data);
    }

    public function itemStore()
    {
        $rules = [
            'item_name' => 'required|min_length[3]',
            'material_type' => 'required',
            'category_id' => 'required|integer',
            'uom' => 'required',
            'reorder_level' => 'required|numeric|greater_than_equal_to[0]',
            'safety_stock' => 'required|numeric|greater_than_equal_to[0]',
            'min_stock' => 'required|numeric|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $itemData = [
            'item_name' => $this->request->getPost('item_name'),
            'material_type' => $this->request->getPost('material_type'),
            'category_id' => $this->request->getPost('category_id'),
            'subcategory_id' => $this->request->getPost('subcategory_id') ?: null,
            'description' => $this->request->getPost('description'),
            'uom' => $this->request->getPost('uom'),
            'hsn_code' => $this->request->getPost('hsn_code'),
            'sac_code' => $this->request->getPost('sac_code'),
            'reorder_level' => $this->request->getPost('reorder_level'),
            'safety_stock' => $this->request->getPost('safety_stock'),
            'min_stock' => $this->request->getPost('min_stock'),
            'max_stock' => $this->request->getPost('max_stock') ?: null,
            'standard_cost' => $this->request->getPost('standard_cost') ?: null,
            'selling_price' => $this->request->getPost('selling_price') ?: null,
            'preferred_supplier_id' => $this->request->getPost('preferred_supplier_id') ?: null,
            'barcode' => $this->request->getPost('barcode'),
            'rfid_tag' => $this->request->getPost('rfid_tag'),
            'weight' => $this->request->getPost('weight') ?: null,
            'weight_uom' => $this->request->getPost('weight_uom'),
            'dimensions' => $this->request->getPost('dimensions'),
            'dimension_uom' => $this->request->getPost('dimension_uom'),
            'shelf_life_days' => $this->request->getPost('shelf_life_days') ?: null,
            'storage_conditions' => $this->request->getPost('storage_conditions'),
            'hazardous' => $this->request->getPost('hazardous') ? true : false,
            'status' => 'active'
        ];

        if ($this->itemModel->insert($itemData)) {
            return redirect()->to('/inventory/items')->with('success', 'Item created successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create item.');
        }
    }

    public function itemEdit($id)
    {
        $item = $this->itemModel->find($id);
        if (!$item) {
            return redirect()->to('/inventory/items')->with('error', 'Item not found.');
        }

        $data = [
            'title' => 'Edit Item',
            'item' => $item,
            'categories' => $this->categoryModel->findAll(),
            'suppliers' => $this->supplierModel->where('status', 'active')->findAll(),
            'item_types' => [
                'raw_material' => 'Raw Material',
                'semi_finished' => 'Semi-Finished',
                'finished_goods' => 'Finished Goods',
                'consumables' => 'Consumables',
                'spare_parts' => 'Spare Parts',
                'packaging' => 'Packaging',
                'waste' => 'Waste'
            ],
            'uom_options' => [
                'kg' => 'Kilogram (kg)',
                'g' => 'Gram (g)',
                'l' => 'Liter (l)',
                'ml' => 'Milliliter (ml)',
                'pcs' => 'Pieces (pcs)',
                'm' => 'Meter (m)',
                'cm' => 'Centimeter (cm)',
                'mm' => 'Millimeter (mm)',
                'sqft' => 'Square Feet (sqft)',
                'sqm' => 'Square Meter (sqm)',
                'box' => 'Box',
                'pack' => 'Pack',
                'roll' => 'Roll',
                'set' => 'Set'
            ]
        ];
        
        return view('inventory/items/edit', $data);
    }

    public function itemUpdate($id)
    {
        $rules = [
            'item_name' => 'required|min_length[3]',
            'material_type' => 'required',
            'category_id' => 'required|integer',
            'uom' => 'required',
            'reorder_level' => 'required|numeric|greater_than_equal_to[0]',
            'safety_stock' => 'required|numeric|greater_than_equal_to[0]',
            'min_stock' => 'required|numeric|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $itemData = [
            'item_name' => $this->request->getPost('item_name'),
            'material_type' => $this->request->getPost('material_type'),
            'category_id' => $this->request->getPost('category_id'),
            'subcategory_id' => $this->request->getPost('subcategory_id') ?: null,
            'description' => $this->request->getPost('description'),
            'uom' => $this->request->getPost('uom'),
            'hsn_code' => $this->request->getPost('hsn_code'),
            'sac_code' => $this->request->getPost('sac_code'),
            'reorder_level' => $this->request->getPost('reorder_level'),
            'safety_stock' => $this->request->getPost('safety_stock'),
            'min_stock' => $this->request->getPost('min_stock'),
            'max_stock' => $this->request->getPost('max_stock') ?: null,
            'standard_cost' => $this->request->getPost('standard_cost') ?: null,
            'selling_price' => $this->request->getPost('selling_price') ?: null,
            'preferred_supplier_id' => $this->request->getPost('preferred_supplier_id') ?: null,
            'barcode' => $this->request->getPost('barcode'),
            'rfid_tag' => $this->request->getPost('rfid_tag'),
            'weight' => $this->request->getPost('weight') ?: null,
            'weight_uom' => $this->request->getPost('weight_uom'),
            'dimensions' => $this->request->getPost('dimensions'),
            'dimension_uom' => $this->request->getPost('dimension_uom'),
            'shelf_life_days' => $this->request->getPost('shelf_life_days') ?: null,
            'storage_conditions' => $this->request->getPost('storage_conditions'),
            'hazardous' => $this->request->getPost('hazardous') ? true : false
        ];

        if ($this->itemModel->update($id, $itemData)) {
            return redirect()->to('/inventory/items')->with('success', 'Item updated successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update item.');
        }
    }

    public function itemView($id)
    {
        $item = $this->itemModel->getItemWithDetails($id);
        if (!$item) {
            return redirect()->to('/inventory/items')->with('error', 'Item not found.');
        }

        $stockByWarehouse = [];
        $transferHistory = [];
        try {
            if (method_exists($this->stockModel, 'getItemStockByWarehouse')) {
                $stockByWarehouse = $this->stockModel->getItemStockByWarehouse($id);
            }
        } catch (\Throwable $e) {
            log_message('error', 'itemView stock history: ' . $e->getMessage());
        }
        try {
            if (method_exists($this->transferModel, 'getItemTransferHistory')) {
                $transferHistory = $this->transferModel->getItemTransferHistory($id);
            }
        } catch (\Throwable $e) {
            log_message('error', 'itemView transfer history: ' . $e->getMessage());
        }

        $data = [
            'title' => 'Item Details',
            'item' => $item,
            'stock_by_warehouse' => $stockByWarehouse,
            'transfer_history' => $transferHistory
        ];
        
        return view('inventory/items/view', $data);
    }

    // ==================== STOCK MANAGEMENT ====================
    
    public function stock()
    {
        $filters = [
            'item_id' => $this->request->getGet('item_id'),
            'warehouse_id' => $this->request->getGet('warehouse_id'),
            'status' => $this->request->getGet('status'),
            'transaction_type' => $this->request->getGet('transaction_type')
        ];

        $data = [
            'title' => 'Stock Management',
            'stock_movements' => $this->stockModel->getStockMovements($filters),
            'items' => $this->itemModel->getActiveItems(),
            'warehouses' => $this->warehouseModel->getActiveWarehouses(),
            'filters' => $filters
        ];
        
        return view('inventory/stock/index', $data);
    }

    public function stockIn()
    {
        $itemId = $this->request->getGet('item_id');
        $warehouseId = $this->request->getGet('warehouse_id');
        $selectedItem = null;
        $selectedWarehouse = null;
        
        if ($itemId) {
            $selectedItem = $this->itemModel->find($itemId);
        }
        
        if ($warehouseId) {
            $selectedWarehouse = $this->warehouseModel->find($warehouseId);
        }
        
        $data = [
            'title' => 'Stock In',
            'items' => $this->itemModel->getActiveItems(),
            'warehouses' => $this->warehouseModel->getActiveWarehouses(),
            'selected_item_id' => $itemId,
            'selected_item' => $selectedItem,
            'selected_warehouse_id' => $warehouseId,
            'selected_warehouse' => $selectedWarehouse,
            'source_types' => [
                'grn' => 'Goods Receipt Note (GRN)',
                'production' => 'Production Output',
                'transfer_in' => 'Stock Transfer In',
                'return' => 'Returns',
                'adjustment' => 'Stock Adjustment'
            ]
        ];
        
        return view('inventory/stock/stock_in', $data);
    }

    public function stockInStore()
    {
        $rules = [
            'item_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'quantity' => 'required|numeric|greater_than[0]',
            'source_type' => 'required',
            'source_document' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $quantity = floatval($this->request->getPost('quantity'));
        $unitCost = floatval($this->request->getPost('unit_cost') ?: 0);
        $totalCost = $quantity * $unitCost;

        // Map source_type to database enum values
        $sourceTypeMap = [
            'grn' => 'purchase',
            'production' => 'production',
            'transfer_in' => 'transfer',
            'return' => 'return',
            'adjustment' => 'adjustment'
        ];
        $sourceType = $this->request->getPost('source_type');
        $dbSourceType = $sourceTypeMap[$sourceType] ?? 'purchase';

        $stockData = [
            'item_id' => intval($this->request->getPost('item_id')),
            'warehouse_id' => intval($this->request->getPost('warehouse_id')),
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => $totalCost,
            'status' => 'available',
            'transaction_type' => 'in', // Database expects 'in', not 'stock_in'
            'transaction_date' => date('Y-m-d'), // DATE field, not DATETIME
            'source_document' => $this->request->getPost('source_document'),
            'source_type' => $dbSourceType,
            'batch_number' => $this->request->getPost('batch_number') ?: null,
            'expiry_date' => $this->request->getPost('expiry_date') ?: null,
            'manufacturing_date' => $this->request->getPost('manufacturing_date') ?: null,
            'location' => $this->request->getPost('location') ?: null,
            'rack' => $this->request->getPost('rack') ?: null,
            'bin' => $this->request->getPost('bin') ?: null,
            'notes' => $this->request->getPost('notes') ?: null
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert stock record into database
            $insertResult = $db->table('stock')->insert($stockData);
            
            if (!$insertResult) {
                $error = $db->error();
                log_message('error', 'Stock In - Failed to insert stock: ' . json_encode($error));
                throw new \Exception('Failed to add stock: ' . ($error['message'] ?? 'Unknown database error'));
            }
            
            // Get the inserted record ID
            $insertId = $db->insertID();
            log_message('info', 'Stock In - Record inserted with ID: ' . $insertId);

            $db->transComplete();

            if ($db->transStatus() === false) {
                $error = $db->error();
                log_message('error', 'Stock In - Transaction failed: ' . json_encode($error));
                return redirect()->back()->withInput()->with('error', 'Failed to add stock: ' . ($error['message'] ?? 'Transaction failed'));
            }

            // Determine redirect URL based on current route
            $redirectUrl = '/stock';
            if (strpos($this->request->getUri()->getPath(), '/inventory/') !== false) {
                $redirectUrl = '/inventory/stock';
            }

            log_message('info', 'Stock In - Stock added successfully: Record ID ' . $insertId . ', Item ID ' . $stockData['item_id'] . ', Quantity: ' . $quantity . ', Warehouse ID: ' . $stockData['warehouse_id']);
            return redirect()->to($redirectUrl)->with('success', 'Stock added successfully! Stock ID: ' . $insertId);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Stock In - Exception: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error adding stock: ' . $e->getMessage());
        }
    }

    public function stockEdit($id)
    {
        $stock = $this->stockModel->find($id);
        
        if (!$stock) {
            return redirect()->to(base_url('stock'))->with('error', 'Stock record not found.');
        }
        
        // Get item and warehouse details
        $selectedItem = $this->itemModel->find($stock['item_id']);
        $selectedWarehouse = $this->warehouseModel->find($stock['warehouse_id']);
        
        // Map database source_type back to form values
        $sourceTypeReverseMap = [
            'purchase' => 'grn',
            'production' => 'production',
            'transfer' => 'transfer_in',
            'return' => 'return',
            'adjustment' => 'adjustment'
        ];
        $formSourceType = $sourceTypeReverseMap[$stock['source_type']] ?? 'grn';
        
        $data = [
            'title' => 'Edit Stock',
            'stock' => $stock,
            'items' => $this->itemModel->getActiveItems(),
            'warehouses' => $this->warehouseModel->getActiveWarehouses(),
            'selected_item_id' => $stock['item_id'],
            'selected_item' => $selectedItem,
            'selected_warehouse_id' => $stock['warehouse_id'],
            'selected_warehouse' => $selectedWarehouse,
            'form_source_type' => $formSourceType,
            'is_edit' => true,
            'source_types' => [
                'grn' => 'Goods Receipt Note (GRN)',
                'production' => 'Production Output',
                'transfer_in' => 'Stock Transfer In',
                'return' => 'Returns',
                'adjustment' => 'Stock Adjustment'
            ]
        ];
        
        return view('inventory/stock/stock_in', $data);
    }

    public function stockUpdate($id)
    {
        $stock = $this->stockModel->find($id);
        
        if (!$stock) {
            return redirect()->to(base_url('stock'))->with('error', 'Stock record not found.');
        }

        $rules = [
            'item_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'quantity' => 'required|numeric|greater_than[0]',
            'source_type' => 'required',
            'source_document' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $quantity = floatval($this->request->getPost('quantity'));
        $unitCost = floatval($this->request->getPost('unit_cost') ?: 0);
        $totalCost = $quantity * $unitCost;

        // Map source_type to database enum values
        $sourceTypeMap = [
            'grn' => 'purchase',
            'production' => 'production',
            'transfer_in' => 'transfer',
            'return' => 'return',
            'adjustment' => 'adjustment'
        ];
        $sourceType = $this->request->getPost('source_type');
        $dbSourceType = $sourceTypeMap[$sourceType] ?? 'purchase';

        $stockData = [
            'item_id' => intval($this->request->getPost('item_id')),
            'warehouse_id' => intval($this->request->getPost('warehouse_id')),
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => $totalCost,
            'status' => $this->request->getPost('status') ?: 'available',
            'transaction_type' => $stock['transaction_type'], // Keep original transaction type
            'transaction_date' => $this->request->getPost('transaction_date') ?: date('Y-m-d'),
            'source_document' => $this->request->getPost('source_document'),
            'source_type' => $dbSourceType,
            'batch_number' => $this->request->getPost('batch_number') ?: null,
            'expiry_date' => $this->request->getPost('expiry_date') ?: null,
            'manufacturing_date' => $this->request->getPost('manufacturing_date') ?: null,
            'location' => $this->request->getPost('location') ?: null,
            'rack' => $this->request->getPost('rack') ?: null,
            'bin' => $this->request->getPost('bin') ?: null,
            'notes' => $this->request->getPost('notes') ?: null
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update stock record
            if (!$db->table('stock')->where('id', $id)->update($stockData)) {
                $error = $db->error();
                log_message('error', 'Stock Update - Failed to update stock: ' . json_encode($error));
                throw new \Exception('Failed to update stock: ' . ($error['message'] ?? 'Unknown database error'));
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                $error = $db->error();
                log_message('error', 'Stock Update - Transaction failed: ' . json_encode($error));
                return redirect()->back()->withInput()->with('error', 'Failed to update stock: ' . ($error['message'] ?? 'Transaction failed'));
            }

            // Determine redirect URL
            $redirectUrl = base_url('stock');
            if (strpos($this->request->getUri()->getPath(), '/inventory/') !== false) {
                $redirectUrl = base_url('inventory/stock');
            }

            log_message('info', 'Stock Update - Stock updated successfully: ID ' . $id . ', Item ID ' . $stockData['item_id'] . ', Quantity: ' . $quantity);
            return redirect()->to($redirectUrl)->with('success', 'Stock updated successfully!');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Stock Update - Exception: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error updating stock: ' . $e->getMessage());
        }
    }

    public function stockOut()
    {
        $itemId = $this->request->getGet('item_id');
        $warehouseId = $this->request->getGet('warehouse_id');
        $selectedItem = null;
        $selectedWarehouse = null;
        $availableStock = 0;
        
        if ($itemId) {
            $selectedItem = $this->itemModel->find($itemId);
        }
        
        if ($warehouseId) {
            $selectedWarehouse = $this->warehouseModel->find($warehouseId);
        }
        
        // Get available stock if both item and warehouse are selected
        if ($itemId && $warehouseId) {
            $stockRecords = $this->stockModel->where('item_id', $itemId)
                                              ->where('warehouse_id', $warehouseId)
                                              ->where('status', 'available')
                                              ->findAll();
            foreach ($stockRecords as $stock) {
                $availableStock += floatval($stock['quantity'] ?? 0);
            }
        }
        
        $data = [
            'title' => 'Stock Out',
            'items' => $this->itemModel->getActiveItems(),
            'warehouses' => $this->warehouseModel->getActiveWarehouses(),
            'selected_item_id' => $itemId,
            'selected_item' => $selectedItem,
            'selected_warehouse_id' => $warehouseId,
            'selected_warehouse' => $selectedWarehouse,
            'available_stock' => $availableStock,
            'source_types' => [
                'sales' => 'Sales Dispatch',
                'production' => 'Production Consumption',
                'transfer_out' => 'Stock Transfer Out',
                'scrap' => 'Scrap/Wastage',
                'adjustment' => 'Stock Adjustment'
            ]
        ];
        
        return view('inventory/stock/stock_out', $data);
    }

    public function stockOutStore()
    {
        $rules = [
            'item_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'quantity' => 'required|numeric|greater_than[0]',
            'source_type' => 'required',
            'source_document' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $itemId = intval($this->request->getPost('item_id'));
        $warehouseId = intval($this->request->getPost('warehouse_id'));
        $quantity = floatval($this->request->getPost('quantity'));
        
        // Check available stock
        $stockRecords = $this->stockModel->where('item_id', $itemId)
                                          ->where('warehouse_id', $warehouseId)
                                          ->where('status', 'available')
                                          ->orderBy('transaction_date', 'ASC') // FIFO
                                          ->findAll();
        
        $availableStock = 0;
        foreach ($stockRecords as $stock) {
            $availableStock += floatval($stock['quantity'] ?? 0);
        }
        
        if ($availableStock < $quantity) {
            return redirect()->back()->withInput()->with('error', "Insufficient stock available. Available: {$availableStock}, Requested: {$quantity}");
        }

        // Map source_type to database enum values
        $sourceTypeMap = [
            'sales' => 'sales',
            'production' => 'production',
            'transfer_out' => 'transfer',
            'scrap' => 'adjustment',
            'adjustment' => 'adjustment'
        ];
        $sourceType = $this->request->getPost('source_type');
        $dbSourceType = $sourceTypeMap[$sourceType] ?? 'adjustment';

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $remainingQuantity = $quantity;
        $sourceDocument = $this->request->getPost('source_document');
            $notes = $this->request->getPost('notes') ?: null;
            
            // Process stock out using FIFO method
            // Only update existing stock records (don't insert new negative records to avoid double deduction)
            foreach ($stockRecords as $stock) {
                if ($remainingQuantity <= 0) break;
                
                $stockQuantity = floatval($stock['quantity'] ?? 0);
                if ($stockQuantity <= 0) continue; // Skip records with zero or negative quantity
                
                $deductQuantity = min($remainingQuantity, $stockQuantity);
                
                // Update existing stock record by reducing quantity
                $newQuantity = $stockQuantity - $deductQuantity;
                
                if ($newQuantity > 0) {
                    // Update the stock record with reduced quantity
                    $updateData = [
                        'quantity' => $newQuantity,
                        'total_cost' => $newQuantity * ($stock['unit_cost'] ?? 0)
                    ];
                    if (!$db->table('stock')->where('id', $stock['id'])->update($updateData)) {
                        $error = $db->error();
                        throw new \Exception('Failed to update stock: ' . ($error['message'] ?? 'Unknown error'));
                    }
        } else {
                    // Delete the stock record if quantity becomes zero
                    if (!$db->table('stock')->where('id', $stock['id'])->delete()) {
                        $error = $db->error();
                        throw new \Exception('Failed to delete stock record: ' . ($error['message'] ?? 'Unknown error'));
                    }
                }
                
                // Insert stock out transaction record for audit trail
                $stockOutData = [
                    'item_id' => $itemId,
                    'warehouse_id' => $warehouseId,
                    'quantity' => $deductQuantity, // Positive quantity for the transaction record
                    'unit_cost' => $stock['unit_cost'] ?? 0,
                    'total_cost' => $deductQuantity * ($stock['unit_cost'] ?? 0),
                    'status' => 'available',
                    'transaction_type' => 'out', // Database expects 'out'
                    'transaction_date' => date('Y-m-d'),
                    'source_document' => $sourceDocument,
                    'source_type' => $dbSourceType,
                    'batch_number' => $stock['batch_number'] ?? null,
                    'location' => $stock['location'] ?? null,
                    'rack' => $stock['rack'] ?? null,
                    'bin' => $stock['bin'] ?? null,
                    'notes' => $notes
                ];
                
                // Insert stock out transaction record (for audit/history)
                if (!$db->table('stock')->insert($stockOutData)) {
                    $error = $db->error();
                    throw new \Exception('Failed to record stock out transaction: ' . ($error['message'] ?? 'Unknown error'));
                }
                
                $remainingQuantity -= $deductQuantity;
            }
            
            if ($remainingQuantity > 0) {
                throw new \Exception("Insufficient stock. Could only deduct " . ($quantity - $remainingQuantity) . " out of " . $quantity);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                $error = $db->error();
                log_message('error', 'Stock Out - Transaction failed: ' . json_encode($error));
                return redirect()->back()->withInput()->with('error', 'Failed to remove stock: ' . ($error['message'] ?? 'Transaction failed'));
            }

            // Determine redirect URL
            $redirectUrl = '/stock';
            if (strpos($this->request->getUri()->getPath(), '/inventory/') !== false) {
                $redirectUrl = '/inventory/stock';
            }

            log_message('info', 'Stock Out - Stock removed successfully: Item ID ' . $itemId . ', Quantity: ' . $quantity);
            return redirect()->to($redirectUrl)->with('success', 'Stock removed successfully!');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Stock Out - Exception: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error removing stock: ' . $e->getMessage());
        }
    }

    /**
     * Quick Stock In - Add 1 unit directly without form
     */
    public function quickStockIn()
    {
        $itemId = $this->request->getGet('item_id');
        $warehouseId = $this->request->getGet('warehouse_id');

        if (!$itemId || !$warehouseId) {
            return redirect()->to('/stock')->with('error', 'Item ID and Warehouse ID are required.');
        }

        // Get item details for unit cost
        $item = $this->itemModel->find($itemId);
        if (!$item) {
            return redirect()->to('/stock')->with('error', 'Item not found.');
        }

        $unitCost = isset($item['cost_price']) ? floatval($item['cost_price']) : 0;
        $quantity = 1; // Always add 1 unit
        $totalCost = $quantity * $unitCost;

        $stockData = [
            'item_id' => intval($itemId),
            'warehouse_id' => intval($warehouseId),
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => $totalCost,
            'status' => 'available',
            'transaction_type' => 'in',
            'transaction_date' => date('Y-m-d'),
            'source_document' => 'QUICK-STOCK-IN-' . date('YmdHis'),
            'source_type' => 'adjustment',
            'notes' => 'Quick stock in - Added 1 unit'
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Check if stock record exists for this item+warehouse combination
            $existingStock = $db->table('stock')
                ->where('item_id', $itemId)
                ->where('warehouse_id', $warehouseId)
                ->where('transaction_type', 'in')
                ->where('status', 'available')
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();
            
            if ($existingStock) {
                // Update existing stock record - add 1 to quantity
                $newQuantity = floatval($existingStock['quantity']) + $quantity;
                $newTotalCost = $newQuantity * $unitCost;
                
                $updateData = [
                    'quantity' => $newQuantity,
                    'total_cost' => $newTotalCost,
                    'unit_cost' => $unitCost, // Update unit cost if changed
                    'transaction_date' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $updateResult = $db->table('stock')
                    ->where('id', $existingStock['id'])
                    ->update($updateData);
                
                if (!$updateResult) {
                    $error = $db->error();
                    log_message('error', 'Quick Stock In - Failed to update: ' . json_encode($error));
                    throw new \Exception('Failed to update stock: ' . ($error['message'] ?? 'Unknown error'));
                }
                
                $db->transComplete();
                
                if ($db->transStatus() === false) {
                    $error = $db->error();
                    log_message('error', 'Quick Stock In - Transaction failed: ' . json_encode($error));
                    return redirect()->to('/stock')->with('error', 'Failed to update stock.');
                }
                
                log_message('info', 'Quick Stock In - Updated stock: Record ID ' . $existingStock['id'] . ', Item ID ' . $itemId . ', Warehouse ID ' . $warehouseId . ', New Quantity: ' . $newQuantity);
                return redirect()->to('/stock')->with('success', 'Stock increased by 1 unit successfully! Current stock: ' . $newQuantity);
            } else {
                // No existing record, insert new one
                $insertResult = $db->table('stock')->insert($stockData);
                
                if (!$insertResult) {
                    $error = $db->error();
                    log_message('error', 'Quick Stock In - Failed to insert: ' . json_encode($error));
                    throw new \Exception('Failed to add stock: ' . ($error['message'] ?? 'Unknown error'));
                }
                
                $insertId = $db->insertID();
                $db->transComplete();
                
                if ($db->transStatus() === false) {
                    $error = $db->error();
                    log_message('error', 'Quick Stock In - Transaction failed: ' . json_encode($error));
                    return redirect()->to('/stock')->with('error', 'Failed to add stock.');
                }
                
                log_message('info', 'Quick Stock In - Created new stock: Record ID ' . $insertId . ', Item ID ' . $itemId . ', Warehouse ID ' . $warehouseId);
                return redirect()->to('/stock')->with('success', 'Stock increased by 1 unit successfully!');
            }

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Quick Stock In - Exception: ' . $e->getMessage());
            return redirect()->to('/stock')->with('error', 'Error adding stock: ' . $e->getMessage());
        }
    }

    /**
     * Quick Stock Out - Remove 1 unit directly without form
     */
    public function quickStockOut()
    {
        $itemId = $this->request->getGet('item_id');
        $warehouseId = $this->request->getGet('warehouse_id');

        if (!$itemId || !$warehouseId) {
            return redirect()->to('/stock')->with('error', 'Item ID and Warehouse ID are required.');
        }

        // Check available stock
        $stockRecords = $this->stockModel->where('item_id', $itemId)
                                          ->where('warehouse_id', $warehouseId)
                                          ->where('status', 'available')
                                          ->where('transaction_type !=', 'out')
                                          ->orderBy('transaction_date', 'ASC')
                                          ->findAll();
        
        $availableStock = 0;
        foreach ($stockRecords as $stock) {
            $availableStock += floatval($stock['quantity'] ?? 0);
        }
        
        if ($availableStock < 1) {
            return redirect()->to('/stock')->with('error', 'Insufficient stock. Available: ' . $availableStock);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $remainingQuantity = 1; // Remove 1 unit
            
            // Process stock out using FIFO method
            foreach ($stockRecords as $stock) {
                if ($remainingQuantity <= 0) break;
                
                $stockQuantity = floatval($stock['quantity'] ?? 0);
                if ($stockQuantity <= 0) continue;
                
                $deductQuantity = min($remainingQuantity, $stockQuantity);
                
                // Update existing stock record
                $newQuantity = $stockQuantity - $deductQuantity;
                
                if ($newQuantity > 0) {
                    $updateData = [
                        'quantity' => $newQuantity,
                        'total_cost' => $newQuantity * ($stock['unit_cost'] ?? 0)
                    ];
                    if (!$db->table('stock')->where('id', $stock['id'])->update($updateData)) {
                        throw new \Exception('Failed to update stock.');
                    }
                } else {
                    if (!$db->table('stock')->where('id', $stock['id'])->delete()) {
                        throw new \Exception('Failed to delete stock record.');
                    }
                }
                
                // Insert stock out transaction record
                $stockOutData = [
                    'item_id' => $itemId,
                    'warehouse_id' => $warehouseId,
                    'quantity' => $deductQuantity,
                    'unit_cost' => $stock['unit_cost'] ?? 0,
                    'total_cost' => $deductQuantity * ($stock['unit_cost'] ?? 0),
                    'status' => 'available',
                    'transaction_type' => 'out',
                    'transaction_date' => date('Y-m-d'),
                    'source_document' => 'QUICK-STOCK-OUT-' . date('YmdHis'),
                    'source_type' => 'adjustment',
                    'notes' => 'Quick stock out - Removed 1 unit'
                ];
                
                if (!$db->table('stock')->insert($stockOutData)) {
                    throw new \Exception('Failed to record stock out transaction.');
                }
                
                $remainingQuantity -= $deductQuantity;
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                $error = $db->error();
                log_message('error', 'Quick Stock Out - Transaction failed: ' . json_encode($error));
                return redirect()->to('/stock')->with('error', 'Failed to remove stock.');
            }

            log_message('info', 'Quick Stock Out - Removed 1 unit: Item ID ' . $itemId . ', Warehouse ID ' . $warehouseId);
            return redirect()->to('/stock')->with('success', 'Stock decreased by 1 unit successfully!');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Quick Stock Out - Exception: ' . $e->getMessage());
            return redirect()->to('/stock')->with('error', 'Error removing stock: ' . $e->getMessage());
        }
    }

    // ==================== STOCK TRANSFERS ====================
    
    public function transfers()
    {
        $filters = [
            'status' => $this->request->getGet('status'),
            'source_warehouse' => $this->request->getGet('source_warehouse'),
            'destination_warehouse' => $this->request->getGet('destination_warehouse')
        ];

        $data = [
            'title' => 'Stock Transfers',
            'transfers' => $this->transferModel->searchTransfers('', $filters),
            'warehouses' => $this->warehouseModel->getActiveWarehouses(),
            'filters' => $filters
        ];
        
        return view('inventory/transfers/index', $data);
    }

    public function transferCreate()
    {
        $data = [
            'title' => 'Create Stock Transfer',
            'warehouses' => $this->warehouseModel->getActiveWarehouses(),
            'items' => $this->itemModel->getActiveItems(),
            'employees' => $this->employeeModel->findAll(),
            'priorities' => [
                'low' => 'Low',
                'normal' => 'Normal',
                'high' => 'High',
                'urgent' => 'Urgent'
            ],
            'transport_modes' => [
                'road' => 'Road',
                'rail' => 'Rail',
                'air' => 'Air',
                'sea' => 'Sea',
                'internal' => 'Internal'
            ]
        ];
        
        return view('inventory/transfers/create', $data);
    }

    public function transferStore()
    {
        $rules = [
            'from_warehouse_id' => 'required|integer',
            'to_warehouse_id' => 'required|integer',
            'transfer_date' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $transferData = [
            'transfer_date' => $this->request->getPost('transfer_date'),
            'from_warehouse_id' => $this->request->getPost('from_warehouse_id') ?: $this->request->getPost('source_warehouse_id'),
            'to_warehouse_id' => $this->request->getPost('to_warehouse_id') ?: $this->request->getPost('destination_warehouse_id'),
            'notes' => $this->request->getPost('notes') ?: null,
            'status' => 'pending',
            'created_by' => session()->get('user_id') ?? 1
        ];

        $transferId = $this->transferModel->insert($transferData);
        if ($transferId) {
            return redirect()->to(base_url("inventory/transfers/edit/{$transferId}"))->with('success', 'Transfer created! Add items to complete.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create transfer.');
        }
    }

    public function transferEdit($id)
    {
        $transfer = $this->transferModel->getTransferWithDetails($id);
        if (!$transfer) {
            return redirect()->to('/inventory/transfers')->with('error', 'Transfer not found.');
        }

        $data = [
            'title' => 'Edit Stock Transfer',
            'transfer' => $transfer,
            'transfer_items' => $this->transferModel->getTransferItems($id),
            'warehouses' => $this->warehouseModel->getActiveWarehouses(),
            'items' => $this->itemModel->getActiveItems(),
            'employees' => $this->employeeModel->findAll(),
            'priorities' => [
                'low' => 'Low',
                'normal' => 'Normal',
                'high' => 'High',
                'urgent' => 'Urgent'
            ],
            'transport_modes' => [
                'road' => 'Road',
                'rail' => 'Rail',
                'air' => 'Air',
                'sea' => 'Sea',
                'internal' => 'Internal'
            ]
        ];
        
        return view('inventory/transfers/edit', $data);
    }

    public function transferUpdate($id)
    {
        $rules = [
            'from_warehouse_id' => 'required|integer',
            'to_warehouse_id' => 'required|integer',
            'transfer_date' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $transferData = [
            'transfer_date' => $this->request->getPost('transfer_date'),
            'from_warehouse_id' => $this->request->getPost('from_warehouse_id') ?: $this->request->getPost('source_warehouse_id'),
            'to_warehouse_id' => $this->request->getPost('to_warehouse_id') ?: $this->request->getPost('destination_warehouse_id'),
            'notes' => $this->request->getPost('notes') ?: null
        ];

        if ($this->transferModel->update($id, $transferData)) {
            return redirect()->to('/inventory/transfers')->with('success', 'Transfer updated successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update transfer.');
        }
    }

    public function transferView($id)
    {
        $transfer = $this->transferModel->getTransferWithDetails($id);
        if (!$transfer) {
            return redirect()->to('/inventory/transfers')->with('error', 'Transfer not found.');
        }

        $data = [
            'title' => 'Transfer Details',
            'transfer' => $transfer,
            'transfer_items' => $this->transferModel->getTransferItems($id)
        ];
        
        return view('inventory/transfers/view', $data);
    }

    public function transferApprove($id)
    {
        $approvedBy = session()->get('user_id') ? session()->get('user_id') : 1; // Default to user ID 1 if not in session
        
        if ($this->transferModel->approveTransfer($id, $approvedBy)) {
            return redirect()->to('/inventory/transfers')->with('success', 'Transfer approved successfully!');
        } else {
            return redirect()->to('/inventory/transfers')->with('error', 'Failed to approve transfer. Check item availability.');
        }
    }

    public function transferStart($id)
    {
        if ($this->transferModel->startTransfer($id)) {
            return redirect()->to('/inventory/transfers')->with('success', 'Transfer started successfully!');
        } else {
            return redirect()->to('/inventory/transfers')->with('error', 'Failed to start transfer.');
        }
    }

    public function transferComplete($id)
    {
        if ($this->transferModel->completeTransfer($id)) {
            return redirect()->to('/inventory/transfers')->with('success', 'Transfer completed successfully!');
        } else {
            return redirect()->to('/inventory/transfers')->with('error', 'Failed to complete transfer.');
        }
    }

    // ==================== STOCK ADJUSTMENTS ====================
    
    public function adjustments()
    {
        $filters = [
            'status' => $this->request->getGet('status'),
            'warehouse_id' => $this->request->getGet('warehouse_id'),
            'adjustment_type' => $this->request->getGet('adjustment_type'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        // Get adjustments from stock table where transaction_type = 'adjustment'
        $adjustments = $this->stockModel->where('transaction_type', 'adjustment')
            ->orderBy('transaction_date', 'DESC')
            ->findAll();

        // Get additional details for each adjustment
        $adjustmentDetails = [];
        foreach ($adjustments as $adj) {
            $item = $this->itemModel->find($adj['item_id']);
            $warehouse = $this->warehouseModel->find($adj['warehouse_id']);
            $adjustmentDetails[] = [
                'id' => $adj['id'],
                'reference_number' => $adj['reference_number'] ?? 'ADJ-' . str_pad($adj['id'], 6, '0', STR_PAD_LEFT),
                'item_name' => $item['item_name'] ?? 'Unknown',
                'warehouse_name' => $warehouse['warehouse_name'] ?? 'Unknown',
                'quantity' => $adj['quantity'],
                'unit_cost' => $adj['unit_cost'] ?? 0,
                'adjustment_type' => $adj['source_type'] ?? 'adjustment',
                'reason' => $adj['notes'] ?? '',
                'status' => $adj['status'] ?? 'pending',
                'transaction_date' => $adj['transaction_date'] ?? $adj['created_at'],
                'created_by' => $adj['created_by'] ?? null
            ];
        }

        $data = [
            'title' => 'Stock Adjustments',
            'adjustments' => $adjustmentDetails,
            'warehouses' => $this->warehouseModel->getActiveWarehouses(),
            'filters' => $filters,
            'stats' => $this->getAdjustmentStats()
        ];
        
        return view('inventory/adjustments/index', $data);
    }

    public function adjustmentCreate()
    {
        $data = [
            'title' => 'Create Stock Adjustment',
            'items' => $this->itemModel->getActiveItems(),
            'warehouses' => $this->warehouseModel->getActiveWarehouses(),
            'adjustment_types' => [
                'increase' => 'Increase Stock',
                'decrease' => 'Decrease Stock',
                'correction' => 'Stock Correction',
                'damage' => 'Damage/Wastage',
                'expiry' => 'Expiry Write-off',
                'count' => 'Physical Count Adjustment'
            ],
            'reasons' => [
                'damage' => 'Damaged Goods',
                'expiry' => 'Expired Goods',
                'theft' => 'Theft/Loss',
                'count_error' => 'Counting Error',
                'location_change' => 'Location Change',
                'quality_rejection' => 'Quality Rejection',
                'other' => 'Other'
            ],
            'reference_number' => 'ADJ-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)
        ];
        
        return view('inventory/adjustments/create', $data);
    }

    public function adjustmentStore()
    {
        $rules = [
            'item_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'adjustment_type' => 'required|in_list[increase,decrease,correction,damage,expiry,count]',
            'quantity' => 'required|numeric|greater_than[0]',
            'reason' => 'required',
            'adjustment_date' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $itemId = $this->request->getPost('item_id');
        $warehouseId = $this->request->getPost('warehouse_id');
        $adjustmentType = $this->request->getPost('adjustment_type');
        $quantity = abs($this->request->getPost('quantity'));
        $reason = $this->request->getPost('reason');
        $unitCost = $this->request->getPost('unit_cost') ?: 0;

        // For decrease adjustments, check if sufficient stock is available
        if (in_array($adjustmentType, ['decrease', 'damage', 'expiry'])) {
            $currentStock = $this->stockModel->getItemStock($itemId, $warehouseId);
            if ($currentStock['available_stock'] < $quantity) {
                return redirect()->back()->withInput()->with('error', 'Insufficient stock. Available: ' . $currentStock['available_stock']);
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create adjustment record
            $adjustmentData = [
                'item_id' => $itemId,
                'warehouse_id' => $warehouseId,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $quantity * $unitCost,
                'status' => 'pending',
                'transaction_type' => 'adjustment',
                'source_type' => $adjustmentType,
                'source_document' => $this->request->getPost('reference_number'),
                'reference_number' => $this->request->getPost('reference_number'),
                'transaction_date' => $this->request->getPost('adjustment_date'),
                'notes' => $reason . ' - ' . ($this->request->getPost('notes') ?? ''),
                'created_by' => session()->get('user_id') ?? 1
            ];

            $adjustmentId = $this->stockModel->insert($adjustmentData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Failed to create stock adjustment.');
            }

            return redirect()->to('/inventory/adjustments')->with('success', 'Stock adjustment created successfully. Please approve to apply changes.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error creating adjustment: ' . $e->getMessage());
        }
    }

    public function adjustmentEdit($id)
    {
        $adjustment = $this->stockModel->where('id', $id)
            ->where('transaction_type', 'adjustment')
            ->first();

        if (!$adjustment) {
            return redirect()->to('/inventory/adjustments')->with('error', 'Adjustment not found.');
        }

        // Check if adjustment can be edited (only pending adjustments)
        if ($adjustment['status'] !== 'pending') {
            return redirect()->to('/inventory/adjustments')->with('error', 'Only pending adjustments can be edited.');
        }

        $data = [
            'title' => 'Edit Stock Adjustment',
            'adjustment' => $adjustment,
            'items' => $this->itemModel->getActiveItems(),
            'warehouses' => $this->warehouseModel->getActiveWarehouses(),
            'adjustment_types' => [
                'increase' => 'Increase Stock',
                'decrease' => 'Decrease Stock',
                'correction' => 'Stock Correction',
                'damage' => 'Damage/Wastage',
                'expiry' => 'Expiry Write-off',
                'count' => 'Physical Count Adjustment'
            ],
            'reasons' => [
                'damage' => 'Damaged Goods',
                'expiry' => 'Expired Goods',
                'theft' => 'Theft/Loss',
                'count_error' => 'Counting Error',
                'location_change' => 'Location Change',
                'quality_rejection' => 'Quality Rejection',
                'other' => 'Other'
            ]
        ];
        
        return view('inventory/adjustments/edit', $data);
    }

    public function adjustmentUpdate($id)
    {
        $adjustment = $this->stockModel->where('id', $id)
            ->where('transaction_type', 'adjustment')
            ->first();

        if (!$adjustment) {
            return redirect()->to('/inventory/adjustments')->with('error', 'Adjustment not found.');
        }

        if ($adjustment['status'] !== 'pending') {
            return redirect()->to('/inventory/adjustments')->with('error', 'Only pending adjustments can be updated.');
        }

        $rules = [
            'item_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'adjustment_type' => 'required|in_list[increase,decrease,correction,damage,expiry,count]',
            'quantity' => 'required|numeric|greater_than[0]',
            'reason' => 'required',
            'adjustment_date' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $itemId = $this->request->getPost('item_id');
        $warehouseId = $this->request->getPost('warehouse_id');
        $adjustmentType = $this->request->getPost('adjustment_type');
        $quantity = abs($this->request->getPost('quantity'));
        $unitCost = $this->request->getPost('unit_cost') ?: 0;

        // For decrease adjustments, check if sufficient stock is available
        if (in_array($adjustmentType, ['decrease', 'damage', 'expiry'])) {
            $currentStock = $this->stockModel->getItemStock($itemId, $warehouseId);
            // Add back the original adjustment quantity before checking
            $originalQuantity = $adjustment['quantity'];
            if ($adjustment['source_type'] === 'decrease' || $adjustment['source_type'] === 'damage' || $adjustment['source_type'] === 'expiry') {
                $availableAfterReversal = $currentStock['available_stock'] + $originalQuantity;
            } else {
                $availableAfterReversal = $currentStock['available_stock'];
            }
            
            if ($availableAfterReversal < $quantity) {
                return redirect()->back()->withInput()->with('error', 'Insufficient stock. Available after reversal: ' . $availableAfterReversal);
            }
        }

        $updateData = [
            'item_id' => $itemId,
            'warehouse_id' => $warehouseId,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => $quantity * $unitCost,
            'source_type' => $adjustmentType,
            'transaction_date' => $this->request->getPost('adjustment_date'),
            'notes' => $this->request->getPost('reason') . ' - ' . ($this->request->getPost('notes') ?? ''),
            'updated_by' => session()->get('user_id') ?? 1
        ];

        if ($this->stockModel->update($id, $updateData)) {
            return redirect()->to('/inventory/adjustments')->with('success', 'Stock adjustment updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update stock adjustment.');
        }
    }

    public function adjustmentView($id)
    {
        $adjustment = $this->stockModel->where('id', $id)
            ->where('transaction_type', 'adjustment')
            ->first();

        if (!$adjustment) {
            return redirect()->to('/inventory/adjustments')->with('error', 'Adjustment not found.');
        }

        $item = $this->itemModel->find($adjustment['item_id']);
        $warehouse = $this->warehouseModel->find($adjustment['warehouse_id']);

        $data = [
            'title' => 'Stock Adjustment Details',
            'adjustment' => $adjustment,
            'item' => $item,
            'warehouse' => $warehouse
        ];
        
        return view('inventory/adjustments/view', $data);
    }

    public function adjustmentApprove($id)
    {
        $adjustment = $this->stockModel->where('id', $id)
            ->where('transaction_type', 'adjustment')
            ->first();

        if (!$adjustment) {
            return redirect()->to('/inventory/adjustments')->with('error', 'Adjustment not found.');
        }

        if ($adjustment['status'] !== 'pending') {
            return redirect()->to('/inventory/adjustments')->with('error', 'Only pending adjustments can be approved.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $itemId = $adjustment['item_id'];
            $warehouseId = $adjustment['warehouse_id'];
            $quantity = $adjustment['quantity'];
            $adjustmentType = $adjustment['source_type'];

            // For decrease adjustments, check stock availability
            if (in_array($adjustmentType, ['decrease', 'damage', 'expiry'])) {
                $currentStock = $this->stockModel->getItemStock($itemId, $warehouseId);
                if ($currentStock['available_stock'] < $quantity) {
                    return redirect()->to('/inventory/adjustments')->with('error', 'Cannot approve: Insufficient stock available.');
                }

                // Remove stock
                if (!$this->stockModel->removeStock($itemId, $warehouseId, $quantity, 'available', $adjustment['reference_number'])) {
                    throw new \Exception('Failed to remove stock.');
                }
            } else {
                // Increase stock
                $stockData = [
                    'item_id' => $itemId,
                    'warehouse_id' => $warehouseId,
                    'quantity' => $quantity,
                    'unit_cost' => $adjustment['unit_cost'] ?? 0,
                    'status' => 'available',
                    'transaction_type' => 'adjustment',
                    'source_type' => $adjustmentType,
                    'source_document' => $adjustment['reference_number'],
                    'reference_number' => $adjustment['reference_number'],
                    'notes' => $adjustment['notes'],
                    'transaction_date' => $adjustment['transaction_date']
                ];

                if (!$this->stockModel->addStock($stockData)) {
                    throw new \Exception('Failed to add stock.');
                }
            }

            // Update adjustment status
            $this->stockModel->update($id, [
                'status' => 'approved',
                'approved_by' => session()->get('user_id') ?? 1
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/inventory/adjustments')->with('error', 'Failed to approve adjustment.');
            }

            return redirect()->to('/inventory/adjustments')->with('success', 'Stock adjustment approved and applied successfully!');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('/inventory/adjustments')->with('error', 'Error approving adjustment: ' . $e->getMessage());
        }
    }

    private function getAdjustmentStats()
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        return [
            'pending' => $this->stockModel->where('transaction_type', 'adjustment')
                ->where('status', 'pending')
                ->countAllResults(),
            'approved_today' => $this->stockModel->where('transaction_type', 'adjustment')
                ->where('status', 'approved')
                ->where('DATE(updated_at)', $today)
                ->countAllResults(),
            'approved_this_month' => $this->stockModel->where('transaction_type', 'adjustment')
                ->where('status', 'approved')
                ->where('DATE_FORMAT(updated_at, "%Y-%m")', $thisMonth)
                ->countAllResults(),
            'total_pending_value' => $this->stockModel->where('transaction_type', 'adjustment')
                ->where('status', 'pending')
                ->selectSum('total_cost')
                ->first()['total_cost'] ?? 0
        ];
    }

    // ==================== REPORTS ====================
    
    public function reports()
    {
        $data = [
            'title' => 'Inventory Reports',
            'warehouse_stats' => $this->warehouseModel->getWarehouseStats(),
            'item_stats' => $this->itemModel->getItemStats(),
            'stock_stats' => $this->stockModel->getStockStats(),
            'transfer_stats' => $this->transferModel->getTransferStats(),
            'low_stock_items' => $this->itemModel->getLowStockItems(),
            'stock_aging' => $this->stockModel->getStockAgingReport(),
            'stock_valuation' => $this->stockModel->getStockValuationReport()
        ];
        
        return view('inventory/reports/index', $data);
    }

    public function exportReport($reportType)
    {
        switch ($reportType) {
            case 'warehouses':
                $csv = $this->warehouseModel->exportToCSV();
                $filename = 'warehouses_' . date('Y-m-d') . '.csv';
                break;
                
            case 'items':
                $csv = $this->itemModel->exportToCSV();
                $filename = 'items_' . date('Y-m-d') . '.csv';
                break;
                
            case 'stock':
                $csv = $this->stockModel->exportStockReport();
                $filename = 'stock_' . date('Y-m-d') . '.csv';
                break;
                
            case 'transfers':
                $csv = $this->transferModel->exportToCSV();
                $filename = 'transfers_' . date('Y-m-d') . '.csv';
                break;
                
            default:
                return redirect()->back()->with('error', 'Invalid report type.');
        }
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo $csv;
        exit;
    }

    public function stockCount()
    {
        $filters = [
            'warehouse_id' => $this->request->getGet('warehouse_id'),
            'count_status' => $this->request->getGet('count_status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $stockCounts = [];
        $stats = [];
        try {
            $stockCountModel = new \App\Models\StockCount();
            $stockCounts = $stockCountModel->getWithRelations();
            $stats = $stockCountModel->getCountStats();
        } catch (\Throwable $e) {
            log_message('error', 'stockCount unavailable: ' . $e->getMessage());
        }

        $data = [
            'title' => 'Stock Count',
            'stock_counts' => $stockCounts,
            'warehouses' => $this->warehouseModel->getActiveWarehouses(),
            'filters' => $filters,
            'stats' => $stats
        ];

        return view('inventory/stock_count/index', $data);
    }

    public function expiryAlerts()
    {
        $days = $this->request->getGet('days') ?: 30;

        $expiryAlerts = [];
        $lowStockItems = [];
        try {
            $batchTrackingModel = new \App\Models\BatchTracking();
            $expiryAlerts = $batchTrackingModel->getExpiryAlerts((int) $days);
        } catch (\Throwable $e) {
            log_message('error', 'expiryAlerts batch: ' . $e->getMessage());
        }
        try {
            $lowStockItems = $this->itemModel->getLowStockItems();
        } catch (\Throwable $e) {
            log_message('error', 'expiryAlerts low stock: ' . $e->getMessage());
        }

        $data = [
            'title' => 'Low Stock & Expiry Alerts',
            'expiry_alerts' => $expiryAlerts,
            'low_stock_items' => $lowStockItems,
            'days' => $days,
        ];

        return view('inventory/alerts/index', $data);
    }

    public function warehouseMap()
    {
        return view('shared/module_page', [
            'title' => 'Warehouse Map',
            'message' => 'Warehouse map page is available.',
        ]);
    }

    public function warehouseLocations($warehouseId)
    {
        return view('shared/module_page', [
            'title' => 'Warehouse Locations',
            'message' => 'Warehouse location details are available.',
            'summary' => ['Warehouse ID' => $warehouseId],
        ]);
    }

    public function warehouseStock($warehouseId)
    {
        return view('shared/module_page', [
            'title' => 'Warehouse Stock',
            'message' => 'Warehouse stock details are available.',
            'summary' => ['Warehouse ID' => $warehouseId],
        ]);
    }

    public function stockScan()
    {
        return view('shared/module_page', [
            'title' => 'Stock Scan',
            'message' => 'Barcode and stock scan page is available.',
        ]);
    }

    public function stockRFID()
    {
        return view('shared/module_page', [
            'title' => 'RFID Stock Tracking',
            'message' => 'RFID stock tracking page is available.',
        ]);
    }

    public function batchTracking()
    {
        return view('shared/module_page', [
            'title' => 'Batch Tracking',
            'message' => 'Batch tracking page is available.',
        ]);
    }

    public function stockAgingReport()
    {
        return view('shared/module_page', [
            'title' => 'Stock Aging Report',
            'message' => 'Stock aging report page is available.',
        ]);
    }

    public function stockValuationReport()
    {
        return view('shared/module_page', [
            'title' => 'Stock Valuation Report',
            'message' => 'Stock valuation report page is available.',
        ]);
    }

    public function movementAnalysisReport()
    {
        return view('shared/module_page', [
            'title' => 'Movement Analysis Report',
            'message' => 'Inventory movement analysis report page is available.',
        ]);
    }
}
