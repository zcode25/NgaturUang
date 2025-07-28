<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\BudgetDetail;
use App\Models\Category;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index() {
        return view('budget.index', [
            'budgets' => Budget::where('user_id', Auth::id())->orderBy('start_date', 'DESC')->get(),
        ]);
    }

    public function create() {
        return view('budget.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'name.required' => 'Nama budget wajib diisi.',
            'name.max' => 'Nama budget maksimal 255 karakter.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Tanggal mulai harus berupa tanggal yang valid.',
            'end_date.required' => 'Tanggal akhir wajib diisi.',
            'end_date.date' => 'Tanggal akhir harus berupa tanggal yang valid.',
            'end_date.after_or_equal' => 'Tanggal akhir harus setelah atau sama dengan tanggal mulai.',
        ]);

        $validated['user_id'] = Auth::id();
        Budget::create($validated);

        return redirect()->route('budget')->with('success', 'Budget berhasil dibuat.');
    }

    public function edit(Budget $budget) {
        if ($budget->user_id !== Auth::id()) {
            return redirect()->route('budget')->with('error', 'Anda tidak memiliki akses ke budget ini.');
        }
        return view('budget.edit', [
            'budget' => $budget,
        ]);
    }

    public function update(Request $request, Budget $budget) {
        if ($budget->user_id !== Auth::id()) {
            return redirect()->route('budget')->with('error', 'Anda tidak memiliki akses ke budget ini.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'name.required' => 'Nama budget wajib diisi.',
            'name.max' => 'Nama budget maksimal 255 karakter.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Tanggal mulai harus berupa tanggal yang valid.',
            'end_date.required' => 'Tanggal akhir wajib diisi.',
            'end_date.date' => 'Tanggal akhir harus berupa tanggal yang valid.',
            'end_date.after_or_equal' => 'Tanggal akhir harus setelah atau sama dengan tanggal mulai.',
        ]);

        $budget->update($validated);

        return redirect()->route('budget')->with('success', 'Budget berhasil diperbarui.');
    }

    public function detail(Budget $budget, Request $request) {
        if ($budget->user_id !== Auth::id()) {
            return redirect()->route('budget')->with('error', 'Anda tidak memiliki akses ke budget ini.');
        }

        $budgetDetails = BudgetDetail::select('budget_details.*')
                            ->join('categories', 'budget_details.category_id', '=', 'categories.id')
                            ->where('budget_details.budget_id', $budget->id)
                            ->with(['category.expenses' => function ($query) use ($budget) {
                                $query->where('status', 'active')
                                    ->whereBetween('date', [$budget->start_date, $budget->end_date]);
                            }])
                            ->orderBy('categories.name', 'asc')
                            ->get();

        $editDetail = null;
        if ($request->has('edit')) {
            $editDetail = BudgetDetail::find($request->edit);
        }

        return view('budget.detail', [
            'budget' => $budget,
            'categories' => Category::where('user_id', Auth::id())->orderBy('name', 'ASC')->get(),
            'budgetDetails' => $budgetDetails,
            'editDetail' => $editDetail
        ]);
    }

    public function storeDetail(Request $request, Budget $budget) {
        if ($budget->user_id !== Auth::id()) {
            return redirect()->route('budget')->with('error', 'Anda tidak memiliki akses ke budget ini.');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
        ], [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'amount.required' => 'Jumlah wajib diisi.',
            'amount.numeric' => 'Jumlah harus berupa angka.',
            'amount.min' => 'Jumlah tidak boleh kurang dari 0.',
        ]);

        $exists = $budget->budgetDetails()->where('category_id', $validated['category_id'])->exists();

        if ($exists) {
            return redirect()->route('budget.detail', ['budget' => $budget])
                ->with('error', 'Kategori ini sudah ditambahkan ke anggaran.');
        }

        $validated['budget_id'] = $budget->id;
        $budget->budgetDetails()->create($validated);

        return redirect()->route('budget.detail', ['budget' => $budget])->with('success', 'Detail budget berhasil ditambahkan.');
    }

    public function updateDetail(Request $request, Budget $budget, BudgetDetail $budgetDetail) {
        if ($budget->user_id !== Auth::id()) {
            return redirect()->route('budget')->with('error', 'Anda tidak memiliki akses ke budget ini.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ], [
            'amount.required' => 'Jumlah wajib diisi.',
            'amount.numeric' => 'Jumlah harus berupa angka.',
            'amount.min' => 'Jumlah tidak boleh kurang dari 0.',
        ]);

        $budgetDetail->update($validated);

        return redirect()->route('budget.detail', $budget->id)->with('success', 'Detail budget berhasil diperbarui.');
    }

    public function budgetDetail(BudgetDetail $budgetDetail) {

        $budget = $budgetDetail->budget;

        $expenses = Expense::where('category_id', $budgetDetail->category_id)
                    ->where('status', 'active')
                    ->whereBetween('date', [$budget->start_date, $budget->end_date])
                    ->with('category')
                    ->orderByDesc('created_at')
                    ->get();

        $remaining = $budgetDetail->amount - $expenses->sum('amount');

        return view('budget.budgetDetail', [
            'budget' => $budget,
            'budgetDetail' => $budgetDetail,
            'expenses' => $expenses,
            'remaining' => $remaining,
        ]);
    }

    public function budgetDestroy(budgetDetail $budgetDetail) 
    {
        $budget_id = $budgetDetail->budget_id;
        try {
            $budgetDetail->delete();
            return redirect()->route('budget.detail', $budget_id)->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('budget.detail', $budget_id)->with('error', 'Kategori tidak dapat dihapus karena masih digunakan.');
        }
        
    }
}
