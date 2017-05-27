<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>

	<h2>create database</h2>

	<?php 

		$dbms="mysql";
		$dbName ='db_database20';
		$user='root';
		$pwd='mymac8';
		$host='127.0.0.1:3306';
		$dsn="$dbms:host=$host;dbname=$dbName";

		try{

			$pdo = new PDO($dsn,$user,$pwd);
			echo "pdo connect success<br/>";

    		$sql_common = "create table if not exists a_common(" 
    				. "id smallint(4) unsigned NOT NULL AUTO_INCREMENT, "
    				. "highid int(4), "
                    . "middleid int(4), "
                    . "elementaryid int(4), "
                    . "smallid int(4), "
                    . "title varchar(100), "
                    . "href text, "
    				. "primary key(id)"
    				. ")ENGINE=InnoDB DEFAULT CHARSET=utf8";

    		$sql_elementarytype = "create table if not exists a_elementarytype(" 
    				. "id smallint(4) unsigned NOT NULL AUTO_INCREMENT, "
    				. "middleid int(4), "
    				. "EnglishName varchar(80), "
                    . "ChineseName varchar(80), "
    				. "primary key(id)"
    				. ")ENGINE=InnoDB DEFAULT CHARSET=utf8";

    		$sql_hightype = "create table if not exists a_hightype(" 
    				. "id smallint(4) unsigned NOT NULL AUTO_INCREMENT, "
                    . "EnglishName varchar(80), "
                    . "ChineseName varchar(80), "
    				. "primary key(id)"
    				. ")ENGINE=InnoDB DEFAULT CHARSET=utf8";

    		$sql_middletype = "create table if not exists a_middletype(" 
    				. "id smallint(4) unsigned NOT NULL AUTO_INCREMENT, "
                    . "highid int(4), "
                    . "EnglishName varchar(80), "
                    . "ChineseName varchar(80), "
    				. "primary key(id)"
    				. ")ENGINE=InnoDB DEFAULT CHARSET=utf8";

        $sql_smalltype = "create table if not exists a_smalltype(" 
            . "id smallint(4) unsigned NOT NULL AUTO_INCREMENT, "
                    . "smallid int(4), "
                    . "EnglishName varchar(80), "
                    . "ChineseName varchar(80), "
            . "primary key(id)"
            . ")ENGINE=InnoDB DEFAULT CHARSET=utf8";

			$sql_arr = array($sql_common, $sql_elementarytype, $sql_hightype, $sql_middletype, $sql_smalltype);

			foreach ($sql_arr as $sql) {
				echo "sql: " . $sql . "<br/>";

				$result = $pdo->prepare($sql);
				if($result->execute()){
					echo "create sql database success<br/>";
				}else{
					echo "create sql database failed<br/>";
				}

				echo "-----------------<br/>";
			}

		}catch(Exception $e){
			echo "error: " . $e.getMeesage();
		}


		// $dbhost = "127.0.0.1:3306";
  //   	$dbuser = "root";
  //   	$dbpsd = "mymac8";
  //   	$conn = mysql_connect($dbhost, $dbuser, $dbpsd);
  //   	if(!$conn){
  //   		die('count not connect ' . mysql_error());
  //   	}
  //   	echo "connect success<br/>";

  //   	mysql_select_db('db_database20');
  //   	echo "use database success" . "<br/>";

  //   	$sql = "create table think_user(" 
  //   		. "id int not null auto_increment, "
  //   		. "user varchar(100) not null, "
  //   		. "pass varchar(100) not null, "
  //   		. "address varchar(100), "
  //   		. "primary key(id)"
  //   		. ")";
		// $retval = mysql_query($sql, $conn);

		// if(!$retval){
		// 	die("create table failed" . mysql_error());
		// }

		// echo "create table success";
		// mysql_close($conn);
	?>
</body>
</html>