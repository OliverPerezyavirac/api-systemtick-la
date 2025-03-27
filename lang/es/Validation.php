<?php

return [
    'attributes' => [
        'first_name' => 'nombre',
        'last_name' => 'apellido',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'avatar_url' => 'URL del avatar',
    ],
    'custom' => [
        'first_name' => [
            'required' => 'El campo :attribute es obligatorio.',
        ],
        'last_name' => [
            'required' => 'El campo :attribute es obligatorio.',
        ],
        'email' => [
            'required' => 'El campo :attribute es obligatorio.',
            'unique' => 'El :attribute ya está en uso.',
        ],
        'password' => [
            'required' => 'El campo :attribute es obligatorio.',
            'confirmed' => 'La confirmación de la contraseña no coincide.',
            'regex' => 'La :attribute debe contener al menos una letra mayúscula, un número y un carácter especial.',
        ],
        'avatar_url' => [
            'ends_with' => 'El campo :attribute debe ser una URL válida de imagen (jpg, jpeg, png, gif).',
        ],
    ],
];
