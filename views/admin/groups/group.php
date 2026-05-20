<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <?php $this->load->view('admin/groups/group_template'); ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    appValidateForm($('#group-form'), {
        name: 'required'
    });
});
</script>
</body>
</html>
