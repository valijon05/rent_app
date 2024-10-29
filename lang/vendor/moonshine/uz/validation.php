<?php

/*
|--------------------------------------------------------------------------
| Validation Language Lines
|--------------------------------------------------------------------------
|
| The following language lines contain the default error messages used by
| the validator class. Some of these rules have multiple versions such
| as the size rules. Feel free to tweak each of these messages here.
|
*/

return [
    'accepted'             => ':attribute ni qabul qilishingiz kerak.',
    'accepted_if'          => ':attribute ni qabul qilishingiz kerak, agar :other :value ga teng bo\'lsa.',
    'active_url'           => ':attribute maydoni haqiqiy URL emas.',
    'after'                => ':attribute maydoni :date dan keyin bo\'lishi kerak.',
    'after_or_equal'       => ':attribute maydoni :date dan keyin yoki teng bo\'lishi kerak.',
    'alpha'                => ':attribute maydoni faqat harflardan iborat bo\'lishi mumkin.',
    'alpha_dash'           => ':attribute maydoni faqat harflar, raqamlar, chiziqcha va pastki chiziqdan iborat bo\'lishi mumkin.',
    'alpha_num'            => ':attribute maydoni faqat harflar va raqamlardan iborat bo\'lishi mumkin.',
    'array'                => ':attribute maydoni massiv bo\'lishi kerak.',
    'before'               => ':attribute maydoni :date dan oldin bo\'lishi kerak.',
    'before_or_equal'      => ':attribute maydoni :date dan oldin yoki teng bo\'lishi kerak.',
    'between'              => [
        'array'   => ':attribute maydonidagi elementlar soni :min dan :max gacha bo\'lishi kerak.',
        'file'    => ':attribute maydoni hajmi :min dan :max kilobaytgacha bo\'lishi kerak.',
        'numeric' => ':attribute maydoni :min dan :max gacha bo\'lishi kerak.',
        'string'  => ':attribute maydonidagi belgilar soni :min dan :max gacha bo\'lishi kerak.',
    ],
    'boolean'              => ':attribute maydoni mantiqiy qiymat bo\'lishi kerak.',
    'confirmed'            => ':attribute maydoni tasdiqlangan qiymat bilan mos kelmaydi.',
    'current_password'     => 'Parol noto‘g‘ri.',
    'date'                 => ':attribute maydoni sana emas.',
    'date_equals'          => ':attribute maydoni :date ga teng bo\'lishi kerak.',
    'date_format'          => ':attribute maydoni :format sana formatiga mos kelmaydi.',
    'declined'             => ':attribute maydoni rad etilishi kerak.',
    'declined_if'          => ':attribute maydoni :other :value ga teng bo\'lganda rad etilishi kerak.',
    'different'            => ':attribute va :other maydonlari har xil bo\'lishi kerak.',
    'digits'               => ':attribute maydonidagi raqam uzunligi :digits bo\'lishi kerak.',
    'digits_between'       => ':attribute maydonidagi raqam uzunligi :min dan :max gacha bo\'lishi kerak.',
    'dimensions'           => ':attribute maydoni tasvirning noto‘g‘ri o‘lchamlariga ega.',
    'distinct'             => ':attribute maydoni takrorlanmasligi kerak.',
    'email'                => ':attribute maydoni haqiqiy elektron pochta manzili bo\'lishi kerak.',
    'ends_with'            => ':attribute maydoni quyidagi qiymatlarning biri bilan tugashi kerak: :values',
    'enum'                 => ':attribute uchun tanlangan qiymat noto‘g‘ri.',
    'exists'               => ':attribute uchun tanlangan qiymat noto‘g‘ri.',
    'file'                 => ':attribute maydoni fayl bo\'lishi kerak.',
    'filled'               => ':attribute maydoni to\'ldirilishi shart.',
    'gt'                   => [
        'array'   => ':attribute maydonidagi elementlar soni :value dan ko‘p bo\'lishi kerak.',
        'file'    => ':attribute maydonidagi fayl hajmi :value kilobaytdan ko‘p bo\'lishi kerak.',
        'numeric' => ':attribute maydoni :value dan ko‘p bo\'lishi kerak.',
        'string'  => ':attribute maydoni :value dan ko‘p bo\'lishi kerak.',
    ],
    'gte'                  => [
        'array'   => ':attribute maydonidagi elementlar soni :value dan ko‘p yoki teng bo\'lishi kerak.',
        'file'    => ':attribute maydonidagi fayl hajmi :value kilobaytdan ko‘p yoki teng bo\'lishi kerak.',
        'numeric' => ':attribute maydoni :value dan ko‘p yoki teng bo\'lishi kerak.',
        'string'  => ':attribute maydoni :value dan ko‘p yoki teng bo\'lishi kerak.',
    ],
    'image'                => ':attribute maydonidagi fayl tasvir bo\'lishi kerak.',
    'in'                   => ':attribute uchun tanlangan qiymat noto‘g‘ri.',
    'in_array'             => ':attribute maydoni :other da mavjud emas.',
    'integer'              => ':attribute maydoni butun son bo\'lishi kerak.',
    'ip'                   => ':attribute maydoni haqiqiy IP manzili bo\'lishi kerak.',
    'ipv4'                 => ':attribute maydoni haqiqiy IPv4 manzili bo\'lishi kerak.',
    'ipv6'                 => ':attribute maydoni haqiqiy IPv6 manzili bo\'lishi kerak.',
    'json'                 => ':attribute maydoni JSON satri bo\'lishi kerak.',
    'lt'                   => [
        'array'   => ':attribute maydonidagi elementlar soni :value dan kam bo\'lishi kerak.',
        'file'    => ':attribute maydonidagi fayl hajmi :value kilobaytdan kam bo\'lishi kerak.',
        'numeric' => ':attribute maydoni :value dan kam bo\'lishi kerak.',
        'string'  => ':attribute maydonidagi belgilar soni :value dan kam bo\'lishi kerak.',
    ],
    'lte'                  => [
        'array'   => ':attribute maydonidagi elementlar soni :value dan kam yoki teng bo\'lishi kerak.',
        'file'    => ':attribute maydoni hajmi :value kilobaytdan kam yoki teng bo\'lishi kerak.',
        'numeric' => ':attribute maydoni :value dan kam yoki teng bo\'lishi kerak.',
        'string'  => ':attribute maydoni :value dan kam yoki teng bo\'lishi kerak.',
    ],
    'mac_address'          => ':attribute maydoni haqiqiy MAC manzili bo\'lishi kerak.',
    'max'                  => [
        'array'   => ':attribute maydonidagi elementlar soni :max dan oshmasligi kerak.',
        'file'    => ':attribute maydoni hajmi :max kilobaytdan oshmasligi kerak.',
        'numeric' => ':attribute maydoni :max dan oshmasligi kerak.',
        'string'  => ':attribute maydoni :max dan oshmasligi kerak.',
    ],
    'mimes'                => ':attribute maydoni quyidagi turlardan biriga ega fayl bo\'lishi kerak: :values.',
    'mimetypes'            => ':attribute maydoni quyidagi turlardan biriga ega fayl bo\'lishi kerak: :values.',
    'min'                  => [
        'array'   => ':attribute maydonidagi elementlar soni :min dan kam bo‘lmasligi kerak.',
        'file'    => ':attribute maydoni hajmi :min kilobaytdan kam bo‘lmasligi kerak.',
        'numeric' => ':attribute maydoni :min dan kam bo‘lmasligi kerak.',
        'string'  => ':attribute maydoni :min dan kam bo‘lmasligi kerak.',
    ],
    'multiple_of'          => ':attribute maydoni :value ga ko‘p bo‘lishi kerak.',
    'not_in'               => ':attribute uchun tanlangan qiymat noto‘g‘ri.',
    'not_regex'            => ':attribute maydoni noto‘g‘ri.',
    'numeric'              => ':attribute maydoni son bo\'lishi kerak.',
    'password'             => 'Noto‘g‘ri parol.',
    'present'              => ':attribute maydoni mavjud bo\'lishi kerak.',
    'prohibited'           => ':attribute maydoni taqiqlangan.',
    'prohibited_if'        => ':attribute maydoni :other :value ga teng bo\'lganda taqiqlangan.',
    'prohibited_unless'    => ':attribute maydoni :other :values da bo‘lmasa taqiqlangan.',
    'prohibits'            => ':attribute maydoni :other ning mavjud bo‘lishini taqiqlaydi.',
    'regex'                => ':attribute maydoni noto‘g‘ri.',
    'required'             => ':attribute maydoni to‘ldirilishi shart.',
    'required_array_keys'  => ':attribute maydonidagi massivda quyidagi kalitlar bo‘lishi shart: :values',
    'required_if'          => ':attribute maydoni :other :value ga teng bo‘lganda to‘ldirilishi shart.',
    'required_unless'      => ':attribute maydoni :other :values da bo‘lmasa to‘ldirilishi shart.',
    'required_with'        => ':attribute maydoni :values bo‘lganda to‘ldirilishi shart.',
    'required_with_all'    => ':attribute maydoni :values bo‘lganda to‘ldirilishi shart.',
    'required_without'     => ':attribute maydoni :values bo‘lmasa to‘ldirilishi shart.',
    'required_without_all' => ':attribute maydoni :values bo‘lmasa to‘ldirilishi shart.',
    'same'                 => ':attribute va :other maydonlari bir xil bo‘lishi kerak.',
    'size'                 => [
        'array'   => ':attribute maydoni :size elementdan iborat bo‘lishi kerak.',
        'file'    => ':attribute maydoni hajmi :size kilobayt bo‘lishi kerak.',
        'numeric' => ':attribute maydoni :size bo‘lishi kerak.',
        'string'  => ':attribute maydoni :size belgidan iborat bo‘lishi kerak.',
    ],
    'starts_with'          => ':attribute maydoni quyidagi qiymatlarning biri bilan boshlanishi kerak: :values',
    'string'               => ':attribute maydoni matn bo‘lishi kerak.',
    'timezone'             => ':attribute maydoni haqiqiy zona bo‘lishi kerak.',
    'unique'               => ':attribute maydoni allaqachon band qilingan.',
    'uploaded'             => ':attribute maydoni yuklashda xato yuz berdi.',
    'url'                  => ':attribute maydoni noto‘g‘ri URL formatida.',
    'uuid'                 => ':attribute maydoni haqiqiy UUID bo‘lishi kerak.',
];
