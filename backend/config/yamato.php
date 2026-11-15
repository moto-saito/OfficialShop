<?php

/*
 * ヤマト運輸 B2クラウド（送り状発行システム）用 CSV の設定
 * 値は .env で設定する。未設定の必須項目があると CSV 出力時にエラーを表示する。
 */
return [
    'b2' => [
        // ご請求先顧客コード（お客様コード。半角数字 最大12桁）
        // ※着払い・クロネコゆうメールでは使わない（送り状に入れない）。それ以外の送り状種類では必須
        'customer_code'       => env('YAMATO_B2_CUSTOMER_CODE'),
        // ご請求先分類コード（半角数字 最大3桁。無ければ空）
        'customer_class_code' => env('YAMATO_B2_CUSTOMER_CLASS_CODE', ''),
        // 運賃管理番号（半角数字2桁。通常 01）※着払い・クロネコゆうメール以外で必須
        'freight_number'      => env('YAMATO_B2_FREIGHT_NUMBER', '01'),

        // 送り状種類（5:着払い, 0:発払い, A:ネコポス, 8:宅急便コンパクト など。既定は 5:着払い）
        'slip_type'           => env('YAMATO_B2_SLIP_TYPE', '5'),
        // クール区分（0:通常, 1:冷凍, 2:冷蔵）
        'cool_type'           => env('YAMATO_B2_COOL_TYPE', '0'),

        // 品名（空なら注文の商品名から作る。例:「醤油 他2点」）
        'item_name'           => env('YAMATO_B2_ITEM_NAME', ''),

        // ご依頼主（発送元）。通常は管理画面「店舗・工場情報」で設定する（そちらが空の項目だけここを使う）
        'sender' => [
            'name'     => env('YAMATO_B2_SENDER_NAME'),
            'phone'    => env('YAMATO_B2_SENDER_PHONE'),
            'postal'   => env('YAMATO_B2_SENDER_POSTAL'),
            'address'  => env('YAMATO_B2_SENDER_ADDRESS'),   // 都道府県から町・番地まで（全角32文字以内）
            'building' => env('YAMATO_B2_SENDER_BUILDING', ''), // 建物名（全角16文字以内）
        ],

        // 1行目に項目名（タイトル行）を出力するか
        'header_row'          => env('YAMATO_B2_HEADER_ROW', true),
    ],
];
