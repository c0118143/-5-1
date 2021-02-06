<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>

<body>

    <?php
    // DB接続設定
    $dsn = 'mysql:dbname=***;host=localhost';
    $user = '***';
    $password = '***';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //データベース内にテーブルを作成
    //SQL文中の「 IF NOT EXISTS 」は「もしまだこのテーブルが存在しないなら」という意味
    $sql = "CREATE TABLE IF NOT EXISTS tbtest"
        . " ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date TEXT,"
        . "pass TEXT"
        . ");";
    $stmt = $pdo->query($sql);
    
    //編集用データの初期値
    $editNumber = '';
    $editName = '';
    $editComment = '';
    $editPassword = '';

    //選択
    if (isset($_POST["edit"])) {  //編集フォームの送信ボタンが押されたら
        if (!empty($_POST["editNo"])) {  //編集フォームが空じゃなかったら → 編集選択機能実行
            //テーブルに登録されたデータを取得
            $sql = 'SELECT * FROM tbtest';  //テーブル名：tbtestからデータを抽出
            $stmt = $pdo->query($sql);  //sql文を実行して，データを取得
            $results = $stmt->fetchAll();  //fetchAllで結果を全て配列で取得
            //編集データをセット
            foreach ($results as $row) {  //foreach：配列の反復処理
                if ($row['id'] == $_POST["editNo"]) {  //もしidが一致したら
                    if ($row['pass'] == $_POST["e_pass"]) {  //もしpassが一致したら
                        //$rowの中にはテーブルのカラム名が入る
                        $editNumber = $row['id'];
                        $editName = $row['name'];
                        $editComment = $row['comment'];
                        $editPassword = $row['pass'];
                    }
                }
            }
        }
    }

//投稿
    if (isset($_POST["submit"])) {  //投稿フォームの送信ボタンが押されたら
        if (empty($_POST["select_num"])) {  //編集フォームが空だったら → 新規投稿実行
            if (empty($_POST["name"])) {  //投稿フォームの名前が空だったら
                echo "名前を入力してください.<br>";
            } else if (empty($_POST["comment"])) {  //投稿フォームのコメントが空だったら
                echo "コメントを入力してください.<br>";
            } else if (empty($_POST["pass"])) {  //投稿フォームのパスワードが空だったら
                echo "パスワードを入力してください.<br>";
            } else {  //投稿フォームに入力があれば，以下の処理を行う
                //データを入力
                //prepare:それぞれのテーブル名にパラメータを与える
                //INSERT：データベースに挿入
                $sql = $pdo->prepare("INSERT INTO tbtest (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
                //パラメータを指定し，変数を代入する
                $sql->bindParam(':name', $name, PDO::PARAM_STR);
                $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql->bindParam(':date', $date, PDO::PARAM_STR);
                $sql->bindParam(':pass', $pass, PDO::PARAM_STR);
                $name = $_POST["name"];
                $comment = $_POST["comment"];
                $date = date("Y/m/d H:i:s");
                $pass = $_POST["pass"];
                $sql->execute();  //SQL文を実行
            }
        //編集
        } else {  //編集フォームが空じゃなかったら → 編集機能実行
            if (empty($_POST["name"])) {
                echo "名前を入力してください.<br>";
            } else if (empty($_POST["comment"])) {
                echo "コメントを入力してください.<br>";
            } else if (empty($_POST["pass"])) {
                echo "パスワードを入力してください.<br>";
            } else {
                $id = $_POST["select_num"]; //変更する投稿番号
                $name = $_POST["name"]; //変更する名前
                $comment = $_POST["comment"]; //変更するコメント
                $date = date("Y/m/d H:i:s"); //変更する日付
                $pass = $_POST["pass"]; //変更するパスワード
                //UPDATE：データを更新
                $sql = 'UPDATE tbtest SET name=:name,comment=:comment, date=:date , pass=:pass WHERE id=:id';
                $stmt = $pdo->prepare($sql);  //SQL文を準備
                //パラメータを指定し，変数を代入する
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();  //SQL文を実行
            }
        }
    }

    //削除
    if (isset($_POST["delete"])) {  //削除フォームの送信ボタンが押されたら
        if (empty($_POST["deleteNo"]) || empty($_POST["del_pass"])) {  //削除フォームが空だったら
            echo "入力が足りません.<br>";
        } else {  //削除フォームが空じゃなかったら，以下の処理を実行
            $id = $_POST["deleteNo"];
            $del_pass = $_POST["del_pass"];
            //delete：データを削除
            $sql = 'delete from tbtest where id=:id and pass=:pass';
            $stmt = $pdo->prepare($sql);  //SQL文を準備
            //パラメータを指定し，変数を代入する
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':pass',$del_pass,PDO::PARAM_STR);
            $stmt->execute();  //SQL文を実行
        }
    }

    ?>

    <form action="" method="post">
        <p>投稿<br>
            <input type="hidden" name="select_num" value="<?php echo $editNumber; ?>">
            <input type="text" name="name" value="<?php echo $editName; ?>" placeholder="名前を入力"><br>
            <input type="text" name="comment" value="<?php echo $editComment; ?>" placeholder="コメントを入力"><br>
            <input type="text" name="pass" value="<?php echo $editPassword; ?>" placeholder="パスワードを入力"><br>
            <input type="submit" name="submit">
        <p>削除<br>
            <input type="number" name="deleteNo" placeholder="削除したい番号"><br>
            <input type="text" name="del_pass" placeholder="パスワードを入力"><br>
            <input type="submit" name="delete" value="削除">
        <p>編集<br>
            <input type="number" name="editNo" placeholder="編集したい番号"><br>
            <input type="text" name="e_pass" placeholder="パスワードを入力"><br>
            <input type="submit" name="edit" value="送信">
            <hr>
    </form>

    <?php
    //表示機能
    //入力したデータレコードを抽出し、表示する
    $sql = 'SELECT * FROM tbtest';  //SELECT：データベースからデータを取得
    $stmt = $pdo->query($sql);  //sql文を実行して，データを取得
    $results = $stmt->fetchAll();  //fetchAllで結果を全て配列で取得
    foreach ($results as $row) {
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'] . ' ';
        echo $row['name'] . ' ';
        echo $row['comment'] . ' ';
        echo $row['date'] . '<br>';
    }
    ?>

</body>

</html>