<?php

return [
    'auth' => [
        'credentials_not_found'             => 'the data entered is incorrect.',
        'not_active'                        => 'this account is deactivated.',
        'is_ban'                            => 'this account has been banned.',
        'login_success'                     => 'you are logged in successfully.',
        'logout_success'                    => 'signed out successfully.',
    ],

    'profile' => [
        'profile_data_updated'              => 'the account information has been modified successfully.',
        'old_password_is_not_correct'       => 'the old password is incorrect.',
        'password_updated_successfully'     => 'the password has been changed successfully.',
    ],

    'error' => [
        'fail'                              => 'something went wrong, please check with the administration.',
        'parent_id_not_true'                => 'paretn id not true.',
        'product_must_contain_one_details'  => 'the product must contain at least one product details.',
        'details_must_contain_one_image'    => 'product details must contain at least one image.',
        'image_required'                    => 'Image field is required',
        'end_date_must_be_after_start_date' => 'end date must be after start date.',
        'discount_value_more_than_product_price' => 'discount value more than product price.',
        'value_id_not_true'                 => 'feature value id not true.',
        'root_not_true'                     => 'Subcategories do not belong to the same main category.',
        'category_id_not_exist'             => 'Subcategories do not exist.',
        'quantity_not_avaliable'            => 'the quantity not available',
        'quantity_not_cover_quantity_required_for_flash_sale' => 'the quantity not available',
    ],

    'delete' => [
        'fail'                              => 'no deletion, try again.',
        'admin'                             => 'the admin has been successfully deleted.',
        'country'                           => 'the country has been successfully deleted.',
        'city'                              => 'the city has been deleted successfully.',
        'district'                          => 'the area has been deleted successfully.',
        'success'                           => 'deleted successfully.',
        'category'                          => 'the category deleted successfuly.'
    ],

    'update' => [
        'fail'                              => 'not modified, try again.',
        'success'                           => 'modified successfully.',
        'country'                           => 'the country has been successfully modified.',
        'city'                              => 'the city has been modified successfully.',
        'district'                          => 'the area has been modified successfully.',
        'category'                          => 'the category has been successfully modified.'
    ],

    'create' => [
        'success'                           => 'added successfully.',
        'fail'                              => 'not saved, try again.',
        'country'                           => 'the country has been added successfully.',
        'city'                              => 'the city has been added successfully.',
        'district'                          => 'the area has been added successfully.',
        'category'                          => 'the category has been added successfully.',
    ],

    'statistics' => [
        'clients'               => 'clients number',
        'products'               => 'products number',
        'total_orders'        => 'total orders',
        'total_revenue'       => 'total revenue',
        'total_prdocuts'      => 'total prdocuts',
        'top_selling_product' => 'top selling product',
        'revenue_chart'  => [
            'revenue'    => 'Revenue',
            'this_year'  => 'This Year',
            'last_year'  => 'Last Year',
        ],
        'revenue_this_month_chart'  => [
            'revenue'    => 'Revenue',
            'this_month'  => 'This Month',
        ],
        'order_status'  => [
            'order_status'    => 'Order Status',
            'pending'         => 'Pending',
            'admin_accepted'  => 'Admin Accepted',
            'admin_rejected'  => 'Admin Rejected',
            'admin_delivered' => 'Admin Delivered'
        ]
    ],

    'notifications' => [
        'orders' => [
            'title' => [
                'admin_accept'    => 'Your order No.:order_id has been approved.',
                'admin_rejected'  => 'Your order No.:order_id has been rejected.',
                'admin_cancel'    => 'Your order No.:order_id has been canceled.',
                'admin_shipping'  => 'Your order No.:order_id has been shipped.',
                'admin_delivered' => 'Your order No.:order has been delivered.',
            ],
            'body'  => [
                'admin_accept'    => 'Your order No.:order_id has been approved.',
                'admin_rejected'  => 'Your order No.:order_id has been rejected.',
                'admin_cancel'    => 'Your order No.:order_id has been canceled.',
                'admin_shipping'  => 'Your order No.:order_id has been shipped.',
                'admin_delivered' => 'Your order No.:order has been delivered.',
            ],
        ],

        'return_order' => [
            'title' => [
                'return_order' => 'Return order number:return_order_id has been reviewed',
            ],
            'body'  => [
                'return_order' => 'Return order number:return_order_id has been reviewed',
            ],
        ],

        'users' => [
            'title' => [
                'new_user' => 'New User',
            ],
            'body'  => [
                'new_user' => 'New User is Registered',
            ],
        ],
    ],
    'noti' => [
        'reply_contact_msg' => 'reply to your message',
    ],

    "permissions" => [
        'admin' => 'admin',
        'profile' => 'profile',
        'country' => 'country',
        'city' => 'city',
        'category' => 'category',
        'brand' => 'brand',
        'productLabel' => 'productLabel',
        'branch' => 'branch',
        'branchArea' => 'branchArea',
        'nutritionFact' => 'nutritionFact',
        'offer' => 'offer',
        'influencer' => 'influencer',
        'coupon' => 'coupon',
        'employee' => 'employee',
        'potencies' => 'potencies',
        'dietaries' => 'dietaries',
        'lifeStages' => 'lifeStages',
        'flavors' => 'flavors',
        'colors' => 'colors',
        'packageSizes' => 'packageSizes',
        'apparelSizes' => 'apparelSizes',
        'sizes' => 'sizes',
        'products' => 'products',
        'productVariants' => 'productVariants',
        'contacts' => 'contacts',
        'about' => 'about',
        'metadata' => 'metadata',
        'settings' => 'settings',
        'statistics' => 'statistics',
        'slider' => 'slider',
        'cancelReason' => 'cancelReason',
        'rejectReason' => 'rejectReason',
        'transferReasons' => 'transferReasons',
        'comingSoon' => 'comingSoon',
        'suppliers' => 'suppliers',
        'block' => 'block',
        'events' => 'events',
        'sections' => 'sections',
        'homeSections' => 'homeSections',
        'role' => 'role',
        'permission' => 'permission',
        'client' => 'client',

        'staticPage' => 'staticPage',
        'questionCategory' => 'questionCategory',
        'faq' => 'faq',
        'order' => 'order',

    ]
];
