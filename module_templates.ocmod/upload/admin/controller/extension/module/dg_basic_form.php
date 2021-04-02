<?php

class ControllerExtensionModuleDGBasicForm extends Controller {
    private $error = array();

    private $name = 'dg_basic_form';

    public function index() {
		$this->load->language('extension/module/' . $this->name);

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/' . $this->name, 'user_token=' . $this->session->data['user_token'], true)
		);
        
        // manipulate post -> $this->request->post

        $data['action'] = $this->url->link('extension/module/' . $this->name, 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/' . $this->name, $data));
    }

    protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/' . $this->name)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		// validate fields -> $this->request->post['field_name']

		return !$this->error;
	}
}