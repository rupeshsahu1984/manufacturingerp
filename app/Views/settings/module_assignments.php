<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-key me-2"></i>Module Assignments
        </h1>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#departmentAssignmentModal">
                <i class="fas fa-plus me-1"></i>Assign to Department
            </button>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#employeeAssignmentModal">
                <i class="fas fa-user-plus me-1"></i>Assign to Employee
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Department Assignments -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-building me-2"></i>Department Module Assignments
            </h6>
        </div>
        <div class="card-body">
            <?php if (empty($departments)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Departments Found</h5>
                    <p class="text-muted">Create departments first to assign modules.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Department</th>
                                <th>Assigned Modules</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($departments as $department): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-building text-primary me-2"></i>
                                            <div>
                                                <strong><?= $department['department_name'] ?></strong>
                                                <?php if ($department['description']): ?>
                                                    <br><small class="text-muted"><?= $department['description'] ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($department['assigned_modules'])): ?>
                                            <div class="d-flex flex-wrap gap-1">
                                                <?php foreach ($department['assigned_modules'] as $module): ?>
                                                    <span class="badge bg-success">
                                                        <?= $availableModules[$module['module_name']] ?? $module['module_name'] ?>
                                                        <button type="button" class="btn-close btn-close-white ms-1" 
                                                                onclick="removeModuleAssignment('department', <?= $department['id'] ?>, '<?= $module['module_name'] ?>')"
                                                                style="font-size: 0.5rem;"></button>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">No modules assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="editDepartmentAssignment(<?= $department['id'] ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Employee Assignments -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-success text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-users me-2"></i>Employee Module Assignments
            </h6>
        </div>
        <div class="card-body">
            <?php if (empty($employees)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Employees Found</h5>
                    <p class="text-muted">Create employees first to assign modules.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Assigned Modules</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employees as $employee): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user text-success me-2"></i>
                                            <div>
                                                <strong><?= $employee['first_name'] . ' ' . $employee['last_name'] ?></strong>
                                                <br><small class="text-muted"><?= $employee['employee_code'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= $employee['department_name'] ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1" id="employee-modules-<?= $employee['id'] ?>">
                                            <span class="text-muted">Loading...</span>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success" 
                                                onclick="editEmployeeAssignment(<?= $employee['id'] ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Department Assignment Modal -->
<div class="modal fade" id="departmentAssignmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-building me-2"></i>Assign Modules to Department
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('settings/assign-modules-to-department') ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="department_id" class="form-label">Select Department</label>
                        <select class="form-select" id="department_id" name="department_id" required>
                            <option value="">Choose Department</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?= $department['id'] ?>"><?= $department['department_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Modules & Permissions</label>
                        <div class="row">
                            <?php foreach ($availableModules as $moduleKey => $moduleName): ?>
                                <div class="col-md-6 mb-2">
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <div class="form-check">
                                                <input class="form-check-input module-checkbox" type="checkbox" 
                                                       id="dept_<?= $moduleKey ?>" name="modules[<?= $moduleKey ?>][assigned]" value="1">
                                                <label class="form-check-label" for="dept_<?= $moduleKey ?>">
                                                    <strong><?= $moduleName ?></strong>
                                                </label>
                                            </div>
                                            <div class="mt-2 module-permissions" style="display: none;">
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="modules[<?= $moduleKey ?>][can_view]" value="1">
                                                            <label class="form-check-label small">View</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="modules[<?= $moduleKey ?>][can_create]" value="1">
                                                            <label class="form-check-label small">Create</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="modules[<?= $moduleKey ?>][can_edit]" value="1">
                                                            <label class="form-check-label small">Edit</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="modules[<?= $moduleKey ?>][can_delete]" value="1">
                                                            <label class="form-check-label small">Delete</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="modules[<?= $moduleKey ?>][can_export]" value="1">
                                                            <label class="form-check-label small">Export</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="modules[<?= $moduleKey ?>][can_print]" value="1">
                                                            <label class="form-check-label small">Print</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Assign Modules
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Employee Assignment Modal -->
<div class="modal fade" id="employeeAssignmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>Assign Modules to Employee
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('settings/assign-modules-to-employee') ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Select Employee</label>
                        <select class="form-select" id="employee_id" name="employee_id" required>
                            <option value="">Choose Employee</option>
                            <?php foreach ($employees as $employee): ?>
                                <option value="<?= $employee['id'] ?>">
                                    <?= $employee['first_name'] . ' ' . $employee['last_name'] ?> (<?= $employee['employee_code'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Modules & Permissions</label>
                        <div class="row">
                            <?php foreach ($availableModules as $moduleKey => $moduleName): ?>
                                <div class="col-md-6 mb-2">
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <div class="form-check">
                                                <input class="form-check-input emp-module-checkbox" type="checkbox" 
                                                       id="emp_<?= $moduleKey ?>" name="modules[<?= $moduleKey ?>][assigned]" value="1">
                                                <label class="form-check-label" for="emp_<?= $moduleKey ?>">
                                                    <strong><?= $moduleName ?></strong>
                                                </label>
                                            </div>
                                            <div class="mt-2 emp-module-permissions" style="display: none;">
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="modules[<?= $moduleKey ?>][can_view]" value="1">
                                                            <label class="form-check-label small">View</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="modules[<?= $moduleKey ?>][can_create]" value="1">
                                                            <label class="form-check-label small">Create</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="modules[<?= $moduleKey ?>][can_edit]" value="1">
                                                            <label class="form-check-label small">Edit</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="modules[<?= $moduleKey ?>][can_delete]" value="1">
                                                            <label class="form-check-label small">Delete</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="modules[<?= $moduleKey ?>][can_export]" value="1">
                                                            <label class="form-check-label small">Export</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="modules[<?= $moduleKey ?>][can_print]" value="1">
                                                            <label class="form-check-label small">Print</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Assign Modules
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Remove Assignment Form -->
<form id="removeAssignmentForm" action="<?= base_url('settings/remove-module-assignment') ?>" method="post" style="display: none;">
    <input type="hidden" name="assignment_type" id="remove_assignment_type">
    <input type="hidden" name="department_id" id="remove_department_id">
    <input type="hidden" name="employee_id" id="remove_employee_id">
    <input type="hidden" name="module_name" id="remove_module_name">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load employee module assignments
    loadEmployeeAssignments();
    
    // Module checkbox event listeners
    document.querySelectorAll('.module-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const permissionsDiv = this.closest('.card-body').querySelector('.module-permissions');
            if (this.checked) {
                permissionsDiv.style.display = 'block';
            } else {
                permissionsDiv.style.display = 'none';
            }
        });
    });
    
    document.querySelectorAll('.emp-module-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const permissionsDiv = this.closest('.card-body').querySelector('.emp-module-permissions');
            if (this.checked) {
                permissionsDiv.style.display = 'block';
            } else {
                permissionsDiv.style.display = 'none';
            }
        });
    });
});

