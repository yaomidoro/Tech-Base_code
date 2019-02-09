<!DOCTYPE html>
<html>
	<head>
		<title>mission_4</title>
	</head>
	<body>	
		<?php
			
			/*データベースへの接続*/
			$dsn='データベース名';
			$user='ユーザー名';
			$password='パスワード';
			$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
			/*テーブルの作成*/
			$sql = "CREATE TABLE IF NOT EXISTS tbtest5"
			."("
			."id INT AUTO_INCREMENT NOT NULL PRIMARY KEY," /*投稿番号を自動で連番で取得*/
			."name char(32),"
			."comment TEXT,"
			."date TEXT,"
			."pass TEXT"
			.");";
			$stmt = $pdo->query($sql);
			
			/*フォームから値を受け取る*/
			$name = htmlspecialchars($_POST['name']); /*htmispecialcharsはクロスサイトスクリプティング対策*/
			$comment = htmlspecialchars($_POST['comment']); 
			$pass_t = htmlspecialchars($_POST['pass_t']); 
			$delete = htmlspecialchars($_POST['delete']);
			$pass_d = htmlspecialchars($_POST['pass_d']);  
			$edit = htmlspecialchars($_POST['edit']); 
			$pass_e = htmlspecialchars($_POST['pass_e']); 
			$edit_check = htmlspecialchars($_POST['edit_check']);
			$date=date("Y/m/d/H:i");
			$check=0;  /*後のif文用*/
			$edit_name;
			if (isset($_POST['enter'])) {
	    			$kbn = htmlspecialchars($_POST['enter']);
    				switch ($kbn) {
       					case "送信":  
					if($name==null){
						echo "名前が入力されていません<br>";
						break;
					}
					if($comment==null){
						echo "コメントが入力されていません<br>";
						break;
					}
					if($pass_t==null){
						echo "パスワードが入力されていません<br>";
						break;
					}
					else if($edit_check==null){ /*編集モードではなく送信ボタンが押された場合の処理*/
						$sql = $pdo -> prepare("INSERT INTO tbtest5 (name,comment,date,pass) VALUES (:name,:comment,:date,:pass)");
						$sql -> bindParam(':name',$name,PDO::PARAM_STR);
						$sql -> bindParam(':comment',$comment,PDO::PARAM_STR);
						$sql -> bindParam(':date',$date,PDO::PARAM_STR);
						$sql -> bindParam(':pass',$pass_t,PDO::PARAM_STR);
						$sql -> execute();
						break;
					}
					else{
						/*編集モードで送信ボタンが押されたときの処理*/
						$id = $edit_check;
						/*送信ボタンを押す段階では、編集用パスワードとパスワードが一致している状態なので、条件に含める必要はない*/
						$sql = 'update tbtest5 set name=:name,comment=:comment,date=:date,pass=:pass where id=:id';
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':name',$name,PDO::PARAM_STR);
						$stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
						$stmt->bindParam(':date',$date,PDO::PARAM_STR);
						$stmt->bindParam(':pass',$pass_t,PDO::PARAM_STR);
						$stmt->bindParam(':id',$id,PDO::PARAM_INT);
						$stmt->execute();
					}
					break;
					
					case "削除":
 
					$id = $delete;
					$pass = $pass_d;
					$sql = 'SELECT*FROM tbtest5 ORDER BY id';
					$stmt = $pdo->query($sql);
					$results = $stmt->fetchALL();
					foreach ($results as $row){
						/*条件に一致するものがあるかの確認*/
						if($row['id']==$delete && $row['pass']==$pass_d){
							$check+=1;
						} 
					}
					
					if($delete==null){
						echo "削除対象番号が入力されていません<br>";
						break;
					}
					if($pass_d==null){
						echo "パスワードが入力されていません<br>";
						break;
					}
					if($check==0){
						echo "削除条件に当てはまる投稿はありませんでした<br>パスワードの入力間違いに気を付けてください";
					}
					else{
						
						$sql = 'delete from tbtest5 where id=:id AND pass=:pass';
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':id',$id,PDO::PARAM_INT);
						$stmt->bindParam(':pass',$pass,PDO::PARAM_STR);
						$stmt->execute();
					}
					break;

					case "編集":  
					if($edit==null){
						echo "編集対象番号が入力されていません<br>";
						break;
					}
					if($pass_e==null){
						echo "パスワードが入力されていません<br>";
						break;
					}
					
					else{
						$sql = 'SELECT*FROM tbtest5 ORDER BY id';
						$stmt = $pdo->query($sql);
						$results = $stmt->fetchALL();
						foreach ($results as $row){
							/*条件に一致するものがあるかの確認*/
							if($row['id']==$edit && $row['pass']==$pass_e){
								$edit_name=$row['name'];
								$edit_comment=$row['comment'];
								$edit_pass_t=$row['pass'];
							} 
						}
						if($edit_name==null){
							echo "編集条件に当てはまる投稿はありませんでした<br>パスワードの入力間違いに気を付けてください";
						}
					}
					break;
					default: echo "エラー"; exit;
				}
			}
		?>
		<form action="mission_4.php" method="post">
			"名前と簡単なコメント、自身の投稿の編集、削除用のパスワードを入力してください！"<br /><br />
			<input type="text" name="name" placeholder = "名前" value = "<?php echo $edit_name; ?>"><br />
			<input type="text" name="comment" placeholder = "コメント" value = "<?php echo $edit_comment; ?>" ><br />
			<input type="text" name="pass_t" placeholder = "パスワード(設定)" value = "<?php echo $edit_pass_t; ?>" ><br />
			<input type="submit"value="送信" name = "enter" >
			<br /><br />
			<input type="text" name="delete" placeholder = "削除対象番号"　><br />
			<input type="text" name="pass_d" placeholder = "パスワード(認証)" ><br />
			<input type="submit"value="削除" name = "enter"><br />
			<br /><br />
			<input type="text" name="edit" placeholder = "編集対象番号" ><br />
			<input type="text" name="pass_e" placeholder = "パスワード(認証)" ><br />
			<input type="submit"value="編集" name = "enter"><br />
			<input type="hidden" name="edit_check" value = "<?php echo $edit; ?>" ><br />
		</form>
		<?php
			/*ブラウザ表示*/
			$sql = 'SELECT*FROM tbtest5 ORDER BY id';
			$stmt = $pdo->query($sql);
			$results = $stmt->fetchALL();
			foreach ($results as $row){
				/*rowの中にはテーブルのカラム名が入る*/
				echo $row['id'].',';
				echo $row['name'].',';
				echo $row['comment'].',';
				echo $row['date'].'<br>';
			}
		?>
	</body>
</html