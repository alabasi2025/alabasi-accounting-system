@extends('layouts.dashboard')

@section('title', 'القيود اليومية')
@section('page-title', 'القيود اليومية')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-book"></i> قائمة القيود اليومية</h5>
        <a href="{{ route('journal-entries.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> قيد جديد
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>التاريخ</th>
                        <th>البيان</th>
                        <th>المبلغ</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries ?? [] as $entry)
                    <tr>
                        <td>{{ $entry->id }}</td>
                        <td>{{ $entry->date }}</td>
                        <td>{{ $entry->description }}</td>
                        <td>{{ number_format($entry->amount ?? 0) }}</td>
                        <td>
                            <span class="badge bg-{{ $entry->status == 'posted' ? 'success' : 'warning' }}">
                                {{ $entry->status == 'posted' ? 'مرحّل' : 'معلق' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('journal-entries.show', $entry->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('journal-entries.edit', $entry->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">لا توجد قيود</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
