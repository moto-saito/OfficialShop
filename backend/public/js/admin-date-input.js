/*
 * 管理画面用の日付入力（resources/views/components/admin/date-input.blade.php）
 * - 年・月・日を手入力。年欄にフォーカスすると「20」を自動入力
 * - 年4桁 / 月2桁 / 日2桁を入れると次の欄へ自動で進む（月 2〜9・日 4〜9 は1桁で確定して進む）
 * - 年・月欄で Enter・区切り文字（/ - . 空白）を押しても次の欄へ進む。方向キーでも移動できる
 * - カレンダーボタンで日付を選べる（表示倍率は CSS の --calendar-scale）
 * - 送信値は hidden に YYYY-MM-DD。不完全・存在しない日付なら送信を止めてエラーを表示
 */
(function () {
    'use strict';

    const YEAR_PREFIX = '20';
    const WEEKDAYS = ['日', '月', '火', '水', '木', '金', '土'];
    const pad = (n) => String(n).padStart(2, '0');
    const toIso = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

    function isValidDate(y, m, d) {
        const date = new Date(y, m - 1, d);
        return date.getFullYear() === y && date.getMonth() === m - 1 && date.getDate() === d;
    }

    function init(root) {
        const hidden = root.querySelector('[data-date-value]');
        const parts = {
            year: root.querySelector('[data-part="year"]'),
            month: root.querySelector('[data-part="month"]'),
            day: root.querySelector('[data-part="day"]'),
        };
        const order = [parts.year, parts.month, parts.day];
        const toggle = root.querySelector('[data-calendar-toggle]');
        const calendar = root.querySelector('[data-calendar]');
        // エラー表示欄（サーバー側のバリデーションエラーが出ていればそれを使う）
        let error = root.nextElementSibling;
        if (!error || !error.classList.contains('admin_date-input_error')) {
            error = document.createElement('p');
            error.className = 'admin_date-input_error';
            error.hidden = true;
            root.insertAdjacentElement('afterend', error);
        }

        // ── 値の確定 ─────────────────────────────
        function selectedDate() {
            const y = Number(parts.year.value), m = Number(parts.month.value), d = Number(parts.day.value);
            if (parts.year.value.length === 4 && parts.month.value && parts.day.value && isValidDate(y, m, d)) {
                return new Date(y, m - 1, d);
            }
            return null;
        }

        // clearError=false: 初期表示時はサーバー側のエラー表示を残す
        function update(clearError = true) {
            const empty = order.every((el) => el.value === '');
            const date = selectedDate();
            hidden.value = date ? toIso(date) : '';

            const message = empty || date ? '' : '日付を正しく入力してください（例: 2026/01/31）';
            parts.year.setCustomValidity(message);
            if (!message && clearError) {
                root.classList.remove('is-invalid');
                error.hidden = true;
            }
        }

        function setDate(date) {
            if (date) {
                parts.year.value = String(date.getFullYear());
                parts.month.value = pad(date.getMonth() + 1);
                parts.day.value = pad(date.getDate());
            } else {
                order.forEach((el) => { el.value = ''; });
            }
            update();
        }

        function focusPart(el, caretToEnd = true) {
            el.focus();
            const pos = caretToEnd ? el.value.length : 0;
            el.setSelectionRange(pos, pos);
        }

        function next(el) {
            const i = order.indexOf(el);
            if (i < order.length - 1) focusPart(order[i + 1]);
        }

        function prev(el) {
            const i = order.indexOf(el);
            if (i > 0) focusPart(order[i - 1]);
        }

        // 月・日が1桁なら 0 埋め
        function padPart(el) {
            if (el !== parts.year && el.value.length === 1 && el.value !== '0') el.value = pad(el.value);
        }

        // ── 手入力 ──────────────────────────────
        // 「20」を自動入力した直後か（最初の数字を必ず「20」の後ろに入れるため）
        let yearAutofilled = false;

        function caretToYearEnd() {
            const end = parts.year.value.length;
            parts.year.setSelectionRange(end, end);
        }

        parts.year.addEventListener('focus', () => {
            if (parts.year.value === '') {
                parts.year.value = YEAR_PREFIX;
                yearAutofilled = true;
                caretToYearEnd();
                // クリックでのフォーカスはこの後カーソル位置が上書きされるので、次のタイミングでも末尾へ。
                // その間に次の欄へ進んでいたら何もしない
                requestAnimationFrame(() => {
                    if (document.activeElement === parts.year && yearAutofilled) caretToYearEnd();
                });
            }
        });

        // クリックでフォーカスした場合、カーソルはクリック位置に置かれるので末尾へ戻す
        parts.year.addEventListener('mouseup', () => {
            if (yearAutofilled && parts.year.selectionStart === parts.year.selectionEnd) caretToYearEnd();
        });

        // 最初の数字・Backspace は、カーソル位置に関係なく「20」の末尾に対して行う
        parts.year.addEventListener('keydown', (e) => {
            if (yearAutofilled && (/^\d$/.test(e.key) || e.key === 'Backspace')) caretToYearEnd();
            if (e.key.length === 1 || e.key === 'Backspace' || e.key === 'Delete') yearAutofilled = false;
        });

        parts.year.addEventListener('blur', () => {
            yearAutofilled = false;
            // 「20」だけ自動入力されて何も入力されなかった場合は空に戻す
            if (parts.year.value === YEAR_PREFIX && parts.month.value === '' && parts.day.value === '') {
                parts.year.value = '';
                update();
            }
        });

        order.forEach((el) => {
            el.addEventListener('input', () => {
                el.value = el.value.replace(/\D/g, '').slice(0, el.maxLength);
                const v = el.value;

                if (el === parts.year && v.length === 4) {
                    next(el);
                } else if (el === parts.month) {
                    if (v.length === 2 && Number(v) > 12) {
                        // 「1」「5」のように13以上になったら、1月として2桁目を日に回す
                        el.value = pad(v[0]);
                        parts.day.value = v[1];
                        focusPart(parts.day);
                    } else if (v.length === 2 || (v.length === 1 && Number(v) >= 2)) {
                        padPart(el);
                        next(el);
                    }
                } else if (el === parts.day) {
                    if (v.length === 1 && Number(v) >= 4) padPart(el);
                }
                update();
            });

            el.addEventListener('blur', () => { padPart(el); update(); });

            el.addEventListener('keydown', (e) => {
                const atStart = el.selectionStart === 0 && el.selectionEnd === 0;
                const atEnd = el.selectionStart === el.value.length;
                if (e.key === 'Backspace' && el.value === '') {
                    e.preventDefault();
                    prev(el);
                } else if (e.key === 'ArrowLeft' && atStart) {
                    e.preventDefault();
                    prev(el);
                } else if (e.key === 'ArrowRight' && atEnd) {
                    e.preventDefault();
                    next(el);
                } else if (['/', '-', '.', ' '].includes(e.key)) {
                    // 区切り文字で次の欄へ
                    e.preventDefault();
                    padPart(el);
                    next(el);
                } else if (e.key === 'Enter' && el !== parts.day && !e.isComposing) {
                    // 年・月欄の Enter は送信せず次の欄へ（日欄の Enter は通常どおり送信）
                    e.preventDefault();
                    padPart(el);
                    next(el);
                }
            });
        });

        // 不完全な日付のまま送信されたらエラー表示
        parts.year.addEventListener('invalid', () => {
            root.classList.add('is-invalid');
            error.textContent = parts.year.validationMessage;
            error.hidden = false;
        });

        // ── カレンダー ───────────────────────────
        let viewYear, viewMonth; // 表示中の年・月（月は 0 始まり）

        function position() {
            const rect = root.getBoundingClientRect();
            const calRect = calendar.getBoundingClientRect();
            const margin = 8;
            let top = rect.bottom + 4;
            if (top + calRect.height > window.innerHeight - margin && rect.top - calRect.height - 4 > margin) {
                top = rect.top - calRect.height - 4; // 下に入らなければ上に出す
            }
            const left = Math.min(Math.max(margin, rect.left), window.innerWidth - calRect.width - margin);
            calendar.style.top = `${Math.max(margin, top)}px`;
            calendar.style.left = `${Math.max(margin, left)}px`;
        }

        function render(focusIso) {
            const selected = selectedDate();
            const todayIso = toIso(new Date());
            const first = new Date(viewYear, viewMonth, 1);
            const start = new Date(viewYear, viewMonth, 1 - first.getDay());

            let html = `
                <div class="admin_date-input_cal-header">
                    <button type="button" class="admin_date-input_cal-nav" data-nav="-1" aria-label="前の月">‹</button>
                    <span class="admin_date-input_cal-title" aria-live="polite">${viewYear}年${viewMonth + 1}月</span>
                    <button type="button" class="admin_date-input_cal-nav" data-nav="1" aria-label="次の月">›</button>
                </div>
                <div class="admin_date-input_cal-grid" role="grid">`;
            WEEKDAYS.forEach((w, i) => {
                const mod = i === 0 ? ' admin_date-input_cal-weekday--sun' : i === 6 ? ' admin_date-input_cal-weekday--sat' : '';
                html += `<span class="admin_date-input_cal-weekday${mod}">${w}</span>`;
            });
            for (let i = 0; i < 42; i++) {
                const d = new Date(start.getFullYear(), start.getMonth(), start.getDate() + i);
                const iso = toIso(d);
                const cls = ['admin_date-input_cal-day'];
                if (d.getMonth() !== viewMonth) cls.push('is-other-month');
                if (iso === todayIso) cls.push('is-today');
                if (selected && iso === toIso(selected)) cls.push('is-selected');
                html += `<button type="button" class="${cls.join(' ')}" data-day="${iso}" tabindex="-1"
                            aria-label="${d.getFullYear()}年${d.getMonth() + 1}月${d.getDate()}日">${d.getDate()}</button>`;
            }
            html += `</div>
                <div class="admin_date-input_cal-footer">
                    <button type="button" class="admin_date-input_cal-action" data-action="today">今日</button>
                    <button type="button" class="admin_date-input_cal-action" data-action="clear">クリア</button>
                </div>`;
            calendar.innerHTML = html;

            // キーボード操作用のフォーカス位置（選択日 → 今日 → 1日）
            const target = calendar.querySelector(`[data-day="${focusIso}"]`)
                || calendar.querySelector('.is-selected')
                || calendar.querySelector(`[data-day="${todayIso}"]:not(.is-other-month)`)
                || calendar.querySelector('.admin_date-input_cal-day:not(.is-other-month)');
            target.tabIndex = 0;
            return target;
        }

        function open() {
            const base = selectedDate() || new Date();
            viewYear = base.getFullYear();
            viewMonth = base.getMonth();
            calendar.hidden = false;
            const target = render();
            position();
            toggle.setAttribute('aria-expanded', 'true');
            target.focus();
        }

        function close(returnFocus = false) {
            if (calendar.hidden) return;
            calendar.hidden = true;
            toggle.setAttribute('aria-expanded', 'false');
            if (returnFocus) toggle.focus();
        }

        function moveMonth(delta, focusIso) {
            const d = new Date(viewYear, viewMonth + delta, 1);
            viewYear = d.getFullYear();
            viewMonth = d.getMonth();
            render(focusIso).focus();
            position();
        }

        toggle.addEventListener('click', () => (calendar.hidden ? open() : close(true)));

        calendar.addEventListener('click', (e) => {
            const nav = e.target.closest('[data-nav]');
            const day = e.target.closest('[data-day]');
            const action = e.target.closest('[data-action]');
            if (nav) {
                moveMonth(Number(nav.dataset.nav));
            } else if (day) {
                const [y, m, d] = day.dataset.day.split('-').map(Number);
                setDate(new Date(y, m - 1, d));
                close(true);
            } else if (action) {
                setDate(action.dataset.action === 'today' ? new Date() : null);
                close(true);
            }
        });

        // 矢印キーで日付移動、Enter で決定、Esc で閉じる
        calendar.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                e.preventDefault();
                close(true);
                return;
            }
            const current = e.target.closest('[data-day]');
            const step = { ArrowLeft: -1, ArrowRight: 1, ArrowUp: -7, ArrowDown: 7 }[e.key];
            if (!current || step === undefined) return;
            e.preventDefault();
            const [y, m, d] = current.dataset.day.split('-').map(Number);
            const target = new Date(y, m - 1, d + step);
            const iso = toIso(target);
            if (target.getMonth() !== viewMonth || target.getFullYear() !== viewYear) {
                moveMonth((target.getFullYear() - viewYear) * 12 + target.getMonth() - viewMonth, iso);
            } else {
                current.tabIndex = -1;
                const el = calendar.querySelector(`[data-day="${iso}"]`);
                el.tabIndex = 0;
                el.focus();
            }
        });

        document.addEventListener('mousedown', (e) => {
            if (!calendar.hidden && !root.contains(e.target)) close();
        });
        window.addEventListener('resize', () => { if (!calendar.hidden) position(); });
        window.addEventListener('scroll', () => { if (!calendar.hidden) position(); }, true);

        toggle.setAttribute('aria-expanded', 'false');
        update(false);
    }

    document.querySelectorAll('[data-date-input]').forEach(init);
})();
