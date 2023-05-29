<?php

namespace App\Http\Controllers;

use App\Jobs\newExpenseNotification;
use App\Models\Expense;
use App\Notifications\NewExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $expenses = Expense::where('user_id', auth()->user()->id)->paginate(10);
        return $this->success($expenses);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate( $request,
            [
                'description' => 'required|string',
                'date'        => 'required|date',
                'amount'       => 'required|numeric',
            ],
            [
                'description.required' => 'Description is required',
                'date.required'        => 'Date is required',
                'amount.required'       => 'amount is required',
            ]
        );

        try {
            DB::beginTransaction();

            $user = auth('api')->user();
            
            $expense = new Expense();
            $expense->description = $request->description;
            $expense->date = $request->date;
            $expense->user_id = $user->id;
            $expense->amount = $request->amount;
            $expense->save();

            DB::commit();
            $user->notify( new NewExpense( $expense, $user ) );
            
            return $this->success($expense, 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->error('Error creating expense', 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $expense = Expense::find($id);

        $this->authorize('view', $expense);

        if (!$expense) {
            return $this->error('Expense not found', 404);
        }
        return $this->success($expense);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate( $request,
            [
                'description' => 'required|string',
                'date'        => 'required|date',
                'amount'       => 'required|numeric',
            ],
            [
                'description.required' => 'Description is required',
                'date.required'        => 'Date is required',
                'amount.required'       => 'amount is required',
            ]
        );

        try {
            DB::beginTransaction();

            $expense = Expense::find($id);
            if (!$expense) {
                return  $this->error('Expense not found', 404);
            }

            $this->authorize('update', $expense);

            $expense->description = $request->description;
            $expense->date = $request->date;
            $expense->user_id = auth('api')->user()->id;
            $expense->amount = $request->amount;
            $expense->save();

            DB::commit();
            return $this->success($expense);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->error('Error updating expense', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $expense = Expense::find($id);
            if (!$expense) {
                return  $this->error('Expense not found', 404);
            }

            $this->authorize('delete', $expense);

            $expense->delete();

            DB::commit();

            return $this->success('Expense deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->error('Error deleting expense', 500);
        }
    }
}
