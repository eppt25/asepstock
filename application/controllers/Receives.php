<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Receives extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Receives';

		$this->load->model('model_receives');
		$this->load->model('model_products');
		$this->load->model('model_company');
	}

	/* 
	* It only redirects to the manage order page
	*/
	public function index()
	{
		if(!in_array('viewProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->data['page_title'] = 'Manage Receives';
		$this->render_template('receives/index', $this->data);		
	}

	/*
	* Fetches the orders data from the orders table 
	* this function is called from the datatable ajax function
	*/
	public function fetchReceivesData()
	{
		$result = array('data' => array());

		$data = $this->model_receives->getReceivesData();
		// $company = $this->model_company->getCompanyData(1);

		foreach ($data as $key => $value) {

			$count_total_item = $this->model_receives->countReceiveItem($value['id']);
			$date = date('d-m-Y', $value['date_time']);
			$time = date('h:i a', $value['date_time']);

			$date_time = $date . ' ' . $time;

			// button
			$buttons = '';


			if(in_array('updateProduct', $this->permission)) {
				$buttons .= ' <a href="'.base_url('receives/update/'.$value['id']).'" class="btn btn-default"><i class="fa fa-pencil"></i></a>';
			}

			if(in_array('deleteProduct', $this->permission)) {
				$buttons .= ' <button type="button" class="btn btn-default" onclick="removeFunc('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
			}


			$result['data'][$key] = array(
				$value['receive_no'],
				$date_time,
				$count_total_item,
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}

	public function create()
	{
		if(!in_array('createProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->data['page_title'] = 'Add Receive';

		$this->form_validation->set_rules('product[]', 'Product name', 'trim|required');
		
	
        if ($this->form_validation->run() == TRUE) {        	
        	
        	$receive_id = $this->model_receives->create();
        	
        	if($receive_id) {
        		$this->session->set_flashdata('success', 'สร้างสำเร็จ');
        		redirect('receives/update/'.$receive_id, 'refresh');
        	}
        	else {
        		$this->session->set_flashdata('errors', 'มีข้อผิดพลาดเกิดขึ้น!!');
        		redirect('receives/create/', 'refresh');
        	}
        }
        else {
            // false case

        	$this->data['products'] = $this->model_products->getActiveProductData();      	

            $this->render_template('receives/create', $this->data);
        }	
	}

	public function getProductValueById()
	{
		$product_id = $this->input->post('product_id');
		if($product_id) {
			$product_data = $this->model_products->getProductData($product_id);
			echo json_encode($product_data);
		}
	}

	public function getTableProductRow()
	{
		$products = $this->model_products->getActiveProductData();
		echo json_encode($products);
	}

	public function update($id)
	{
		if(!in_array('updateProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		if(!$id) {
			redirect('dashboard', 'refresh');
		}

		$this->data['page_title'] = 'Update Receive';

		$this->form_validation->set_rules('product[]', 'Product name', 'trim|required');
		
	
        if ($this->form_validation->run() == TRUE) {        	
        	
        	$update = $this->model_receives->update($id);
        	
        	if($update == true) {
        		$this->session->set_flashdata('success', 'แก้ไขสำเร็จ');
        		redirect('receives/update/'.$id, 'refresh');
        	}
        	else {
        		$this->session->set_flashdata('errors', 'มีข้อผิดพลาดเกิดขึ้น!!');
        		redirect('receives/update/'.$id, 'refresh');
        	}
        }
        else {
            // false case

        	$result = array();
        	$receives_data = $this->model_receives->getReceivesData($id);

    		$result['receive'] = $receives_data;
    		$receives_item = $this->model_receives->getReceivesItemData($receives_data['id']);

    		foreach($receives_item as $k => $v) {
    			$result['receive_item'][] = $v;
    		}

    		$this->data['receive_data'] = $result;

        	$this->data['products'] = $this->model_products->getActiveProductData();      	

            $this->render_template('receives/edit', $this->data);
        }
	}

		/*
	* It removes the data from the database
	* and it returns the response into the json format
	*/
	public function remove()
	{
		if(!in_array('deleteProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$receive_id = $this->input->post('receive_id');

        $response = array();
        if($receive_id) {
            $delete = $this->model_receives->remove($receive_id);
            if($delete == true) {
                $response['success'] = true;
                $response['messages'] = "ลบข้อมูลสำเร็จ"; 
            }
            else {
                $response['success'] = false;
                $response['messages'] = "เกิดข้อผิดพลาดขึ้นในฐานข้อมูลขณะที่กำลังลบข้อมูล";
            }
        }
        else {
            $response['success'] = false;
            $response['messages'] = "โปรดรีเฟรชหน้าเว็บใหม่อีกครั้ง!!";
        }

        echo json_encode($response); 
	}
}