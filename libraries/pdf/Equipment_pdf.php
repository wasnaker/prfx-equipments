<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Equipment_pdf extends App_pdf
{
    protected $equipment;

    private $equipment_number;

    public function __construct($equipment, $tag = '')
    {
        $this->load_language($equipment->client_id);

        $equipment                = hooks()->apply_filters('equipment_html_pdf_data', $equipment);
        $GLOBALS['equipment_pdf'] = $equipment;

        parent::__construct();

        $this->tag             = $tag;
        $this->equipment        = $equipment;
        $this->equipment_number = format_equipment_number($this->equipment->id);

        $this->SetTitle($this->equipment_number);
    }

    public function prepare()
    {
        $this->with_number_to_word($this->equipment->client_id);

        $this->set_view_vars([
            'status'          => $this->equipment->status,
            'equipment_number' => $this->equipment_number,
            'equipment'        => $this->equipment,
        ]);

        return $this->build();
    }

    protected function type()
    {
        return 'equipment';
    }

    protected function file_path()
    {
        $theme      = active_clients_theme();
        $customPath = APPPATH . 'views/themes/' . $theme . '/views/my_equipmentpdf.php';
        $actualPath = FCPATH . 'modules/equipments/views/themes/' . $theme . '/views/equipmentpdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
