<?php

return [
    'auth' => [
        'credentials_not_found'             => 'البيانات المدخلة غير صحيحة.',
        'not_active'                        => 'هذا الحساب غير مفعل.',
        'is_ban'                            => 'هذا الحساب تم حظرة.',
        'login_success'                     => 'تم تسجيل الدخول بنجاح.',
        'logout_success'                    => 'تم تسجيل الخروج بنجاح.',
    ],

    'profile' => [
        'profile_data_updated'              => 'تم تعديل بيانات الحساب بنجاح.',
        'old_password_is_not_correct'       => 'كلمة المرور القديمة غير صحيحه.',
        'password_updated_successfully'     => 'تم تغيير كلمة المرور بنجاح.',
    ],

    'error' => [
        'fail'                              => 'حدث خطأ ما برجاء مراجعة الادارة.',
        'parent_id_not_true'                => 'parent id غير صحيح',
        'product_must_contain_one_details'  => 'يجب أن يحتوي المنتج على تفاصيل واحدة على الأقل.',
        'details_must_contain_one_image'    => 'يجب أن تحتوي تفاصيل المنتج على صورة واحدة على الأقل.',
        // 'value_id_not_true'                 => '',
        'image_required'                    => ' الصورة مطلوب.',
        'end_date_must_be_after_start_date' => 'يجب أن يكون تاريخ الانتهاء بعد تاريخ البدء.',
        'discount_value_more_than_product_price' => 'قيمة الخصم أكبر من سعر المنتج.',
        'value_id_not_true'                 => ' قيمة الخصائص غير صحيح.',
        'root_not_true'                     => 'الفئات الفرعيه لا ينتمون الي نفس الفئه الرئيسيه.',
        'category_id_not_exist'             => 'قيمة  الفئة الفرعيه غير موجود.',
        'quantity_not_avaliable'            => 'الكمية غير متاحة.',
        'quantity_not_cover_quantity_required_for_flash_sale' => 'الكمية غير متاحة.',
    ],

    'delete' => [
        'fail'                              => 'لم يتم الحذف حاول مرة اخرى.',
        'admin'                             => 'تم حذف الادمن بنجاح.',
        'country'                           => 'تم حذف الدولة بنجاح.',
        'city'                              => 'تم حذف المدينه بنجاح.',
        'district'                          => 'تم حذف المنطقه بنجاح.',
        'success'                           => 'تم الحذف بنجاح.',
        'category'                          => 'تم حذف الفئة بنجاح.',
        'color'                             => 'تم حذف اللون بنجاح.',
        'size'                              => 'تم حذف الحجم بنجاح.',
    ],

    'update' => [
        'fail'                              => 'لم يتم التعديل حاول مرة اخرى.',
        'success'                           => 'تم التعديل بنجاح.',
        'country'                           => 'تم تعديل الدولة بنجاح.',
        'city'                              => 'تم تعديل المدينه بنجاح.',
        'district'                          => 'تم تعديل المنطقه بنجاح.',
        'category'                          => 'تم تعديل الفئة بنجاح.',
        'color'                             => 'تم تعديل اللون بنجاح.',
        'size'                              => 'تم تعديل الحجم بنجاح.',
    ],

    'create' => [
        'success'                           => 'تمت الاضافة بنجاح.',
        'fail'                              => 'لم يتم الحفظ حاول مرة اخرى.',
        'country'                           => 'تمت اضافة الدولة بنجاح.',
        'city'                              => 'تمت اضافة المدينه بنجاح.',
        'district'                          => 'تمت اضافة المنطقه بنجاح.',
        'category'                          => 'تمت اضافة الفئة بنجاح.',
        'color'                             => 'تمت اضافة اللون بنجاح.',
        'size'                              => 'تمت اضافة الحجم بنجاح.',
    ],

    'statistics' => [
        'total_orders'        => 'إجمالي الطلبات',
        'total_revenue'       => 'إجمالي الإيرادات',
        'total_prdocuts'      => 'إجمالي المنتجات',
        'top_selling_product' => 'المنتجات الأكثر مبيعا',
        'revenue_chart'  => [
            'revenue'    => 'الأرباح',
            'this_year'  => 'هذه السنة',
            'last_year'  => 'العام الماضي',
        ],
        'order_status'  => [
            'order_status'    => 'حالة الطلب',
            'pending'         => 'قيد الانتظار',
            'admin_accepted'  => 'مقبول من قبل الإدارة',
            'admin_rejected'  => 'مرفوض من قبل الإدارة',
            'admin_delivered' => 'تم التسليم'
        ]
    ],

    'notifications' => [
        'orders' => [
            'title' => [
                'admin_accept'    => 'تم الموافقه علي طلبك رقم:order_id',
                'admin_rejected'  => 'تم رفض طلبك رقم:order_id',
                'admin_cancel'    => 'تم إلغاء طلبك رقم:order_id',
                'admin_shipping'  => 'تم شحن طلبك رقم:order_id',
                'admin_delivered' => 'تم توصيل طلبك رقم:order_id',
            ],
            'body'  => [
                'admin_accept'    => 'تم الموافقه علي طلبك رقم:order_id',
                'admin_rejected'  => 'تم رفض طلبك رقم:order_id',
                'admin_cancel'    => 'تم إلغاء طلبك رقم:order_id',
                'admin_shipping'  => 'تم شحن طلبك رقم:order_id',
                'admin_delivered' => 'تم توصيل طلبك رقم:order_id',
            ],
        ],

        'return_order' => [
            'title' => [
                'return_order' => 'تم مراجعة طلب الإسترجاع رقم:return_order_id',
            ],
            'body'  => [
                'return_order' => 'تم مراجعة طلب الإسترجاع رقم:return_order_id',
            ],
        ],
    ],
    
    
    'noti' => [
        'reply_contact_msg' => 'الرد علي رسالتك ',
        ],
];
