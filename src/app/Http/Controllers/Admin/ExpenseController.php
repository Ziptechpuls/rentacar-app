<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExpenseController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:fixed,variable',
            'category' => 'required|in:rent,utilities,insurance,maintenance,fuel,other',
            'date' => 'required|date',
            'is_recurring' => 'boolean',
        ]);

        $expense = Expense::create([
            'name' => $request->name,
            'description' => $request->description,
            'amount' => $request->amount,
            'type' => $request->type,
            'category' => $request->category,
            'date' => $request->date,
            'is_recurring' => $request->has('is_recurring'),
        ]);

        return response()->json([
            'success' => true,
            'message' => '経費が追加されました',
            'expense' => $expense
        ]);
    }

    public function destroy(Expense $expense): JsonResponse
    {
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => '経費が削除されました'
        ]);
    }
}
