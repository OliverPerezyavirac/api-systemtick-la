<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validación Líneas de Idioma
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas de idioma contienen los mensajes de error por defecto
    | utilizados por la clase validator. Algunas de estas reglas tienen múltiples
    | versiones como las reglas de tamaño. Puedes ajustar cada uno de estos mensajes
    | aquí.
    |
    */

    // Mensajes de validación
    'accepted' => 'El :attribute debe ser aceptado.',
    'accepted_if' => 'El :attribute debe ser aceptado cuando :other sea :value.',
    'active_url' => 'El :attribute no es una URL válida.',
    'after' => 'El :attribute debe ser una fecha después de :date.',
    'after_or_equal' => 'El :attribute debe ser una fecha después o igual a :date.',
    'alpha' => 'El :attribute debe contener solo letras.',
    'alpha_dash' => 'El :attribute debe contener solo letras, números, guiones y guiones bajos.',
    'alpha_num' => 'El :attribute debe contener solo letras y números.',
    'array' => 'El :attribute debe ser un array.',
    'ascii' => 'El :attribute debe contener solo caracteres alfanuméricos y símbolos de un solo byte.',
    'before' => 'El :attribute debe ser una fecha antes de :date.',
    'before_or_equal' => 'El :attribute debe ser una fecha antes o igual a :date.',

    // Mensajes de validación para between
    'between' => [
        'array' => 'El :attribute debe tener entre :min y :max elementos.',
        'file' => 'El :attribute debe estar entre :min y :max kilobytes.',
        'numeric' => 'El :attribute debe estar entre :min y :max.',
        'string' => 'El :attribute debe estar entre :min y :max caracteres.',
    ],

    // Mensajes de validación para boolean
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'current_password' => 'La contraseña es incorrecta.',

    // Mensajes de validación para date
    'date' => 'El :attribute no es una fecha válida.',
    'date_equals' => 'El :attribute debe ser una fecha igual a :date.',
    'date_format' => 'El :attribute no coincide con el formato :format.',

    // Mensajes de validación para decimal
    'decimal' => 'El :attribute debe tener :decimal decimales.',
    'declined' => 'El :attribute debe ser rechazado.',
    'declined_if' => 'El :attribute debe ser rechazado cuando :other sea :value.',

    // Mensajes de validación para different
    'different' => 'El :attribute y :other deben ser diferentes.',
    'digits' => 'El :attribute debe tener :digits dígitos.',
    'digits_between' => 'El :attribute debe estar entre :min y :max dígitos.',

    // Mensajes de validación para dimensions
    'dimensions' => 'El :attribute tiene dimensiones de imagen inválidas.',
    'distinct' => 'El campo :attribute tiene un valor duplicado.',

    // Mensajes de validación para doesnt_end_with
    'doesnt_end_with' => 'El :attribute no puede terminar con uno de los siguientes: :values.',

    // Mensajes de validación para doesnt_start_with
    'doesnt_start_with' => 'El :attribute no puede empezar con uno de los siguientes: :values.',

    // Mensajes de validación para email
    'email' => 'El :attribute debe ser una dirección de correo electrónico válida.',

    // Mensajes de validación para ends_with
    'ends_with' => 'El :attribute debe terminar con uno de los siguientes: :values.',

    // Mensajes de validación para enum
    'enum' => 'El :attribute seleccionado es inválido.',

    // Mensajes de validación para exists
    'exists' => 'El :attribute seleccionado es inválido.',

    // Mensajes de validación para file
    'file' => 'El :attribute debe ser un archivo.',

    // Mensajes de validación para filled
    'filled' => 'El campo :attribute debe tener un valor.',

    // Mensajes de validación para gt
    'gt' => [
        'array' => 'El :attribute debe tener más de :value elementos.',
        'file' => 'El :attribute debe ser mayor que :value kilobytes.',
        'numeric' => 'El :attribute debe ser mayor que :value.',
        'string' => 'El :attribute debe ser mayor que :value caracteres.',
    ],

    // Mensajes de validación para gte
    'gte' => [
        'array' => 'El :attribute debe tener :value elementos o más.',
        'file' => 'El :attribute debe ser mayor o igual a :value kilobytes.',
        'numeric' => 'El :attribute debe ser mayor o igual a :value.',
        'string' => 'El :attribute debe ser mayor o igual a :value caracteres.',
    ],

    // Mensajes de validación para image
    'image' => 'El :attribute debe ser una imagen.',
    'in' => 'El :attribute seleccionado es inválido.',
    'in_array' => 'El campo :attribute no existe en :other.',
    'integer' => 'El :attribute debe ser un número entero.',
    'ip' => 'El :attribute debe ser una dirección IP válida.',
    'ipv4' => 'El :attribute debe ser una dirección IPv4 válida.',
    'ipv6' => 'El :attribute debe ser una dirección IPv6 válida.',
    'json' => 'El :attribute debe ser una cadena JSON válida.',
    'lowercase' => 'El :attribute debe ser en minúsculas.',

    // Mensajes de validación para lt
    'lt' => [
        'array' => 'El :attribute debe tener menos de :value elementos.',
        'file' => 'El :attribute debe ser menor que :value kilobytes.',
        'numeric' => 'El :attribute debe ser menor que :value.',
        'string' => 'El :attribute debe ser menor que :value caracteres.',
    ],

    // Mensajes de validación para lte
    'lte' => [
        'array' => 'El :attribute no debe tener más de :value elementos.',
        'file' => 'El :attribute debe ser menor o igual a :value kilobytes.',
        'numeric' => 'El :attribute debe ser menor o igual a :value.',
        'string' => 'El :attribute debe ser menor o igual a :value caracteres.',
    ],

    // Mensajes de validación para mac_address
    'mac_address' => 'El :attribute debe ser una dirección MAC válida.',
    'max' => [
        'array' => 'El :attribute no debe tener más de :max elementos.',
        'file' => 'El :attribute no debe ser mayor que :max kilobytes.',
        'numeric' => 'El :attribute no debe ser mayor que :max.',
        'string' => 'El :attribute no debe ser mayor que :max caracteres.',
    ],
    'max_digits' => 'El :attribute no debe tener más de :max dígitos.',
    'mimes' => 'El :attribute debe ser un archivo de tipo: :values.',
    'mimetypes' => 'El :attribute debe ser un archivo de tipo: :values.',

    // Mensajes de validación para min
    'min' => [
        'array' => 'El :attribute debe tener al menos :min elementos.',
        'file' => 'El :attribute debe tener al menos :min kilobytes.',
        'numeric' => 'El :attribute debe tener al menos :min.',
        'string' => 'El :attribute debe tener al menos :min caracteres.',
    ],

    // Mensajes de validación para min_digits
    'min_digits' => 'El :attribute debe tener al menos :min dígitos.',
    'missing' => 'El campo :attribute debe estar ausente.',
    'missing_if' => 'El campo :attribute debe estar ausente cuando :other sea :value.',
    'missing_unless' => 'El campo :attribute debe estar ausente a menos que :other sea :value.',
    'missing_with' => 'El campo :attribute debe estar ausente cuando :values esté presente.',
    'missing_with_all' => 'El campo :attribute debe estar ausente cuando :values estén presentes.',
    'multiple_of' => 'El :attribute debe ser un múltiplo de :value.',
    'not_in' => 'El :attribute seleccionado es inválido.',
    'not_regex' => 'El formato de :attribute es inválido.',
    'numeric' => 'El :attribute debe ser un número.',

    // Mensajes de validación para password
    'password' => [
        'letters' => 'El :attribute debe contener al menos una letra.',
        'mixed' => 'El :attribute debe contener al menos una letra mayúscula y una letra minúscula.',
        'numbers' => 'El :attribute debe contener al menos un número.',
        'symbols' => 'El :attribute debe contener al menos un símbolo.',
        'uncompromised' => 'El :attribute proporcionado ha aparecido en una filtración de datos. Por favor, elija un :attribute diferente.',
    ],

    // Mensajes de validación para present
    'present' => 'El campo :attribute debe estar presente.',
    'prohibited' => 'El campo :attribute está prohibido.',
    'prohibited_if' => 'El campo :attribute está prohibido cuando :other sea :value.',
    'prohibited_unless' => 'El campo :attribute está prohibido a menos que :other sea :values.',
    'prohibits' => 'El campo :attribute prohibe que :other esté presente.',
    'regex' => 'El formato de :attribute es inválido.',
    'required' => 'El campo :attribute es requerido.',
    'required_array_keys' => 'El campo :attribute debe contener entradas para: :values.',
    'required_if' => 'El campo :attribute es requerido cuando :other sea :value.',
    'required_if_accepted' => 'El campo :attribute es requerido cuando :other sea aceptado.',
    'required_unless' => 'El campo :attribute es requerido a menos que :other sea :values.',
    'required_with' => 'El campo :attribute es requerido cuando :values esté presente.',
    'required_with_all' => 'El campo :attribute es requerido cuando :values estén presentes.',
    'required_without' => 'El campo :attribute es requerido cuando :values no esté presente.',
    'required_without_all' => 'El campo :attribute es requerido cuando ninguno de :values esté presente.',
    'same' => 'El :attribute y :other deben coincidir.',

    // Mensajes de validación para size
    'size' => [
        'array' => 'El :attribute debe contener :size elementos.',
        'file' => 'El :attribute debe ser :size kilobytes.',
        'numeric' => 'El :attribute debe ser :size.',
        'string' => 'El :attribute debe ser :size caracteres.',
    ],

    // Mensajes de validación para starts_with
    'starts_with' => 'El :attribute debe empezar con uno de los siguientes: :values.',
    'string' => 'El :attribute debe ser una cadena.',
    'timezone' => 'El :attribute debe ser una zona horaria válida.',
    'unique' => 'El :attribute ya ha sido tomado.',
    'uploaded' => 'El :attribute no se pudo subir.',
    'uppercase' => 'El :attribute debe ser en mayúsculas.',
    'url' => 'El :attribute debe ser una URL válida.',
    'ulid' => 'El :attribute debe ser un ULID válido.',
    'uuid' => 'El :attribute debe ser un UUID válido.',

    // Mensajes de validación para custom
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'mensaje personalizado para :attribute',
        ],
    ],

    // Mensajes de validación para attributes
    'attributes' => [
        'first_name' => 'nombre',
        'last_name' => 'apellido',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'avatar_url' => 'URL del avatar',
    ],

];
