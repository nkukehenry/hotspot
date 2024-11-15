<?php
namespace App\Helpers;

class CodeGenerator
{
    public static function generateUniqueCode($model, $length = 8)
    {
        do {
            $code = strtoupper(bin2hex(random_bytes($length / 2))); // Generate a random code
        } while ($model::where('code', $code)->exists()); // Ensure it's unique

        return $code;
    }
}
