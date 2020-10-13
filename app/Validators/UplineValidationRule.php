<?php

namespace App\Validators;

use App\Models\UserDetail;
use Illuminate\Contracts\Validation\Rule;

class UplineValidationRule implements Rule
{
    private $input;

    public function __construct(array $input)
    {
        $this->input = $input;
    }

    public function passes($attribute, $value)
    {
        $type = $this->input['type'];

        if ($type === config('global.type.reseller')) {
            $isValidUpline = UserDetail::where('upline_identifier', $value)->first();
            return $isValidUpline;
        }
        return true;
    }

    public function message()
    {
        return 'Agent ID tidak valid!';
    }
}