<?php
require_once 'vendor/autoload.php'; // If you're using Composer (recommended)
defined('BASEPATH') OR exit('No direct script access allowed');

class Security extends CI_Controller {

 	public function __construct() {
		parent::__construct();
		if ($this->session->userdata('id_usu')) { //isLogin
			redirect("/", "refresh");
		}
		$this->load->library('html');
		$this->load->library('layout', 'layout');
		$this->load->model('User_model');
		$this->load->library('encryption');
	}
	public function forgot_password(){
		$email_usu=html_purify($this->input->post('email_usu'));
		$data = [
            "email_usu" => $email_usu
        ];
		$response = $this->User_model->validate_email_internal($data); 
		if ($response && isset($response['success'])) {
			if ($response['success']) {
				$pre_toke  = array('id' => $response['id_usu'], 'time' => date('d-m-Y h:i:sa'));
				$token = str_replace("/", "slash", $this->encryption->encrypt(json_encode($pre_toke))) ;
				$enlace = base_url() . 'security/forgot_password_view/'.$token;
				$email = new \SendGrid\Mail\Mail(); 
				$email->setFrom("soporte@adminpro.com", "Soporte Adminpro");
				$email->setSubject("Reestablecer contraseña de ".$email_usu);
				$email->addTo($email_usu, "Estimado Usuario");
				$email->addContent("text/html", str_replace("###ENLACE###", $enlace, str_replace("###USUARIO###", $email_usu, file_get_contents('email-templates/forgot_password.html'))));
				$sendgrid = new \SendGrid(SENDGRID_API_KEY);
				try {
					$response = $sendgrid->send($email);
					if ($response->statusCode() == 202) {
						echo json_encode(array('success' => true, 'msg' => "Se envió a su correo las instrucciones para reestablecer contraseña."));
					}
				} catch (Exception $e) {
					echo 'Caught exception: '. $e->getMessage() ."\n";
				}
			}else{
				echo json_encode($response);
			}
		}else{
			echo false;
		}
	}
	public function forgot_password_view($token = false){
		if ($token) {
			$token = str_replace("slash", "/", $token);
			$data =  $this->encryption->decrypt($token);
			if ($data) {
				$data_deco = json_decode($data);
				$fechaa = strtotime($data_deco->time);
				$diff = (time() - $fechaa)/60;
				if ($diff > 600) {
					$this->session->set_flashdata('forgoterror', 'Ya excedió el tiempo permitido.');
					redirect('login', 'refresh');
				}else{
					$this->session->set_userdata('id_forgot', $data_deco->id);
					$this->load->view('security/reset_password');
				}
			}else{
				$this->session->set_flashdata('forgoterror', '¡Acceso invalido!');
				redirect('login', 'refresh');
			}
		}else{
			redirect("login", "refresh");
		}
	}
	public function reset_password(){
		$this->load->library('encryption');
		$pass = html_purify($this->input->post('password'));

		if ($pass) {
			$id_usu = $this->session->userdata('id_forgot');
			$password = $this->encryption->encrypt($pass);
			$data = array('password' => $password);
			$response = $this->User_model->action_user($id_usu, $data);
			echo json_encode($response);
		}else{
			echo false;
		}
	}
}
