<?php

namespace App\Services;

use App\Models\Order;
use App\Models\StoreInfo;

/**
 * ヤマト運輸 B2クラウド「送り状発行データ（基本レイアウト）」の CSV 行を作る。
 * レイアウトはヤマト運輸公開の「Ｂ２クラウド 送り状発行データレイアウト 入出力用」（全97項目）に準拠。
 * 文字数の上限は Shift_JIS のバイト数（全角2・半角1）で判定する。
 */
class YamatoB2Csv
{
    /** 送り状種類のうち、ご請求先顧客コード・分類コード・運賃管理番号を入れない（非入力項目の）もの */
    private const NO_BILLING_SLIP_TYPES = [
        '3', // クロネコゆうメール
        '5', // 着払い
    ];

    /** 送り状種類として指定できる値 */
    private const SLIP_TYPES = ['0', '2', '3', '4', '5', '6', '7', '8', '9', 'A'];

    /** 項目名（No.1〜97） */
    public const COLUMNS = [
        'お客様管理番号', '送り状種類', 'クール区分', '伝票番号', '出荷予定日', 'お届け予定（指定）日', '配達時間帯',
        'お届け先コード', 'お届け先電話番号', 'お届け先電話番号枝番', 'お届け先郵便番号', 'お届け先住所',
        'お届け先住所（アパートマンション名）', 'お届け先会社・部門名１', 'お届け先会社・部門名２', 'お届け先名',
        'お届け先名略称カナ', '敬称', 'ご依頼主コード', 'ご依頼主電話番号', 'ご依頼主電話番号枝番', 'ご依頼主郵便番号',
        'ご依頼主住所', 'ご依頼主住所（アパートマンション名）', 'ご依頼主名', 'ご依頼主略称カナ', '品名コード１', '品名１',
        '品名コード２', '品名２', '荷扱い１', '荷扱い２', '記事', 'コレクト代金引換額（税込）', 'コレクト内消費税額等',
        '営業所止置き', '営業所コード', '発行枚数', '個数口枠の印字', 'ご請求先顧客コード', 'ご請求先分類コード',
        '運賃管理番号', 'クロネコwebコレクトデータ登録', 'クロネコwebコレクト加盟店番号', 'クロネコwebコレクト申込受付番号１',
        'クロネコwebコレクト申込受付番号２', 'クロネコwebコレクト申込受付番号３', 'お届け予定ｅメール利用区分',
        'お届け予定ｅメールe-mailアドレス', '入力機種', 'お届け予定eメールメッセージ', 'お届け完了ｅメール利用区分',
        'お届け完了ｅメールe-mailアドレス', 'お届け完了eメールメッセージ', 'クロネコ収納代行利用区分', '予備',
        '収納代行請求金額(税込)', '収納代行内消費税額等', '収納代行請求先郵便番号', '収納代行請求先住所',
        '収納代行請求先住所（アパートマンション名）', '収納代行請求先会社・部門名１', '収納代行請求先会社・部門名２',
        '収納代行請求先名(漢字)', '収納代行請求先名(カナ)', '収納代行問合せ先名(漢字)', '収納代行問合せ先郵便番号',
        '収納代行問合せ先住所', '収納代行問合せ先住所（アパートマンション名）', '収納代行問合せ先電話番号', '収納代行管理番号',
        '収納代行品名', '収納代行備考', '複数口くくりキー', '検索キータイトル1', '検索キー1', '検索キータイトル2', '検索キー2',
        '検索キータイトル3', '検索キー3', '検索キータイトル4', '検索キー4', '検索キータイトル5', '検索キー5', '予備', '予備',
        '投函予定メール利用区分', '投函予定メールe-mailアドレス', '投函予定メールメッセージ',
        '投函完了メール（お届け先宛）利用区分', '投函完了メール（お届け先宛）e-mailアドレス', '投函完了メール（お届け先宛）メールメッセージ',
        '投函完了メール（ご依頼主宛）利用区分', '投函完了メール（ご依頼主宛）e-mailアドレス', '投函完了メール（ご依頼主宛）メールメッセージ',
        '連携管理番号', '通知メールアドレス',
    ];

    /** @var string[] 出力できない理由 */
    private array $errors = [];

