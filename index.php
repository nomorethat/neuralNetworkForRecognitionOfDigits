<!DOCTUPE html>
<html>
<head>
	<title>Управление Нейронной Сетью</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta http-equiv="Cache-Control" content="no-cache" />
	<script type="text/javascript" src="js/jquery-1.9.0.min.js"></script>
	<script type="text/javascript" src="js/interaction_with_neuronal_network.js"></script>
	<link rel="stylesheet" href="css/main.css" type="text/css" />
</head>
<body>
	
	<div id="all_page">
		<header>
			<div id="header_admin_workspace">
				<span id="logo_name">УПРАВЛЕНИЕ НЕЙРОННОЙ СЕТЬЮ</span>
			</div>
		</header>
		<article>
			<div id="neuro_workspace">
				<div id="menu">
					<div id="show_recognizing">
						РАСПОЗНАВАНИЕ
					</div>
					<div id="show_education">
						ОБУЧЕНИЕ
					</div>
				</div>
		
				<div id="to_recognize">
					<h3>Распознать</h3>
					<form id="upload-image" enctype="multipart/form-data">
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<div id="div_for_img_of_product">
										<img id="preview" />
									</div>
								</td>
								<td id="table_form_of_to_recognize">	
									<table  border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td>
												<div class="file-upload">
													<label>
														<input id="the_file_for_recognizing" name="image" type="file" enctype="multipart/form-data"/>
														<span>Выберите файл</span>
													</label>
												</div>
											</td>
											<td>
												<div id="file_name"></div>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<div id="sdfsdf"></div>
												<input id="submit_of_recognizing" type="submit" value="Распознать" />
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<span id="title_size_of_fragments_for_recognizing">Размер фрагментов разбиения: </span>
												<select id="size_of_fragments_for_recognizing" name="size_of_fragments_for_recognizing">
													<option value="10"  selected>10
													<option value="5">5
												</select>
											</td>
										</tr>
									</table>	
								</td>
							</tr>	
						</table>
					</form>		
				</div>
				
				
				<div id="to_educate">
					<table border="0" cellpadding="0" cellspacing="0">
						
						<tr>
							<td colspan="2">
								<h3>Обучить</h3>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input id="enter_the_category_name_in_education" type="text" name="enter_the_category_name_in_education"type="text" placeholder="Введите название категории" />
							</td>
						</tr>
						<tr>
							<td>
								<br />
								<span class="title_of_the_setting">Обучающая выборка: </span>
								
								<span>training_set/</span><input id="directory_with_training_set" type="text" name="directory_with_training_set"type="text"/>
							</td>
							<td>
								
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<span class="title_of_the_setting">Размер фрагментов разбиения: </span>
								<select id="size_of_a_fragment" name="size_of_a_fragment">
									<option value="10"  selected>10
									<option value="5">5
									<option value="2">2
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<span class="title_of_the_setting">Порог A-элемента: </span>
								<select id="threshold_of_a_element" name="threshold_of_a_element">
									<option value="1"  selected>0.15
									<option value="2">0.25
									<option value="3">0.5
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<span class="title_of_the_setting">Алгоритм обучения: </span>
								<select id="learning_algorithm" name="learning_algorithm">
									<option value="1"  selected>Альфа-система подкрепления
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<span class="title_of_the_setting">R - растановка: </span>
								<select id="arrangement_of_r_elements" name="arrangement_of_r_elements">
									<option value="1"  selected>Линии
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<span class="title_of_the_setting">Итераций обучения: </span><input id="itetation_of_education" type="text" name="itetation_of_education" value="100" />
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<span class="title_of_the_setting">Результат настройки весов: </span><br /><span id="result_of_adjusting_the_weights">0</span>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<span class="title_of_the_setting">Время обучения: </span><span id="time_of_education">0</span>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input id="submit_to_educate" type="submit" value="Обучить" />
								<span class="learning">Обучаюсь</span><span class="learning" id="learning_three_dots">. . .</span>
							</td>
						</tr>
					</table>		
				</div>
		</article>		
	</div>
	<div id="clear"></div>
	<footer>	
	</footer>
</body>
</html>