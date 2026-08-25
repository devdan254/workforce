<?php if (isset($component)) { $__componentOriginal91fdd17964e43374ae18c674f95cdaa3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal91fdd17964e43374ae18c674f95cdaa3 = $attributes; } ?>
<?php $component = App\View\Components\AdminLayout::resolve(['title' => 'Dashboard'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AdminLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

    <div class="mb-4">
        <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">Welcome back, <?php echo e(explode(' ', auth()->user()->name)[0]); ?>!</h2>
        <p class="text-secondary mb-0">Here's a 360° overview of Altura Workforce Solutions right now.</p>
    </div>

    
    <h3 class="h6 fw-semibold text-secondary text-uppercase mb-3" style="letter-spacing:.04em;font-size:.8rem;">Financial Overview</h3>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-6 col-lg-3">
            <a href="<?php echo e(route('admin.payments-management.index', ['status' => 'confirmed'])); ?>" class="text-decoration-none">
                <div class="card stat-card p-3 h-100">
                    <div class="stat-label">Amount Paid</div>
                    <?php $__empty_1 = true; $__currentLoopData = $amountPaidByCurrency; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="stat-value" style="<?php echo e(!$loop->first ? 'font-size:1rem;' : ''); ?>"><?php echo e($currency); ?> <?php echo e(number_format($amount, 0)); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="stat-value">—</div>
                    <?php endif; ?>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <a href="<?php echo e(route('admin.payments-management.index', ['status' => 'pending'])); ?>" class="text-decoration-none">
                <div class="card stat-card p-3 h-100">
                    <div class="stat-label">Amount Pending</div>
                    <?php $__empty_1 = true; $__currentLoopData = $amountPendingByCurrency; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="stat-value" style="<?php echo e(!$loop->first ? 'font-size:1rem;' : ''); ?>"><?php echo e($currency); ?> <?php echo e(number_format($amount, 0)); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="stat-value">—</div>
                    <?php endif; ?>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <a href="<?php echo e(route('admin.payments-management.index')); ?>" class="text-decoration-none">
                <div class="card stat-card p-3 h-100">
                    <div class="stat-label">Total Payments</div>
                    <?php $__empty_1 = true; $__currentLoopData = $totalByCurrency; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="stat-value" style="<?php echo e(!$loop->first ? 'font-size:1rem;' : ''); ?>"><?php echo e($currency); ?> <?php echo e(number_format($amount, 0)); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="stat-value">—</div>
                    <?php endif; ?>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <a href="<?php echo e(route('admin.payments-management.index')); ?>" class="text-decoration-none">
                <div class="card stat-card p-3 h-100">
                    <div class="stat-label">Pending Invoices</div>
                    <div class="stat-value"><?php echo e(number_format($pendingInvoicesCount)); ?></div>
                </div>
            </a>
        </div>
    </div>

    
    <h3 class="h6 fw-semibold text-secondary text-uppercase mb-3" style="letter-spacing:.04em;font-size:.8rem;">Applications &amp; Services</h3>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-6 col-lg-3">
            <a href="<?php echo e(route('admin.students.index')); ?>" class="text-decoration-none">
                <div class="card stat-card p-3 h-100">
                    <div class="stat-label">Student Applications</div>
                    <div class="stat-value"><?php echo e(number_format($studentApplicationsCount)); ?></div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <a href="<?php echo e(route('admin.job-seekers.index')); ?>" class="text-decoration-none">
                <div class="card stat-card p-3 h-100">
                    <div class="stat-label">Job Applications</div>
                    <div class="stat-value"><?php echo e(number_format($jobApplicationsCount)); ?></div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <a href="<?php echo e(route('admin.worker-requests.index')); ?>" class="text-decoration-none">
                <div class="card stat-card p-3 h-100">
                    <div class="stat-label">Worker Requests</div>
                    <div class="stat-value"><?php echo e(number_format($workerRequestsCount)); ?></div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <a href="<?php echo e(route('admin.visa-management.index')); ?>" class="text-decoration-none">
                <div class="card stat-card p-3 h-100">
                    <div class="stat-label">Visa Applications</div>
                    <div class="stat-value"><?php echo e(number_format($visaApplicationsCount)); ?></div>
                </div>
            </a>
        </div>
    </div>
    
    <h3 class="h6 fw-semibold text-secondary text-uppercase mb-3" style="letter-spacing:.04em;font-size:.8rem;">System &amp; Operations</h3>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-6 col-lg-3">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label mb-2">System Users</div>
                <?php $__currentLoopData = $userBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-secondary"><?php echo e($label); ?></span>
                        <span class="fw-semibold"><?php echo e(number_format($count)); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <hr class="my-2">
                <div class="d-flex justify-content-between">
                    <span class="stat-label mb-0">All Users</span>
                    <span class="stat-value" style="font-size:1.1rem;"><?php echo e(number_format($allUsersCount)); ?></span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label mb-2">Upcoming Appointments</div>
                <a href="<?php echo e(route('admin.students.index')); ?>" class="d-flex justify-content-between text-decoration-none small mb-1">
                    <span class="text-secondary">Students</span>
                    <span class="fw-semibold"><?php echo e(str_pad($appointmentBreakdown['Students'], 2, '0', STR_PAD_LEFT)); ?></span>
                </a>
                <a href="<?php echo e(route('admin.job-seekers.index')); ?>" class="d-flex justify-content-between text-decoration-none small mb-1">
                    <span class="text-secondary">Job Applicants</span>
                    <span class="fw-semibold"><?php echo e(str_pad($appointmentBreakdown['Job Applicants'], 2, '0', STR_PAD_LEFT)); ?></span>
                </a>
                <a href="<?php echo e(route('admin.employers.index')); ?>" class="d-flex justify-content-between text-decoration-none small mb-1">
                    <span class="text-secondary">Employers</span>
                    <span class="fw-semibold"><?php echo e(str_pad($appointmentBreakdown['Employers'], 2, '0', STR_PAD_LEFT)); ?></span>
                </a>
                <hr class="my-2">
                <div class="d-flex justify-content-between">
                    <span class="stat-label mb-0">Total Upcoming</span>
                    <span class="stat-value" style="font-size:1.1rem;"><?php echo e(number_format($upcomingAppointmentsCount)); ?></span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label mb-2">Support Tickets</div>
                <a href="<?php echo e(route('admin.support.index', ['status' => 'open'])); ?>" class="d-flex justify-content-between text-decoration-none small mb-1">
                    <span class="text-secondary">Open</span>
                    <span class="fw-semibold"><?php echo e(str_pad($ticketBreakdown['Open'], 2, '0', STR_PAD_LEFT)); ?></span>
                </a>
                <a href="<?php echo e(route('admin.support.index', ['status' => ['in_progress', 'waiting_for_student']])); ?>" class="d-flex justify-content-between text-decoration-none small mb-1">
                    <span class="text-secondary">In Progress</span>
                    <span class="fw-semibold"><?php echo e(str_pad($ticketBreakdown['In Progress'], 2, '0', STR_PAD_LEFT)); ?></span>
                </a>
                <a href="<?php echo e(route('admin.support.index', ['status' => 'resolved'])); ?>" class="d-flex justify-content-between text-decoration-none small mb-1">
                    <span class="text-secondary">Resolved</span>
                    <span class="fw-semibold"><?php echo e(str_pad($ticketBreakdown['Resolved'], 2, '0', STR_PAD_LEFT)); ?></span>
                </a>
                <a href="<?php echo e(route('admin.support.index', ['status' => 'closed'])); ?>" class="d-flex justify-content-between text-decoration-none small">
                    <span class="text-secondary">Closed</span>
                    <span class="fw-semibold"><?php echo e(str_pad($ticketBreakdown['Closed'], 2, '0', STR_PAD_LEFT)); ?></span>
                </a>
            </div>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label mb-2">Notifications</div>
                <a href="<?php echo e(route('admin.notifications.index', ['read' => 0])); ?>" class="d-flex justify-content-between text-decoration-none small mb-1">
                    <span class="text-secondary">Unread</span>
                    <span class="fw-semibold"><?php echo e(str_pad($notificationBreakdown['Unread'], 2, '0', STR_PAD_LEFT)); ?></span>
                </a>
                <a href="<?php echo e(route('admin.notifications.index', ['read' => 1])); ?>" class="d-flex justify-content-between text-decoration-none small">
                    <span class="text-secondary">Read</span>
                    <span class="fw-semibold"><?php echo e(str_pad($notificationBreakdown['Read'], 2, '0', STR_PAD_LEFT)); ?></span>
                </a>
            </div>
        </div>
    </div>

    
    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card stat-card">
                <div class="p-3 border-bottom">
                    <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Recent Applications</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Applicant</th><th>Type</th><th>Reference</th><th>Date</th><th>Status</th><th class="text-end">Action</th></tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $recentApplications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="small fw-semibold"><?php echo e($item['applicant']); ?></td>
                                    <td class="small text-secondary"><?php echo e($item['type']); ?></td>
                                    <td class="small"><?php echo e($item['reference']); ?></td>
                                    <td class="small"><?php echo e($item['date']?->format('d M Y') ?? '—'); ?></td>
                                    <td><span class="badge bg-primary-subtle text-primary-emphasis"><?php echo e($item['status']); ?></span></td>
                                    <td class="text-end">
                                        <?php if($item['url']): ?>
                                            <a href="<?php echo e($item['url']); ?>" class="btn btn-sm btn-outline-secondary">View</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="6" class="text-center text-secondary py-4">No recent activity.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card stat-card">
                <div class="p-3 border-bottom">
                    <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Recent Payments</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Payer</th><th>Type</th><th>Invoice #</th><th>Amount</th><th>Date</th><th>Status</th><th class="text-end">Action</th></tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $recentPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php $transaction = $payment->transactions->first(); ?>
                                <tr>
                                    <td class="small fw-semibold"><?php echo e($payment->student?->name ?? '—'); ?></td>
                                    <td class="small text-secondary"><?php echo e($payment->computed_category); ?></td>
                                    <td class="small">
                                        <?php if($payment->invoice): ?>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#dash-invoice-<?php echo e($payment->id); ?>" class="fw-semibold"><?php echo e($payment->invoice->invoice_number); ?></a>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td class="small fw-semibold"><?php echo e($payment->currency); ?> <?php echo e(number_format($payment->amount, 2)); ?></td>
                                    <td class="small"><?php echo e($payment->created_at->format('d M Y')); ?></td>
                                    <td><span class="badge <?php echo e(match($payment->status) { 'confirmed' => 'bg-success-subtle text-success-emphasis', 'failed' => 'bg-danger-subtle text-danger-emphasis', 'refunded' => 'bg-secondary-subtle text-secondary-emphasis', default => 'bg-warning-subtle text-warning-emphasis' }); ?>"><?php echo e(ucfirst($payment->status)); ?></span></td>
                                    <td class="text-end">
                                        <a href="<?php echo e(route('admin.payments-management.index')); ?>" class="btn btn-sm btn-outline-secondary">Manage</a>

                                        
                                        <?php if($payment->invoice): ?>
                                            <div class="modal fade" id="dash-invoice-<?php echo e($payment->id); ?>" tabindex="-1">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Invoice <?php echo e($payment->invoice->invoice_number); ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row g-3 mb-3">
                                                                <div class="col-6">
                                                                    <div class="small text-secondary">Customer</div>
                                                                    <div class="fw-semibold"><?php echo e($payment->invoice->student?->name ?? '—'); ?></div>
                                                                    <div class="small text-secondary"><?php echo e($payment->invoice->student?->email); ?></div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="small text-secondary">Related Application</div>
                                                                    <div class="fw-semibold small">
                                                                        <?php if($payment->invoice->visaApplication): ?>
                                                                            Visa — <?php echo e($payment->invoice->visaApplication->destination_country); ?>

                                                                        <?php elseif($payment->invoice->studyApplication): ?>
                                                                            Study Application — <?php echo e($payment->invoice->studyApplication->student?->name); ?>

                                                                        <?php elseif($payment->invoice->jobApplication): ?>
                                                                            Job Application — <?php echo e($payment->invoice->jobApplication->jobSeeker?->name); ?>

                                                                        <?php else: ?>
                                                                            General — not tied to a specific application
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <table class="table table-sm mb-3">
                                                                <thead class="table-light">
                                                                    <tr><th>Description</th><th class="text-end">Qty</th><th class="text-end">Unit Price</th><th class="text-end">Total</th></tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $__currentLoopData = $payment->invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <tr>
                                                                            <td class="small"><?php echo e($item->description); ?></td>
                                                                            <td class="text-end small"><?php echo e($item->quantity); ?></td>
                                                                            <td class="text-end small"><?php echo e($payment->invoice->currency); ?> <?php echo e(number_format($item->unit_price, 2)); ?></td>
                                                                            <td class="text-end small"><?php echo e($payment->invoice->currency); ?> <?php echo e(number_format($item->line_total, 2)); ?></td>
                                                                        </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </tbody>
                                                            </table>
                                                            <div class="row g-2 small">
                                                                <div class="col-6 text-secondary">Total Amount</div>
                                                                <div class="col-6 text-end fw-semibold"><?php echo e($payment->invoice->currency); ?> <?php echo e(number_format($payment->invoice->total, 2)); ?></div>
                                                                <div class="col-6 text-secondary">Amount Paid</div>
                                                                <div class="col-6 text-end text-success fw-semibold"><?php echo e($payment->invoice->currency); ?> <?php echo e(number_format($payment->invoice->amount_paid, 2)); ?></div>
                                                                <div class="col-6 text-secondary">Balance / Pending</div>
                                                                <div class="col-6 text-end fw-semibold <?php echo e($payment->invoice->balance > 0 ? 'text-danger' : 'text-success'); ?>"><?php echo e($payment->invoice->currency); ?> <?php echo e(number_format($payment->invoice->balance, 2)); ?></div>
                                                                <div class="col-6 text-secondary">Due Date</div>
                                                                <div class="col-6 text-end"><?php echo e($payment->invoice->due_date?->format('d M Y') ?? '—'); ?></div>
                                                                <div class="col-6 text-secondary">Invoice Status</div>
                                                                <div class="col-6 text-end">
                                                                    <span class="badge <?php echo e(match($payment->invoice->status) { 'paid' => 'bg-success-subtle text-success-emphasis', 'sent' => 'bg-primary-subtle text-primary-emphasis', 'overdue' => 'bg-danger-subtle text-danger-emphasis', 'cancelled' => 'bg-secondary-subtle text-secondary-emphasis', default => 'bg-warning-subtle text-warning-emphasis' }); ?>"><?php echo e(ucfirst($payment->invoice->status)); ?></span>
                                                                </div>
                                                                <?php if($transaction): ?>
                                                                    <div class="col-6 text-secondary">Transaction Code</div>
                                                                    <div class="col-6 text-end fw-semibold"><?php echo e($transaction->gateway_reference ?? '—'); ?></div>
                                                                    <div class="col-6 text-secondary">Payment Method</div>
                                                                    <div class="col-6 text-end text-capitalize"><?php echo e(str_replace('_', ' ', $payment->method)); ?></div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a href="<?php echo e(route('admin.payments-management.invoices.download', $payment->invoice)); ?>" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-download"></i> Download</a>
                                                            <a href="<?php echo e(route('admin.payments-management.invoices.download', $payment->invoice)); ?>" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-print"></i> Print</a>
                                                            <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="7" class="text-center text-secondary py-4">No recent payments.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal91fdd17964e43374ae18c674f95cdaa3)): ?>
<?php $attributes = $__attributesOriginal91fdd17964e43374ae18c674f95cdaa3; ?>
<?php unset($__attributesOriginal91fdd17964e43374ae18c674f95cdaa3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal91fdd17964e43374ae18c674f95cdaa3)): ?>
<?php $component = $__componentOriginal91fdd17964e43374ae18c674f95cdaa3; ?>
<?php unset($__componentOriginal91fdd17964e43374ae18c674f95cdaa3); ?>
<?php endif; ?>
<?php /**PATH C:\wamp64\www\altura-workforce\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>