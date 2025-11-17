@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="bi bi-speedometer2"></i> لوحة التحكم</h2>

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-list-ul"></i> دليل الحسابات</h5>
                    <h2>{{ $stats['total_accounts'] }}</h2>
                    <p class="card-text">إجمالي الحسابات</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-journal-text"></i> القيود اليومية</h5>
                    <h2>{{ $stats['total_journal_entries'] }}</h2>
                    <p class="card-text">إجمالي القيود</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-people"></i> الحسابات التحليلية</h5>
                    <h2>{{ $stats['total_analytical_accounts'] }}</h2>
                    <p class="card-text">العملاء والموردين</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-receipt"></i> السندات</h5>
                    <h2>{{ $stats['total_vouchers'] }}</h2>
                    <p class="card-text">إجمالي السندات</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5><i class="bi bi-clock-history"></i> آخر القيود اليومية</h5>
                </div>
                <div class="card-body">
                    @if($recent_entries->count() > 0)
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>رقم القيد</th>
                                    <th>التاريخ</th>
                                    <th>الوصف</th>
                                    <th>المبلغ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_entries as $entry)
                                <tr>
                                    <td>{{ $entry->entry_number }}</td>
                                    <td>{{ $entry->entry_date->format('Y-m-d') }}</td>
                                    <td>{{ $entry->description }}</td>
                                    <td>{{ number_format($entry->total_debit, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center text-muted">لا توجد قيود حتى الآن</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5><i class="bi bi-exclamation-triangle"></i> القيود والسندات المعلقة</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong>{{ $stats['pending_journal_entries'] }}</strong> قيد يومي في انتظار الاعتماد
                    </div>
                    <div class="alert alert-info">
                        <strong>{{ $stats['pending_vouchers'] }}</strong> سند في انتظار الاعتماد
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
