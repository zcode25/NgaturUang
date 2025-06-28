<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\Models\Wallet;
use Maatwebsite\Excel\Concerns\FromCollection;

// class StatementExport implements FromCollection
// {
//     /**
//     * @return \Illuminate\Support\Collection
//     */
//     public function collection()
//     {
//         return Wallet::all();
//     }
// }

class StatementExport implements FromView
{
    protected $statements;
    protected $startDate;
    protected $endDate;

    public function __construct($statements, $startDate, $endDate)
    {
        $this->statements = $statements;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        return view('statement.export', [
            'statements' => $this->statements,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
    }
}
