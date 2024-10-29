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
        return new Voucher([
            'code' => $row['Username'],
            'package_id' => $this->packageId,
            'is_used' => false,
        ]);
    }
}
