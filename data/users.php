<?php
	include 'include/db.php';
	function shiftRowToObject($row) {
		$object = new stdClass();
		$object->name 	= $row->name;
		$object->email 	= $row->email;
		$object->id 	= $row->id;
		return $object;
	}
	function selectAll($db)
	{
		if($stmt = $db->prepare('CALL getUsers()'))
		{
			$stmt->execute(); 
			$objects = [];
			$result = $stmt->get_result();
			while($row = $result->fetch_object())
			{
				$objects[] = shiftRowToObject($row);
			}
			header('Content-Type: application/json');
			echo json_encode($objects);
			$stmt->free_result();
			$stmt->close();
		}
		else {http_response_code(500);}
	}
	function selectById($db, $id)
	{
		$object = new stdClass();
		if($stmt = $db->prepare('CALL getUserById(?)'))
		{
			$stmt->bind_param('i', $id);
			$stmt->execute();
			$row = $stmt->get_result()->fetch_object();
			$object = shiftRowToObject($row);
			header('Content-Type: application/json');
			echo json_encode($object);
			$stmt->free_result();
			$stmt->close();
		}
		else {http_response_code(500);}
	}
	function insert($db, $data)
	{
		if($stmt = $db->prepare('CALL createUser(?,?)'))
		{
			$stmt->bind_param('ss', $data->name, $data->email);
			$stmt->execute();
			$stmt->bind_result($id);
			$stmt->fetch();
			echo $id;
			$stmt->free_result();
			$stmt->close();
		}
		else {http_response_code(500);}
	}
	function update($db, $data)
	{
		if($stmt = $db->prepare('CALL editUser(?, ?, ?)'))
		{
			$stmt->bind_param('iss', $data->id, $data->name, $data->email);
			$stmt->execute();
			echo $stmt->affected_rows;
			$stmt->free_result();
			$stmt->close();
		}
		else {http_response_code(500);}
	}
	function delete($db, $id)
	{
		if($stmt = $db->prepare('CALL deleteUser(?);'))
		{
			$stmt->bind_param('i', $id);
			$stmt->execute();
			echo $stmt->affected_rows;
			$stmt->free_result();
			$stmt->close();
		}
		else {http_response_code(500);}
	}
	function login($db, $name, $email)
	{
		if($stmt = $db->prepare('CALL login(?,?)'))
		{
			//* DEBUG */ echo '<p>name:' . $name . '|email:' . $email . '</p>';
			$stmt->bind_param('ss', $name, $email);
			$stmt->execute();
			$stmt->bind_result($id);
			$stmt->fetch();
			echo $id;
			$stmt->free_result();
			$stmt->close();
		}
		else {http_response_code(500);}
	}

	try 
	{
		switch($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				if (isset($_GET['name']) && isset($_GET['email']))
				{
					//* DEBUG */ echo 'name:' . $_GET['name'] . '|email:' . $_GET['email'];
					login($db, $_GET['name'], $_GET['email']);
				}
				else if(isset($_GET['id']))
				{
					selectById($db, $_GET['id']);
				}
				else
				{
					selectAll($db);
				}
				break;
			case 'POST':
				$data = json_decode(file_get_contents("php://input"));
				insert($db, $data);
				break;
			case 'PUT':
				$data = json_decode(file_get_contents("php://input"));
				update($db, $data);
				break;
			case 'DELETE':
				if(isset($_GET['id']))
				{
					delete($db, $_GET['id']);
				}
				break;
		}
		$db->close();
	} 
	catch (Exception $e) 
	{
		http_response_code(500);
		die();
	}
?>