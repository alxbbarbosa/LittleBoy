<?php
namespace Abbarbosa\LittleBoy\Framework;

/**
 * ==============================================================================================================
 *
 * Validate: Classe para validação de dados
 *
 * ----------------------------------------------------
 *
 * @author Alexandre Bezerra Barbosa <alxbbarbosa@yahoo.com.br>
 * @copyright (c) 2018, Alexandre Bezerra Barbosa
 * @version 1.00
 * ==============================================================================================================
 */
class Validate
{

	protected $errors;
	protected $details;

	public function set($data) {

		if(!is_array($data)) {
			$data = (array) $data;
		}

		foreach ($data as $field => $validation) {
			
			$this->value(request($field), $validation, $field);
		}
		return !array_search(false, $this->errors, true);
	}


	public function value($value, $validation, $field){

		$pieces = explode('|', $validation);
		foreach ($pieces as $validate) {
			$options = explode(":", $validate);
			if(count($options) > 1) {
				$this->errors[] = $this->validation($field, $options[0], $value, $options[1]);
			} else {
				$this->errors[] = $this->validation($field, $options[0], $value);
			}
		}
		return !array_search(false, $this->errors, true);
	}


	protected function validation($field, $type, $value, $param = null)
	{

		switch ($type) {
			case 'max':
			$result = !is_null($param) && strlen($value) <= $param; break;
			case 'min':
			$result =  !is_null($param) && strlen($value) >= $param; break;
			case 'required':
			$result =  !is_null($value); break;
			case 'numeric':
			$result =  preg_match("/^[0-9]{1,}$/", $value); break;
			case 'email':
			$result =  !!filter_var(filter_var($value, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL); break;
			default:
			throw new \Exception("Tipo de validação não implementado");
		}
		if($result !== true) {
			$message = $this->message($field, $type, $param);
			$this->details[] = $message;
			session()->error($message);
		}
		return $result;
	}

	protected function message($field, $type, $param = null){
		switch ($type) {
			case 'max':
			return "O campo {$field} suporta o tamanho máximo de {$param} caracteres.";
			case 'min':
			return "O campo {$field} exige o mínimo de {$param} caracteres.";
			case 'required':
			return "O preenchimento do campo {$field} é requerido.";
			case 'numeric':
			return "O campo {$field} suporta apenas valores numéricos.";
			case 'email':
			return "O email preenchido no campo {$field} não é inválido";
		}
	}

}