<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Products';

		$this->load->model('model_products');
		$this->load->model('model_brands');
		$this->load->model('model_category');
		$this->load->model('model_stores');
		$this->load->model('model_attributes');
        $this->load->model('model_company');
	}

    /* 
    * It only redirects to the manage product page
    */
	public function index()
	{
        if(!in_array('viewProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->render_template('products/index', $this->data);	
	}

    /*
    * It Fetches the products data from the product table 
    * this function is called from the datatable ajax function
    */
	public function fetchProductData()
	{
		$result = array('data' => array());

		$data = $this->model_products->getProductData();
        $company = $this->model_company->getCompanyData(1);

		foreach ($data as $key => $value) {

            $store_data = $this->model_stores->getStoresData($value['store_id']);
			// button
            

            $buttons = '';

            if(in_array('updateProduct', $this->permission)) {
                $buttons .= ' <button type="button" class="btn btn-default" onclick="increaseQuantity('.$value['id'].')" data-toggle="modal" data-target="#increaseQuantityModal"><i class="fa fa-plus"></i></button>';
            }

            if(in_array('updateProduct', $this->permission)) {
                $buttons .= ' <button type="button" class="btn btn-default" onclick="decreaseQuantity('.$value['id'].')" data-toggle="modal" data-target="#decreaseQuantityModal"><i class="fa fa-minus"></i></button>';
            }

            if(in_array('updateProduct', $this->permission)) {
                $buttons .= ' <button type="button" onclick="window.location=\''.base_url('products/update/'.$value['id']).'\'" class="btn btn-default"><i class="fa fa-pencil"></i></button>';
            }
            
            if(in_array('deleteProduct', $this->permission)) { 
    			$buttons .= ' <button type="button" class="btn btn-default" onclick="removeFunc('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
            }		

			$img = '<img src="'.base_url($value['image']).'" alt="'.$value['name'].'" class="img-circle" width="50" height="50" />';


            // $qty_status = '';
            $qty_status = '<span class="label label-success">สินค้ายังมีอยู่</span>';
           
            if($value['qty'] <= 10 && $value['qty'] > 0) {
                $qty_status = '<span class="label label-warning">สินค้าใกล้หมดแล้ว!</span>';
            } else if($value['qty'] <= 0) {
                $qty_status = '<span class="label label-danger">สินค้าหมดแล้ว!</span>';
                // $value['qty'] = 0;
            }


			$result['data'][$key] = array(
				$img,
				$value['name'],
				$value['price'] .' '. $company['currency'],
                // $value['qty'] . ' ' . $qty_status,
                $value['qty'],
                $store_data['name'],
                $qty_status,
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}	

    /*
    * If the validation is not valid, then it redirects to the create page.
    * If the validation for each input field is valid then it inserts the data into the database 
    * and it stores the operation message into the session flashdata and display on the manage product page
    */
	public function create()
	{
		if(!in_array('createProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->form_validation->set_rules('product_name', 'Product name', 'trim|required');
		$this->form_validation->set_rules('price', 'Price', 'trim|required');
		$this->form_validation->set_rules('qty', 'Qty', 'trim|required');
        $this->form_validation->set_rules('store', 'Store', 'trim|required');
		
	
        if ($this->form_validation->run() == TRUE) {
            // true case
        	$upload_image = $this->upload_image();

        	$data = array(
        		'name' => $this->input->post('product_name'),
        		'price' => $this->input->post('price'),
        		'qty' => $this->input->post('qty'),
        		'image' => $upload_image,
        		'description' => $this->input->post('description'),
        		'attribute_value_id' => json_encode($this->input->post('attributes_value_id')),
        		'brand_id' => json_encode($this->input->post('brands')),
        		'category_id' => json_encode($this->input->post('category')),
                'store_id' => $this->input->post('store'),
        	);

        	$create = $this->model_products->create($data);
        	if($create == true) {
        		$this->session->set_flashdata('success', 'เพิ่มสินค้าสำเร็จ');
        		redirect('products/', 'refresh');
        	}
        	else {
        		$this->session->set_flashdata('errors', 'มีข้อผิดพลาดเกิดขึ้น!!');
        		redirect('products/create', 'refresh');
        	}
        }
        else {
            // false case

        	// attributes 
        	$attribute_data = $this->model_attributes->getActiveAttributeData();

        	$attributes_final_data = array();
        	foreach ($attribute_data as $k => $v) {
        		$attributes_final_data[$k]['attribute_data'] = $v;

        		$value = $this->model_attributes->getAttributeValueData($v['id']);

        		$attributes_final_data[$k]['attribute_value'] = $value;
        	}

        	$this->data['attributes'] = $attributes_final_data;
			$this->data['brands'] = $this->model_brands->getActiveBrands();        	
			$this->data['category'] = $this->model_category->getActiveCategroy();        	
			$this->data['stores'] = $this->model_stores->getActiveStore();        	

            $this->render_template('products/create', $this->data);
        }	
	}

    /*
    * This function is invoked from another function to upload the image into the assets folder
    * and returns the image path
    */
	public function upload_image()
    {
    	// assets/images/product_image
        $config['upload_path'] = 'assets/images/product_image';
        $config['file_name'] =  uniqid();
        $config['allowed_types'] = 'gif|jpg|png';
        // $config['max_size'] = '1000';

        // $config['max_width']  = '1024';s
        // $config['max_height']  = '768';

        $this->load->library('upload', $config);
        if ( ! $this->upload->do_upload('product_image'))
        {
            $error = $this->upload->display_errors();
            return $error;
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            $type = explode('.', $_FILES['product_image']['name']);
            $type = $type[count($type) - 1];
            
            $path = $config['upload_path'].'/'.$config['file_name'].'.'.$type;
            return ($data == true) ? $path : false;            
        }
    }

    /*
    * If the validation is not valid, then it redirects to the edit product page 
    * If the validation is successfully then it updates the data into the database 
    * and it stores the operation message into the session flashdata and display on the manage product page
    */
	public function update($product_id)
	{      
        if(!in_array('updateProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        if(!$product_id) {
            redirect('dashboard', 'refresh');
        }

        $this->form_validation->set_rules('product_name', 'Product name', 'trim|required');
        $this->form_validation->set_rules('price', 'Price', 'trim|required');
        $this->form_validation->set_rules('qty', 'Qty', 'trim|required');
        $this->form_validation->set_rules('store', 'Store', 'trim|required');

        if ($this->form_validation->run() == TRUE) {
            // true case
            
            $data = array(
                'name' => $this->input->post('product_name'),
                'price' => $this->input->post('price'),
                'qty' => $this->input->post('qty'),
                'description' => $this->input->post('description'),
                'attribute_value_id' => json_encode($this->input->post('attributes_value_id')),
                'brand_id' => json_encode($this->input->post('brands')),
                'category_id' => json_encode($this->input->post('category')),
                'store_id' => $this->input->post('store'),
            );

            
            if($_FILES['product_image']['size'] > 0) {
                $upload_image = $this->upload_image();
                $upload_image = array('image' => $upload_image);
                
                $this->model_products->update($upload_image, $product_id);
            }

            $update = $this->model_products->update($data, $product_id);
            if($update == true) {
                $this->session->set_flashdata('success', 'แก้ไขข้อมูลสินค้าสำเร็จ');
                redirect('products/', 'refresh');
            }
            else {
                $this->session->set_flashdata('errors', 'มีข้อผิดพลาดเกิดขึ้น!!');
                redirect('products/update/'.$product_id, 'refresh');
            }
        }
        else {
            // attributes 
            $attribute_data = $this->model_attributes->getActiveAttributeData();

            $attributes_final_data = array();
            foreach ($attribute_data as $k => $v) {
                $attributes_final_data[$k]['attribute_data'] = $v;

                $value = $this->model_attributes->getAttributeValueData($v['id']);

                $attributes_final_data[$k]['attribute_value'] = $value;
            }
            
            // false case
            $this->data['attributes'] = $attributes_final_data;
            $this->data['brands'] = $this->model_brands->getActiveBrands();         
            $this->data['category'] = $this->model_category->getActiveCategroy();           
            $this->data['stores'] = $this->model_stores->getActiveStore();          

            $product_data = $this->model_products->getProductData($product_id);
            $this->data['product_data'] = $product_data;
            $this->render_template('products/edit', $this->data); 
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
        
        $this->form_validation->set_rules('product_name', 'Product name', 'trim|required');
        
        $product_id = $this->input->post('product_id');

        $response = array();
        if($product_id) {
            $delete = $this->model_products->remove($product_id);
            if($delete == true) {
                $response['success'] = true;
                $response['messages'] = "ลบข้อมูลสำเร็จ"; 
            }
            else {
                $response['success'] = false;
                $response['messages'] = "เกิดข้อผิดพลาดขึ้นในฐานข้อมูลขณะที่กำลังลบข้อมูลของสินค้า";
            }
        }
        else {
            $response['success'] = false;
            $response['messages'] = "โปรดรีเฟรชหน้าเว็บใหม่อีกครั้ง!!";
        }

        echo json_encode($response);
	}

    public function increaseQuantity()
    {
        if(!in_array('updateProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        $this->form_validation->set_rules('product_name', 'Product name', 'trim|required');
        
        $product_id = $this->input->post('product_id_increase');
        $quantity = $this->input->post('quantity_increase');

        $response = array();

        if($this->input->post('product_id_increase') && $this->input->post('quantity_increase')) {

            
            // update the product quantity
            $update = $this->model_products->increaseQty($product_id, $quantity);
            if($update == true) {
                $response['success'] = true;
                $response['messages'] = "เพิ่มจำนวนสินค้าสำเร็จ";
            } else {
                $response['success'] = false;
                $response['messages'] = "เกิดข้อผิดพลาดในการเพิ่มจำนวนสินค้า";
            }
        } else {
            $response['success'] = false;
            $response['messages'] = "ต้องใส่จำนวนสินค้า";
        }

        echo json_encode($response);
    }

    public function decreaseQuantity()
    {
        if(!in_array('updateProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        $this->form_validation->set_rules('product_name', 'Product name', 'trim|required');
        
        $product_id = $this->input->post('product_id_decrease');
        $quantity = $this->input->post('quantity_decrease');
        $nowQuantity = $this->model_products->getProductQuantity($product_id);

        $response = array();

        if($this->input->post('product_id_decrease') && $this->input->post('quantity_decrease')) {

        
            // update the product quantity
            if ($nowQuantity < $quantity) {
                $response['success'] = false;
                $response['messages'] = "จำนวนสินค้าที่จะลดมากกว่าจำนวนสินค้าปัจจุบัน";
            } else {
                $update = $this->model_products->decreaseQty($product_id, $quantity);
                
                if($update == true) {
                    $response['success'] = true;
                    $response['messages'] = "ลดจำนวนสินค้าสำเร็จ";
                } else {
                    $response['success'] = false;
                    $response['messages'] = "เกิดข้อผิดพลาดในการลดจำนวนสินค้า";
                }                
            }

        } else {
            $response['success'] = false;
            $response['messages'] = "ต้องใส่จำนวนสินค้า";
        }

        echo json_encode($response);
    }

}