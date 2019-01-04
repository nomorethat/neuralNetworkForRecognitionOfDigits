$(document).ready(function(){
	
	/* файл отправляет ajax-запросы серверу */
	
	$("#show_recognizing").click(show_recognizing);
	$("#show_education").click(show_education);
	
	/* переключение обучение-распознавание */
	
	function show_recognizing(){
		$("#to_recognize").show();
		$("#to_educate").hide();
		$("#show_education").css("background-color", "#555555");
		$("#show_recognizing").css("background-color", "#222222");
	}
	
	function show_education(){
		$("#to_recognize").hide();
		$("#to_educate").show();
		$("#show_education").css("background-color", "#222222");
		$("#show_recognizing").css("background-color", "#555555");
	}
	
	/* обучение */
	
	$("#submit_to_educate").bind("click", submit_to_educate);
	function submit_to_educate(){
		var date = new Date(); //оценка времени работы
		start_script = date.getTime();
		reset_errors();
		
		var the_category_name_in_education = $.trim($("#enter_the_category_name_in_education").val());
		var directory_with_training_set = $.trim($("#directory_with_training_set").val());
		var size_of_a_fragment = $("#size_of_a_fragment option:selected").text();
		var threshold_of_a_element = $("#threshold_of_a_element option:selected").text();
		var learning_algorithm = $("#learning_algorithm option:selected").text();
		var arrangement_of_r_elements = $("#arrangement_of_r_elements option:selected").text();
		var itetation_of_education = $.trim($("#itetation_of_education").val());
		
		var is_error = check_on_valid(the_category_name_in_education, directory_with_training_set, itetation_of_education);
		
		if(is_error == false)
			send_data_for_learning(the_category_name_in_education, directory_with_training_set, size_of_a_fragment, threshold_of_a_element, learning_algorithm, arrangement_of_r_elements, itetation_of_education);
	}
	
	function check_on_valid(the_category_name_in_education, directory_with_training_set, itetation_of_education){
		var is_error = false;
		if(the_category_name_in_education === ""){
			$('<span id="directory_is_not_specified" class="message_error">*Категория не указана</span>').insertAfter("#enter_the_category_name_in_education");
			var is_error = true;
			return;
		}
		if(directory_with_training_set === ""){
			$('<span id="error_directory_with_training_set" class="message_error">*Директория не указана</span>').insertAfter("#directory_with_training_set");
			var is_error = true;
			return;
		}
		if(itetation_of_education === ""){
			$('<span id="itetation_of_education_is_not_specified" class="message_error">*Количество итераций не указано</span>').insertAfter("#itetation_of_education");
			var is_error = true;
			return;
		}
		if(isNaN(itetation_of_education) === true){
			$('<span id="itetation_of_education_is_not_specified" class="message_error">*Укажите числовое значение</span>').insertAfter("#itetation_of_education");
			var is_error = true;
			return;
		}
		return is_error;
	}
	
	function reset_errors(){
		$("#dir_not_exists_error").remove();
		$("#dir_already_exists").remove();
		$("#directory_is_not_specified").remove();
		$("#error_directory_with_training_set").remove();
		$("#itetation_of_education_is_not_specified").remove();
	}
	
	function send_data_for_learning(the_category_name_in_education, directory_with_training_set, size_of_a_fragment, threshold_of_a_element, learning_algorithm, arrangement_of_r_elements, itetation_of_education){
		$('.learning').show();
		load_imitation();
		
		$.ajax({
			url: "php/controller_class.php",
			type: "POST",
			data: ({
				"submit_to_educate": "true", 
				"the_category_name_in_education": the_category_name_in_education, 
				"directory_with_training_set": directory_with_training_set, 
				"size_of_a_fragment": size_of_a_fragment, 
				"threshold_of_a_element": threshold_of_a_element, 
				"learning_algorithm": learning_algorithm, 
				"arrangement_of_r_elements": arrangement_of_r_elements, 
				"itetation_of_education": itetation_of_education
			}),
			success: function (success_submit){
				if(success_submit === "-1"){
					$('<span id="dir_not_exists_error" class="message_error">*Указанная директория не существует</span>').insertAfter("#directory_with_training_set");
				}
				if(success_submit == "already exist"){
					$('<span id="dir_already_exists" class="message_error">*Указанная категория уже существует</span>').insertAfter("#enter_the_category_name_in_education");
					var no_correct = true;
					$('.learning').hide();
				}
				else{
					var end_script = (new Date).getTime();
					$("#time_of_education").text((end_script - start_script)/1000 + " секунд");
					$("#result_of_adjusting_the_weights").text(success_submit);
					clearInterval(timer);
				}
				$('.learning').hide();
			}
		});
	}
	
	function load_imitation(){
		x = 0;
		timer = setInterval(learning, 260);
	}
	
	function learning(){
		if(x === 0) $('#learning_three_dots').text("");
		if(x === 1) $('#learning_three_dots').text(".");
		if(x === 2) $('#learning_three_dots').text(". .");
		if(x === 3){
			 $('#learning_three_dots').text(". . .");
			 x = -1;
		}
		x++;
	}
	
	
	
	
	
	/* распознавание */
	
	//нужно для загрузки файлов, чтобы обрезать путь к файлу, оставив только URI
	
	$(".file-upload input[type=file]").change(function(){
		var filename = $(this).val();
		var pos = filename.lastIndexOf("\\");
		if(pos != -1){
			filename = filename.substr(pos+1);
		}
		$("#file_name").text(filename);
	});
	
	$(".file-upload_in_education input[type=file]").change(function(){
		var filename = $(this).val();
		var pos = filename.lastIndexOf("\\");
		if(pos != -1){
			filename = filename.substr(pos+1);
		}
		$("#file_name_in_education").text(filename);
	});
	
	
	
	
	function readImage ( input ) {//При помощи функции readImage() мы будем считывать файл с поля формы и передавать его в блок для предварительного просмотра. 
		if (input.files && input.files[0]) {
		var reader = new FileReader();//Создается объект FileReader. Он позволяет веб-приложению считывать содержимое файла на компьютере пользователя. 

			reader.onload = function (e) {//Событие .onload сработает когда содержимое будет считано, при помощи этого события мы выведем изображение в блок предварительного просмотра.
				$('#preview').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);// метод .readAsDataURL() запускает процесс чтения файла, по завершению чтения будет выполнено событие .onload и картинка появится у вас на экране.
		}
	}

	$('#the_file_for_recognizing').bind("change", function(){
		$("#file_no_selected").remove();
		readImage(this);
	});

	$('#upload-image').on('submit',(function(e) {
		e.preventDefault();//Перехват формы и её обработка. При клике на кнопку «Отправить» событие будет перехвачено скриптом и при помощи функции .preventDefault() форма не отправит данные в index.html. .preventDefault() служит для отмены вызова каких-либо событий.

		$("#verdict").remove();
		
		var formData = new FormData(this);//Объект FormData нужен нам для создания POST запроса к нашему скрипту, это намного проще чем вписывать каждый элемент формы в строку. Создали объект, заполнили данными, отдали в наш ajax.

		$.ajax({
			type:'POST', // Тип запроса
			url: 'php/controller_class.php', // Скрипт обработчика
			data: formData, // Данные которые мы передаем
			cache:false, // В запросах POST отключено по умолчанию, но перестрахуемся
			contentType: false, // Тип кодирования данных мы задали в форме, это отключим
			processData: false, // Отключаем, так как передаем файл
			success:function(success){
				if(success == "file_no_selected"){
					$('<span id="file_no_selected" class="message_error">*Файл не выбран</span>').insertAfter("#file_name");
					$('#file_no_selected').css("display", "inline-block");
					$('#file_no_selected').css("color", "#f00");
					$('#file_no_selected').css("margin-top", "-16px");
				}
				var verdict = new Array();
				var rows = success.split("|rows|");
				for(var i = 0; i < rows.length; i++){
					cols = rows[i].split("|cols|");
					verdict[i] = cols;
				}
				verdict = verdict.slice(0, verdict.length - 1);
				
				// $("#to_recognize").append('<div id="verdict">' + success +'</div>')
				$("#to_recognize").append('<div id="verdict"></div>')
				$("#verdict").append('<span><b>Вердикт:</b> сеть считает, что с вероятностью <b>' + verdict[0][1] + '%</b> это "' + verdict[0][0].toLowerCase() + '".</span><span></span><br />');
				
				for(var i = 0; i < 3; i++){
					$("#verdict").append('<br /><span class="response_more_that_40"><b>' + verdict[i][0] + ': </b> ' + verdict[i][1] + '%</span><span></span>');
					//$("#verdict").append('<br /><br /><span class="response_more_that_40"><b>' + verdict[i][0] + ': </b> ' + verdict[i][1] + '%</span><span></span><br /><span class="response_more_that_40"><b>Отклик: </b> (' + verdict[i][2] + ') </span>');
				}
			}
		});
	}));
	
});