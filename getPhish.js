$(document).ready(function() {
  $("input[type='radio'][name='method']").change(function() {
  	if (this.value == 'Command') {
		document.getElementById("inputField").innerHTML = "<textarea name='command' cols='50' rows='5'>";
  		}
  	if (this.value == 'File') {
  		document.getElementById("inputField").innerHTML = "<input type='file' id='fileUpload' name='fileUpload'><br>";
  		}
  	});
  });