    /**
     * 注文1件分の行を作る。問題があれば例外ではなく errors() に理由をためる。
     *
     * @return array<int, string>|null 97項目の配列（問題がある場合は null）
     */
    public function row(Order $order): ?array
    {
        $this->errors = [];
        $config = config('yamato.b2');
        $sender = $this->sender();

        // ── 設定（.env）の確認 ──
        $slipType = strtoupper(trim((string) ($config['slip_type'] ?? '')));
        if (!in_array($slipType, self::SLIP_TYPES, true)) {
            $this->errors[] = '送り状種類（YAMATO_B2_SLIP_TYPE）は 0〜9 または A で設定してください（5:着払い, 0:発払い など）。';
        }

        // 着払い・クロネコゆうメールは、ご請求先（顧客コード・分類コード・運賃管理番号）を入れない
        $customerCode = $classCode = $freight = '';
        if (!in_array($slipType, self::NO_BILLING_SLIP_TYPES, true)) {
            $customerCode = $this->digits($config['customer_code'] ?? '', 'ご請求先顧客コード（YAMATO_B2_CUSTOMER_CODE）', 12, true);
            $classCode = $this->digits($config['customer_class_code'] ?? '', 'ご請求先分類コード（YAMATO_B2_CUSTOMER_CLASS_CODE）', 3, false);
            $freight = $this->digits($config['freight_number'] ?? '', '運賃管理番号（YAMATO_B2_FREIGHT_NUMBER）', 2, true);
        }
        $where = '（管理画面「店舗・工場情報」の「送り状のご依頼主」で設定）';
        $senderName = $this->text($sender['name'], 'ご依頼主名' . $where, 32, true);
        $senderPhone = $this->phone($sender['phone'], 'ご依頼主の電話番号' . $where);
        $senderPostal = $this->postal($sender['postal'], 'ご依頼主の郵便番号' . $where);
        $senderAddress = $this->text($sender['address'], 'ご依頼主の住所' . $where, 64, true);
        $senderBuilding = $this->text($sender['building'], 'ご依頼主の建物名' . $where, 32, false);

        // ── お届け先（注文の購入者情報）──
        $phone = $this->phone($order->phone_number, 'お届け先電話番号');
        $postal = $this->postal($order->postal_code, 'お届け先郵便番号');
        [$address, $building] = $this->splitAddress($order->prefecture, $order->address);
        $name = $this->text($order->recipient_name, 'お届け先名', 32, true);
        $itemName = $this->itemName($order);

        if ($this->errors) {
            return null;
        }

        $row = array_fill(0, count(self::COLUMNS), '');
        $set = function (int $no, string $value) use (&$row) {
            $row[$no - 1] = $value;
        };

        $set(1, str_replace('-', '', $order->order_number)); // お客様管理番号（半角英数字）
        $set(2, $slipType);
        $set(3, (string) $config['cool_type']);
        $set(5, now()->format('Y/m/d'));                       // 出荷予定日（出力した日）
        $set(9, $phone);
        $set(11, $postal);
        $set(12, $address);
        $set(13, $building);
        $set(16, $name);
        $set(18, '様');
        $set(20, $senderPhone);
        $set(22, $senderPostal);
        $set(23, $senderAddress);
        $set(24, $senderBuilding);
        $set(25, $senderName);
        $set(28, $itemName);
        $set(40, $customerCode);
        $set(41, $classCode);
        $set(42, $freight);

        return $row;
    }

    /** @return string[] */
    public function errors(): array
    {
        return $this->errors;
    }

    /** CSV の文字コード（Shift_JIS / Windows-31J）に変換する */
    public static function toSjis(string $value): string
    {
        return mb_convert_encoding($value, 'SJIS-win', 'UTF-8');
    }

    // ─────────────────────────────────────────────

    /**
     * ご依頼主（発送元）。管理画面「店舗・工場情報」の値を優先し、空の項目は .env（YAMATO_B2_SENDER_*）を使う
     *
     * @return array{name: string, phone: string, postal: string, address: string, building: string}
     */
    private function sender(): array
    {
        $store = StoreInfo::first();
        $fallback = config('yamato.b2.sender', []);
        $sender = [];
        foreach (['name', 'phone', 'postal', 'address', 'building'] as $key) {
            $value = trim((string) ($store?->{'sender_' . $key} ?? ''));
            $sender[$key] = $value !== '' ? $value : (string) ($fallback[$key] ?? '');
        }

        return $sender;
    }

    /** Shift_JIS でのバイト数（全角2・半角1） */
    private function bytes(string $value): int
    {
        return strlen(self::toSjis($value));
    }

    /** Shift_JIS で表せない文字（絵文字・一部の旧字体など）が無いか */
    private function encodable(string $value): bool
    {
        return mb_convert_encoding(self::toSjis($value), 'UTF-8', 'SJIS-win') === $value;
    }

    private function normalize(?string $value): string
    {
        // 全角スペースは半角に、連続する空白は1つにまとめる
        return trim(preg_replace('/\s+/u', ' ', str_replace('　', ' ', (string) $value)));
    }

    private function text(?string $value, string $label, int $maxBytes, bool $required): string
    {
        $value = $this->normalize($value);
        if ($value === '') {
            if ($required) {
                $this->errors[] = "{$label}が入力されていません。";
            }
            return '';
        }
        if (!$this->encodable($value)) {
            $this->errors[] = "{$label}に送り状で使えない文字（絵文字・一部の旧字体など）が含まれています。";
        } elseif ($this->bytes($value) > $maxBytes) {
            $this->errors[] = sprintf('%sが長すぎます（全角%d文字・半角%d文字まで）。', $label, intdiv($maxBytes, 2), $maxBytes);
        }
        return $value;
    }

