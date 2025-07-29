<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use App\Models\ExpenseAmount;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $admin = auth('admin')->user();
        $companyId = $admin->company_id;
        
        if (!$companyId) {
            return redirect()->back()->with('error', '管理者ユーザーに会社IDが設定されていません。');
        }
        
        $categories = ExpenseCategory::where('company_id', $companyId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.expense-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,variable',
        ]);

        $admin = auth('admin')->user();
        $companyId = $admin->company_id;
        
        if (!$companyId) {
            return redirect()->back()->with('error', '管理者ユーザーに会社IDが設定されていません。');
        }
        
        ExpenseCategory::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'type' => $request->type,
            'sort_order' => ExpenseCategory::where('company_id', $companyId)->max('sort_order') + 1,
        ]);

        return redirect()->back()->with('success', '経費項目を追加しました。');
    }

    public function storeAmount(Request $request)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $admin = auth('admin')->user();
        $companyId = $admin->company_id;
        
        if (!$companyId) {
            return redirect()->back()->with('error', '管理者ユーザーに会社IDが設定されていません。');
        }

        // 既存の金額レコードを更新または新規作成
        ExpenseAmount::updateOrCreate(
            [
                'company_id' => $companyId,
                'expense_category_id' => $request->expense_category_id,
                'effective_date' => now()->toDateString(),
            ],
            [
                'amount' => $request->amount,
            ]
        );

        return response()->json(['success' => true, 'message' => '経費金額を保存しました。']);
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,variable',
        ]);

        $expenseCategory->update([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        return redirect()->back()->with('success', '経費項目を更新しました。');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->delete();
        return redirect()->back()->with('success', '経費項目を削除しました。');
    }

    public function toggleActive(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->update([
            'is_active' => !$expenseCategory->is_active
        ]);

        return redirect()->back()->with('success', '経費項目の状態を更新しました。');
    }
}
