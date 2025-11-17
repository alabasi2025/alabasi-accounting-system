<tr>
    <td style="padding-right: {{ $level * 20 }}px;">
        {{ $account->account_code }}
    </td>
    <td>
        @if($account->is_main)
            <strong>{{ $account->name }}</strong>
            <span class="badge bg-primary">رئيسي</span>
        @else
            {{ $account->name }}
            <span class="badge bg-success">فرعي</span>
        @endif
    </td>
    <td>
        @if($account->accountType)
            {{ $account->accountType->name }}
        @endif
        @if($account->analyticalAccountType)
            <span class="badge bg-info">{{ $account->analyticalAccountType->name }}</span>
        @endif
    </td>
    <td>
        @if($account->is_active)
            <span class="badge bg-success">نشط</span>
        @else
            <span class="badge bg-secondary">غير نشط</span>
        @endif
    </td>
</tr>
@foreach($account->children as $child)
    @include('setup.partials.account-tree-item', ['account' => $child, 'level' => $level + 1])
@endforeach
