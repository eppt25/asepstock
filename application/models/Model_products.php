<?php 

class Model_products extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* get the brand data */
	public function getProductData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM `products` where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM `products` ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getProductQuantity($id) {
		if ($id) {
			$sql = "SELECT `qty` FROM `products` WHERE id = $id";
			$query = $this->db->query($sql);
			return $query->row()->qty;
		}
	}

	public function getActiveProductData()
	{
		$sql = "SELECT * FROM `products` WHERE availability = ? ORDER BY id DESC";
		$query = $this->db->query($sql, array(1));
		return $query->result_array();
	}
	
	public function create($data)
	{
		if($data) {
			$insert = $this->db->insert('products', $data);
			return ($insert == true) ? true : false;
		}
	}

	public function update($data, $id)
	{
		if($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('products', $data);
			return ($update == true) ? true : false;
		}
	}

	public function remove($id)
	{
		if($id) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('products');
			return ($delete == true) ? true : false;
		}
	}

	public function increaseQty($id, $user_qty)
	{
		if($id && $user_qty) {
			$this->db->where('id', $id);
			$this->db->set('qty', 'qty+' . $user_qty, FALSE);
			$data = array();
			$update = $this->db->update('products', $data);
			return ($update == true) ? true : false;
		}
	}

	public function decreaseQty($id, $user_qty)
	{
		if($id && $user_qty) {
			$this->db->where('id', $id);
			$this->db->set('qty', 'qty-' . $user_qty, FALSE);
			$data = array();
			$decrease = $this->db->update('products', $data);
			return ($decrease == true) ? true : false;
		}
	}

	

	public function countTotalProducts()
	{
		$sql = "SELECT * FROM `products`";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	public function outOfStockProducts()
	{
		$sql = "SELECT * FROM `products` WHERE `QTY` <= 10";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

}