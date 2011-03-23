<?php
// 
//	phpMyPump
//	ver 1.0
//
//  phpmypump.php
//  
//  Created by Intuito Lab. on 2010-10-07.
//  Copyright 2010 Intuito Lab. All rights reserved.
//
//	This program is free software: you can redistribute it and/or modify
//	it under the terms of the GNU General Public License as published by
//	the Free Software Foundation, either version 3 of the License, or
//	(at your option) any later version.
// 
//	This program is distributed in the hope that it will be useful,
//	but WITHOUT ANY WARRANTY; without even the implied warranty of
//	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//	GNU General Public License for more details.
// 
//	You should have received a copy of the GNU General Public License
//	along with this program.  If not, see <http://www.gnu.org/licenses/>.
// 
// 

// set database host here
$dbhost = "localhost";

// mysql user name
$dbuser = "root";

// mysql password
$dbpass = "root";

// and then the name of your database
$database = "intuito_filecast";

//
//
// Do not touch the code below if you don't know what you are doing!
//
//
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title>phpMyPump</title>
	<style type="text/css" media="screen">
		* {
			font-family: Arial, Verdana, sans-serif;
		}
		
		th {
			background: #dadada;
		}
		
		a {
			text-decoration: none;
			color: blue;
		}
		
		a:hover {
			text-decoration: underline;
			color: red;
		}
		
		table tr td select {
			width: 100px;
			height: 15px;
		}
		
		body {
			background: #dadada;
		}
		
		table {
			width: 100%;
			border-bottom: 1px solid silver;
			padding-bottom: 10px;
		}
		
		table tr td input[type=text] {
			width: 100%;
		}
		
		#wrapper {
			margin: 0 auto;
			border: 2px solid silver;
			width: 70%;
			background: white;
		}
	
		#header {
			background: #333;
			color: white;
			line-height: 10px;
			padding: 10px;
		}
		
		#choosetable {
			padding: 10px;			
			border-bottom: 1px solid silver;
			height: 30px;
			line-height: 30px;
		}
		
		#pump {
			padding: 10px;			
			border-bottom: 1px solid silver;
		}
		
		#process {
			padding: 10px;			
		}
		
		#footer {
			width: 70%;
			text-align: center;
			margin: 0 auto;
			font-size: 11px;
			padding-top: 10px;
			color: #888888;
		}
		
		.ko {
		 	background-color: #ffe0de;
		 	border: 1px solid red;
		 	padding: 10px;
		 	clear:both;
			color: red;
		}

		.ok {
			color: green;
		 	background-color: #d2ffd2;
		 	border: 1px solid #6c9423;
		 	padding: 10px;
		 	clear:both;
		}
		
		.r0 {
			background: white;
		}
		
		.r1 {
			background: #dadada;
		}
		
		.c {
			text-align: center;
		}
	</style>
