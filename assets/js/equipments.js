/* Equipments Module JS */

function validate_equipment_form(selector) {
    selector = typeof selector == 'undefined' ? '#equipment-form' : selector;

    appValidateForm($(selector), {
        clientid: {
            required: {
                depends: function() {
                    return !$('select#clientid').hasClass('customer-removed');
                },
            },
        },
        date:     'required',
        currency: 'required',
        number:   { required: true },
    });

    $('body').find('input[name="number"]').rules('add', {
        remote: {
            url:  admin_url + 'equipments/validate_equipment_number',
            type: 'post',
            data: {
                number: function() {
                    return $('input[name="number"]').val();
                },
                isedit: function() {
                    return $('input[name="number"]').data('isedit');
                },
                original_number: function() {
                    return $('input[name="number"]').data('original-number');
                },
                date: function() {
                    return $('body').find('.equipment-form input[name="date"]').val();
                },
            },
        },
        messages: {
            remote: app.lang.estimate_number_exists,
        },
    });
}

function equipment_mark_status(e, id, status) {
    e.preventDefault();
    $.post(admin_url + 'equipments/mark_action_status/' + status + '/' + id, function(resp) {
        if (resp.success) {
            alert_float('success', resp.message);
            $('#equipment-status-badge').html(resp.status_html);
            $('li[data-mark-status]').show();
            $('li[data-mark-status="' + status + '"]').hide();
            if ($.fn.DataTable.isDataTable('#equipments')) {
                $('#equipments').DataTable().ajax.reload(null, false);
            }
            // Reload quick stats counters
            $.get(admin_url + 'equipments/quick_stats', function(qs) {
                if (qs.success) {
                    $('.quick-top-stats .tw-grid').html(qs.html);
                }
            }, 'json');
        } else {
            alert_float('danger', resp.message);
        }
    }, 'json');
}

function equipment_pipeline() {
    init_kanban(
        'equipments/get_pipeline',
        equipments_pipeline_update,
        '.pipeline-status',
        290,
        360
    );
}

function equipments_pipeline_sort(type) {
    kan_ban_sort(type, equipment_pipeline);
}

function equipments_pipeline_update(ui, object) {
    if (object === ui.item.parent()[0]) {
        var data = {
            equipmentid: $(ui.item).attr('data-equipment-id'),
            status: $(ui.item.parent()[0]).attr('data-status-id'),
            order: [],
        };

        $.each($(ui.item).parents('.pipeline-status').find('li'), function(idx, el) {
            var id = $(el).attr('data-equipment-id');
            if (id) {
                data.order.push([id, idx + 1]);
            }
        });

        check_kanban_empty_col('[data-equipment-id]');

        setTimeout(function() {
            $.post(admin_url + 'equipments/update_pipeline', data).done(function(response) {
                update_kan_ban_total_when_moving(ui, data.status);
                equipment_pipeline();
            });
        }, 500);
    }
}

function equipment_pipeline_open(id) {
    if (id === '') {
        return;
    }
    requestGet('equipments/pipeline_open/' + id).done(function(response) {
        var visible = $('.equipment-pipeline:visible').length > 0;
        $('#equipment').html(response);
        if (!visible) {
            $('.equipment-pipeline').modal({
                show: true,
                backdrop: 'static',
                keyboard: false,
            });
        } else {
            $('#equipment')
                .find('.modal.equipment-pipeline')
                .removeClass('fade')
                .addClass('in')
                .css('display', 'block');
        }
    });
}

function init_equipment_notes() {
    $("body").off("submit", "#equipment-notes").on("submit", "#equipment-notes", function () {
        var form = $(this);
        var description = form.find('textarea[name="description"]').val().trim();
        if (description === "") {
            return false;
        }
        $.post(form.attr("action"), form.serialize()).done(function (rel_id) {
            form.find('textarea[name="description"]').val("");
            get_sales_notes(rel_id, "equipments");
        });
        return false;
    });
}

function init_myequipment_notes() {
    $("body").off("submit", "#myequipment-notes").on("submit", "#myequipment-notes", function () {
        var form = $(this);
        var description = form.find('textarea[name="description"]').val().trim();
        if (description === "") { return false; }
        $.post(form.attr("action"), form.serialize()).done(function (rel_id) {
            form.find('textarea[name="description"]').val("");
            get_sales_notes(rel_id, "equipments/myequipments");
        });
        return false;
    });
}

