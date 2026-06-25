<?= $this->extend('layouts/main') ?>\n\n<?= $this->section('content') ?>
    
            </div>

            <!-- Product Summary -->
            <div class="content-card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-box me-2"></i>Material Summary</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr data-selectable>
                                    <td class="fw-bold" style="width: 150px;">Material Code:</td>
                                    <td><span class="badge bg-primary"><?= $product['product_code'] ?></span></td>
                                </tr>
                                <tr data-selectable>
                                    <td class="fw-bold">Material Name:</td>
                                    <td><?= $product['product_name'] ?></td>
                                </tr>
                                <tr data-selectable>
                                    <td class="fw-bold">Category:</td>
                                    <td><?= isset($product['category_name']) ? $product['category_name'] : 'N/A' ?></td>
                                </tr>
                                <tr data-selectable>
                                    <td class="fw-bold">Material Type:</td>
                                    <td>
                                        <span class="material-type-badge material-type-<?= $product['material_type'] ?>">
                                            <?= ucwords(str_replace('_', ' ', $product['material_type'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr data-selectable>
                                    <td class="fw-bold" style="width: 150px;">Unit Price:</td>
                                    <td>
                                        <?php if ($product['unit_price'] > 0): ?>
                                            <span class="text-success fw-bold">₹<?= number_format($product['unit_price'], 2) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Not set</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr data-selectable>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        <span class="status-badge <?= $product['status'] == 'active' ? 'status-active' : 'status-inactive' ?>">
                                            <?= ucfirst($product['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr data-selectable>
                                    <td class="fw-bold">Created:</td>
                                    <td><?= date('M d, Y', strtotime($product['created_at'])) ?></td>
                                </tr>
                                <tr data-selectable>
                                    <td class="fw-bold">Last Updated:</td>
                                    <td><?= date('M d, Y', strtotime($product['updated_at'])) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="stats-grid mb-4">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= number_format(isset($metrics['total_usage']) ? $metrics['total_usage'] : 0) ?></div>
                        <div class="stat-label">Total Usage</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <div class="stat-value">₹<?= number_format(isset($metrics['total_cost']) ? $metrics['total_cost'] : 0, 2) ?></div>
                        <div class="stat-label">Total Cost</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= number_format(isset($metrics['waste_percentage']) ? $metrics['waste_percentage'] : 0, 1) ?>%</div>
                        <div class="stat-label">Waste Rate</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon info">
                        <i class="fas fa-trending-up"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= number_format(isset($metrics['efficiency_score']) ? $metrics['efficiency_score'] : 0, 1) ?></div>
                        <div class="stat-label">Efficiency Score</div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row mb-4">
                <!-- Usage Trend Chart -->
                <div class="col-md-6">
                    <div class="content-card">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-line me-2"></i>Usage Trend (Last 12 Months)</h5>
                        </div>
                        <div class="card-body p-4">
                            <canvas id="usageChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Cost Analysis Chart -->
                <div class="col-md-6">
                    <div class="content-card">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-pie me-2"></i>Cost Breakdown</h5>
                        </div>
                        <div class="card-body p-4">
                            <canvas id="costChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Analysis -->
            <div class="row mb-4">
                <!-- Monthly Performance -->
                <div class="col-md-8">
                    <div class="content-card">
                        <div class="card-header">
                            <h5><i class="fas fa-calendar-alt me-2"></i>Monthly Performance</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr data-selectable>
                                            <th>Month</th>
                                            <th>Usage</th>
                                            <th>Cost</th>
                                            <th>Waste</th>
                                            <th>Efficiency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($monthlyData) && !empty($monthlyData)): ?>
                                            <?php foreach ($monthlyData as $month): ?>
                                                <tr data-selectable>
                                                    <td><?= $month['month'] ?></td>
                                                    <td><?= number_format($month['usage']) ?> <?= $product['unit'] ?></td>
                                                    <td>₹<?= number_format($month['cost'], 2) ?></td>
                                                    <td><?= number_format($month['waste'], 1) ?>%</td>
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-success" 
                                                                 style="width: <?= $month['efficiency'] ?>%">
                                                                <?= number_format($month['efficiency'], 1) ?>%
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr data-selectable>
                                                <td colspan="5" class="text-center text-muted">
                                                    <i class="fas fa-info-circle me-2"></i>No monthly data available
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Insights -->
                <div class="col-md-4">
                    <div class="content-card">
                        <div class="card-header">
                            <h5><i class="fas fa-lightbulb me-2"></i>Key Insights</h5>
                        </div>
                        <div class="card-body p-4">
                            <?php if (isset($insights) && !empty($insights)): ?>
                                <?php foreach ($insights as $insight): ?>
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <?= $insight ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No insights available. Add more data to generate insights.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommendations -->
            <div class="content-card">
                <div class="card-header">
                    <h5><i class="fas fa-lightbulb me-2"></i>Recommendations</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Cost Optimization</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Consider bulk purchasing to reduce unit costs
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Review supplier contracts for better pricing
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Implement waste reduction strategies
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Efficiency Improvements</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Optimize production processes
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Train staff on proper material handling
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Regular maintenance of equipment
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    

    
<?= $this->endSection() ?> 