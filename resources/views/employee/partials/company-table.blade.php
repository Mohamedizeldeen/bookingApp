<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-light">
            <tr>
                <th>Company</th>
                <th>Subscription Status</th>
                <th>Payment Info</th>
                <th>Next Due</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($companies as $company)
            <tr class="{{ $company->is_blocked ? 'table-danger' : ($company->subscription_status === 'active' ? 'table-light' : ($company->subscription_status === 'expired' ? 'table-warning' : 'table-info')) }}">
                <td>
                    <div>
                        <strong>{{ $company->company_name }}</strong>
                        @if($company->is_blocked)
                            <span class="badge badge-danger ml-2">BLOCKED</span>
                        @endif
                        <br>
                        <small class="text-muted">
                            {{ $company->contact_email }}<br>
                            {{ $company->users_count }} users | {{ $company->services_count }} services
                        </small>
                    </div>
                </td>
                <td>
                    <span class="badge badge-{{ $company->subscription_status === 'active' ? 'success' : ($company->subscription_status === 'expired' ? 'danger' : 'warning') }} badge-lg">
                        {{ ucfirst($company->subscription_status) }}
                    </span>
                    <br>
                    <small class="text-muted">${{ $company->monthly_fee }}/month</small>
                </td>
                <td>
                    @if($company->last_payment_date)
                        <strong>Last Payment:</strong><br>
                        {{ $company->last_payment_date->format('M d, Y') }}<br>
                        <small class="text-muted">{{ $company->last_payment_date->diffForHumans() }}</small>
                    @else
                        <span class="text-muted">No payments recorded</span>
                    @endif
                </td>
                <td>
                    @if($company->next_payment_due)
                        {{ $company->next_payment_due->format('M d, Y') }}<br>
                        <small class="text-muted {{ $company->next_payment_due->isPast() ? 'text-danger font-weight-bold' : 'text-success' }}">
                            {{ $company->next_payment_due->diffForHumans() }}
                            @if($company->next_payment_due->isPast())
                                ⚠️ OVERDUE
                            @endif
                        </small>
                    @else
                        <span class="text-muted">Not set</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group-vertical btn-group-sm" role="group">
                        <a href="{{ route('employee.company.view', $company) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        
                        @if($company->subscription_status !== 'active' || $company->next_payment_due && $company->next_payment_due->isPast())
                            <form action="{{ route('employee.company.mark-payment', $company) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Mark payment as received for {{ $company->company_name }}?')">
                                    <i class="fas fa-check"></i> Mark Paid
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    No companies found in this category.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
