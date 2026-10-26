<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingFee extends Model
{
    /** 都道府県（JIS X 0401 の順） */
    public const PREFECTURES = [
        '北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県',
        '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県',
        '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県',
        '静岡県', '愛知県', '三重県', '滋賀県', '京都府', '大阪府', '兵庫県',
        '奈良県', '和歌山県', '鳥取県', '島根県', '岡山県', '広島県', '山口県',
        '徳島県', '香川県', '愛媛県', '高知県', '福岡県', '佐賀県', '長崎県',
        '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県',
    ];

    protected $fillable = [
        'prefecture',
        'fee',
    ];

    /** 都道府県の送料を返す（未登録の場合は 0 円） */
    public static function feeFor(string $prefecture): int
    {
        return (int) static::where('prefecture', $prefecture)->value('fee');
    }

    /**
     * 全都道府県の送料を PREFECTURES の順で返す
     * 行が欠けている都道府県は 0 円として補完する
     *
     * @return array<string, int>
     */
    public static function feeMap(): array
    {
        $fees = static::pluck('fee', 'prefecture');

        return collect(self::PREFECTURES)
            ->mapWithKeys(fn ($prefecture) => [$prefecture => (int) ($fees[$prefecture] ?? 0)])
            ->all();
    }
}
