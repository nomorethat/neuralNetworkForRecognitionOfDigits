<?php

	require_once "interaction_with_database_class.php";
	require_once "perceptron_class.php";
	
	session_start();
	
	class Recognizing{
		public function preprocessing_of_images($image){
			$this -> preprocessing_of_break_into_fragments($image);
			$all_inputs = $_SESSION["all_inputs"]; //двумерный массив с векторами всех изображений
			$_SESSION["outputs_of_image_on_all_settings"] = array();
			
			$this -> preprocessing_for_send_in_network();
		}
		
		private function preprocessing_of_break_into_fragments($image){
			$size_of_fragments_for_recognizing = $_SESSION["size_of_fragments_for_recognizing"];
			$step = $size_of_fragments_for_recognizing;
			
			$step_x = $step;
			$step_y = $step;
			
			$a_x = 0;
			$a_y = 0;
			
			$fragment = 0;
			
			$array_with_the_number_of_black_pixels_in_each_fragment = array();
			
			$this -> break_into_fragment($image, $a_x, $a_y, $step_x, $step_y, $fragment, $array_with_the_number_of_black_pixels_in_each_fragment);
			
			$array_with_the_number_of_black_pixels_in_each_fragment = $_SESSION["tmp_array"];
			$_SESSION["all_inputs"] = $array_with_the_number_of_black_pixels_in_each_fragment;
			
			$_SESSION["count_of_fragments"] = count($_SESSION["all_inputs"]);
			
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
			
			/*переводим в проценты и представим в виде числа от 0 до 1 */
			
			$one_percent = ($step_x*$step_y)/100;
			$array_with_the_number_of_black_pixels_in_each_fragment[$fragment] = $array_with_the_number_of_black_pixels_in_each_fragment[$fragment]/$one_percent;
			$array_with_the_number_of_black_pixels_in_each_fragment[$fragment] = (round($array_with_the_number_of_black_pixels_in_each_fragment[$fragment]))/100;			
			
			$a_x = $a_x + $step_x; //переходим к следующему фрагменту
			
			$this -> break_into_fragment($image, $a_x, $a_y, $step_x, $step_y, $fragment, $array_with_the_number_of_black_pixels_in_each_fragment);
		}
		
		private function preprocessing_for_send_in_network(){
			$count_of_fragments = $_SESSION["count_of_fragments"];
			$all_categories_with_the_given_partition = $this -> getAllCaregories($count_of_fragments);
			
			$neuro = new Perceptron();
			//echo count($all_categories_with_the_given_partition);
			$mode = "recognizing";
			
			$outputs_of_image_on_all_settings = Array();
			$_SESSION["outputs_of_image_on_all_settings"] = $outputs_of_image_on_all_settings;
			
			for($k = 0; $k < count($all_categories_with_the_given_partition); $k++){
				$this_category = $all_categories_with_the_given_partition[$k]["category"];
				$weights_of_this_category = $all_categories_with_the_given_partition[$k]["weights_A_R"];
				$weights_of_this_category = explode(",", $weights_of_this_category);
				$_SESSION["weights_a_r"] = $weights_of_this_category;

				$neuro -> initialization_of_inputs($mode);
			}

			$this -> postprocessing_outputs($all_categories_with_the_given_partition);
		}
		
		private function getAllCaregories($count_of_fragments){
			$db = Database::getDB();
			$all_categories_with_the_given_partition = $db -> getAllCaregories($count_of_fragments);
			return $all_categories_with_the_given_partition;
		}
		
		private function postprocessing_outputs($all_categories_with_the_given_partition){
			$outputs_of_image_on_all_settings = $_SESSION["outputs_of_image_on_all_settings"]; //все возможные выходы входного изображения на всех возможных настройках сети
			
			$one_percent = 100/count($outputs_of_image_on_all_settings[0]);//процент вложения одного выхода в результат
			
			$result_of_recognizing = Array();
			
			for($i = 0; $i < count($outputs_of_image_on_all_settings); $i++){
				$persent_of_this_category = 0;
				
				$perfect_output = $all_categories_with_the_given_partition[$i]["perfect_output"];
				$best_output = $all_categories_with_the_given_partition[$i]["best_output"];
				$worst_output = $all_categories_with_the_given_partition[$i]["worst_output"];
				
				$perfect_output = explode(",", $perfect_output);
				$best_output = explode(",", $best_output);
				$worst_output = explode(",", $worst_output);
				
				// (выводы не удаляй. Они очень хорошо показывают выход текущего изображения и сравнение его с лучшим, идеальным и худшим выходами на всех настройках)
				/* echo "Изображение на распознавание: ";
				print_r($outputs_of_image_on_all_settings[$i]);
				echo "<br /><br />";
				echo "Лучший выход: ";
				print_r($best_output);
				echo "<br /><br />";
				echo "Идеальный выход: ";
				print_r($perfect_output);
				echo "<br /><br />";
				echo "Худший выход: ";
				print_r($worst_output);
				echo "<br /><br />";
				echo "---------------";
				echo "<br /><br />"; */
				
				for($j = 0; $j < count($outputs_of_image_on_all_settings[$i]); $j++){
					if(($outputs_of_image_on_all_settings[$i][$j] <= $best_output[$j]) && ($outputs_of_image_on_all_settings[$i][$j] >= $worst_output[$j])){//если попали в возможный диапазон
							
						$up = $best_output[$j] - $perfect_output[$j]; //отклонение вверх от идеального
						$down = $perfect_output[$j] - $worst_output[$j];//отклонение вниз от идеального
						
						//Version 02.12.2018
						if($up > $down) {
							$epsilon_range = $down;
						}
						else {
							$epsilon_range = $up;
						}
												
						$alpha_range = $epsilon_range/2;
						$alpha_up = $perfect_output[$j] + $alpha_range;
						$alpha_down = $perfect_output[$j] - $alpha_range;
												
						if($outputs_of_image_on_all_settings[$i][$j] > $perfect_output[$j]){
							$deviation_of_the_current = $outputs_of_image_on_all_settings[$i][$j] - $perfect_output[$j]; // отконение текущего
						}
						else
							$deviation_of_the_current = $perfect_output[$j] - $outputs_of_image_on_all_settings[$i][$j]; // отконение текущего
						
						if($deviation_of_the_current >= $epsilon_range){ // если значение выхода выходит за пределы епсилон-диапазона
							$persent_of_this_category += 0;
						}
						if($deviation_of_the_current <= $alpha_range){ // если значение выхода входит в пределы альфа-диапазона
							$persent_of_this_category += $one_percent;
						}
						if(($deviation_of_the_current < $epsilon_range) && ($deviation_of_the_current > $alpha_range)){
							$sub_persent_of_this_category = $one_percent/100; //мы и без того скромный процент вложения в результат одного выходного нейрона дробим ещё на 100 частей
							if($outputs_of_image_on_all_settings[$i][$j] > $perfect_output[$j]){
								$approximation_of_current = $perfect_output[$j] + $epsilon_range - $outputs_of_image_on_all_settings[$i][$j];
							}
							if($outputs_of_image_on_all_settings[$i][$j] < $perfect_output[$j]){
								$approximation_of_current = $perfect_output[$j] - $outputs_of_image_on_all_settings[$i][$j];
							}
							$one_percent_of_approximation = $alpha_range/100;
							$percent_of_approximation_of_current = $approximation_of_current/$one_percent_of_approximation;
							$persent_of_this_category += $sub_persent_of_this_category * $percent_of_approximation_of_current;
						}
					}
					else 
						$persent_of_this_category += 0;
				}
				$result_of_recognizing[$i]["category"] = $all_categories_with_the_given_partition[$i]["category"];
				$result_of_recognizing[$i]["percent"] = $persent_of_this_category;
				$result_of_recognizing[$i]["outputs"] = $outputs_of_image_on_all_settings[$i];
			}
			$this -> converted_to_string_for_return_to_jquery($result_of_recognizing);
		}
		
		private function converted_to_string_for_return_to_jquery($result_of_recognizing){
			
			for($i = 0; $i < count($result_of_recognizing); $i++){
				$result_of_recognizing[$i]["outputs"] = implode($result_of_recognizing[$i]["outputs"], ", ");
			}
			
			//отсортируем сразу
			function mmm($v1, $v2){
				if ($v1["percent"] == $v2["percent"]) return 0;
				return ($v1["percent"] < $v2["percent"])? 1: -1;
			}
			usort($result_of_recognizing, "mmm"); 
			
			for($i = 0; $i < count($result_of_recognizing); $i++){
				$str_1 .= implode("|cols|", $result_of_recognizing[$i]);
				$str_1 = $str_1."|rows|";
			}
			echo $str_1;
		}
	}
?>