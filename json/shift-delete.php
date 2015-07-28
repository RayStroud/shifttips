<?php
	include 'include/dbconnect.php';

	$shift = json_decode(file_get_contents("php://input"));
	$id = isset($shift->$id) && is_numeric($shift->$id) ? $id : null;

	if(isset($id))
	{
		$shiftSQL = $db->prepare("DELETE FROM shift WHERE id = ?");
		$shiftSQL->bind_param('i', $id);
		$shiftSQL->execute();

		//check if delete failed
		if ($shiftSQL->affected_rows < 1)
		{
			http_response_code(500);
			die();
		}

		$shiftSQL->close();
	}
	else
	{
		http_response_code(500);
		die();
	}
?>