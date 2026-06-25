<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-edit me-3"></i>Edit Employee</h1>
    <div class="header-actions">
        <a href="<?= base_url('hr/employees') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Employees
        </a>
    </div>
</div>

<!-- Alert Messages -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Edit Form -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-user me-2"></i>Employee Information</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= base_url('hr/employee/update/' . $employee['id']) ?>">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="employee_code" class="form-label">Employee Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="employee_code" name="employee_code" 
                               value="<?= old('employee_code', $employee['employee_code']) ?>" required>
                        <div class="form-text">Enter a unique employee code</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">User Account (Optional)</label>
                        <select class="form-select" id="user_id" name="user_id">
                            <option value="">Select User Account</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>" <?= (old('user_id', $employee['user_id']) == $user['id']) ? 'selected' : '' ?>>
                                    <?= esc($user['username']) ?> (<?= esc($user['role']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Link to existing user account (optional)</div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" 
                               value="<?= old('first_name', $employee['first_name']) ?>" required>
                        <div class="form-text">Enter employee's first name</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" 
                               value="<?= old('last_name', $employee['last_name']) ?>" required>
                        <div class="form-text">Enter employee's last name</div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= old('email', $employee['email']) ?>" required>
                        <div class="form-text">Enter employee's email address</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               value="<?= old('phone', $employee['phone']) ?>" required>
                        <div class="form-text">Enter employee's phone number</div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3" 
                          placeholder="Enter employee's address..."><?= old('address', $employee['address']) ?></textarea>
                <div class="form-text">Enter employee's complete address</div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                        <select class="form-select" id="department_id" name="department_id" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>" <?= (old('department_id', $employee['department_id']) == $dept['id']) ? 'selected' : '' ?>>
                                    <?= esc($dept['department_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Select employee's department</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="designation" class="form-label">Designation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="designation" name="designation" 
                               value="<?= old('designation', $employee['designation']) ?>" required>
                        <div class="form-text">Enter employee's job title/designation</div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="joining_date" class="form-label">Joining Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="joining_date" name="joining_date" 
                               value="<?= old('joining_date', $employee['joining_date']) ?>" required>
                        <div class="form-text">Select employee's joining date</div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="salary" class="form-label">Salary <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="salary" name="salary" 
                               value="<?= old('salary', $employee['salary']) ?>" step="0.01" min="0" required>
                        <div class="form-text">Enter employee's basic salary</div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" <?= (old('status', $employee['status']) == 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= (old('status', $employee['status']) == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                            <option value="terminated" <?= (old('status', $employee['status']) == 'terminated') ? 'selected' : '' ?>>Terminated</option>
                        </select>
                        <div class="form-text">Set the status for this employee</div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="allowances" class="form-label">Allowances</label>
                        <input type="number" class="form-control" id="allowances" name="allowances" 
                               value="<?= old('allowances', isset($employee['allowances']) ? $employee['allowances'] : 0) ?>" step="0.01" min="0">
                        <div class="form-text">Enter additional allowances (optional)</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="deductions" class="form-label">Deductions</label>
                        <input type="number" class="form-control" id="deductions" name="deductions" 
                               value="<?= old('deductions', isset($employee['deductions']) ? $employee['deductions'] : 0) ?>" step="0.01" min="0">
                        <div class="form-text">Enter salary deductions (optional)</div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('hr/employees') ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Employee
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add any client-side validation or functionality here
});
</script>
<?= $this->endSection() ?>
