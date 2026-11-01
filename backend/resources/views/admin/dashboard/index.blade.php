@extends('admin.layouts.app')

@section('title', 'ダッシュボード')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/admin-dashboard.css') }}">
@endpush

@section('content')

{{-- サマリーカード --}}
<div class="admin_dashboard_summary-grid">
    <div class="admin_dashboard_summary-card">
        <p class="admin_dashboard_summary-label">総注文数</p>
        <p class="admin_dashboard_summary-value">{{ number_format($totalOrderCount) }}<span class="admin_dashboard_summary-unit">件</span></p>
    </div>
    <div class="admin_dashboard_summary-card">
        <p class="admin_dashboard_summary-label">本日の注文数</p>
        <p class="admin_dashboard_summary-value">{{ number_format($todayOrderCount) }}<span class="admin_dashboard_summary-unit">件</span></p>
    </div>
    <div class="admin_dashboard_summary-card">
        <p class="admin_dashboard_summary-label">売上合計</p>
        <p class="admin_dashboard_summary-value">¥{{ number_format($totalSales) }}</p>
    </div>
    <div class="admin_dashboard_summary-card">
        <p class="admin_dashboard_summary-label">未発送件数</p>
        <p class="admin_dashboard_summary-value">{{ number_format($unshippedCount) }}<span class="admin_dashboard_summary-unit">件</span></p>
    </div>
</div>

{{-- グラフ --}}
<div class="admin_dashboard_chart-grid">
    {{-- 売上推移（発送済・完了 かつ 支払済） --}}
    <section class="admin_dashboard_chart-card">
        <div class="admin_dashboard_chart-header">
            <div>
                <h2 class="common_section-title">売上推移</h2>
                <p class="admin_dashboard_chart-note">発送済・完了 かつ 支払済の注文（決済日基準）</p>
            </div>
            <div class="admin_dashboard_period" role="group" aria-label="表示期間">
                <button type="button" class="admin_dashboard_period-button is-active" data-period="1m">1ヶ月</button>
                <button type="button" class="admin_dashboard_period-button" data-period="3m">3ヶ月</button>
                <button type="button" class="admin_dashboard_period-button" data-period="9m">9ヶ月</button>
            </div>
        </div>
        <p class="admin_dashboard_chart-total">
            期間合計 <span id="salesPeriodTotal" class="admin_dashboard_chart-total-value">¥0</span>
        </p>
        <div class="admin_dashboard_line-wrap">
            <canvas id="salesChart" aria-label="売上推移グラフ" role="img"></canvas>
        </div>
    </section>

    {{-- 現在の注文状況 --}}
    <section class="admin_dashboard_chart-card">
        <div class="admin_dashboard_chart-header">
            <div>
                <h2 class="common_section-title">現在の注文状況</h2>
                <p class="admin_dashboard_chart-note">完了・発送済（支払済）を除く</p>
            </div>
        </div>
        <div class="admin_dashboard_donut-body">
            <div class="admin_dashboard_donut-wrap">
                <canvas id="statusChart" aria-label="注文状況グラフ" role="img"></canvas>
                <div class="admin_dashboard_donut-center">
                    <span class="admin_dashboard_donut-total">{{ number_format($statusChart['total']) }}</span>
                    <span class="admin_dashboard_donut-unit">件</span>
                </div>
            </div>
            <ul class="admin_dashboard_legend">
                @foreach ($statusChart['items'] as $i => $item)
                    <li class="admin_dashboard_legend-item">
                        <span class="admin_dashboard_legend-swatch admin_dashboard_legend-swatch--{{ $i }}"></span>
                        <span class="admin_dashboard_legend-label">{{ $item['label'] }}</span>
                        <span class="admin_dashboard_legend-value">{{ number_format($item['count']) }}件</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
</div>

