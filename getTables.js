      // This file just links each button for the team selection to a hardcoded string value that gets passed to get_tables.php.
      // Are there better ways to do this? Probably.
      $(document).ready(function() {
	$("#france").click(function() {
	  $("#getTables").load("get_tables.php", {
		selected_country: "france"
	  });
	});
	$("#spain").click(function() {
	  $("#getTables").load("get_tables.php", {
		selected_country: "spain"
	  });
	});
	$("#england").click(function() {
	  $("#getTables").load("get_tables.php", {
		selected_country: "england"
	  });
	});
	$("#germany").click(function() {
	  $("#getTables").load("get_tables.php", {
		selected_country: "germany"
	  });
	});
	$("#rome").click(function() {
	  $("#getTables").load("get_tables.php", {
		selected_country: "rome"
	  });
	});
	$("#russia").click(function() {
	  $("#getTables").load("get_tables.php", {
		selected_country: "russia"
	  });
	});
	$("#japan").click(function() {
	  $("#getTables").load("get_tables.php", {
		selected_country: "japan"
	  });
	});
	$("#china").click(function() {
	  $("#getTables").load("get_tables.php", {
		selected_country: "china"
	  });
	});
      });
