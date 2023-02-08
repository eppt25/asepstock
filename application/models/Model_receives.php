<?php 

class Model_receives extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* get the orders data */
	public function getReceivesData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM `receives` WHERE id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM `receives` ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getReceivesItemData($receive_id = null)
	{
		if(!$receive_id) {
			return false;
		}

		$sql = "SELECT * FROM `receives_item` WHERE receive_id = ?";
		$query = $this->db->query($sql, array($receive_id));
		return $query->result_array();
	}

	public function create()
	{
		$user_id = $this->session->userdata('id');
		$receive_no = 'RESBI-'.strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));
    	$data = array(
    		'receive_no' => $receive_no,
    		'date_time' => strtotime(date('Y-m-d h:i:s a')),
    		'receive_status' => 2,
    		'user_id' => $user_id
    	);

		$insert = $this->db->insert('receives', $data);
		$receive_id = $this->db->insert_id();

		$this->load->model('model_products');

		$count_product = count($this->input->post('product'));
    	for($x = 0; $x < $count_product; $x++) {
    		$items = array(
    			'receive_id' => $receive_id,
    			'product_id' => $this->input->post('product')[$x],
    			'qty' => $this->input->post('qty')[$x]
    		);

    		$this->db->insert('receives_item', $items);

    		// now increase the stock FROM `the` product
    		$product_data = $this->model_products->getProductData($this->input->post('product')[$x]);
    		$qty = (int) $product_data['qty'] + (int) $this->input->post('qty')[$x];

    		$update_product = array('qty' => $qty);


    		$this->model_products->update($update_product, $this->input->post('product')[$x]);
    	}

		return ($receive_id) ? $receive_id : false;
	}

	public function update($id)
	{
		if($id) {
			$user_id = $this->session->userdata('id');
			// fetch the order data 

			$data = array(
				'date_time' => strtotime(date('Y-m-d h:i:s a')),
				'receive_status' => 2,
				'user_id' => $user_id
	    	);

			$this->db->where('id', $id);
			$update = $this->db->update('receives', $data);

			// now the order item 
			// first we will replace the product qty to original and subtract the qty again
			$this->load->model('model_products');
			$get_receive_item = $this->getReceivesItemData($id);
			foreach ($get_receive_item as $k => $v) {
				$product_id = $v['product_id'];
				$qty = $v['qty'];
				// get the product 
				$product_data = $this->model_products->getProductData($product_id);
				$update_qty = $product_data['qty'] - $qty;
				$update_product_data = array('qty' => $update_qty);
				
				// update the product qty
				$this->model_products->update($update_product_data, $product_id);
			}

			// now remove the order item data 
			$this->db->where('receive_id', $id);
			$this->db->delete('receives_item');

			// now increase the product qty
			$count_product = count($this->input->post('product'));
	    	for($x = 0; $x < $count_product; $x++) {
	    		$items = array(
	    			'receive_id' => $id,
	    			'product_id' => $this->input->post('product')[$x],
	    			'qty' => $this->input->post('qty')[$x]
	    		);
	    		$this->db->insert('receives_item', $items);

	    		// now decrease the stock FROM `the` product
	    		$product_data = $this->model_products->getProductData($this->input->post('product')[$x]);
	    		$qty = (int) $product_data['qty'] + (int) $this->input->post('qty')[$x];

	    		$update_product = array('qty' => $qty);
	    		$this->model_products->update($update_product, $this->input->post('product')[$x]);
	    	}

			return true;
		}
	}

	public function remove($id)
	{
		if($id) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('receives');

			$this->db->where('receive_id', $id);
			$delete_item = $this->db->delete('receives_item');
			return ($delete == true && $delete_item) ? true : false;
		}
	}

	public function countReceiveItem($receive_id)
	{
		if($receive_id) {
			$sql = "SELECT * FROM `receives_item` WHERE receive_id = ?";
			$query = $this->db->query($sql, array($receive_id));
			return $query->row()->qty;
		}
	}

}