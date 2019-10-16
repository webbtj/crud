<?php

return [
    'models' => [
        // Add your model class names here (full namespace)
        // Exmaple: "App\\Employee"

        // Add a multi dimentional array to configure excluded, readonly,
        // included fields on standard views as well as validation.

            /* Example:
            [
                'model' => "App\\Employee",
                'show' => [
                    'exclude' => ['field_name'],
                ],
                'edit' => [
                    'exclude' => ['field_name'],
                    'readonly' => ['field_name'],
                ],
                'create' => [
                    'exclude' => ['field_name'],
                    'include' => ['updated_at'],
                    'readonly' => ['field_name'],
                ],
                'index' => [
                    'include' => ['id', 'first_name', 'last_name', 'email', 'enabled'],
                ],
                'update' => [
                    'validation' => [
                        'first_name' => 'required',
                        'last_name' => 'required',
                    ],
                ],
                'store' => [
                    'validation' => [
                        'first_name' => 'required',
                        'last_name' => 'required',
                    ],
                ]
            ]
            */

       // Certain fields (id, created_at, updated_at) are automatically
       // readonly / removed from forms. These can be overridden by including
       // them in "include".
       // If "include" is populated with anything other than these three fields
       // only fields in "include" will be displayed. This does not apply to
       // "index". "index" only displays fields in "include".
    ]
];
