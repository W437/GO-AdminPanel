@extends('layouts.admin.app')

@section('title', translate('messages.database_manager'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .db-manager-wrapper {
            min-height: 70vh;
        }
        .db-table-list {
            max-height: 70vh;
            overflow-y: auto;
        }
        .db-table-list .list-group-item {
            border: none;
            border-bottom: 1px solid #f1f3f5;
            cursor: pointer;
        }
        .db-table-list .list-group-item.active {
            background: rgba(59, 125, 221, .1);
            color: #1e2022;
        }
        .db-table-loader {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, .65);
            z-index: 2;
            font-weight: 600;
            color: #677788;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
        }
        .db-table-loader.active {
            opacity: 1;
            pointer-events: all;
        }
        .db-empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #8c98a4;
        }
        .db-columns-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: .75rem;
        }
        .db-columns-grid .db-column-item {
            padding: .75rem;
            border: 1px solid #e7eaf3;
            border-radius: .5rem;
        }
        .db-columns-grid .db-column-item h6 {
            font-size: .875rem;
            margin-bottom: .25rem;
        }
        #db-row-editor .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title text-capitalize mb-3">
                {{ translate('messages.database_manager') }}
            </h1>
            <p class="text-muted mb-0">
                {{ translate('messages.explore_database_tables_edit_records_and_review_structures') }}
            </p>
        </div>

        <div class="row g-3 mb-4" id="db-stats">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase mb-1">{{ translate('messages.total_tables') }}</h6>
                        <div class="d-flex align-items-baseline">
                            <span class="display-4 text-dark" id="db-stat-tables">{{ number_format($stats['table_count'] ?? 0) }}</span>
                        </div>
                        <small class="text-muted">{{ translate('messages.tables_detected_in_this_database') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase mb-1">{{ translate('messages.total_rows') }}</h6>
                        <div class="d-flex align-items-baseline">
                            <span class="display-4 text-dark" id="db-stat-rows">{{ number_format($stats['total_rows'] ?? 0) }}</span>
                        </div>
                        <small class="text-muted">{{ translate('messages.rows_across_visible_tables') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase mb-1">{{ translate('messages.database_size') }}</h6>
                        <div class="d-flex align-items-baseline">
                            <span class="display-4 text-dark" id="db-stat-size">{{ $stats['database_size_human'] ?? '0 B' }}</span>
                        </div>
                        <small class="text-muted">{{ translate('messages.approx_storage_used') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 db-manager-wrapper">
            <div class="col-lg-3">
                <div class="card h-100">
                    <div class="card-header pb-2">
                        <h5 class="card-title mb-3">{{ translate('messages.tables') }}</h5>
                        <div class="input-group input-group-merge">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="tio-search"></i></span>
                            </div>
                            <input type="search" id="db-table-search" class="form-control"
                                placeholder="{{ translate('messages.search_tables') }}">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush db-table-list" id="db-table-list"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="card h-100">
                    <div class="card-header flex-between">
                        <div>
                            <h5 class="mb-1" id="db-active-table">{{ $defaultTable ?? translate('messages.no_table_selected') }}</h5>
                            <small class="text-muted" id="db-active-table-meta"></small>
                        </div>
                        <div class="d-flex align-items-center" style="column-gap: .75rem;">
                            <select class="custom-select w-auto" id="db-per-page">
                                <option value="25">25</option>
                                <option value="50" selected>50</option>
                                <option value="100">100</option>
                            </select>
                            <button class="btn btn-outline-primary" type="button" id="db-refresh">{{ translate('messages.refresh') }}</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-soft-warning d-none" id="db-sensitive-warning">
                            <strong>{{ translate('messages.sensitive_table') }}:</strong>
                            <span>{{ translate('messages.this_table_contains_sensitive_data_handle_with_care') }}</span>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">{{ translate('messages.columns') }}</h6>
                                <small class="text-muted" id="db-column-count"></small>
                            </div>
                            <div class="db-columns-grid" id="db-columns-grid"></div>
                        </div>

                        <div class="table-responsive position-relative">
                            <div class="db-table-loader" id="db-table-loader">
                                <span>{{ translate('messages.loading_table_data') }}</span>
                            </div>
                            <div id="db-empty-state" class="db-empty-state">
                                {{ translate('messages.select_a_table_to_load_rows') }}
                            </div>
                            <table class="table table-borderless table-hover align-middle mb-0" id="db-data-table">
                                <thead>
                                    <tr id="db-table-head"></tr>
                                </thead>
                                <tbody id="db-table-body"></tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4" id="db-pagination" style="display:none;">
                            <div>
                                <small class="text-muted" id="db-pagination-meta"></small>
                            </div>
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-secondary" type="button" id="db-prev">{{ translate('messages.previous') }}</button>
                                <button class="btn btn-outline-secondary" type="button" id="db-next">{{ translate('messages.next') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="db-row-editor" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form id="db-row-form">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ translate('messages.edit_record') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="db-row-fields"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('messages.cancel') }}</button>
                        <button type="submit" class="btn btn--primary">{{ translate('messages.save_changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        'use strict';
        (function ($) {
            const state = {
                tables: @json($tableMeta),
                selectedTable: @json($defaultTable),
                rows: [],
                columns: [],
                primaryKey: null,
                perPage: 50,
                page: 1,
                total: 0,
                loading: false,
                editing: {
                    primaryValue: null,
                },
                stats: @json($stats),
            };

            const endpoints = {
                base: "{{ url('admin/business-settings/db-manager') }}",
                tables: "{{ route('admin.business-settings.db-manager.tables') }}",
            };

            const elements = {
                tableList: $('#db-table-list'),
                tableSearch: $('#db-table-search'),
                activeTable: $('#db-active-table'),
                activeMeta: $('#db-active-table-meta'),
                perPage: $('#db-per-page'),
                refresh: $('#db-refresh'),
                loader: $('#db-table-loader'),
                emptyState: $('#db-empty-state'),
                head: $('#db-table-head'),
                body: $('#db-table-body'),
                pagination: $('#db-pagination'),
                paginationMeta: $('#db-pagination-meta'),
                prev: $('#db-prev'),
                next: $('#db-next'),
                columnsGrid: $('#db-columns-grid'),
                columnCount: $('#db-column-count'),
                rowModal: $('#db-row-editor'),
                rowFields: $('#db-row-fields'),
                rowForm: $('#db-row-form'),
                statTables: $('#db-stat-tables'),
                statRows: $('#db-stat-rows'),
                statSize: $('#db-stat-size'),
                sensitiveWarning: $('#db-sensitive-warning'),
            };

            const csrf = $('meta[name="csrf-token"]').attr('content');

            function formatNumber(value) {
                if (value === undefined || value === null || isNaN(value)) {
                    return '0';
                }
                    return Number(value).toLocaleString();
            }

            function getSelectedTableMeta() {
                return state.tables.find(item => item.name === state.selectedTable) || null;
            }

            function renderSensitiveWarning() {
                const meta = getSelectedTableMeta();
                const show = meta && meta.sensitive;
                elements.sensitiveWarning.toggleClass('d-none', !show);
            }

            function renderStats() {
                if (!state.stats) {
                    return;
                }

                elements.statTables.text(formatNumber(state.stats.table_count));
                elements.statRows.text(formatNumber(state.stats.total_rows));
                elements.statSize.text(state.stats.database_size_human || '0 B');
            }

            function renderTables(filter = '') {
                elements.tableList.empty();
                const normalized = filter.trim().toLowerCase();
                const filteredTables = state.tables.filter(item =>
                    !normalized || item.name.toLowerCase().includes(normalized)
                );

                if (!filteredTables.length) {
                    elements.tableList.append(
                        $('<div/>', { class: 'list-group-item text-center text-muted' }).text('{{ translate('messages.no_tables_found') }}')
                    );
                    return;
                }

                filteredTables.forEach(item => {
                    const button = $('<button/>', {
                        type: 'button',
                        class: 'list-group-item list-group-item-action d-flex justify-content-between align-items-center',
                    });
                    button.toggleClass('active', state.selectedTable === item.name);
                    button.append(
                        $('<span/>', { class: 'text-truncate mr-2' }).text(item.name)
                    );

                    const badgeWrapper = $('<div/>', { class: 'd-flex align-items-center' });
                    badgeWrapper.append(
                        $('<span/>', { class: 'badge badge-soft-primary badge-pill ml-2' }).text(item.rows)
                    );

                    if (item.sensitive) {
                        badgeWrapper.append(
                            $('<span/>', { class: 'badge badge-soft-danger badge-pill ml-1' }).text('{{ translate('messages.sensitive') }}')
                        );
                    }

                    button.append(badgeWrapper);
                    button.on('click', () => selectTable(item.name));
                    elements.tableList.append(button);
                });
            }

            function selectTable(name) {
                if (!name) {
                    return;
                }
                state.selectedTable = name;
                state.page = 1;
                renderTables(elements.tableSearch.val());
                loadTable();
            }

            function setLoading(status) {
                state.loading = status;
                elements.loader.toggleClass('active', status);
            }

            function loadTable() {
                if (!state.selectedTable) {
                    elements.emptyState.show();
                    elements.head.empty();
                    elements.body.empty();
                    elements.columnsGrid.empty();
                    elements.activeMeta.text('');
                    elements.pagination.hide();
                    elements.sensitiveWarning.addClass('d-none');
                    return;
                }

                elements.emptyState.hide();
                setLoading(true);
                const url = `${endpoints.base}/table/${encodeURIComponent(state.selectedTable)}`;
                $.get(url, { page: state.page, per_page: state.perPage })
                    .done((response) => {
                        state.columns = response.columns || [];
                        state.rows = response.rows || [];
                        state.primaryKey = response.primary_key;
                        state.total = response.total || 0;
                        state.perPage = response.per_page;
                        state.page = response.page;
                        elements.perPage.val(state.perPage);
                        updateTableView();
                    })
                    .fail(() => {
                        toastr.error('{{ translate('messages.failed_to_load_table_data') }}');
                    })
                    .always(() => setLoading(false));
            }

            function updateTableView() {
                elements.activeTable.text(state.selectedTable || '{{ translate('messages.no_table_selected') }}');
                elements.activeMeta.text(state.selectedTable ? `${state.total} {{ translate('messages.total_rows') }}` : '');
                renderSensitiveWarning();

                elements.head.empty();
                state.columns.forEach(column => {
                    const th = $('<th/>').text(`${column.name}`);
                    th.append('<br><small class="text-muted">' + column.type + '</small>');
                    elements.head.append(th);
                });

                if (state.primaryKey) {
                    elements.head.append($('<th/>').text('{{ translate('messages.actions') }}'));
                }

                elements.body.empty();

                if (!state.rows.length) {
                    const actionColumns = state.primaryKey ? 1 : 0;
                    const colspan = Math.max(1, state.columns.length + actionColumns);
                    const emptyRow = $('<tr/>').append(
                        $('<td/>', { colspan: colspan, class: 'text-center text-muted py-5' })
                            .text('{{ translate('messages.no_rows_found_for_this_table') }}')
                    );
                    elements.body.append(emptyRow);
                } else {
                    state.rows.forEach((row, index) => {
                        const tr = $('<tr/>');
                        state.columns.forEach(column => {
                            const raw = row[column.name];
                            let rendered;
                            if (raw === null || typeof raw === 'undefined') {
                                rendered = '<span class="text-muted">NULL</span>';
                            } else if (typeof raw === 'object') {
                                const codeWrapper = $('<code/>').text(JSON.stringify(raw));
                                rendered = $('<div/>').append(codeWrapper).html();
                            } else {
                                rendered = $('<div/>').text(raw).html();
                            }
                            tr.append($('<td/>').html(rendered));
                        });

                        if (state.primaryKey) {
                            const actionTd = $('<td/>');
                            const editBtn = $('<button/>', {
                                class: 'btn btn-sm btn-outline-primary',
                                text: '{{ translate('messages.edit') }}',
                                type: 'button',
                            });
                            editBtn.on('click', () => openEditor(index));
                            actionTd.append(editBtn);
                            tr.append(actionTd);
                        }
                        elements.body.append(tr);
                    });
                }

                renderColumns();
                updatePagination();
            }

            function renderColumns() {
                elements.columnsGrid.empty();
                if (!state.columns.length) {
                    elements.columnsGrid.append(
                        $('<div/>', { class: 'text-muted' }).text('{{ translate('messages.no_columns_to_display') }}')
                    );
                    elements.columnCount.text('');
                    return;
                }

                elements.columnCount.text(`${state.columns.length} {{ translate('messages.columns_total') }}`);

                state.columns.forEach(column => {
                    const item = $('<div/>', { class: 'db-column-item' });
                    item.append($('<h6/>').text(column.name));
                    const meta = [column.type];
                    if (column.length) {
                        meta.push(`(${column.length})`);
                    }
                    if (!column.not_null) {
                        meta.push('{{ translate('messages.nullable') }}');
                    }
                    if (column.autoincrement) {
                        meta.push('{{ translate('messages.auto_increment') }}');
                    }
                    item.append($('<p/>', { class: 'mb-0 text-muted small' }).text(meta.join(' ')));
                    if (column.default !== null && column.default !== undefined) {
                        const defaultValue = typeof column.default === 'object'
                            ? JSON.stringify(column.default)
                            : column.default;
                        item.append($('<small/>', { class: 'd-block text-muted' }).text('{{ translate('messages.default') }}: ' + defaultValue));
                    }
                    elements.columnsGrid.append(item);
                });
            }

            function updatePagination() {
                if (!state.rows.length) {
                    elements.pagination.hide();
                    return;
                }

                const from = ((state.page - 1) * state.perPage) + 1;
                const to = Math.min(state.page * state.perPage, state.total);
                elements.paginationMeta.text(`${from}-${to} {{ translate('messages.of') }} ${state.total}`);
                elements.pagination.show();
                elements.prev.prop('disabled', state.page === 1);
                const totalPages = Math.ceil(state.total / state.perPage) || 1;
                elements.next.prop('disabled', state.page >= totalPages);
            }

            function openEditor(index) {
                const row = state.rows[index];
                if (!row || !state.primaryKey) {
                    return;
                }

                const primaryValue = row[state.primaryKey];
                if (typeof primaryValue === 'undefined' || primaryValue === null) {
                    toastr.error('{{ translate('messages.unable_to_edit_row_without_primary_key_value') }}');
                    return;
                }

                state.editing.primaryValue = primaryValue;
                elements.rowFields.empty();

                state.columns.forEach(column => {
                    const group = $('<div/>', { class: 'form-group' });
                    const label = $('<label/>').text(`${column.name} (${column.type})`);
                    let input;
                    const value = row[column.name];
                    let safeValue = value === null || typeof value === 'undefined' ? '' : value;
                    if (typeof safeValue === 'object') {
                        safeValue = JSON.stringify(safeValue);
                    }

                    if (column.autoincrement && column.name === state.primaryKey) {
                        input = $('<input/>', { class: 'form-control', readonly: true, disabled: true });
                        input.val(safeValue);
                    } else if (column.type && column.type.indexOf('text') > -1) {
                        input = $('<textarea/>', { class: 'form-control', rows: 3, name: `values[${column.name}]` });
                        input.val(safeValue);
                    } else if (column.type === 'boolean') {
                        input = $('<select/>', { class: 'form-control', name: `values[${column.name}]` });
                        input.append('<option value="1">{{ translate('messages.true') }}</option>');
                        input.append('<option value="0">{{ translate('messages.false') }}</option>');
                        const normalized = String(safeValue).toLowerCase();
                        const truthy = ['1', 'true', 't', 'on', 'yes'];
                        input.val(truthy.includes(normalized) ? '1' : '0');
                    } else {
                        input = $('<input/>', { class: 'form-control', type: 'text', name: `values[${column.name}]` });
                        input.val(safeValue);
                    }

                    if (!column.not_null) {
                        input.attr('placeholder', '{{ translate('messages.leave_blank_for_null') }}');
                    }

                    group.append(label).append(input);
                    elements.rowFields.append(group);
                });

                elements.rowModal.modal('show');
            }

            function submitRow(event) {
                event.preventDefault();
                if (!state.primaryKey || state.editing.primaryValue === null) {
                    return;
                }

                    const url = `${endpoints.base}/table/${encodeURIComponent(state.selectedTable)}/rows/${encodeURIComponent(state.editing.primaryValue)}`;
                const formData = elements.rowForm.serializeArray();
                const payload = { _token: csrf, _method: 'PUT' };
                formData.forEach(item => {
                    payload[item.name] = item.value;
                });

                elements.rowForm.find('button[type="submit"]').prop('disabled', true);

                $.ajax({
                    url,
                    method: 'POST',
                    data: payload,
                })
                    .done(response => {
                        toastr.success(response.message || '{{ translate('messages.record_updated_successfully') }}');
                        elements.rowModal.modal('hide');
                        loadTable();
                    })
                    .fail(xhr => {
                        const message = xhr.responseJSON?.message || '{{ translate('messages.failed_to_update_record') }}';
                        toastr.error(message);
                    })
                    .always(() => {
                        elements.rowForm.find('button[type="submit"]').prop('disabled', false);
                    });
            }

            elements.tableSearch.on('input', function () {
                renderTables($(this).val());
            });

            elements.perPage.on('change', function () {
                state.perPage = parseInt($(this).val(), 10) || 50;
                state.page = 1;
                loadTable();
            });

            elements.refresh.on('click', function () {
                loadTable();
            });

            elements.prev.on('click', function () {
                if (state.page > 1) {
                    state.page -= 1;
                    loadTable();
                }
            });

            elements.next.on('click', function () {
                const totalPages = Math.ceil(state.total / state.perPage) || 1;
                if (state.page < totalPages) {
                    state.page += 1;
                    loadTable();
                }
            });

            elements.rowForm.on('submit', submitRow);

            renderTables();
            renderStats();
            if (state.selectedTable) {
                loadTable();
            }
        })(jQuery);
    </script>
@endpush
