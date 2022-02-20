<?php

class ControllerExtensionModuleDGBasicForm extends Controller {
    private $error = array();

    private $name = 'dg_basic_form';

    public function index() {
		$this->load->language('extension/module/' . $this->name);

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('setting/module');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule($this->name, $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->post['save_stay'])) {
				$this->response->redirect($this->url->link('extension/module/' . $this->name, '&module_id=' . $this->request->get['module_id'] . '&user_token=' . $this->session->data['user_token']));
			} else {
				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
			}
		}

		// Success
		if (isset($this->session->data['success'])) {
			$data['alert_success'] = $this->session->data['success'];
		} else {
			$data['alert_success'] = false;
		}

		// Errors reset
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['category'])) {
			$data['error_category'] = $this->error['category'];
		} else {
			$data['error_category'] = '';
		}

		if (isset($this->error['manufacturer'])) {
			$data['error_manufacturer'] = $this->error['manufacturer'];
		} else {
			$data['error_manufacturer'] = '';
		}

		if (isset($this->error['sort_order'])) {
			$data['error_sort_order'] = $this->error['sort_order'];
		} else {
			$data['error_sort_order'] = '';
		}

        $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);
        
        // manipulate post -> $this->request->post
		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/' . $this->name, 'user_token=' . $this->session->data['user_token'], true);
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/' . $this->name, 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['action'] = $this->url->link('extension/module/' . $this->name, 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/' . $this->name, 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		// Category
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} else if (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['checkbox'])) {
			$data['checkbox'] = $this->request->post['checkbox'];
		} else if (!empty($module_info)) {
			$data['checkbox'] = $module_info['checkbox'];
		} else {
			$data['checkbox'] = '0';
		}

		// Category Autocomplete
		if (isset($this->request->post['path'])) {
			$data['path'] = $this->request->post['path'];
		} else if (!empty($module_info)) {
			$data['path'] = $module_info['path'];	
		} else {
			$data['path'] = false;
		}
		if (isset($this->request->post['category'])) {
			$data['category'] = $this->request->post['category'];
		} else if (!empty($module_info)) {
			$data['category'] = $module_info['category'];
		} else {
			$data['category'] = 0;
		}

		// Products Array Autocomplete
		$data['products'] = array();

		if (!empty($this->request->post['products'])) {
			$products = $this->request->post['products'];
		} elseif (!empty($module_info['products'])) {
			$products = $module_info['products'];
		} else {
			$products = array();
		}
		
		foreach ($products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				$data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name']
				);
			}
		}

		// Manufacturer Autocomplete
		if (isset($this->request->post['manufacturer'])) {
			$data['manufacturer'] = $this->request->post['manufacturer'];
		} else if (!empty($module_info)) {
			$data['manufacturer'] = $module_info['manufacturer'];
		} else {
			$data['manufacturer'] = false;
		}
		if (isset($this->request->post['man_id'])) {
			$data['man_id'] = $this->request->post['man_id'];
		} else if (!empty($module_info)) {
			$data['man_id'] = $module_info['man_id'];
		} else {
			$data['man_id'] = 0;
		}

		// Image
		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} else if (!empty($module_info)) {
			$data['image'] = $module_info['image'];
		} else {
			$data['image'] = '';
		}

		if (!empty($data['image']) && is_file(DIR_IMAGE . $data['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($data['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		// SortOrder
		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} else if (!empty($module_info)) {
			$data['sort_order'] = $module_info['sort_order'];	
		} else {
			$data['sort_order'] = 0;
		}

        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/' . $this->name, $data));
    }

    protected function validate() {
		// echo '<pre>'; var_dump($this->request->post); echo '</pre>';
		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');

		if (!$this->user->hasPermission('modify', 'extension/module/' . $this->name)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		// validate fields -> $this->request->post['field_name']
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!empty($this->request->post['category']) && intval($this->request->post['category']) <= 0 && empty($this->model_catalog_category->getCategory(intval($this->request->post['category'])))) {
			$this->error['category'] = $this->language->get('error_category');
		}

		if (!empty($this->request->post['man_id']) && intval($this->request->post['man_id']) <= 0 && empty($this->model_catalog_manufacturer->getManufacturer(intval($this->request->post['man_id'])))) {
			$this->error['manufacturer'] = $this->language->get('error_manufacturer');
		}

		if (intval($this->request->post['sort_order']) < 0) {
			$this->error['sort_order'] = $this->language->get('error_sort_order');
		}

		return !$this->error;
	}
}