function init_equipment(id) {
    load_small_table_item(
        id,
        "#equipment",
        "equipmentid",
        "equipments/get_equipment_data_ajax",
        ".table-equipments"
    );
}

function init_subgroup(id) {
    load_small_table_item(
        id,
        "#subgroup",
        "subgroupid",
        "equipments/subgroups/get_subgroup_data_ajax",
        ".table-subgroups"
    );
}

function init_equipments_total(manual) {
    if ($("#equipments_total").length === 0) {
        return;
    }
    var _quo_total_href_manual = $(".equipments-total");
    if (
        $("body").hasClass("equipments-total-manual") &&
        typeof manual == "undefined" &&
        !_quo_total_href_manual.hasClass("initialized")
    ) {
        return;
    }
    _quo_total_href_manual.addClass("initialized");
    var currency = $("body").find('select[name="total_currency"]').val();
    var _years = $("body")
        .find('select[name="equipments_total_years"]')
        .selectpicker("val");
    var years = [];
    $.each(_years, function (i, _y) {
        if (_y !== "") {
            years.push(_y);
        }
    });

    var customer_id = "";
    var project_id = "";

    var _customer_id = $('.customer_profile input[name="userid"]').val();
    var _project_id = $('input[name="project_id"]').val();
    if (typeof _customer_id != "undefined") {
        customer_id = _customer_id;
    } else if (typeof _project_id != "undefined") {
        project_id = _project_id;
    }

    $.post(admin_url + "equipments/get_equipments_total", {
        currency: currency,
        init_total: true,
        years: years,
        customer_id: customer_id,
        project_id: project_id,
    }).done(function (response) {
        $("#equipments_total").html(response);
    });
}

function init_equipment_equipment_select() {
    var $sel = $('#equipment_equipment_select');
    if (!$sel.length) { return; }

    $sel.on('changed.bs.select', function(e, clickedIndex) {
        if (clickedIndex === undefined) { return; }
        var id = $(this).val();
        if (!id) { return; }

        $.getJSON(admin_url + 'equipments/get_equipment_data/' + id, function(eq) {
            if (!eq || !eq.id) { return; }

            if ($('#equipment-equipment-tbody tr[data-id="' + eq.id + '"]').length) {
                alert_float('warning', 'Equipment already added.');
                $sel.selectpicker('val', '');
                return;
            }

            var idx = $('#equipment-equipment-tbody tr').length;
            var row = '<tr class="equipment-equipment-row" data-id="' + eq.id + '">'
                + '<td>' + $('<span>').text(eq.item_name).html()
                + '<input type="hidden" name="equipment[' + idx + '][customer_equipment_id]" value="' + eq.id + '"></td>'
                + '<td>' + $('<span>').text(eq.unit_code).html() + '</td>'
                + '<td>' + $('<span>').text(eq.serial_number || '').html() + '</td>'
                + '<td>' + $('<span>').text(eq.location || '').html() + '</td>'
                + '<td>' + $('<span>').text(eq.cert_expired_date || '').html() + '</td>'
                + '<td><a href="#" class="btn btn-danger btn-sm equipment-equipment-remove" data-id="' + eq.id + '">'
                + '<i class="fa fa-times"></i></a></td>'
                + '</tr>';

            $('#equipment-equipment-tbody').append(row);
            $sel.selectpicker('val', '');
        });
    });

    $(document).off('click', '.equipment-equipment-remove').on('click', '.equipment-equipment-remove', function(e) {
        e.preventDefault();
        $(this).closest('tr').remove();
        $('#equipment-equipment-tbody tr').each(function(i) {
            $(this).find('input[type="hidden"]').attr('name', 'equipment[' + i + '][customer_equipment_id]');
        });
    });
}

function init_ajax_equipment_task_search_by_project_id() {
    init_ajax_search('equipment_task', '#equipment_task_select.ajax-search', {
        project_id: function() { return $('#project_id').val(); }
    });

    $('body').off('change.equipment_task', '#project_id')
             .on('change.equipment_task', '#project_id', function() {
        $('#equipment_task_select').selectpicker('val', '');
    });

    init_equipment_task_select();
}

