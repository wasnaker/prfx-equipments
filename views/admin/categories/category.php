<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <?php $this->load->view('admin/categories/category_template'); ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
var _cat_all_subgroups = <?= json_encode(array_values($subgroups)); ?>;

$(function() {
    $('#group_id').on('changed.bs.select', function() {
        _cat_refresh_subgroups($(this).val(), '');
    });
    appValidateForm($('#category-form'), { name: 'required', subgroup_id: 'required' });
});

function _cat_refresh_subgroups(group_id, selected_id) {
    var filtered = group_id
        ? _cat_all_subgroups.filter(function(sg) { return sg.group_id == group_id; })
        : _cat_all_subgroups;

    var $sel = $('#subgroup_id');
    $sel.find('option:not([value=""])').remove();

    $.each(filtered, function(i, sg) {
        var opt = $('<option>').val(sg.id).text(sg.name);
        if (selected_id && sg.id == selected_id) { opt.attr('selected', true); }
        $sel.append(opt);
    });
    $sel.selectpicker('refresh');
}
</script>
</body>
</html>