</head>
<body>
	
	<?php
		// connecting to the database
		$dbc = mysql_connect($dbhost, $dbuser, $dbpass)or die(mysql_error());
		// selecting the right db
		mysql_select_db($database)or die(mysql_error());
		
		// fetching all tables
		$rs = mysql_query("SHOW TABLES");

	?>
	
	<div id="wrapper">
		<div id="header">
			<h1>phpMyPump</h1>
		</div>
		<div id="body">
			<div id="choosetable">
				<form action="phpmypump.php" method="post">	
					<select name="table" id="table" onchange="if(this.value) { submit(); }">
						<option value="">-- Select a table --</option>
						<?php while($tables = mysql_fetch_array($rs)): ?>
							<?php $selected = ($tables[0] == $_POST['table']) ? "selected=\"selected\"" : ""; ?>
							<option value="<?php echo $tables[0] ?>" <?php echo $selected?> ><?php echo $tables[0]?></option>
						<?php endwhile; ?>
					</select>
					<input type="hidden" name="choose" value="choose" />
				</form>
			</div>
		
			<?php 
			if($_POST['choose']) 
			{	 
				$rsfields = mysql_query("DESCRIBE {$_POST['table']}");
			?>
				<div id="pump">
				
					<h3>Config pump</h3>
					
					<table cellspacing="5" cellpadding="5">
						<tr>
							<th>Field</th>
							<th>Type</th>
							<th>Pump with</th>
							<th>From</th>
							<th>To</th>
							<th>Set</th>
						</tr>

					<form action="phpmypump.php" method="post">
						
						<input type="hidden" name="table" value="<?php echo $_POST['table']?>" />
						
						<?php $r = 0; ?>
						
						<?php while($fields = mysql_fetch_assoc($rsfields)): ?>
							<tr class="r<?php echo $r?>">
								<td><?php echo $fields['Field']?></td>
								<td><?php echo $fields['Type']?></td>
								<td class="c">
									<select name="<?php echo $fields['Field']?>_pump">
										<option value="ignore">Ignore</option>
										<option value="characters">Characters</option>
										<option value="int">Numeric int</option>
										<option value="currency">Currency</option>
									</select>
								</td>
								<td><input type="text" name="<?php echo $fields['Field']?>_from" value="" id="from" /></td>
								<td><input type="text" name="<?php echo $fields['Field']?>_to" value="" id="to" /></td>
								<td><input type="text" name="<?php echo $fields['Field']?>_set" value="" id="set" /></td>
							</tr>
						<?php $r = 1 - $r; ?>
						
						<?php endwhile; ?>
						
					</table>

					<div class="howmany">
						<p>How many rows?</p>
						<input type="text" name="rows" value="100" id="rows" />
						<input type="submit" name="go" value="Process" id="go" />
					</div>
						
				</form>
					
				</div>
			<? } ?>
	
			<?php 
				if($_POST['go']) 
				{
					// getting table fields
					$rs = mysql_query("DESCRIBE {$_POST['table']}");
			?>
				<div id="process">
					<h1>Processing</h1>
	
					<?php
						
						// how many rows
						$q = $_POST['rows'];
						
						// running through fields
						while($fields = mysql_fetch_assoc($rs)) 
						{

							// field name
							$fname = $fields['Field'];
			
							// this field must be added to the insert query
							if($_POST["{$fname}_pump"] != "ignore")
							{
								$valid[$fname] = $fname;
							}

						}
		
						// starting processing...
						for($i=0; $i<=$q; $i++)
						{
							// glueing field names
							$campi = implode(',', $valid);
							
							// empty values for now
							$values = array();
							
							// cycling through the valid fields
							foreach($valid as $f)
							{
								// switching upon the passed combo
								switch ($_POST["{$f}_pump"])
								{
									
									// pumping random characters
									case 'characters':
										if($_POST["{$f}_set"]) {
											$values[] = rndSet($_POST["{$f}_set"]);
										} else {
											$values[] = rndCharacters($_POST["{$f}_from"], $_POST["{$f}_to"]);
										}
						
									break;
					
									// pumping integers
									case 'int':
										if($_POST["{$f}_set"]) {
											$values[] = rndSet($_POST["{$f}_set"]);
										} else {
											$values[] = rndNumbers($_POST["{$f}_from"], $_POST["{$f}_to"]);
										}
									break;
					
									// pumping decimals
									case 'currency':
										if($_POST["{$f}_set"]) {
											$values[] = rndSet($_POST["{$f}_set"]);
										} else {
											$values[] = rndCurrency($_POST["{$f}_from"], $_POST["{$f}_to"]);
										}
									break;
					
								}
				
							}

							// glueing values
							$values = implode(', ', $values);
							
							// executing the single INSERT query
							$sql = mysql_query("INSERT INTO {$_POST['table']} ({$campi}) VALUES ({$values})");
						}
				
						// if some mysql errors happen
						if(mysql_error()) {
							echo "<div class='ko'>Errore MySQL: ".mysql_error()."</div>";			
						} else {
							echo "<div class='ok'>{$q} righe inserite con successo!</div>";
						}

					?>
	
				<?php } ?>
			
			</div>
			
		</div>
		
	</div><!-- fine wrapper-->
	
	<div id="footer">
		- &copy; 2010 <a href="http://www.intuitolab.com" target="_blank">Intuito Lab.</a> -
	</div>
	
</body>
</html>

<?php

	//
	// generating random sentences using only alpha
	//
	function rndCharacters($from, $to)
	{
		// decido a caso la lunghezza della stringa
		$length = mt_rand($from, $to);
		
		// preparo i caratteri da usare
		$chars = "abcd efghi jklmnopqrstuvw xyzABCD EFGHIJKLMNOPQRS TUVWX YZ";
		$chars_length = (strlen($chars) - 1);
		
		// inizializzo la stringa con il primo char
		$string = $chars{mt_rand(0, $chars_length)};
		
		// genero la stringa random
		for ($i = 1; $i < $length; $i++)
		{
			$string .= $chars{mt_rand(0, $chars_length)};
		}
		
		// ritorno la stringa
		return "'".$string."'";
		
	}


	//
	//	generating random integer numbers
	//
	function rndNumbers($from, $to)
	{
		// decido a caso la lunghezza della stringa
		$random = mt_rand($from, $to);	
		return $random;
	}
	
	//
	//	generating random decimal values
	//
	function rndCurrency($from, $to)
	{
		$random = mt_rand($from, $to);
		$rand = mt_rand(0, 99);
		$float = $random.".".$rand;
		return $float;
	}
	
	//
	//	randomly selecting a value into a passed set
	//
	function rndSet($set)
	{
		$random = explode(",", $set);
		$select = mt_rand(0, count($random) - 1);		
		$string = stripslashes($random[$select]);
		return $string;
	
	}

?>