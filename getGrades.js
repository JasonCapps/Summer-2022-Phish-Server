      //Like with getTables.js, this file just links each button for selecting a team to a hardcoded string value that gets passed to the linked php page.
      $(document).ready(function() {
	$("#france").click(function() {
	  $("#getGrades").load("getGrades.php", {
		selected_country: "france"
	  });
	});
	$("#spain").click(function() {
	  $("#getGrades").load("getGrades.php", {
		selected_country: "spain"
	  });
	});
	$("#england").click(function() {
	  $("#getGrades").load("getGrades.php", {
		selected_country: "england"
	  });
	});
	$("#germany").click(function() {
	  $("#getGrades").load("getGrades.php", {
		selected_country: "germany"
	  });
	});
	$("#rome").click(function() {
	  $("#getGrades").load("getGrades.php", {
		selected_country: "rome"
	  });
	});
	$("#russia").click(function() {
	  $("#getGrades").load("getGrades.php", {
		selected_country: "russia"
	  });
	});
	$("#japan").click(function() {
	  $("#getGrades").load("getGrades.php", {
		selected_country: "japan"
	  });
	});
	$("#china").click(function() {
	  $("#getGrades").load("getGrades.php", {
		selected_country: "china"
	  });
	});
      });
