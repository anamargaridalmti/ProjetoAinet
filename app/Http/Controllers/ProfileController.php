<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Mostra o perfil (apenas se for Cliente 'C')
    public function edit()
    {
        if (Auth::user()->user_type !== 'C') {
            abort(403, 'Os funcionários/admins não possuem acesso direto ao perfil.');
        }
        return view('profile');
    }

    // Atualiza os dados e a fotografia do Cliente
    public function update(Request $request)
    {
        $user = Auth::user();

        if ($user->user_type !== 'C') {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'photo' => ['nullable', 'image', 'max:2048'], // máx 2MB
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        // Trata o upload da fotografia (avatar)
        if ($request->hasFile('photo')) {
            // Apaga a foto antiga se existir
            if ($user->photo_url) {
                Storage::delete('public/profiles/' . $user->photo_url);
            }

            $file = $request->file('photo');
            $filename = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/profiles', $filename);

            $user->photo_url = $filename;
        }

        $user->save();

        return back()->with('status', 'Perfil atualizado com sucesso!');
    }
}
