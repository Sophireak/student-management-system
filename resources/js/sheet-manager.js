// resources/js/sheet-manager.js
// Single object that manages all sheet interactions.
// Imported once, works across monthly, semester, annual sheets.

const SheetManager = (function () {

    // ── State ────────────────────────────────────────────────────
    let totalRows  = 0;
    let totalCols  = 0;
    let modified   = new Set();  // tracks which cells changed
    let maxScore   = 100;

    // ── Initialise ───────────────────────────────────────────────
    function init(options = {}) {
        totalRows = options.totalRows ?? 0;
        totalCols = options.totalCols ?? 0;
        maxScore  = options.maxScore  ?? 100;

        bindEvents();
        recalculateAll();
        updateStatusBar();

        // Warn before leaving with unsaved changes
        window.addEventListener('beforeunload', function (e) {
            if (modified.size > 0) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Clear warning on form submit
        const form = document.getElementById('reportForm');
        if (form) {
            form.addEventListener('submit', function () {
                modified.clear();
            });
        }
    }

    // ── Bind events to all cell inputs ───────────────────────────
    function bindEvents() {
        const inputs = document.querySelectorAll(
            '.cell-input:not([readonly]):not([disabled])'
        );

        inputs.forEach(input => {
            // Keyboard navigation
            input.addEventListener('keydown', handleKeydown);

            // Value change feedback
            input.addEventListener('input', handleInput);
            input.addEventListener('change', handleChange);

            // Focus — select content for fast overwrite
            input.addEventListener('focus', function () {
                if (this.type === 'number' || this.type === 'text') {
                    this.select();
                }
                updateCellStatus(this);
            });

            input.addEventListener('blur', function () {
                clearCellStatus();
            });
        });
    }

    // ── Keyboard Navigation ──────────────────────────────────────
    function handleKeydown(e) {
        const row = parseInt(this.dataset.row);
        const col = parseInt(this.dataset.col);

        // Only navigate for number inputs — selects use native keys
        if (this.tagName === 'SELECT') return;

        let nextRow = row;
        let nextCol = col;

        switch (e.key) {
            case 'ArrowRight':
            case 'Tab':
                if (e.shiftKey && e.key === 'Tab') {
                    nextCol = col - 1 >= 0 ? col - 1 : totalCols - 1;
                    if (nextCol === totalCols - 1)
                        nextRow = row - 1 >= 0 ? row - 1 : totalRows - 1;
                } else {
                    e.preventDefault();
                    nextCol = col + 1 < totalCols ? col + 1 : 0;
                    if (nextCol === 0)
                        nextRow = row + 1 < totalRows ? row + 1 : 0;
                }
                break;
            case 'ArrowLeft':
                nextCol = col - 1 >= 0 ? col - 1 : totalCols - 1;
                if (nextCol === totalCols - 1)
                    nextRow = row - 1 >= 0 ? row - 1 : totalRows - 1;
                break;
            case 'ArrowDown':
            case 'Enter':
                e.preventDefault();
                nextRow = row + 1 < totalRows ? row + 1 : 0;
                break;
            case 'ArrowUp':
                e.preventDefault();
                nextRow = row - 1 >= 0 ? row - 1 : totalRows - 1;
                break;
            case 'Escape':
                resetCell(this);
                return;
            default:
                return;
        }

        focusCell(nextRow, nextCol);
    }

    // ── Input handler (while typing) ─────────────────────────────
    function handleInput() {
        const cellKey = cellId(this);
        const type    = this.dataset.type;
        const original = this.dataset.original ?? '';

        if (type === 'numeric') {
            const val = parseFloat(this.value);
            const max = parseFloat(this.dataset.max ?? maxScore);

            if (this.value === '') {
                setCellState(this, 'empty');
                modified.delete(cellKey);
            } else if (isNaN(val) || val < 0 || val > max) {
                setCellState(this, 'invalid');
                modified.add(cellKey);
            } else {
                setCellState(this, this.value != original ? 'modified' : 'saved');
                if (this.value != original) {
                    modified.add(cellKey);
                } else {
                    modified.delete(cellKey);
                }
            }
        }

        recalculateRow(parseInt(this.dataset.row));
        recalculateColumn(parseInt(this.dataset.col));
        updateStatusBar();
    }

    // ── Change handler (for selects) ─────────────────────────────
    function handleChange() {
        const cellKey  = cellId(this);
        const original = this.dataset.original ?? '';

        if (this.value === '') {
            setCellState(this, 'empty');
            modified.delete(cellKey);
        } else if (this.value !== original) {
            setCellState(this, 'modified');
            modified.add(cellKey);
        } else {
            setCellState(this, 'saved');
            modified.delete(cellKey);
        }

        updateStatusBar();
    }

    // ── Set cell visual state ────────────────────────────────────
    function setCellState(input, state) {
        input.classList.remove(
            'cell-empty', 'cell-saved', 'cell-modified', 'cell-invalid'
        );
        input.classList.add(`cell-${state}`);
    }

    // ── Focus a specific cell by row+col ─────────────────────────
    function focusCell(row, col) {
        const next = document.querySelector(
            `.cell-input[data-row="${row}"][data-col="${col}"]`
        );
        if (next) {
            next.focus();
            if (next.select) next.select();
        }
    }

    // ── Reset a single cell to its original value ─────────────────
    function resetCell(input) {
        const original = input.dataset.original ?? '';
        input.value    = original;
        const cellKey  = cellId(input);
        modified.delete(cellKey);
        setCellState(input, original !== '' ? 'saved' : 'empty');
        recalculateRow(parseInt(input.dataset.row));
        updateStatusBar();
    }

    // ── Recalculate row average ──────────────────────────────────
    function recalculateRow(row) {
        const avgEl = document.getElementById(`row-avg-${row}`);
        if (! avgEl) return;

        const inputs = document.querySelectorAll(
            `.cell-input[data-row="${row}"][data-type="numeric"]`
        );
        let sum = 0, count = 0;

        inputs.forEach(inp => {
            const v = parseFloat(inp.value);
            if (! isNaN(v) && inp.value !== '') { sum += v; count++; }
        });

        avgEl.textContent = count > 0
            ? (sum / count).toFixed(1)
            : '—';
    }

    // ── Recalculate column average ───────────────────────────────
    function recalculateColumn(col) {
        const avgEl = document.getElementById(`col-avg-${col}`);
        if (! avgEl) return;

        const inputs = document.querySelectorAll(
            `.cell-input[data-col="${col}"][data-type="numeric"]`
        );
        let sum = 0, count = 0;

        inputs.forEach(inp => {
            const v = parseFloat(inp.value);
            if (! isNaN(v) && inp.value !== '') { sum += v; count++; }
        });

        avgEl.textContent = count > 0
            ? (sum / count).toFixed(1)
            : '—';
    }

    // ── Recalculate all averages on load ─────────────────────────
    function recalculateAll() {
        for (let r = 0; r < totalRows; r++) recalculateRow(r);
        for (let c = 0; c < totalCols; c++) recalculateColumn(c);
    }

    // ── Update the status bar counters ───────────────────────────
    function updateStatusBar() {
        const allInputs = document.querySelectorAll('.cell-input');
        let filled      = 0;

        allInputs.forEach(inp => {
            if (inp.value !== '' && inp.value !== null) filled++;
        });

        const modEl  = document.getElementById('modifiedCount');
        const filEl  = document.getElementById('filledCount');
        const saveBtn = document.getElementById('saveBtn');
        const total   = totalRows * totalCols;

        if (modEl)  modEl.textContent  = `${modified.size} modified`;
        if (filEl)  filEl.textContent  = `${filled} / ${total} filled`;
        if (saveBtn) {
            saveBtn.disabled = modified.size === 0;
        }
    }

    // ── Cell status bar (bottom of screen) ───────────────────────
    function updateCellStatus(input) {
        const status = document.getElementById('cellStatus');
        if (! status) return;

        const row = parseInt(input.dataset.row) + 1;
        const col = parseInt(input.dataset.col) + 1;
        const max = input.dataset.max ?? '';

        status.textContent = `Row ${row}, Col ${col}${max ? ` — Max: ${max}` : ''}`;
    }

    function clearCellStatus() {
        const status = document.getElementById('cellStatus');
        if (status) status.textContent = 'Ready';
    }

    // ── Public: fill all empty numeric cells with 0 ──────────────
    function fillEmpty() {
        const inputs = document.querySelectorAll(
            '.cell-input[data-type="numeric"]:not([readonly])'
        );
        inputs.forEach(inp => {
            if (inp.value === '') {
                inp.value = 0;
                setCellState(inp, 'modified');
                modified.add(cellId(inp));
                recalculateRow(parseInt(inp.dataset.row));
                recalculateColumn(parseInt(inp.dataset.col));
            }
        });
        updateStatusBar();
    }

    // ── Public: reset all modified cells ─────────────────────────
    function resetModified() {
        if (modified.size === 0) return;
        if (! confirm('Reset all unsaved changes?')) return;

        document.querySelectorAll('.cell-input').forEach(inp => {
            const original = inp.dataset.original ?? '';
            if (inp.value !== original) {
                inp.value = original;
                setCellState(inp, original !== '' ? 'saved' : 'empty');
            }
        });

        modified.clear();
        recalculateAll();
        updateStatusBar();
    }

    // ── Helpers ──────────────────────────────────────────────────
    function cellId(input) {
        return `${input.dataset.row}:${input.dataset.col}`;
    }

    // ── Public API ───────────────────────────────────────────────
    return { init, fillEmpty, resetModified };

})();