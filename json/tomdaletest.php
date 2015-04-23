<?php

$array = json_decode('[{"sid":"1","wage":"9","date":"Tue Jan 20, 2015","startTime":"11:30AM","endTime":"3:00PM","hours":"3.50","firstTable":"11:45AM","campHours":"0.5","sales":"256.11","tipout":"9","transfers":"1","cash":"14","due":"35","covers":"9","cut":"G","section":"1,2,8,9,10","notes":"lunch shift"},{"sid":"2","wage":"9","date":"Wed Jan 21, 2015","startTime":"4:30PM","endTime":"11:00PM","firstTable":"4:45PM","hours":"6.50","campHours":"0","sales":"964.76","tipout":"40","transfers":"2","cash":"100","due":"12","covers":"30","cut":"G","section":"28,35,36","notes":"dinner shift"},{"sid":"3","wage":"9","date":"Thu Jan 22, 2015","startTime":"6:00PM","endTime":"11:30PM","hours":"5.50","firstTable":"6:00PM","campHours":"0","sales":"1064.76","tipout":"44","transfers":"0","cash":"0","due":"112","covers":"29","cut":"C","section":"29,33,34","notes":"closing shift"}]');


	echo "<pre>"; print_r($array); echo '</pre>';
?>