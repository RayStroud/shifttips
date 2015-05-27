<?php
	include 'include/dbconnect.php';

	$id = isset($_GET['id']) ? $_GET['id'] : null;

	if(isset($id))
	{
		$shiftSQL = $db->prepare("DELETE FROM shift WHERE id = ?");
		$shiftSQL->bind_param('i', $id);
		$shiftSQL->execute();

		//check if delete failed
		if ($shiftSQL->affected_rows < 1)
		{
			header('Location: error.php');
		}
		else
		{
			header('Location: all.php');
		}

		$shiftSQL->close();
	}
	else
	{
		header('Location: .');
	}
?>