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
		$pwd='ubuntu912';
		$host='127.0.0.1:3306';
		$dsn="$dbms:host=$host;dbname=$dbName";

		try{

			$pdo = new PDO($dsn,$user,$pwd);
			echo "pdo connect success<br/>";

    		$sql_style = "create table if not exists a_style("
    				. "id smallint(4) unsigned NOT NULL AUTO_INCREMENT, "
    				. "show_name varchar(100) not null, "
                    . "modify_time int(4), "
                    . "show_order int(4), "
    				. "primary key(id)"
    				. ")ENGINE=InnoDB DEFAULT CHARSET=utf8";

            $sql_space = "create table if not exists a_space("
                . "id smallint(4) unsigned NOT NULL AUTO_INCREMENT, "
                . "show_name varchar(100) not null, "
                . "modify_time int(4), "
                . "show_order int(4), "
                . "primary key(id)"
                . ")ENGINE=InnoDB DEFAULT CHARSET=utf8";


//    		$sql_elementarytype = "create table if not exists a_elementarytype("
//    				. "id smallint(4) unsigned NOT NULL AUTO_INCREMENT, "
//    				. "middleid int(4), "
//    				. "EnglishName varchar(80), "
//                    . "ChineseName varchar(80), "
//    				. "primary key(id)"
//    				. ")ENGINE=InnoDB DEFAULT CHARSET=utf8";
//
//    		$sql_hightype = "create table if not exists a_hightype("
//    				. "id smallint(4) unsigned NOT NULL AUTO_INCREMENT, "
//                    . "EnglishName varchar(80), "
//                    . "ChineseName varchar(80), "
//    				. "primary key(id)"
//    				. ")ENGINE=InnoDB DEFAULT CHARSET=utf8";
//
//    		$sql_middletype = "create table if not exists a_middletype("
//    				. "id smallint(4) unsigned NOT NULL AUTO_INCREMENT, "
//                    . "highid int(4), "
//                    . "EnglishName varchar(80), "
//                    . "ChineseName varchar(80), "
//    				. "primary key(id)"
//    				. ")ENGINE=InnoDB DEFAULT CHARSET=utf8";
//
//        $sql_smalltype = "create table if not exists a_smalltype("
//            . "id smallint(4) unsigned NOT NULL AUTO_INCREMENT, "
//                    . "smallid int(4), "
//                    . "EnglishName varchar(80), "
//                    . "ChineseName varchar(80), "
//            . "primary key(id)"
//            . ")ENGINE=InnoDB DEFAULT CHARSET=utf8";
//
//        $sql_form = "create table if not exists a_Form("
//          . "id smallint(4) unsigned NOT NULL AUTO_INCREMENT, "
//          . "title varchar(255) NOT NULL,"
//          . "content varchar(255) NOT NULL,"
//          . "create_time int(11) unsigned, "
//            . "primary key(id)"
//            . ")ENGINE=InnoDB DEFAULT CHARSET=utf8";
//
//        $sql_hightype2 = "create table if not exists a_hightype2("
//            . "id smallint(4) unsigned NOT NULL AUTO_INCREMENT, "
//                    . "englishname varchar(80), "
//                    . "chinesename varchar(80), "
//            . "primary key(id)"
//            . ")ENGINE=InnoDB DEFAULT CHARSET=utf8";

			$sql_arr = array($sql_style, $sql_space);

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
