<?php
namespace App\Controllers;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use App\Models\Product;
use App\Models\User;
use Exception;

class PurchaseRequisitionController extends BaseController
{
    protected $purchaseRequisitionModel;
    protected $purchaseRequisitionItemModel;
    protected $productModel;
    protected $userModel;

    public function __construct()
    {
        $this->purchaseRequisitionModel = new PurchaseRequisition();
        $this->purchaseRequisitionItemModel = new PurchaseRequisitionItem();
        $this->productModel = new Product();
        $this->userModel = new User();
    }

    public function index()
    {
        $filters = [
            'status' => $this->request->getGet('status'),
            'priority' => $this->request->getGet('priority'),
            'department' => $this->request->getGet('department'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $data = [
            'title' => 'Purchase Requisitions - PRODX',
            'requisitions' => $this->purchaseRequisitionModel->getPurchaseRequisitions($filters),
            'stats' => $this->purchaseRequisitionModel->getPRStats(),
            'filters' => $filters
        ];

        return view('purchase_requisition/index', $data);
    }

    public function create()
    {
        // Test database connection
        try {
            $db = \Config\Database::connect();
            log_message('info', 'Database connection successful');
        } catch (Exception $e) {
            log_message('error', 'Database connection failed: ' . $e->getMessage());
            // Database connection failed, use fallback values
            $data = [
                'title' => 'Create Purchase Requisition - PRODX',
                'pr_number' => 'PR' . date('Y') . date('m') . str_pad(1, 4, '0', STR_PAD_LEFT),
                'products' => [],
                'users' => []
            ];
            return view('purchase_requisition/create', $data);
        }

        // Generate PR number with fallback
        try {
            $pr_number = $this->purchaseRequisitionModel->generatePRNumber();
            log_message('info', 'PR number generated: ' . $pr_number);
        } catch (Exception $e) {
            log_message('error', 'PR number generation failed: ' . $e->getMessage());
            // Fallback PR number generation
            $pr_number = 'PR' . date('Y') . date('m') . str_pad(1, 4, '0', STR_PAD_LEFT);
        }

        // Get products with fallback
        try {
            $products = $this->productModel->where('status', 'active')->findAll();
            log_message('info', 'Products loaded: ' . count($products));
        } catch (Exception $e) {
            log_message('error', 'Products loading failed: ' . $e->getMessage());
            $products = [];
        }

        // Get users with fallback
        try {
            $users = $this->userModel->where('status', 'active')->findAll();
            log_message('info', 'Users loaded: ' . count($users));
        } catch (Exception $e) {
            log_message('error', 'Users loading failed: ' . $e->getMessage());
            $users = [];
        }

        $data = [
            'title' => 'Create Purchase Requisition - PRODX',
            'pr_number' => $pr_number,
            'products' => $products,
            'users' => $users
        ];

        log_message('info', 'Create view data prepared successfully');
        return view('purchase_requisition/create', $data);
    }

    public function store()
    {
        $rules = [
            'pr_number' => 'required',
            'department' => 'required|max_length[50]',
            'priority' => 'required|in_list[low,medium,high,urgent]',
            'required_date' => 'required|valid_date',
            'items' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('purchase-requisition/create')->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle PR Number uniqueness
        $prNumber = $this->request->getPost('pr_number');
        if ($this->purchaseRequisitionModel->where('pr_number', $prNumber)->countAllResults() > 0) {
            $prNumber = $this->purchaseRequisitionModel->generatePRNumber();
        }

        $data = [
            'pr_number' => $prNumber,
            'requested_by' => session()->get('user_id'),
            'department' => $this->request->getPost('department'),
            'priority' => $this->request->getPost('priority'),
            'status' => 'draft',
            'required_date' => $this->request->getPost('required_date'),
            'remarks' => $this->request->getPost('remarks')
        ];

        $prId = $this->purchaseRequisitionModel->insert($data);
        
        if ($prId) {
            $itemsJson = $this->request->getPost('items');
            $items = json_decode($itemsJson, true);
            
            if ($items) {
                $this->purchaseRequisitionItemModel->addItems($prId, $items);
            }
            
            return redirect()->to('purchase-requisition')->with('success', 'Purchase Requisition created successfully');
        }

        return redirect()->to('purchase-requisition/create')->withInput()->with('error', 'Failed to create Purchase Requisition');
    }

    public function show($id)
    {
        $pr = $this->purchaseRequisitionModel->getPurchaseRequisitionWithItems($id);
        
        if (!$pr) {
            return redirect()->to('purchase-requisition')->with('error', 'Purchase Requisition not found');
        }

        $data = [
            'title' => 'View Purchase Requisition - PRODX',
            'pr' => $pr
        ];

        return view('purchase_requisition/show', $data);
    }

    public function edit($id)
    {
        $pr = $this->purchaseRequisitionModel->getPurchaseRequisitionWithItems($id);
        
        if (!$pr) {
            return redirect()->to('purchase-requisition')->with('error', 'Purchase Requisition not found');
        }

        if ($pr['status'] !== 'draft') {
            return redirect()->to('purchase-requisition')->with('error', 'Only draft PRs can be edited');
        }

        $data = [
            'title' => 'Edit Purchase Requisition - PRODX',
            'pr' => $pr,
            'products' => $this->productModel->where('status', 'active')->findAll(),
            'users' => $this->userModel->where('status', 'active')->findAll()
        ];

        return view('purchase_requisition/edit', $data);
    }

    public function update($id)
    {
        $pr = $this->purchaseRequisitionModel->find($id);
        
        if (!$pr) {
            return redirect()->to('purchase-requisition')->with('error', 'Purchase Requisition not found');
        }

        if ($pr['status'] !== 'draft') {
            return redirect()->to('purchase-requisition')->with('error', 'Only draft PRs can be edited');
        }

        $rules = [
            'department' => 'required|max_length[50]',
            'priority' => 'required|in_list[low,medium,high,urgent]',
            'required_date' => 'required|valid_date',
            'items' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'department' => $this->request->getPost('department'),
            'priority' => $this->request->getPost('priority'),
            'required_date' => $this->request->getPost('required_date'),
            'remarks' => $this->request->getPost('remarks')
        ];

        if ($this->purchaseRequisitionModel->update($id, $data)) {
            $items = json_decode($this->request->getPost('items'), true);
            if ($items) {
                $this->purchaseRequisitionItemModel->updateItems($id, $items);
            }
            
            return redirect()->to('purchase-requisition')->with('success', 'Purchase Requisition updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update Purchase Requisition');
    }

    public function updateStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        if (!$id || !$status) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid parameters']);
        }

        $pr = $this->purchaseRequisitionModel->find($id);
        if (!$pr) {
            return $this->response->setJSON(['success' => false, 'message' => 'Purchase Requisition not found']);
        }

        if ($this->purchaseRequisitionModel->updateStatus($id, $status)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status']);
    }

    public function delete($id)
    {
        $pr = $this->purchaseRequisitionModel->find($id);
        
        if (!$pr) {
            return redirect()->to('purchase-requisition')->with('error', 'Purchase Requisition not found');
        }

        if ($pr['status'] !== 'draft') {
            return redirect()->to('purchase-requisition')->with('error', 'Only draft PRs can be deleted');
        }

        $this->purchaseRequisitionItemModel->deleteItemsByPRId($id);
        
        if ($this->purchaseRequisitionModel->delete($id)) {
            return redirect()->to('purchase-requisition')->with('success', 'Purchase Requisition deleted successfully');
        }

        return redirect()->to('purchase-requisition')->with('error', 'Failed to delete Purchase Requisition');
    }

    public function deleteItem($itemId)
    {
        $item = $this->purchaseRequisitionItemModel->find($itemId);
        
        if (!$item) {
            return $this->response->setJSON(['success' => false, 'message' => 'Item not found']);
        }

        // Verify PR status is draft (optional but recommended safety check)
        $pr = $this->purchaseRequisitionModel->find($item['pr_id']);
        if ($pr['status'] !== 'draft') {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot delete items from non-draft PR']);
        }

        if ($this->purchaseRequisitionItemModel->delete($itemId)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Item deleted successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete item']);
    }

    public function getProducts()
    {
        $products = $this->productModel->where('status', 'active')
            ->select('id, product_name, product_code, unit, cost_price')
            ->findAll();
        
        return $this->response->setJSON($products);
    }

    public function print($id)
    {
        $pr = $this->purchaseRequisitionModel->getPurchaseRequisitionWithItems($id);
        
        if (!$pr) {
            return redirect()->to('purchase-requisition')->with('error', 'Purchase Requisition not found');
        }

        $data = [
            'title' => 'Print Purchase Requisition - PRODX',
            'pr' => $pr
        ];

        return view('purchase_requisition/print', $data);
    }
} 