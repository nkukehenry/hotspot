<?php

namespace App\Imports;

use App\Models\Voucher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;

use Illuminate\Validation\Rule;
use App\Models\Package;

class VouchersImport implements ToModel, WithValidation
{
    protected $packageId;
    protected $siteId;

    public function __construct($packageId)
    {
        $this->packageId = $packageId;
        $this->siteId = Package::findOrFail($packageId)->site_id;
    }

    public function rules(): array
    {
        return [
            '0' => [
                'required',
                Rule::unique('vouchers', 'code')->where(function ($query) {
                    return $query->where('site_id', $this->siteId);
                }),
            ],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.unique' => 'Voucher is already in the system for this site.',
            '0.required' => 'Voucher code is missing.',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            '0' => 'voucher code',
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Use index 0 for the first column
        $voucherCode = isset($row[0]) ? trim((string)$row[0]) : null;

        if (empty($voucherCode)) {
            return null;
        }

        // Skip if the value looks like a header
        $lowerVoucher = strtolower($voucherCode);
        if (in_array($lowerVoucher, ['voucher', 'code', 'username', 'token', 'vouchers'])) {
            return null;
        }
       
        return new Voucher([
            'code' => $voucherCode,
            'package_id' => $this->packageId,
            'site_id' => $this->siteId,
            'is_used' => false,
        ]);
    }
}
