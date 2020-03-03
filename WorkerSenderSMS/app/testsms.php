<!DOCTYPE html>

<html>
	<head>
		 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		 <script src="https://code.jquery.com/jquery-3.4.1.min.js"> </script>
	</head>

	<body>

		<form id="frmSave" action="/enqueue" method="post">
			<h2>Enviar SMS</h2>

			<label>Teléfono:</label> <br/>
			<input type="text" id="phone" /> <br/><br/>

			<label>Mensaje:</label> <br/>
			<input type="text" id="message" /> <br/><br/>

			<label>IP:</label> <br/>
			<input type="text" id="ip" /> <br/><br/>

			<label>Callback URL:</label> <br/>
			<input type="text" id="callback" /> <br/><br/>

			<input type="submit" value="Enviar" />
		</form>

		<br/><br/>

		<form id="frmValidate" action="/checkphone" method="get">
			<h2>Validar Número de Móvil</h2>

			<label>Teléfono:</label> <br/>
			<input type="text" id="mobile" /> <br/><br/>

			<label>Idioma:</label> <br/>
			<input type="text" id="country" value="ES" disabled /> <br/><br/>

			<input type="submit" value="Enviar" />
		</form>

		<br/><br/>

		<h2>Resultado:</h2>
		<textarea id="msgbox"  style="width:500px;height:300px;"></textarea>


		<script>
			$(function() {

				$("#frmSave").on('submit', function(e) {
					e.preventDefault();

					var phone	=	$("#phone").val();
					var message	=	$("#message").val();
					var ip	=	$("#ip").val();
					var callback	=	$("#callback").val();

					var data = {
						phone: phone,
						message: message,
						ip: ip,
						callback: callback
					};

					var params = {
						type: 'POST',
						url: '/enqueue',
						data: data
					};

					$.ajax(params)
					.done(function( data ) {
						$("#msgbox").html( JSON.stringify(data) );
					});
				});

				$("#frmValidate").on('submit', function(e) {
					e.preventDefault();

					var phone = $("#mobile").val();
					var country = $("#country").val();
					var url = '/checkphone/' + phone + '/' + country;

					$.get(url)
					.done(function( data ) {
						$("#msgbox").html( JSON.stringify(data) );
					});
				});

			});
		</script>


	</body>
</html>
