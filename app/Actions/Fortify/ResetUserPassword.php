<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use \App\Concerns\PasswordValidationRules;

    /**
     * Valida e restaura a palavra-passe do utilizador.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'password' => $this->passwordRules(),
        ])->validate();

        // Força a gravação da nova senha encriptada em Bcrypt diretamente
        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
