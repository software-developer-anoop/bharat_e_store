<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('customers')
            ->select('id', 'customer_name', 'customer_email', 'customer_phone','customer_gender','referral_code','referrer_code','profile_status')
            ->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Phone','Gender','Referral Code','Referrer Code','Profile Status'];
    }
}

