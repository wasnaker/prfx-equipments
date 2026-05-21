<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!class_exists('Api_base')) {
    require_once(FCPATH . 'modules/apps/controllers/Api_base.php');
}

/**
 * Customer Equipment Portal API — JWT-based.
 *
 * All routes require: Authorization: Bearer {access_token}  (client_type = customer)
 *
 * GET  /api/v1/customer/equipment           — paginated list scoped to customer
 * GET  /api/v1/customer/equipment/{id}      — single equipment detail
 */
class Myequipments_api extends Api_base
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('equipments/myequipments_model');
    }

    // ── GET /api/v1/customer/equipment ───────────────────────────────────────

    public function index()
    {
        $this->_auth_customer();

        $pfx    = db_prefix();
        $page   = max(1, (int) ($this->input->get('page') ?? 1));
        $limit  = min(50, max(1, (int) ($this->input->get('per_page') ?? 20)));
        $offset = ($page - 1) * $limit;

        $status = $this->input->get('status');
        $search = trim($this->input->get('q') ?? '');

        $this->db->select(
            "{$pfx}customer_equipment.*,
             {$pfx}items.description as item_name,
             {$pfx}items.code as item_code,
             (SELECT name FROM {$pfx}items_subgroups WHERE id = {$pfx}items.subgroup_id) as subgroup_name,
             (SELECT name FROM {$pfx}items_groups    WHERE id = {$pfx}items.group_id)    as group_name",
            false
        );
        $this->db->from("{$pfx}customer_equipment");
        $this->db->join("{$pfx}items", "{$pfx}items.id = {$pfx}customer_equipment.item_id", 'left');
        $this->db->where("{$pfx}customer_equipment.client_id", (int) $this->api_client_id);

        if ($search) {
            $this->db->group_start();
            $this->db->like("{$pfx}customer_equipment.customer_equipment_name", $search);
            $this->db->or_like("{$pfx}customer_equipment.unit_code", $search);
            $this->db->or_like("{$pfx}customer_equipment.serial_number", $search);
            $this->db->or_like("{$pfx}items.description", $search);
            $this->db->group_end();
        }

        $total = $this->db->count_all_results('', false);

        $this->db->order_by("{$pfx}customer_equipment.datecreated", 'DESC');
        $this->db->limit($limit, $offset);
        $rows = $this->db->get()->result();

        $this->json([
            'data'  => array_map([$this, '_equipment_resource'], $rows),
            'meta'  => [
                'total'    => $total,
                'page'     => $page,
                'per_page' => $limit,
                'pages'    => (int) ceil($total / $limit),
            ],
        ]);
    }

    // ── GET /api/v1/customer/equipment/{id} ──────────────────────────────────

    public function show($id)
    {
        $this->_auth_customer();

        $pfx = db_prefix();
        $this->db->select(
            "{$pfx}customer_equipment.*,
             {$pfx}items.description as item_name,
             {$pfx}items.code as item_code,
             (SELECT name FROM {$pfx}items_subgroups WHERE id = {$pfx}items.subgroup_id) as subgroup_name,
             (SELECT name FROM {$pfx}items_groups    WHERE id = {$pfx}items.group_id)    as group_name",
            false
        );
        $this->db->from("{$pfx}customer_equipment");
        $this->db->join("{$pfx}items", "{$pfx}items.id = {$pfx}customer_equipment.item_id", 'left');
        $this->db->where("{$pfx}customer_equipment.id", (int) $id);
        $this->db->where("{$pfx}customer_equipment.client_id", (int) $this->api_client_id);

        $row = $this->db->get()->row();

        if (!$row) {
            $this->error('Equipment not found.', 404);
        }

        $this->json([
            'equipment' => $this->_equipment_resource($row),
        ]);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // PRIVATE
    // ═════════════════════════════════════════════════════════════════════════

    private function _auth_customer(): void
    {
        $this->authenticate();

        if ($this->api_client_type !== 'customer') {
            $this->error('Access restricted to customer accounts.', 403);
        }

        if (!$this->api_client_id) {
            $this->error('No company linked to this account.', 403);
        }
    }

    private function _equipment_resource(object $row): array
    {
        return [
            'id'                      => (int) $row->id,
            'customer_equipment_name' => $row->customer_equipment_name,
            'unit_code'               => $row->unit_code,
            'reference'               => $row->reference ?? null,
            'serial_number'           => $row->serial_number ?? null,
            'latest_cert_number'      => $row->latest_cert_number ?? null,
            'location'                => $row->location,
            'capacity'                => $row->capacity ?? null,
            'brand'                   => $row->brand ?? null,
            'model_type'              => $row->model_type ?? null,
            'procurement_year'        => $row->procurement_year ?? null,
            'cert_expired_date'       => $row->cert_expired_date ?? null,
            'cert_status'             => $this->_cert_status($row->cert_expired_date ?? null),
            'notes'                   => $row->notes ?? null,
            'item'                    => [
                'id'            => (int) ($row->item_id ?? 0),
                'name'          => $row->item_name ?? null,
                'code'          => $row->item_code ?? null,
                'group_name'    => $row->group_name ?? null,
                'subgroup_name' => $row->subgroup_name ?? null,
            ],
            'datecreated' => $row->datecreated,
        ];
    }

    private function _cert_status(?string $cert_expired_date): string
    {
        if (empty($cert_expired_date)) {
            return 'draft';
        }
        $today = date('Y-m-d');
        $soon  = date('Y-m-d', strtotime('+30 days'));

        if ($cert_expired_date < $today)  { return 'expired'; }
        if ($cert_expired_date <= $soon)  { return 'expiring_soon'; }
        return 'active';
    }
}
