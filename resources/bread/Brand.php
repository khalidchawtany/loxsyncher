<?php

return [

    // do not add trailing slashes
    'paths' => [
        'stubs'       => 'vendor/kjdion84/laraback/resources/bread/stubs/default',
        'controller'  => 'App/Http/Controllers',
        'model'       => 'app',
        'factory'     => 'database/factories',
        'seed'        => 'database/seeds',
        'views'       => 'resources/views',
        'request'     => 'App/Http/Requests',
        'dashboard'   => 'resources/views/layouts/dashboard.blade.php',
        'navbar'      => 'resources/views/vendor/laraback/layouts/app.blade.php',
        'routes'      => 'routes/web.php',
        'permissions' => 'resources/views/user_manager/permission_rows.blade.php',
        'home_icon'   => 'resources/views/home/index.blade.php',
        'home_icon_css' => 'public/jeasyui/themes/icon.css',
    ],

	'options' => [
		'model_dialog'  => true,
	],

    // model attribute definitions
    'attributes' => [
        'name' => [
            'schema'          => "string('bread_attribute_name')->nullable()",
            'factory'         => 'name',
            'rule_store'      => 'bail|string|required',
            'rule_update'     => 'bail|string|required',
			'datagrid_column' => 'readonly_field',
        ],
        'company' => [
            'schema'          => "string('bread_attribute_name')->nullable()",
            'factory'         => 'name',
            'rule_store'      => 'bail|string|required',
            'rule_update'     => 'bail|string|required',
			'datagrid_column' => 'readonly_field',
        ],
        'product_id' => [
            'schema'          => "unsignedInteger('bread_attribute_name')",
            'foreign'         => "foreign('product_id')->references('id')->on('products')",
            'rule_store'      => 'bail|numeric|required',
            'rule_update'     => 'bail|numeric|nullable',
			'datagrid_column' => 'readonly_field',
			'as'              => 'products.kurdish_name',
        ],
        'note' => [
            'schema'          => "text('bread_attribute_name')->nullable()",
            'factory'         => 'text',
            'rule_store'      => 'bail|string|nullable',
            'rule_update'     => 'bail|string|nullable',
			'datagrid_column' => 'readonly_field',
        ],
        'user_id' => [
            'schema'          => "unsignedInteger('bread_attribute_name')",
            'foreign'         => "foreign('user_id')->references('id')->on('users')",
			'as'              => 'users.kurdish_name',
			'datagrid_column' => 'readonly_field',
        ],
    ],

];
