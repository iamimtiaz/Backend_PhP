<?php
function time2seconds($time)
	{
		list($hours, $mins, $secs) = explode(':', $time);
		return ($hours * 3600 ) + ($mins * 60 ) + $secs;
	};
//Function That calculate next_day First Time	
function lala($next_g)
		{
			$next = mysql_query("Select * from re_prayertime where ordering = '$next_g' AND time > 0 order by time ASC LIMIT 1");			
			while($row = mysql_fetch_array($next))
			{	//Array fetches each row
			//$g = $row['ordering'] ; //. " " .$row["ordering"]."<br/>";		// We dont need to show id which is row[0]
			$nextDay_start = time2seconds($row['time']) ."<br/>";
			}
			return $nextDay_start;
		};
		
function ulala ($curday)
{
	if($curday == 'Monday') return 1;
	else if($curday == 'Tuesday') return 2;
	else if($curday == 'Wednesday') return 3;
	else if($curday == 'Thursday') return 4;
	else if($curday == 'Friday') return 5;
	else if($curday == 'Saturday') return 6;
	else if($curday == 'Sunday') return 7;
	
};

if (isset($_POST['tag']) && $_POST['tag'] != '')
{
	
	$tag = $_POST['tag'];
	
	require_once 'DBFunctions.php';
	$db = new DBFunctions();
	
	$response = array("tag" => $tag, "error" => FALSE);
	
	if($tag == 'login'){
		
		$email = $_POST['email'];
		$password = $_POST['password'];
		
		$user = $db->getUserByEmailAndPassword($email, $password);
		if($user != false){
			$response["error"] = FALSE;
			$response["uid"] = $user["unique_id"];
			$response["user"]["name"] = $user["name"];
			$response["user"]["email"] = $user["email"];
			$response["user"]["created_at"] = $user["created_at"];
			$response["user"]["updated_at"] = $user["updated_at"];
			echo json_encode($response);
		}else{
			$response["error"] = TRUE;
			$response["error_msg"] = "Incorrect email or password";
			echo json_encode($response);
		}
	}


	else if($tag == 'gettime_a'){
		
		$email = $_POST['emni'];
		
	date_default_timezone_set('UTC');
	$t = date('H:i:s');		//Server Day & Time
	//echo $t ."<br/>";
	$current_day = date('l');  //Server Day
	//echo $current_day ."<br/>";
	
	//Converting Server time to Second
	$h = (int) date('H');
	$m = (int) date('i');
	$s = (int) date('s');

	$current_total = $h*3600 + $m*60 + $s ; //Total Second of Server Time 
	//echo "Current time in second: ".$current_total ."<br/>";
	
	//#Fetch time from database
	$result = mysql_query("Select * from re_prayertime where week_days = '$current_day' AND time_to_sec(time) > '$current_total' order by time ASC LIMIT 2"); //result contains all rows 
	if(!$result)
	{
		die("Database Connection failed: " . mysql_error());
	}
	//count Number of fetched Row
	$num_rows = mysql_num_rows($result);

	//Variables 
	
	$g=0;
	$next_g=0; //next ordering variable
	$nextDay_next=0 ; //next day next time variable 
	$diff_next = 0;
	$end = 0;
	$day=0;
	$gudum = 0; 
	
	while($row = mysql_fetch_array($result)){	//Array fetches each row
			
			$g = $row['ordering'] ;;		// We dont need to show id which is row[0]
			
			//Jodi Next time duita available thake tahole eita immediate NEXT time
			$end = $row['time'];
			//echo $end ."<br/>" ;
			break;			
		}
	  /*  ##Code Change Starts Here## */
	   
		
		//For next Day first time
		if( $num_rows == 2 )
		  {
			while($row = mysql_fetch_array($result))
			{	
				$g = $row['ordering'] ;			
				//Jodi Next time duita available thake tahole eita  NEXT-to-NEXT time
				$nextDay_start = time2seconds($row['time']) ."<br/>";		
			}
			//echo $nextDay_start ."<br/>" ;	
			
			
		  } 
		else //( $num_rows == 1  )
		{
			if( $num_rows == 1 )
			{
				$next_g = $g+1 ;
				if($next_g == 8)
				{
				
				//IF ordering = 8 then it should be seset to 1
					$next_g = 1;
					$nextDay_start= lala($next_g);
				}
				else
				{
					//var_dump($next_g);
					//echo "else";
						$nextDay_start= lala($next_g);
				}
			}
			else if ( $num_rows == 0 )
			{
				$gudum = ulala ($current_day);	// Today Order
				
				$next_g = $gudum + 1; 
				//var_dump($next_g);
				
				$nextDay_start= lala($next_g);
				//var_dump($nextDay_next);
				//echo "<br>Test: ".$nextDay_start;
			}
		  }
		  
		 /* ##Here end = 1st time 
			##		nextDay_start = 2nd time (if both time available on current day)
			##		nextDay_start = Next day time (if only one time available on current day) */
		 
		 
		 //Convert Fetched time to Second
		$to = time2seconds($end);
		

		//Next Day First-Time|OR|Next-to-Next time Difference with Current Time
		if( $num_rows == 1 || $num_rows == 0 )
		{
			$diff_next = ( 86400 - $current_total ) + $nextDay_start;
		}
		else if( $num_rows == 2 )
		{
			$diff_next = abs( $nextDay_start - $current_total );
		}
		
		
		
		
		/*  ##Code Change Done Here## */
		
		
		
		if( $num_rows == 0 )
		{
			$diff = $diff_next
		}
		else 		
		{
			$diff = abs($to - $current_total);
		}

		// ^Rayan Part Done

		
		
		$low=0;


		if($diff=low){
			$response["error"] = TRUE;
			$response["need"] = $low;
			//$response["need_alarm"] = $low;
			$response["error_msg"] = "No Prayer Time AVAILABLE ";
			echo json_encode($response);


		}
		else{
			$response["error"] = FALSE;
			$response["need"] = $diff;
			//$response["need_alarm"] = $diff_next;
			echo json_encode($response);
		}
	}
	
		
else if($tag == 'gettime_b'){
		
		$email = $_POST['emni'];
		
	date_default_timezone_set('UTC');
	$t = date('H:i:s');		//Server Day & Time
	//echo $t ."<br/>";
	$current_day = date('l');  //Server Day
	//echo $current_day ."<br/>";
	
	//Converting Server time to Second
	$h = (int) date('H');
	$m = (int) date('i');
	$s = (int) date('s');
	//var_dump($h);
	//var_dump($m);
	//var_dump($s);
	$current_total = $h*3600 + $m*60 + $s ; //Total Second of Server Time 
	//echo "Current time in second: ".$current_total ."<br/>";
	
	//#Fetch time from database
	$result = mysql_query("Select * from re_prayertime where week_days = '$current_day' AND time_to_sec(time) > '$current_total' order by time ASC LIMIT 2"); //result contains all rows 
		if(!$result)
	{
		die("Database Connection failed: " . mysql_error());
	}
	//count Number of fetched Row
	$num_rows = mysql_num_rows($result);

	// Variable 
	$g=0;
	$next_g=0; //next ordering variable
	$nextDay_next=0 ; //next day next time variable 
	$diff_next = 0;
	$end = 0;
	$day=0;
	$gudum = 0;
	
	while($row = mysql_fetch_array($result)){	//Array fetches each row
			
			$g = $row['ordering'] ;;		// We dont need to show id which is row[0]
			
			//Jodi Next time duita available thake tahole eita immediate NEXT time
			$end = $row['time'];
			//echo $end ."<br/>" ;
			break;			
		}
	  /*  ##Code Change Starts Here## */
	   
		
		//For next Day first time
	     if( $num_rows == 2 )
		  {
			while($row = mysql_fetch_array($result))
			{	
				$g = $row['ordering'] ;			
				//Jodi Next time duita available thake tahole eita  NEXT-to-NEXT time
				$nextDay_start = time2seconds($row['time']) ."<br/>";		
			}
			//echo $nextDay_start ."<br/>" ;	
			
			
		  } 
		else 
		{
			if( $num_rows == 1 )
			{
				$next_g = $g+1 ;
				var_dump($next_g);
				if($next_g == 8)
				{
				//echo "#eita";
				//IF ordering = 8 then it should be seset to 1
					$next_g = 1;
					$nextDay_start= lala($next_g);
				}
				else
				{
					//var_dump($next_g);
					//echo "else";
						$nextDay_start= lala($next_g);
				}
			}
			else if ( $num_rows == 0 )
			{
				$gudum = ulala ($current_day);	// Today Order
				
				$next_g = $gudum + 1; 
				var_dump($next_g);
				
				$nextDay_start= lala($next_g);
				//var_dump($nextDay_next);
				//echo "<br>Test: ".$nextDay_start;
			}
		  }
		  
		 /* ##Here end = 1st time 
			##		nextDay_start = 2nd time (if both time available on current day)
			##		nextDay_start = Next day time (if only one time available on current day) */
		 
		 //Convert Fetched time to Second
		$to = time2seconds($end);
		 
		//Next Day First-Time|OR|Next-to-Next time Difference with Current Time
		if( $num_rows == 1 || $num_rows == 0 )
		{
			//echo "inside";
			$diff_next = ( 86400 - $current_total ) + $nextDay_start;
		}
		else if( $num_rows == 2 )
		{
			$diff_next = abs( $nextDay_start - $current_total );
		}
				
		/*  ##Code Change Done Here## */
		

		
		//echo "Next time: ".$to."<br>";
		if( $num_rows == 0 )
		{
			$diff = $diff_next;
			//echo "imtiaz";
		}
		else 		
		{
			$diff = abs($to - $current_total);
		}


		
		
		$low=0;


		if($diff=$low){
			$response["error"] = TRUE;
			//$response["need"] = $low;
			$response["need_alarm"] = $low;
			$response["error_msg"] = "No Prayer Time AVAILABLE ";
			echo json_encode($response);


		}
		else{
			$response["error"] = FALSE;
			//$response["need"] = $diff;
			$response["need_alarm"] = $diff_next;
			echo json_encode($response);
		}
	}
	
	

			else if($tag == 'gmailregister'){
		
		$name = $_POST['name'];
		$email = $_POST['email'];
		$regId=$_POST['regId'];
		
		
		if($db->isUserExisted($email)){
			$response["error"] = TRUE;
			$response["error_msg"] = "User already exist";
			echo json_encode($response);
		}else{
			
			$user = $db->gmailUser($name, $email,$regId);
			if($user){
				$response["error"] = FALSE;
				$response["uid"] = $user["unique_id"];
				$response["user"]["name"] = $user["name"];
				$response["user"]["email"] = $user["email"];
				$response["user"]["created_at"] = $user["created_at"];
				$response["user"]["updated_at"] = $user["updated_at"];
				echo json_encode($response);
			}
			else{
				$response["error"] = TRUE;
				$response["error_msg"] = " occured in Registration";
				echo json_encode($response);
			}
		}
	}






	else if($tag == 'register'){
		
		$name = $_POST['name'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$regId = $_POST['regId'];
		
		if($db->isUserExisted($email)){
			$response["error"] = TRUE;
			$response["error_msg"] = "User already exist";
			echo json_encode($response);
		}else{
			
			$user = $db->storeUser($name, $email, $password,$regId);
			if($user){
				$response["error"] = FALSE;
				$response["uid"] = $user["unique_id"];
				$response["user"]["name"] = $user["name"];
				$response["user"]["email"] = $user["email"];
				$response["user"]["created_at"] = $user["created_at"];
				$response["user"]["updated_at"] = $user["updated_at"];
				echo json_encode($response);
			}
			else{
				$response["error"] = TRUE;
				$response["error_msg"] = "   Registration happened";
				echo json_encode($response);
			}
		}
	}else{
		$response["error"] = TRUE;
		$response["error_msg"] = "Unknown 'tag' value. It should be either login or register";
		echo json_encode($response);
	}
	
	
}  else{
	$response["error"] = TRUE;
	$response["error_msg"] = "Required parameter 'tag' is missing";
	
	echo json_encode($response);
}

?>