function init_equipment_task_select() {
    var $sel = $('#equipment_task_select');
    if (!$sel.length) { return; }

    $sel.on('changed.bs.select', function(e, clickedIndex) {
        if (clickedIndex === undefined) { return; }
        var id = $(this).val();
        if (!id) { return; }

        if ($('#equipment-tasks-tbody tr[data-id="' + id + '"]').length) {
            alert_float('warning', 'Task already added.');
            $sel.selectpicker('val', '');
            return;
        }

        $.getJSON(admin_url + 'equipments/get_task_data/' + id, function(task) {
            if (!task || !task.id) { return; }

            var idx      = $('#equipment-tasks-tbody tr').length;
            var taskName  = $('<span>').text(task.description).html();

            function buildAndAppend(itemName, unitCode, serialNo, location, certExpired) {
                var row = '<tr class="equipment-task-row" data-id="' + task.id + '">'
                    + '<td>' + taskName
                    + '<input type="hidden" name="tasks[' + idx + '][task_id]" value="' + task.id + '"></td>'
                    + '<td>' + itemName   + '</td>'
                    + '<td>' + unitCode   + '</td>'
                    + '<td>' + serialNo   + '</td>'
                    + '<td>' + location   + '</td>'
                    + '<td>' + certExpired + '</td>'
                    + '<td><a href="#" class="btn btn-danger btn-sm equipment-task-remove" data-id="' + task.id + '">'
                    + '<i class="fa fa-times"></i></a></td>'
                    + '</tr>';
                $('#equipment-tasks-tbody').append(row);
                $sel.selectpicker('val', '');
            }

            if (task.customer_equipment_id) {
                $.getJSON(admin_url + 'equipments/get_equipment_data/' + task.customer_equipment_id, function(eq) {
                    buildAndAppend(
                        eq ? $('<span>').text(eq.item_name).html()            : '',
                        eq ? $('<span>').text(eq.unit_code).html()            : '',
                        eq ? $('<span>').text(eq.serial_number || '').html()  : '',
                        eq ? $('<span>').text(eq.location || '').html()       : '',
                        eq ? $('<span>').text(eq.cert_expired_date || '').html() : ''
                    );
                });
            } else {
                buildAndAppend('', '', '', '', '');
            }
        });
    });

    $(document).off('click', '.equipment-task-remove').on('click', '.equipment-task-remove', function(e) {
        e.preventDefault();
        $(this).closest('tr').remove();
        $('#equipment-tasks-tbody tr').each(function(i) {
            $(this).find('input[type="hidden"]').attr('name', 'tasks[' + i + '][task_id]');
        });
    });
}

function init_ajax_project_search_by_customer_id() {
    init_ajax_search('equipment_project', '#project_id.ajax-search', {
        customer_id: function() { return $('#clientid').val(); }
    });

    $('body').off('change.equipment_project', '.f_client_id #clientid')
             .on('change.equipment_project', '.f_client_id #clientid', function() {
        var clientid = $(this).val();
        var $wrapper = $('.projects-wrapper');

        // Reset equipment and task selects when customer changes
        $('#equipment_equipment_select').selectpicker('val', '');
        $('#equipment_task_select').selectpicker('val', '');

        if (!clientid) {
            $wrapper.addClass('hide');
            $('#project_id').selectpicker('val', '');
            return;
        }
        $.post(admin_url + 'misc/get_relation_data', {
            type: 'equipment_project',
            customer_id: clientid,
            q: ''
        }, function(data) {
            if (data && data.length > 0) {
                $wrapper.removeClass('hide');
            } else {
                $wrapper.addClass('hide');
                $('#project_id').selectpicker('val', '');
            }
        }, 'json');
    });
}

/**
 * Subgroups filtered by group_id selection
 * Usage: call in form that has #group_id and #subgroup_id.ajax-search
 */
function init_ajax_search_subgroups_by_group_id() {
    init_ajax_search('equipment_subgroup', '#subgroup_id.ajax-search', {
        group_id: function() { return $('#group_id').val(); }
    });

    $('body').off('change.equipment_subgroup', '#group_id')
             .on('change.equipment_subgroup', '#group_id', function() {
        $('#subgroup_id').selectpicker('val', '');
        $('#category_id').selectpicker('val', '');
    });
}

/**
 * Categories filtered by subgroup_id selection
 * Usage: call in form that has #subgroup_id and #category_id.ajax-search
 */
function init_ajax_search_categories_by_subgroup_id() {
    init_ajax_search('equipment_category', '#category_id.ajax-search', {
        subgroup_id: function() { return $('#subgroup_id').val(); }
    });

    $('body').off('change.equipment_category', '#subgroup_id')
             .on('change.equipment_category', '#subgroup_id', function() {
        $('#category_id').selectpicker('val', '');
    });
}
