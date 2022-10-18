<?php

namespace App\Observers;

use App\Models\Cheque;
use App\Models\Rango_Cheque;
use Exception;

class RangoChequeObserver
{
    /**
     * Handle the Cheque "created" event.
     *
     * @param  \App\Models\Cheque  $cheque
     * @return void
     */
    public function created(Cheque $cheque)
    {
        $rango = Rango_Cheque::CuentaRangoCheque($cheque->cuenta_bancaria_id)->first();
        if($cheque->cheque_numero > $rango->rango_fin){
            return throw new Exception('Cheque Fuera de Rango');
        }
       
    }

    /**
     * Handle the Cheque "updated" event.
     *
     * @param  \App\Models\Cheque  $cheque
     * @return void
     */
    public function updated(Cheque $cheque)
    {
        //
    }

    /**
     * Handle the Cheque "deleted" event.
     *
     * @param  \App\Models\Cheque  $cheque
     * @return void
     */
    public function deleted(Cheque $cheque)
    {
        //
    }

    /**
     * Handle the Cheque "restored" event.
     *
     * @param  \App\Models\Cheque  $cheque
     * @return void
     */
    public function restored(Cheque $cheque)
    {
        //
    }

    /**
     * Handle the Cheque "force deleted" event.
     *
     * @param  \App\Models\Cheque  $cheque
     * @return void
     */
    public function forceDeleted(Cheque $cheque)
    {
        //
    }
}
