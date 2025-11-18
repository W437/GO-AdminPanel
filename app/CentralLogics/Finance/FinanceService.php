<?php

namespace App\CentralLogics\Finance;

use App\CentralLogics\Helpers;
use App\Models\Expense;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    public static function expenseCreate($amount,$type,$datetime,$created_by,$order_id=null,$restaurant_id=null,$user_id=null,$description='',$delivery_man_id=null)
    {
        $expense = new Expense();
        $expense->amount = $amount;
        $expense->type = $type;
        $expense->order_id = $order_id;
        $expense->created_by = $created_by;
        $expense->restaurant_id = $restaurant_id;
        $expense->delivery_man_id = $delivery_man_id;
        $expense->user_id = $user_id;
        $expense->description = $description;
        $expense->created_at = $datetime;
        $expense->updated_at = $datetime;
        return $expense->save();
    }

}