    private function digits(?string $value, string $label, int $maxLength, bool $required): string
    {
        $value = mb_convert_kana($this->normalize($value), 'n');
        if ($value === '') {
            if ($required) {
                $this->errors[] = "{$label}が設定されていません。";
            }
            return '';
        }
        if (!preg_match('/^\d{1,' . $maxLength . '}$/', $value)) {
            $this->errors[] = "{$label}は半角数字{$maxLength}桁以内で設定してください。";
        }
        return $value;
    }

    private function phone(?string $value, string $label): string
    {
        // 全角数字・全角ハイフン類を半角に
        $value = preg_replace('/[‐－―ー−]/u', '-', mb_convert_kana($this->normalize($value), 'n'));
        $value = preg_replace('/[^\d\-]/', '', $value);
        if ($value === '' || !preg_match('/\d/', $value)) {
            $this->errors[] = "{$label}が入力されていません。";
        } elseif (strlen($value) > 15) {
            $this->errors[] = "{$label}が長すぎます（ハイフン込み15文字まで）。";
        }
        return $value;
    }

    private function postal(?string $value, string $label): string
    {
        $digits = preg_replace('/\D/', '', mb_convert_kana($this->normalize($value), 'n'));
        if (strlen($digits) !== 7) {
            $this->errors[] = "{$label}は7桁で入力してください。";
            return $digits;
        }
        return substr($digits, 0, 3) . '-' . substr($digits, 3);
    }

    /**
     * 住所を「お届け先住所（都道府県〜町・番地。全角32文字まで）」と
     * 「アパートマンション名（全角16文字まで）」に分ける。
     * 収まらない場合は空白の位置で分け、空白が無ければ文字数で分ける。
     *
     * @return array{0: string, 1: string}
     */
    private function splitAddress(?string $prefecture, ?string $address): array
    {
        $prefecture = $this->normalize($prefecture);
        $address = $this->normalize($address);
        if (!str_starts_with($address, $prefecture)) {
            $address = $prefecture . $address; // 住所に都道府県が無ければ先頭に付ける
        }

        if ($address === '' || $address === $prefecture) {
            $this->errors[] = 'お届け先住所が入力されていません。';
            return ['', ''];
        }
        if (!$this->encodable($address)) {
            $this->errors[] = 'お届け先住所に送り状で使えない文字（絵文字・一部の旧字体など）が含まれています。';
            return [$address, ''];
        }
        if ($this->bytes($address) <= 64) {
            return [$address, ''];
        }

        // 空白で分けられるなら、収まる範囲で一番後ろの空白で分ける（建物名を後ろに回す）
        $main = null;
        $offset = 0;
        while (($pos = mb_strpos($address, ' ', $offset)) !== false) {
            if ($this->bytes(mb_substr($address, 0, $pos)) > 64) {
                break;
            }
            $main = $pos;
            $offset = $pos + 1;
        }
        if ($main !== null) {
            $first = mb_substr($address, 0, $main);
            $rest = trim(mb_substr($address, $main + 1));
        } else {
            // 空白が無い場合は 64 バイトに収まるところで分ける
            $first = '';
            foreach (mb_str_split($address) as $char) {
                if ($this->bytes($first . $char) > 64) {
                    break;
                }
                $first .= $char;
            }
            $rest = mb_substr($address, mb_strlen($first));
        }

        if ($this->bytes($rest) > 32) {
            $this->errors[] = 'お届け先住所が長すぎて送り状に収まりません（住所 全角32文字＋建物名 全角16文字まで）。B2クラウドで直接入力してください。';
        }
        return [$first, $rest];
    }

    /** 品名１（全角25文字まで）。設定が無ければ「先頭の商品名 他N点」 */
    private function itemName(Order $order): string
    {
        $configured = $this->normalize(config('yamato.b2.item_name'));
        if ($configured !== '') {
            return $this->text($configured, '品名（YAMATO_B2_ITEM_NAME）', 50, true);
        }

        $items = $order->items;
        if ($items->isEmpty()) {
            $this->errors[] = '注文に商品が無いため品名を作れません。YAMATO_B2_ITEM_NAME を設定してください。';
            return '';
        }

        $suffix = $items->count() > 1 ? ' 他' . ($items->count() - 1) . '点' : '';
        $name = $this->normalize($items->first()->product_name);
        // 送り状で使えない文字は除き、収まらない分は切り詰める（品名は多少省略しても配送に影響しない）
        $name = implode('', array_filter(mb_str_split($name), fn ($c) => $this->encodable($c)));
        while ($name !== '' && $this->bytes($name . $suffix) > 50) {
            $name = mb_substr($name, 0, -1);
        }
        return $name . $suffix;
    }
}
