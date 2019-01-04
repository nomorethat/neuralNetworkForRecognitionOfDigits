<?php
	require_once "perceptron_class.php";
	require_once "interaction_with_database_class.php";
	
	class Education{
		public function get_training_set(){
			$dir = $_SESSION["directory_with_training_set"];
			
			if(!is_dir("../images/training_set/".$dir)){
				echo -1;
				exit;
			}
			
			$filelist = array();
			if ($handle = opendir("../images/training_set/".$dir)) {
				while ($entry = readdir($handle)) {
					$filelist[] = $entry;
				}
				closedir($handle);
			}
			unset($filelist[0]);
			unset($filelist[1]);
			$filelist = array_values($filelist);
			
			for($i = 0; $i < count($filelist); $i++){//чтобы отсортировать массив по имени файла
				$filelist[$i] = substr($filelist[$i], 0, -4);
			}
			sort($filelist);
			
			$this -> preprocessing_of_images($filelist, $dir);
		}
		
		private function preprocessing_of_images($filelist, $dir){			
			for($i = 0; $i < count($filelist); $i++){
				$image = imageCreateFromJpeg("../images/training_set/".$dir."/".$filelist[$i].".jpg");
				$this -> preprocessing_of_break_into_fragments($image);
				$all_inputs[$i] = $_SESSION["all_inputs"]; //двумерный массив с векторами всех изображений
			}
			$_SESSION["all_inputs"] = $all_inputs;
			$_SESSION["all_inputs_of_all_images"] = $all_inputs;
			$this -> preprocessing_for_send_in_network($all_inputs);
			$this -> imitation_of_a_given_number_of_iteration_of_education();
			$this -> get_outputs_of_training_set_after_training();
			$perfect_output = $this -> calculate_of_perfect_output();
			$best_output = $this -> calculate_of_best_output();
			$worst_output = $this -> calculate_of_worst_output();
			$this -> insert_category($perfect_output, $best_output, $worst_output);
		}
		
		private function preprocessing_of_break_into_fragments($image){
			$step = $_SESSION["size_of_a_fragment"];
			
			$step_x = $step;
			$step_y = $step;
			
			$a_x = 0;
			$a_y = 0;
			
			$fragment = 0;
			
			$array_with_the_number_of_black_pixels_in_each_fragment = array();
			
			$this -> break_into_fragment($image, $a_x, $a_y, $step_x, $step_y, $fragment, $array_with_the_number_of_black_pixels_in_each_fragment);
			
			$array_with_the_number_of_black_pixels_in_each_fragment = $_SESSION["tmp_array"];
			$_SESSION["all_inputs"] = $array_with_the_number_of_black_pixels_in_each_fragment;
		}
		
		private function break_into_fragment($image, $a_x, $a_y, $step_x, $step_y, $fragment, $array_with_the_number_of_black_pixels_in_each_fragment){
			
			if(($a_x === imageSX($image)) && ($a_y === imageSY($image) - $step_y)){ //если последний фрагмент в изображении
				$_SESSION["tmp_array"] = $array_with_the_number_of_black_pixels_in_each_fragment;
				return $array_with_the_number_of_black_pixels_in_each_fragment;
			}
			
			if($a_x === imageSX($image)){ //опускаемся на шаг в картинке и снова переходим в начало изображения
				$a_x = 0;
				$a_y = $a_y + $step_y;
			}

			$fragment++;
			
			$the_number_of_black_pixels_in_each_fragment = 0;
			
			for($k = $a_x; $k < ($a_x + $step_x); $k++){
				for($j = $a_y; $j < ($a_y + $step_y); $j++){ //здесь считаем число чёрных пикселей во фрагменте
					$color_current_pixel = imageColorAt($image, $k, $j);
					if($color_current_pixel == $black)
						$the_number_of_black_pixels_in_each_fragment++;
				}
			}
			
			$array_with_the_number_of_black_pixels_in_each_fragment[$fragment] = $the_number_of_black_pixels_in_each_fragment;
			
			/* переводим в проценты и представим в виде числа от 0 до 1 */
			
			$one_percent = ($step_x*$step_y)/100;
			$array_with_the_number_of_black_pixels_in_each_fragment[$fragment] = $array_with_the_number_of_black_pixels_in_each_fragment[$fragment]/$one_percent;
			$array_with_the_number_of_black_pixels_in_each_fragment[$fragment] = (round($array_with_the_number_of_black_pixels_in_each_fragment[$fragment]))/100;			
			
			$a_x = $a_x + $step_x; //переходим к следующему фрагменту
			
			$this -> break_into_fragment($image, $a_x, $a_y, $step_x, $step_y, $fragment, $array_with_the_number_of_black_pixels_in_each_fragment);
		}
		
		private function preprocessing_for_send_in_network($all_inputs){ //принимаем двумерный массив с векторами всех изображений
			$weights_a_r = array();
			
			$size_of_a_fragment = (int)$_SESSION["size_of_a_fragment"];
			$count_of_fragments = (200/$size_of_a_fragment)*(300/$size_of_a_fragment);//сколько всего фрагментов мы можем получить при заданной размерности одного фрагмента

			$_SESSION["count_of_fragments"] = $count_of_fragments;
			
			for($i = 0; $i < $count_of_fragments; $i++){//инициализируем A-R связи единицами
				$weights_a_r[$i] = 1;
			}
			$_SESSION["weights_a_r"] = $weights_a_r;
			
			$neuro = new Perceptron();
			$mode = "education";
			for($i = 0; $i < count($all_inputs); $i++){
				$_SESSION["all_inputs"] = $all_inputs[$i];
				$neuro -> initialization_of_inputs($mode);// отправляем каждый вектор в сеть
			}
		}
		
		private function imitation_of_a_given_number_of_iteration_of_education(){//имитация 10ти итераций обучения
			$weights_a_r = $_SESSION["weights_a_r"];
			
			$iteration_of_education = $_SESSION["itetation_of_education"];
			
			for($c = 0; $c < count($weights_a_r); $c++){
				$weights_a_r[$c] = $weights_a_r[$c] - 1;
				$weights_a_r[$c] = $weights_a_r[$c] * $iteration_of_education;
				$weights_a_r[$c] = $weights_a_r[$c] + 1;
			}
			
			$_SESSION["weights_a_r"] = $weights_a_r;
		}
		
		private function get_outputs_of_training_set_after_training(){
			$all_inputs_of_all_images = $_SESSION["all_inputs_of_all_images"];//двумерный массив с векторами всех изображений
			$weights_a_r = $_SESSION["weights_a_r"];
			
			$array_of_outputs_of_training_set_after_training = array();//здесь будут собраны все выходы обучающей выборки, после прохождения ею по уже обученной сети
			$_SESSION["array_of_outputs_of_training_set_after_training"] = $array_of_outputs_of_training_set_after_training;
			
			$neuro = new Perceptron();
			for($i = 0; $i < count($all_inputs_of_all_images); $i++){
				$neuro -> re_initialization_of_inputs($all_inputs_of_all_images[$i]);
			} 
		}
		
		private function calculate_of_perfect_output(){
			$sum = 0;
			$array_of_outputs_of_training_set_after_training = $_SESSION["array_of_outputs_of_training_set_after_training"];
			for($i = 0; $i < count($array_of_outputs_of_training_set_after_training[0]); $i++){ 
				for($j = 0; $j < count($array_of_outputs_of_training_set_after_training); $j++){
					$sum += $array_of_outputs_of_training_set_after_training[$j][$i];
				}
				$perfect_output[] = round($sum/count($array_of_outputs_of_training_set_after_training), 2);
				$sum = 0;
			}
			return $perfect_output;
		}
		
		private function calculate_of_best_output(){
			$array_of_outputs_of_training_set_after_training = $_SESSION["array_of_outputs_of_training_set_after_training"];
	
			for($i = 0; $i < count($array_of_outputs_of_training_set_after_training[0]); $i++){ 
				$max_of_col = $array_of_outputs_of_training_set_after_training[0][$i];
				for($j = 0; $j < count($array_of_outputs_of_training_set_after_training); $j++){
					if($array_of_outputs_of_training_set_after_training[$j][$i] > $max_of_col)
						$max_of_col = $array_of_outputs_of_training_set_after_training[$j][$i];
				}	
				$max_outputs[] = $max_of_col;
			}
			return $max_outputs;
		}
		
		private function calculate_of_worst_output(){
			$array_of_outputs_of_training_set_after_training = $_SESSION["array_of_outputs_of_training_set_after_training"];
	
			for($i = 0; $i < count($array_of_outputs_of_training_set_after_training[0]); $i++){ 
				$min_of_col = $array_of_outputs_of_training_set_after_training[0][$i];
				for($j = 0; $j < count($array_of_outputs_of_training_set_after_training); $j++){
					if($array_of_outputs_of_training_set_after_training[$j][$i] < $min_of_col)
						$min_of_col = $array_of_outputs_of_training_set_after_training[$j][$i];
				}	
				$min_outputs[] = $min_of_col;
			}
			return $min_outputs;
		}
		
		private function insert_category($perfect_output, $best_output, $worst_output){
			$weights_a_r = $_SESSION["weights_a_r"];
			
			$perfect_output = implode(", ", $perfect_output);
			$best_output = implode(", ", $best_output);
			$worst_output = implode(", ", $worst_output);
			$weights_a_r = implode(", ", $weights_a_r);
			$_SESSION["weights_a_r"] = $weights_a_r;
			
			$the_category_name_in_education = $_SESSION["the_category_name_in_education"];
			$threshold_of_a_element = $_SESSION["threshold_of_a_element"];
			$training_algorithm = $_SESSION["learning_algorithm"];
			$number_inputs_on_education = $_SESSION["count_of_fragments"];
			$arrangement_of_r_elements = $_SESSION["arrangement_of_r_elements"];
			$itetation_of_education = $_SESSION["itetation_of_education"];
			
			$db1 = Database::getDB();
			$db1 -> insert_category($the_category_name_in_education, $number_inputs_on_education, $threshold_of_a_element, $training_algorithm, $arrangement_of_r_elements, $itetation_of_education, $perfect_output, $best_output, $worst_output, $weights_a_r);
		}
	}
?>