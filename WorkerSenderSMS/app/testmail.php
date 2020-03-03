<!DOCTYPE html>

<html>
	<head>
		 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		 <script src="https://code.jquery.com/jquery-3.4.1.min.js"> </script>
	</head>

	<body>

		<form id="frmSendmail" name="frmSendmail" action="/sendmail" method="post">
			<h2>Enviar Email</h2>

			<label>From:</label> <br/>
			<input type="text" id="from" value="no-reply@motofan.com" style="width:200px;" /> <br/><br/>

			<label>To:</label> <br/>
			<input type="text" id="to" style="width:200px;" /> <br/><br/>

			<label>Subject:</label> <br/>
			<input type="text" id="subject" style="width:200px;" /> <br/><br/>

			<label>Body:</label> <br/>
			<textarea id="body" style="width:500px;height:200px;"></textarea> <br/><br/>

			<input type="submit" value="Enviar" />
		</form>

		<br/><br/>

		<h2>Resultado:</h2>
		<textarea id="msgbox" style="width:600px;height:300px;"></textarea>


		<script type="text/javascript">
			$(function() {

				$("#frmSendmail").on('submit', function(e) {
					e.preventDefault();

					var from = $("#from").val();
					var to = $("#to").val();
					var subject = $("#subject").val();
					var body = $("#body").val();

					var data = {
						from: from,
						to: to,
						subject: subject,
						body: body
					};

					var params = {
						type: 'POST',
						url: '/sendmail',
						data: data
					};

					$.ajax(params)
					.done(function( html ) {
						$("#msgbox").append( JSON.stringify(html) );
					});
				});

			});
		</script>


	</body>
</html>
