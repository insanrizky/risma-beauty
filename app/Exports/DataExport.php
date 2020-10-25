<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DataExport implements FromCollection, WithHeadings
{
    protected $start_date;
    protected $end_date;

    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function headings(): array
    {
        return [
            'ID Klaim',
            'ID AGEN/RESELLER',
            'Nama Lengkap',
            'Tipe',
            'Email',
            'Bank',
            'Nomor Rekening',
            'Poin',
            'Nominal per Poin',
            'Status Klaim',
            'Tanggal',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return DB::table('users')
                ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
                ->leftJoin('banks', 'user_details.bank_id', '=', 'banks.id')
                ->join('claims', 'users.id', '=', 'claims.user_id')
                ->leftJoin('point_settings', 'claims.pointsetting_id', '=', 'point_settings.id')
                ->whereNotNull('user_details.identifier')
                // ->where('claims.status', config('global.claim_status.claimed'))
                ->selectRaw('
                    claims.id as "ID Klaim",
                    user_details.identifier as ID,
                    users.name as "Nama Lengkap",
                    users.type as Tipe,
                    users.email as Email,
                    banks.name as Bank,
                    user_details.account_number as "Nomor Rekening",
                    claims.total_pcs as "Poin",
                    point_settings.amount as "Nominal per Poin",
                    claims.status as "Status Klaim",
                    claims.created_at as "Tanggal"
                ')
                ->orderBy('claims.created_at', 'DESC')
                ->get();
    }
}
