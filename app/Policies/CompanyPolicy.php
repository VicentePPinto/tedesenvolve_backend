<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function access(User $user, Company $company)
    {
        return $user->company_id === $company->id;
    }
}