{{-- 最新注文5件 --}}
<div class="admin_card">
    <div class="admin_dashboard_orders-header">
        <h2 class="common_section-title">最新注文</h2>
        <a href="{{ route('admin.orders.index') }}" class="admin_dashboard_orders-link">すべて見る →</a>
    </div>
    <table class="common_table">
        <thead>
            <tr>
                <th>注文番号</th>
                <th class="admin_dashboard_col-date">注文日</th>
                <th>購入者氏名</th>
                <th class="admin_dashboard_col-amount">注文金額</th>
                <th class="admin_dashboard_col-status">ステータス</th>
                <th class="admin_dashboard_col-action"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($latestOrders as $order)
                <tr>
                    <td class="admin_dashboard_row-number">{{ $order->order_number }}</td>
                    <td class="admin_dashboard_row-date">{{ $order->created_at->format('Y/m/d H:i') }}</td>
                    <td class="admin_dashboard_row-recipient">{{ $order->recipient_name }}</td>
                    <td class="admin_dashboard_row-amount">{{ $order->formatted_total_price }}</td>
                    <td>
                        <span class="common_status-badge {{ $order->status_color }}">
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td class="admin_dashboard_row-action">
                        <a href="{{ route('admin.orders.show', $order) }}" class="common_button-outline--xs">
                            詳細
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="admin_dashboard_empty">注文がありません</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(() => {
    const salesData  = @json($salesChart);
    const statusData = @json($statusChart);
    const css = getComputedStyle(document.documentElement);
    const token = (name, fallback) => css.getPropertyValue(name).trim() || fallback;

    const colorText    = token('--color-admin-muted', '#8a97a5');
    const colorGrid    = token('--color-admin-border', '#293541');
    const colorSurface = token('--color-admin-surface', '#1c252f');
    const colorLine    = token('--color-admin-accent', '#70b9d8');
    // 受付中 / 準備中 / 発送済（未払い） / その他（CSSの凡例色と揃える）
    const statusColors = ['#c98500', '#3987e5', '#d55181', '#5d6b79'];

    const yen = (v) => '¥' + Number(v).toLocaleString('ja-JP');

    Chart.defaults.color = colorText;
    Chart.defaults.font.family = 'inherit';

    // ── 売上推移（線グラフ） ──
    const totalEl = document.getElementById('salesPeriodTotal');
    const salesChart = new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: { labels: [], datasets: [{
            label: '売上',
            data: [],
            borderColor: colorLine,
            backgroundColor: 'rgba(112, 185, 216, .12)',
            borderWidth: 2,
            fill: true,
            tension: .25,
            pointRadius: 0,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: colorLine,
            pointHoverBorderColor: colorSurface,
            pointHoverBorderWidth: 2,
        }]},
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: (ctx) => yen(ctx.parsed.y) } },
            },
            scales: {
                x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkipPadding: 12 } },
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { color: colorGrid },
                    ticks: { maxTicksLimit: 5, callback: (v) => yen(v) },
                },
            },
        },
    });

    const setPeriod = (period) => {
        const d = salesData[period];
        salesChart.data.labels = d.labels;
        salesChart.data.datasets[0].data = d.values;
        salesChart.update();
        totalEl.textContent = yen(d.values.reduce((a, b) => a + b, 0));
        document.querySelectorAll('.admin_dashboard_period-button').forEach((b) => {
            b.classList.toggle('is-active', b.dataset.period === period);
        });
    };
    document.querySelectorAll('.admin_dashboard_period-button').forEach((b) => {
        b.addEventListener('click', () => setPeriod(b.dataset.period));
    });
    setPeriod('1m');

    // ── 現在の注文状況（ドーナツグラフ） ──
    const hasData = statusData.total > 0;
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: hasData ? statusData.items.map((i) => i.label) : ['データなし'],
            datasets: [{
                data: hasData ? statusData.items.map((i) => i.count) : [1],
                backgroundColor: hasData ? statusColors : [colorGrid],
                borderColor: colorSurface,
                borderWidth: 2,
                hoverOffset: 4,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: hasData,
                    callbacks: { label: (ctx) => `${ctx.label}: ${ctx.parsed.toLocaleString('ja-JP')}件` },
                },
            },
        },
    });
})();
</script>
@endpush
