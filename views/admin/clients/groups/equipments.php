<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (isset($client)) { ?>
<h4 class="customer-profile-group-heading">
    <?= _l('equipments'); ?></h4>

<?php if (staff_can('create', 'equipments')) { ?>
<a href="<?= admin_url('equipments/equipment?customer_id=' . $client->userid); ?>"
    class="btn btn-primary mbot15<?= $client->active == 0 ? ' disabled' : ''; ?>">
    <i class="fa-regular fa-plus tw-mr-1"></i>
    <?= _l('create_new_equipment'); ?>
</a>
<?php } ?>
<?php if (staff_can('view', 'equipments') || staff_can('view_own', 'equipments') || get_option('allow_staff_view_equipments_assigned') == '1') { ?>
<a href="#" class="btn btn-default mbot15" data-toggle="modal" data-target="#client_zip_equipments">
    <i class="fa-regular fa-file-zipper tw-mr-1"></i>
    <?= _l('zip_equipments'); ?>
</a>
<?php } ?>
<div id="equipments_total" class="tw-mb-5"></div>
<?php
    $this->load->view('admin/equipments/table_html', ['class' => 'equipments-single-client']);
    $this->load->view('admin/clients/modals/zip_equipments');
    ?>
<?php } ?>