<?php

namespace App\Imports;

use App\Models\Voucher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VouchersImport implements ToModel, WithHeadingRow
{
    protected $packageId;

    public function __construct($packageId)
    {
        $this->packageId = $packageId;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
       
        // Check if the voucher code already exists
        $existingVoucher = Voucher::where('code', $row['code'])->first();

        // If the voucher does not exist, create a new one
        if (!$existingVoucher) {
            return new Voucher([
                'code' => $row['code'], // Ensure this matches the header in your Excel file
                'package_id' => $this->packageId,
                'is_used' => false,
            ]);
        }

        return null;
    }
}
