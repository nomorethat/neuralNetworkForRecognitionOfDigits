<?php
	
	session_start();
	
	class Perceptron{
		public function initialization_of_inputs($mode){
			$all_inputs = $_SESSION["all_inputs"];
			$s_element = $all_inputs;
			$this -> s_a_connection($s_element, $mode);
		}
		
		private function s_a_connection($s_element, $mode){
			$a_element = array();
			
			for($i = 0; $i < count($s_element); $i++){
				$a_element[$i] = $s_element[$i]*1;
			}
			$this -> layer_of_a_elements($a_element, $mode);
		}
		
		private function layer_of_a_elements($a_element, $mode){
			$weights_a_r = $_SESSION["weights_a_r"];
			
			if($mode == "education"){
				for($k = 0; $k < count($a_element); $k++){
					if($a_element[$k] < $_SESSION["threshold_of_a_element"])
						$a_element[$k] = 0;
					else
						$weights_a_r[$k]++;
				}
			}
			if($mode == "recognizing"){
				for($k = 0; $k < count($a_element); $k++){
					if($a_element[$k] < 0.15)
						$a_element[$k] = 0;
				}
			}
			
			$_SESSION["weights_a_r"] = $weights_a_r;
			
			$this -> a_r_connection($a_element, $mode);
		}
		
		private function a_r_connection($a_element, $mode){
			$r_element = array();
			$r_inputs = array_chunk($a_element, 4);
			
			$weights_a_r = $_SESSION["weights_a_r"];
			
			for($i = 0; $i < count($r_inputs); $i++){ // сумматор	
				for($j = 0; $j < count($r_inputs[$i]); $j++){
					$r_element[$i] += $r_inputs[$i][$j] * $weights_a_r[$i];
				}
			}
			if($mode == "recognizing"){
				$outputs_of_image_on_all_settings = $_SESSION["outputs_of_image_on_all_settings"];
				$outputs_of_image_on_all_settings[] = $r_element;
				$_SESSION["outputs_of_image_on_all_settings"] = $outputs_of_image_on_all_settings;
			}
			
		}
		
		
		
		
		public function re_initialization_of_inputs($array_with_the_number_of_black_pixels_in_each_fragment){
			$s_element = $array_with_the_number_of_black_pixels_in_each_fragment;
			$this -> re_s_a_connection($s_element);
		}
		
		private function re_s_a_connection($s_element){
			$a_element = array();
			for($i = 0; $i < count($s_element); $i++){
				$a_element[$i] = $s_element[$i]*1;
			}
			$this -> re_layer_of_a_elements($a_element);
		}

		private function re_layer_of_a_elements($a_element){
			for($k = 0; $k < count($a_element); $k++){
				if($a_element[$k] < $_SESSION["threshold_of_a_element"])
					$a_element[$k] = 0;
			}
				
			$this -> re_a_r_connection($a_element);
		}
		
		private function re_a_r_connection($a_element){
			$r_element = array();
			$r_inputs = array_chunk($a_element, 4);
			
			$weights_a_r = $_SESSION["weights_a_r"];
			
			for($i = 0; $i < count($r_inputs); $i++){ // сумматор	
				for($j = 0; $j < count($r_inputs[$i]); $j++){
					$r_element[$i] += $r_inputs[$i][$j] * $weights_a_r[$i];
				}
			}
			
			$array_of_outputs_of_training_set_after_training = $_SESSION["array_of_outputs_of_training_set_after_training"];
			$array_of_outputs_of_training_set_after_training[] = $r_element;
			$_SESSION["array_of_outputs_of_training_set_after_training"] = $array_of_outputs_of_training_set_after_training;
		}
	}
?>