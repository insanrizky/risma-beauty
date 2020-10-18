<?php

namespace App\Actions\Fortify;

use App\Models\Team;
use App\Models\User;
use App\Models\UserDetail;
use App\Validators\UplineValidationRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'type' => ['required', 'in:AGENT,RESELLER'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'upline_identifier' => [new UplineValidationRule($input)],
        ])->validate();

        return DB::transaction(function () use ($input) {
            return tap(User::create([
                'type' => $input['type'],
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]), function (User $user) use ($input) {
                $this->createTeam($user);
                $this->createUserDetails($user, $input);
            });
        });
    }

    /**
     * Create a user details for the user.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function createUserDetails(User $user, $input)
    {
        $payload = [
            'user_id' => $user->id,
            'status' => config('global.status.in_registration'),
        ];
        if ($user->type === config('global.type.reseller')) {
            $payload['upline_identifier'] = $input['upline_identifier'];
        }
        UserDetail::create($payload);
    }

    /**
     * Create a personal team for the user.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function createTeam(User $user)
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }
}
