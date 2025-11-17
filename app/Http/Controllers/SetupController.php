<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Unit;
use App\Models\Company;
use App\Models\Branch;
use App\Models\AccountType;
use App\Models\AnalyticalAccountType;
use App\Models\Account;

class SetupController extends Controller
{
    public function index()
    {
        // عرض الهيكلية الحالية
        $units = Unit::with(['companies.branches'])->get();
        $companies = Company::with(['unit', 'branches', 'accountTypes', 'analyticalAccountTypes', 'accounts'])->get();
        
        return view('setup.index', compact('units', 'companies'));
    }

    public function execute(Request $request)
    {
        try {
            DB::beginTransaction();

            // 1. إنشاء الوحدات
            $unitHd = Unit::create([
                'unit_code' => 'UNIT-HD',
                'unit_name' => 'وحدة أعمال الحديدة',
                'description' => 'وحدة أعمال الحديدة',
                'is_active' => true,
            ]);

            $unitAb = Unit::create([
                'unit_code' => 'UNIT-AB',
                'unit_name' => 'وحدة أعمال العباسي',
                'description' => 'وحدة أعمال العباسي',
                'is_active' => true,
            ]);

            // 2. إنشاء المؤسسات في وحدة أعمال الحديدة
            $comEmp = Company::create([
                'unit_id' => $unitHd->id,
                'company_code' => 'COM-EMP',
                'company_name' => 'مؤسسة أعمال الموظفين',
                'description' => 'مؤسسة أعمال الموظفين',
                'is_active' => true,
            ]);

            $comAcc = Company::create([
                'unit_id' => $unitHd->id,
                'company_code' => 'COM-ACC',
                'company_name' => 'مؤسسة أعمال المحاسب',
                'description' => 'مؤسسة أعمال المحاسب',
                'is_active' => true,
            ]);

            Company::create([
                'unit_id' => $unitHd->id,
                'company_code' => 'COM-SYS',
                'company_name' => 'مؤسسة الأنظمة',
                'description' => 'مؤسسة الأنظمة',
                'is_active' => true,
            ]);

            Company::create([
                'unit_id' => $unitHd->id,
                'company_code' => 'COM-BUD',
                'company_name' => 'مؤسسة الميزانية التقديرية',
                'description' => 'مؤسسة الميزانية التقديرية',
                'is_active' => true,
            ]);

            Company::create([
                'unit_id' => $unitHd->id,
                'company_code' => 'COM-PL',
                'company_name' => 'مؤسسة الأرباح والخسائر',
                'description' => 'مؤسسة الأرباح والخسائر',
                'is_active' => true,
            ]);

            Company::create([
                'unit_id' => $unitHd->id,
                'company_code' => 'COM-MA',
                'company_name' => 'مؤسسة محمدي والعباس',
                'description' => 'مؤسسة محمدي والعباس',
                'is_active' => true,
            ]);

            // 3. إنشاء مؤسسة النقدية في وحدة أعمال العباسي
            $comCash = Company::create([
                'unit_id' => $unitAb->id,
                'company_code' => 'COM-CASH',
                'company_name' => 'مؤسسة النقدية',
                'description' => 'مؤسسة النقدية',
                'is_active' => true,
            ]);

            // 4. إنشاء الفروع في مؤسسة أعمال الموظفين
            $branches = [
                ['branch_code' => 'BR-EMP-MAIN', 'name' => 'الفرع الرئيسي'],
                ['branch_code' => 'BR-EMP-DHM', 'name' => 'فرع الدهمية'],
                ['branch_code' => 'BR-EMP-GHL', 'name' => 'فرع غليل'],
                ['branch_code' => 'BR-EMP-SAB', 'name' => 'فرع الصبالية'],
                ['branch_code' => 'BR-EMP-JAM', 'name' => 'فرع جمال'],
            ];

            foreach ($branches as $branch) {
                Branch::create([
                    'company_id' => $comEmp->id,
                    'branch_code' => $branch['code'],
                    'branch_name' => $branch['name'],
                    'description' => $branch['name'] . ' - أعمال الموظفين',
                    'is_active' => true,
                ]);
            }

            // 5. إنشاء الفروع في مؤسسة أعمال المحاسب
            $branchesAcc = [
                ['branch_code' => 'BR-ACC-MAIN', 'name' => 'الفرع الرئيسي'],
                ['branch_code' => 'BR-ACC-DHM', 'name' => 'فرع الدهمية'],
                ['branch_code' => 'BR-ACC-GHL', 'name' => 'فرع غليل'],
                ['branch_code' => 'BR-ACC-SAB', 'name' => 'فرع الصبالية'],
                ['branch_code' => 'BR-ACC-JAM', 'name' => 'فرع جمال'],
            ];

            foreach ($branchesAcc as $branch) {
                Branch::create([
                    'company_id' => $comAcc->id,
                    'branch_code' => $branch['code'],
                    'branch_name' => $branch['name'],
                    'description' => $branch['name'] . ' - أعمال المحاسب',
                    'is_active' => true,
                ]);
            }

            // 6. إنشاء الفرع الرئيسي في مؤسسة النقدية
            Branch::create([
                'company_id' => $comCash->id,
                'branch_code' => 'BR-CASH-MAIN',
                'name' => 'الفرع الرئيسي',
                'description' => 'الفرع الرئيسي - مؤسسة النقدية',
                'is_active' => true,
            ]);

            // 7. إنشاء أنواع الحسابات في مؤسسة النقدية
            $accTypeAst = AccountType::create([
                'company_id' => $comCash->id,
                'code' => 'AST',
                'name' => 'أصول',
                'nature' => 'debit',
                'description' => 'الأصول المتداولة والثابتة',
                'is_active' => true,
            ]);

            // 8. إنشاء أنواع الحسابات التحليلية في مؤسسة النقدية
            $anaTypeBank = AnalyticalAccountType::create([
                'company_id' => $comCash->id,
                'code' => 'BANK',
                'name' => 'بنك',
                'description' => 'الحسابات البنكية',
                'is_active' => true,
            ]);

            $anaTypeCashier = AnalyticalAccountType::create([
                'company_id' => $comCash->id,
                'code' => 'CASHIER',
                'name' => 'صراف',
                'description' => 'الصرافين',
                'is_active' => true,
            ]);

            $anaTypeWallet = AnalyticalAccountType::create([
                'company_id' => $comCash->id,
                'code' => 'WALLET',
                'name' => 'محفظة',
                'description' => 'المحافظ الإلكترونية',
                'is_active' => true,
            ]);

            $anaTypeCash = AnalyticalAccountType::create([
                'company_id' => $comCash->id,
                'code' => 'CASH',
                'name' => 'صندوق',
                'description' => 'صناديق العباسي',
                'is_active' => true,
            ]);

            // 9. إنشاء دليل الحسابات في مؤسسة النقدية

            // الحساب الرئيسي: الأصول
            $accAssets = Account::create([
                'company_id' => $comCash->id,
                'account_code' => '1000',
                'name' => 'الأصول',
                'account_type_id' => $accTypeAst->id,
                'is_main' => true,
                'description' => 'الأصول الرئيسية',
                'is_active' => true,
            ]);

            // الحساب الرئيسي: الأصول المتداولة
            $accCurrentAssets = Account::create([
                'company_id' => $comCash->id,
                'account_code' => '1100',
                'name' => 'الأصول المتداولة',
                'account_type_id' => $accTypeAst->id,
                'parent_id' => $accAssets->id,
                'is_main' => true,
                'description' => 'الأصول المتداولة',
                'is_active' => true,
            ]);

            // الحساب الرئيسي: النقدية
            $accCash = Account::create([
                'company_id' => $comCash->id,
                'account_code' => '1110',
                'name' => 'النقدية',
                'account_type_id' => $accTypeAst->id,
                'parent_id' => $accCurrentAssets->id,
                'is_main' => true,
                'description' => 'النقدية والأرصدة البنكية',
                'is_active' => true,
            ]);

            // الحسابات الفرعية
            Account::create([
                'company_id' => $comCash->id,
                'account_code' => '1111',
                'name' => 'البنوك',
                'account_type_id' => $accTypeAst->id,
                'analytical_account_type_id' => $anaTypeBank->id,
                'parent_id' => $accCash->id,
                'is_main' => false,
                'description' => 'الحسابات البنكية',
                'is_active' => true,
            ]);

            Account::create([
                'company_id' => $comCash->id,
                'account_code' => '1112',
                'name' => 'الصرافين',
                'account_type_id' => $accTypeAst->id,
                'analytical_account_type_id' => $anaTypeCashier->id,
                'parent_id' => $accCash->id,
                'is_main' => false,
                'description' => 'حسابات الصرافين',
                'is_active' => true,
            ]);

            Account::create([
                'company_id' => $comCash->id,
                'account_code' => '1113',
                'name' => 'المحافظ',
                'account_type_id' => $accTypeAst->id,
                'analytical_account_type_id' => $anaTypeWallet->id,
                'parent_id' => $accCash->id,
                'is_main' => false,
                'description' => 'المحافظ الإلكترونية',
                'is_active' => true,
            ]);

            Account::create([
                'company_id' => $comCash->id,
                'account_code' => '1114',
                'name' => 'صناديق العباسي',
                'account_type_id' => $accTypeAst->id,
                'analytical_account_type_id' => $anaTypeCash->id,
                'parent_id' => $accCash->id,
                'is_main' => false,
                'description' => 'صناديق العباسي النقدية',
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->route('setup.index')->with('success', '✅ تم إنشاء الهيكلية بنجاح!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('setup.index')->with('error', '❌ حدث خطأ: ' . $e->getMessage());
        }
    }

    public function reset(Request $request)
    {
        try {
            DB::beginTransaction();

            // حذف كل البيانات
            DB::table('accounts')->delete();
            DB::table('analytical_account_types')->delete();
            DB::table('account_types')->delete();
            DB::table('branches')->delete();
            DB::table('companies')->delete();
            DB::table('units')->delete();

            DB::commit();

            return redirect()->route('setup.index')->with('success', '✅ تم حذف جميع البيانات بنجاح!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('setup.index')->with('error', '❌ حدث خطأ: ' . $e->getMessage());
        }
    }
}
