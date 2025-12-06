<?php

return [

    'validation' => [
        'error' => 'Error de validación. Por favor, revisa los campos proporcionados.',
        'required' => 'El campo :field es obligatorio.',
    ],

    'user' => [
        'registered_success' => 'Usuario registrado exitosamente.',
        'EMAIL_ALREADY_EXISTS' => 'El correo electrónico que has teclado ya existe. Debes elegir otro',
        'INVALID_CREDENTIALS' => 'Las credenciales proporcionadas son incorrectas.',
        'INVALID_USER_ID_FORMAT' => 'El ID de usuario tiene un formato inválido.',
        'EMPTY_USER_ID' => 'Error interno: ID de usuario no generado.',
        
        // Campos vacíos
        'EMPTY_EMAIL' => 'El campo email es obligatorio.',
        'EMPTY_PASSWORD' => 'El campo password es obligatorio.',
        'EMPTY_NAME' => 'El campo name es obligatorio.',
        
        // Formatos incorrectos
        'INVALID_EMAIL_FORMAT' => 'El correo electrónico tiene un formato inválido. Debe tener el formato email@email.com',
        'INVALID_USER_NAME' => 'El nombre debe tener entre 3 y 100 caracteres.',
        'INVALID_PASSWORD' => 'La contraseña debe contener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.',
        'PASSWORD_CONFIRMATION_MISMATCH' => 'La confirmación de la contraseña no coincide.',
        
        // Roles
        'EMPTY_ROLE' => 'El rol no puede estar vacío.',
        'INVALID_ROLE' => 'El rol proporcionado no es válido.',
        'INVALID_ROLE_TYPE' => 'Todos los roles deben ser instancias válidas de UserRole.',
        'INVALID_ROLE_FOR_REGISTRATION' => 'Solo puedes registrarte como cliente o follower. Los administradores son designados por clientes.',
        'ROLES_REQUIRED' => 'Debes seleccionar al menos un rol.',
    ],

    'unexpected_error' => 'Ha ocurrido un error inesperado. Por favor, inténtalo de nuevo más tarde.',
];