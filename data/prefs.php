<?php
	include 'include/db.php';
	function prefsRowToObject($row) {
		$object = new stdClass();
		$object->list = new stdClass();
		$object->grid = new stdClass();
		$object->summary = new stdClass();
		$object->period = new stdClass();
		$object->add = new stdClass();
		$object->edit = new stdClass();
		$object->view = new stdClass();
		$object->list->lunchDinner = $row->l_lunchDinner == 1;
		$object->list->dayOfWeek = $row->l_dayOfWeek == 1;
		$object->list->startTime = $row->l_startTime == 1;
		$object->list->endTime = $row->l_endTime == 1;
		$object->list->hours = $row->l_hours == 1;
		$object->list->earnedWage = $row->l_earnedWage == 1;
		$object->list->earnedTips = $row->l_earnedTips == 1;
		$object->list->earnedTotal = $row->l_earnedTotal == 1;
		$object->list->firstTable = $row->l_firstTable == 1;
		$object->list->sales = $row->l_sales == 1;
		$object->list->tipout = $row->l_tipout == 1;
		$object->list->transfers = $row->l_transfers == 1;
		$object->list->covers = $row->l_covers == 1;
		$object->list->campHours = $row->l_campHours == 1;
		$object->list->salesPerHour = $row->l_salesPerHour == 1;
		$object->list->salesPerCover = $row->l_salesPerCover == 1;
		$object->list->tipsPercent = $row->l_tipsPercent == 1;
		$object->list->tipoutPercent = $row->l_tipoutPercent == 1;
		$object->list->tipsVsWage = $row->l_tipsVsWage == 1;
		$object->list->hourly = $row->l_hourly == 1;
		$object->list->cash = $row->l_cash == 1;
		$object->list->due = $row->l_due == 1;
		$object->list->dueCheck = $row->l_dueCheck == 1;
		$object->list->cut = $row->l_cut == 1;
		$object->list->section = $row->l_section == 1;
		$object->list->notes = $row->l_notes == 1;
		$object->list->noCampHourly = $row->l_noCampHourly == 1;
		$object->grid->startTime = $row->g_startTime == 1;
		$object->grid->endTime = $row->g_endTime == 1;
		$object->grid->sales = $row->g_sales == 1;
		$object->grid->earnedTips = $row->g_earnedTips == 1;
		$object->grid->tipsPercent = $row->g_tipsPercent == 1;
		$object->grid->hourly = $row->g_hourly == 1;
		$object->grid->hours = $row->g_hours == 1;
		$object->grid->location = $row->g_location == 1;
		$object->grid->wage = $row->g_wage == 1;
		$object->grid->earnedWage = $row->g_earnedWage == 1;
		$object->grid->earnedTotal = $row->g_earnedTotal == 1;
		$object->grid->tipout = $row->g_tipout == 1;
		$object->grid->transfers = $row->g_transfers == 1;
		$object->grid->covers = $row->g_covers == 1;
		$object->grid->campHours = $row->g_campHours == 1;
		$object->grid->salesPerHour = $row->g_salesPerHour == 1;
		$object->grid->salesPerCover = $row->g_salesPerCover == 1;
		$object->grid->tipoutPercent = $row->g_tipoutPercent == 1;
		$object->grid->tipsVsWage = $row->g_tipsVsWage == 1;
		$object->grid->cash = $row->g_cash == 1;
		$object->grid->due = $row->g_due == 1;
		$object->grid->dueCheck = $row->g_dueCheck == 1;
		$object->grid->cut = $row->g_cut == 1;
		$object->grid->section = $row->g_section == 1;
		$object->grid->noCampHourly = $row->g_noCampHourly == 1;
		$object->summary->hours = $row->s_hours == 1;
		$object->summary->earnedWage = $row->s_earnedWage == 1;
		$object->summary->earnedTips = $row->s_earnedTips == 1;
		$object->summary->earnedTotal = $row->s_earnedTotal == 1;
		$object->summary->sales = $row->s_sales == 1;
		$object->summary->tipout = $row->s_tipout == 1;
		$object->summary->covers = $row->s_covers == 1;
		$object->summary->campHours = $row->s_campHours == 1;
		$object->summary->salesPerHour = $row->s_salesPerHour == 1;
		$object->summary->salesPerCover = $row->s_salesPerCover == 1;
		$object->summary->tipsPercent = $row->s_tipsPercent == 1;
		$object->summary->tipoutPercent = $row->s_tipoutPercent == 1;
		$object->summary->tipsVsWage = $row->s_tipsVsWage == 1;
		$object->summary->hourly = $row->s_hourly == 1;
		$object->summary->transfers = $row->s_transfers == 1;
		$object->summary->noCampHourly = $row->s_noCampHourly == 1;
		$object->period->shifts = $row->p_shifts == 1;
		$object->period->hours = $row->p_hours == 1;
		$object->period->earnedWage = $row->p_earnedWage == 1;
		$object->period->earnedTips = $row->p_earnedTips == 1;
		$object->period->earnedTotal = $row->p_earnedTotal == 1;
		$object->period->sales = $row->p_sales == 1;
		$object->period->tipout = $row->p_tipout == 1;
		$object->period->covers = $row->p_covers == 1;
		$object->period->campHours = $row->p_campHours == 1;
		$object->period->salesPerHour = $row->p_salesPerHour == 1;
		$object->period->salesPerCover = $row->p_salesPerCover == 1;
		$object->period->tipsPercent = $row->p_tipsPercent == 1;
		$object->period->tipoutPercent = $row->p_tipoutPercent == 1;
		$object->period->tipsVsWage = $row->p_tipsVsWage == 1;
		$object->period->hourly = $row->p_hourly == 1;
		$object->period->transfers = $row->p_transfers == 1;
		$object->period->noCampHourly = $row->p_noCampHourly == 1;
		$object->add->location = $row->a_location == 1;
		$object->add->wage = $row->a_wage == 1;
		$object->add->startTime = $row->a_startTime == 1;
		$object->add->endTime = $row->a_endTime == 1;
		$object->add->firstTable = $row->a_firstTable == 1;
		$object->add->campHours = $row->a_campHours == 1;
		$object->add->sales = $row->a_sales == 1;
		$object->add->covers = $row->a_covers == 1;
		$object->add->tipout = $row->a_tipout == 1;
		$object->add->transfers = $row->a_transfers == 1;
		$object->add->cash = $row->a_cash == 1;
		$object->add->due = $row->a_due == 1;
		$object->add->section = $row->a_section == 1;
		$object->add->cut = $row->a_cut == 1;
		$object->add->notes = $row->a_notes == 1;
		$object->edit->location = $row->e_location == 1;
		$object->edit->wage = $row->e_wage == 1;
		$object->edit->startTime = $row->e_startTime == 1;
		$object->edit->endTime = $row->e_endTime == 1;
		$object->edit->firstTable = $row->e_firstTable == 1;
		$object->edit->campHours = $row->e_campHours == 1;
		$object->edit->sales = $row->e_sales == 1;
		$object->edit->covers = $row->e_covers == 1;
		$object->edit->tipout = $row->e_tipout == 1;
		$object->edit->transfers = $row->e_transfers == 1;
		$object->edit->cash = $row->e_cash == 1;
		$object->edit->due = $row->e_due == 1;
		$object->edit->section = $row->e_section == 1;
		$object->edit->cut = $row->e_cut == 1;
		$object->edit->notes = $row->e_notes == 1;
		$object->view->startTime = $row->v_startTime == 1;
		$object->view->endTime = $row->v_endTime == 1;
		$object->view->hours = $row->v_hours == 1;
		$object->view->location = $row->v_location == 1;
		$object->view->wage = $row->v_wage == 1;
		$object->view->sales = $row->v_sales == 1;
		$object->view->covers = $row->v_covers == 1;
		$object->view->salesPerHour = $row->v_salesPerHour == 1;
		$object->view->salesPerCover = $row->v_salesPerCover == 1;
		$object->view->tipout = $row->v_tipout == 1;
		$object->view->tipoutPercent = $row->v_tipoutPercent == 1;
		$object->view->transfers = $row->v_transfers == 1;
		$object->view->cash = $row->v_cash == 1;
		$object->view->due = $row->v_due == 1;
		$object->view->earnedWage = $row->v_earnedWage == 1;
		$object->view->earnedTips = $row->v_earnedTips == 1;
		$object->view->tipsPercent = $row->v_tipsPercent == 1;
		$object->view->earnedTotal = $row->v_earnedTotal == 1;
		$object->view->tipsVsWage = $row->v_tipsVsWage == 1;
		$object->view->hourly = $row->v_hourly == 1;
		$object->view->firstTable = $row->v_firstTable == 1;
		$object->view->campHours = $row->v_campHours == 1;
		$object->view->section = $row->v_section == 1;
		$object->view->cut = $row->v_cut == 1;
		$object->view->notes = $row->v_notes == 1;
		$object->view->noCampHourly = $row->v_noCampHourly == 1;
		return $object;
	}
	function saveUserPrefs
	(
		$db, $id, $l_lunchDinner, $l_dayOfWeek, $l_startTime, $l_endTime, $l_hours, $l_earnedWage, $l_earnedTips, $l_earnedTotal, $l_firstTable, $l_sales, $l_tipout, $l_transfers, $l_covers, $l_campHours, $l_salesPerHour, $l_salesPerCover, $l_tipsPercent, $l_tipoutPercent, $l_tipsVsWage, $l_hourly, $l_cash, $l_due, $l_dueCheck, $l_cut, $l_section, $l_notes, $l_noCampHourly, $g_startTime, $g_endTime, $g_sales, $g_earnedTips, $g_tipsPercent, $g_hourly, $g_hours, $g_location, $g_wage, $g_earnedWage, $g_earnedTotal, $g_tipout, $g_transfers, $g_covers, $g_campHours, $g_salesPerHour, $g_salesPerCover, $g_tipoutPercent, $g_tipsVsWage, $g_cash, $g_due, $g_dueCheck, $g_cut, $g_section, $g_noCampHourly, $s_hours, $s_earnedWage, $s_earnedTips, $s_earnedTotal, $s_sales, $s_tipout, $s_covers, $s_campHours, $s_salesPerHour, $s_salesPerCover, $s_tipsPercent, $s_tipoutPercent, $s_tipsVsWage, $s_hourly, $s_transfers, $s_noCampHourly, $p_shifts, $p_hours, $p_earnedWage, $p_earnedTips, $p_earnedTotal, $p_sales, $p_tipout, $p_covers, $p_campHours, $p_salesPerHour, $p_salesPerCover, $p_tipsPercent, $p_tipoutPercent, $p_tipsVsWage, $p_hourly, $p_transfers, $p_noCampHourly, $a_location, $a_wage, $a_startTime, $a_endTime, $a_firstTable, $a_campHours, $a_sales, $a_covers, $a_tipout, $a_transfers, $a_cash, $a_due, $a_section, $a_cut, $a_notes, $e_location, $e_wage, $e_startTime, $e_endTime, $e_firstTable, $e_campHours, $e_sales, $e_covers, $e_tipout, $e_transfers, $e_cash, $e_due, $e_section, $e_cut, $e_notes, $v_startTime, $v_endTime, $v_hours, $v_location, $v_wage, $v_sales, $v_covers, $v_salesPerHour, $v_salesPerCover, $v_tipout, $v_tipoutPercent, $v_transfers, $v_cash, $v_due, $v_earnedWage, $v_earnedTips, $v_tipsPercent, $v_earnedTotal, $v_tipsVsWage, $v_hourly, $v_firstTable, $v_campHours, $v_section, $v_cut, $v_notes, $v_noCampHourly
	)
	{
		if($stmt = $db->prepare('CALL saveUserPrefs(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
		{
			$stmt->bind_param('iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii', $id, $l_lunchDinner, $l_dayOfWeek, $l_startTime, $l_endTime, $l_hours, $l_earnedWage, $l_earnedTips, $l_earnedTotal, $l_firstTable, $l_sales, $l_tipout, $l_transfers, $l_covers, $l_campHours, $l_salesPerHour, $l_salesPerCover, $l_tipsPercent, $l_tipoutPercent, $l_tipsVsWage, $l_hourly, $l_cash, $l_due, $l_dueCheck, $l_cut, $l_section, $l_notes, $l_noCampHourly, $g_startTime, $g_endTime, $g_sales, $g_earnedTips, $g_tipsPercent, $g_hourly, $g_hours, $g_location, $g_wage, $g_earnedWage, $g_earnedTotal, $g_tipout, $g_transfers, $g_covers, $g_campHours, $g_salesPerHour, $g_salesPerCover, $g_tipoutPercent, $g_tipsVsWage, $g_cash, $g_due, $g_dueCheck, $g_cut, $g_section, $g_noCampHourly, $s_hours, $s_earnedWage, $s_earnedTips, $s_earnedTotal, $s_sales, $s_tipout, $s_covers, $s_campHours, $s_salesPerHour, $s_salesPerCover, $s_tipsPercent, $s_tipoutPercent, $s_tipsVsWage, $s_hourly, $s_transfers, $s_noCampHourly, $p_shifts, $p_hours, $p_earnedWage, $p_earnedTips, $p_earnedTotal, $p_sales, $p_tipout, $p_covers, $p_campHours, $p_salesPerHour, $p_salesPerCover, $p_tipsPercent, $p_tipoutPercent, $p_tipsVsWage, $p_hourly, $p_transfers, $p_noCampHourly, $a_location, $a_wage, $a_startTime, $a_endTime, $a_firstTable, $a_campHours, $a_sales, $a_covers, $a_tipout, $a_transfers, $a_cash, $a_due, $a_section, $a_cut, $a_notes, $e_location, $e_wage, $e_startTime, $e_endTime, $e_firstTable, $e_campHours, $e_sales, $e_covers, $e_tipout, $e_transfers, $e_cash, $e_due, $e_section, $e_cut, $e_notes, $v_startTime, $v_endTime, $v_hours, $v_location, $v_wage, $v_sales, $v_covers, $v_salesPerHour, $v_salesPerCover, $v_tipout, $v_tipoutPercent, $v_transfers, $v_cash, $v_due, $v_earnedWage, $v_earnedTips, $v_tipsPercent, $v_earnedTotal, $v_tipsVsWage, $v_hourly, $v_firstTable, $v_campHours, $v_section, $v_cut, $v_notes, $v_noCampHourly);
			$stmt->execute();
			echo $stmt->affected_rows;
			$stmt->free_result();
			$stmt->close();
		}
		else {http_response_code(500);}
	}
	function getUserPrefs($db, $id)
	{
		$object = new stdClass();
		if($stmt = $db->prepare('CALL getUserPrefs(?)'))
		{
			$stmt->bind_param('i', $id);
			$stmt->execute();
			$row = $stmt->get_result()->fetch_object();
			$object = prefsRowToObject($row);
			header('Content-Type: application/json');
			echo json_encode($object);
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
				if(isset($_GET['id']))
				{
					if(isset($_GET['savePrefs']))
					{
						saveUserPrefs($db, $_GET['id'], isset($_GET['l_lunchDinner']), isset($_GET['l_dayOfWeek']), isset($_GET['l_startTime']), isset($_GET['l_endTime']), isset($_GET['l_hours']), isset($_GET['l_earnedWage']), isset($_GET['l_earnedTips']), isset($_GET['l_earnedTotal']), isset($_GET['l_firstTable']), isset($_GET['l_sales']), isset($_GET['l_tipout']), isset($_GET['l_transfers']), isset($_GET['l_covers']), isset($_GET['l_campHours']), isset($_GET['l_salesPerHour']), isset($_GET['l_salesPerCover']), isset($_GET['l_tipsPercent']), isset($_GET['l_tipoutPercent']), isset($_GET['l_tipsVsWage']), isset($_GET['l_hourly']), isset($_GET['l_cash']), isset($_GET['l_due']), isset($_GET['l_dueCheck']), isset($_GET['l_cut']), isset($_GET['l_section']), isset($_GET['l_notes']), isset($_GET['l_noCampHourly']), isset($_GET['g_startTime']), isset($_GET['g_endTime']), isset($_GET['g_sales']), isset($_GET['g_earnedTips']), isset($_GET['g_tipsPercent']), isset($_GET['g_hourly']), isset($_GET['g_hours']), isset($_GET['g_location']), isset($_GET['g_wage']), isset($_GET['g_earnedWage']), isset($_GET['g_earnedTotal']), isset($_GET['g_tipout']), isset($_GET['g_transfers']), isset($_GET['g_covers']), isset($_GET['g_campHours']), isset($_GET['g_salesPerHour']), isset($_GET['g_salesPerCover']), isset($_GET['g_tipoutPercent']), isset($_GET['g_tipsVsWage']), isset($_GET['g_cash']), isset($_GET['g_due']), isset($_GET['g_dueCheck']), isset($_GET['g_cut']), isset($_GET['g_section']), isset($_GET['g_noCampHourly']), isset($_GET['s_hours']), isset($_GET['s_earnedWage']), isset($_GET['s_earnedTips']), isset($_GET['s_earnedTotal']), isset($_GET['s_sales']), isset($_GET['s_tipout']), isset($_GET['s_covers']), isset($_GET['s_campHours']), isset($_GET['s_salesPerHour']), isset($_GET['s_salesPerCover']), isset($_GET['s_tipsPercent']), isset($_GET['s_tipoutPercent']), isset($_GET['s_tipsVsWage']), isset($_GET['s_hourly']), isset($_GET['s_transfers']), isset($_GET['s_noCampHourly']), isset($_GET['p_shifts']), isset($_GET['p_hours']), isset($_GET['p_earnedWage']), isset($_GET['p_earnedTips']), isset($_GET['p_earnedTotal']), isset($_GET['p_sales']), isset($_GET['p_tipout']), isset($_GET['p_covers']), isset($_GET['p_campHours']), isset($_GET['p_salesPerHour']), isset($_GET['p_salesPerCover']), isset($_GET['p_tipsPercent']), isset($_GET['p_tipoutPercent']), isset($_GET['p_tipsVsWage']), isset($_GET['p_hourly']), isset($_GET['p_transfers']), isset($_GET['p_noCampHourly']), isset($_GET['a_location']), isset($_GET['a_wage']), isset($_GET['a_startTime']), isset($_GET['a_endTime']), isset($_GET['a_firstTable']), isset($_GET['a_campHours']), isset($_GET['a_sales']), isset($_GET['a_covers']), isset($_GET['a_tipout']), isset($_GET['a_transfers']), isset($_GET['a_cash']), isset($_GET['a_due']), isset($_GET['a_section']), isset($_GET['a_cut']), isset($_GET['a_notes']), isset($_GET['e_location']), isset($_GET['e_wage']), isset($_GET['e_startTime']), isset($_GET['e_endTime']), isset($_GET['e_firstTable']), isset($_GET['e_campHours']), isset($_GET['e_sales']), isset($_GET['e_covers']), isset($_GET['e_tipout']), isset($_GET['e_transfers']), isset($_GET['e_cash']), isset($_GET['e_due']), isset($_GET['e_section']), isset($_GET['e_cut']), isset($_GET['e_notes']), isset($_GET['v_startTime']), isset($_GET['v_endTime']), isset($_GET['v_hours']), isset($_GET['v_location']), isset($_GET['v_wage']), isset($_GET['v_sales']), isset($_GET['v_covers']), isset($_GET['v_salesPerHour']), isset($_GET['v_salesPerCover']), isset($_GET['v_tipout']), isset($_GET['v_tipoutPercent']), isset($_GET['v_transfers']), isset($_GET['v_cash']), isset($_GET['v_due']), isset($_GET['v_earnedWage']), isset($_GET['v_earnedTips']), isset($_GET['v_tipsPercent']), isset($_GET['v_earnedTotal']), isset($_GET['v_tipsVsWage']), isset($_GET['v_hourly']), isset($_GET['v_firstTable']), isset($_GET['v_campHours']), isset($_GET['v_section']), isset($_GET['v_cut']), isset($_GET['v_notes']), isset($_GET['v_noCampHourly']));
					}
					else if(isset($_GET['getPrefs']))
					{
						getUserPrefs($db, $_GET['id']);
					}
				}
				else 
				{
					http_response_code(400);
				}
				break;
			case 'POST':
				$data = json_decode(file_get_contents("php://input"));
				//insert($db, $data);
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