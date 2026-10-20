<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateUser extends Command
{
    protected $signature = 'app:create-user';

    protected $description = 'Buat akun login tanpa password bawaan';

    public function handle(): int
    {
        $input = [
            'name' => $this->ask('Nama'),
            'email' => $this->ask('Email'),
            'password' => $this->secret('Password (minimal 12 karakter)'),
            'password_confirmation' => $this->secret('Konfirmasi password'),
        ];

        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $data = $validator->validated();
        unset($data['password_confirmation']);

        // Pertahankan kompatibilitas dengan kolom wajib pada skema template lama.
        User::create([...$data, 'gender' => 'L']);

        $this->info('Akun berhasil dibuat. Silakan masuk melalui /login.');

        return self::SUCCESS;
    }
}