function loadEmployeeAssignments() {
    const employees = <?= json_encode(array_column($employees, 'id')) ?>;
    
    employees.forEach(employeeId => {
        fetch(`<?= base_url('settings/get-module-assignments') ?>?type=employee&id=${employeeId}`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById(`employee-modules-${employeeId}`);
                if (data.length > 0) {
                    container.innerHTML = data.map(module => 
                        `<span class="badge bg-success">
                            ${module.module_name}
                            <button type="button" class="btn-close btn-close-white ms-1" 
                                    onclick="removeModuleAssignment('employee', ${employeeId}, '${module.module_name}')"
                                    style="font-size: 0.5rem;"></button>
                        </span>`
                    ).join('');
                } else {
                    container.innerHTML = '<span class="text-muted">No modules assigned</span>';
                }
            })
            .catch(error => {
                console.error('Error loading employee assignments:', error);
                document.getElementById(`employee-modules-${employeeId}`).innerHTML = 
                    '<span class="text-danger">Error loading assignments</span>';
            });
    });
}

function removeModuleAssignment(type, id, moduleName) {
    if (confirm('Are you sure you want to remove this module assignment?')) {
        document.getElementById('remove_assignment_type').value = type;
        document.getElementById('remove_module_name').value = moduleName;
        
        if (type === 'department') {
            document.getElementById('remove_department_id').value = id;
        } else {
            document.getElementById('remove_employee_id').value = id;
        }
        
        document.getElementById('removeAssignmentForm').submit();
    }
}

function editDepartmentAssignment(departmentId) {
    // Populate modal with existing assignments
    // This would require additional AJAX call to get current assignments
    document.getElementById('department_id').value = departmentId;
    new bootstrap.Modal(document.getElementById('departmentAssignmentModal')).show();
}

function editEmployeeAssignment(employeeId) {
    // Populate modal with existing assignments
    // This would require additional AJAX call to get current assignments
    document.getElementById('employee_id').value = employeeId;
    new bootstrap.Modal(document.getElementById('employeeAssignmentModal')).show();
}
</script>

<?= $this->endSection() ?>
