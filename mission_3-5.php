<!DOCTYPE html>
<html lang="ja"><html>
<head>
  <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes"><!-- for smartphone. ここは一旦、いじらなくてOKです。 -->
 <meta charset="UTF-8">
 <title>mission 3-5</title>
</head>

<body>
    
<?php
    $filename = "mission_3-5.txt";

    $old_submission = array("", "", 0, "");           
    if(empty($_POST["edit_num"]) == FALSE){
        $old_submission = getOldComment($filename, $_POST["edit_num"]);
    }
   
?>


<hr>
新規フォーム
<form action="mission_3-5.php" method="post">
    <input type="hidden" name="new_edit_num" value = "<?php if(empty($_POST["edit_num"]) == FALSE && $old_submission[2] == 1){echo$_POST["edit_num"];}?>"><br>
    パスワード:<br>
    <input type="text" name = "password" size = "50" value = "<?php {echo $old_submission[3];}?>"><br>
 名前:<br>
 <input type="text" name="name" size="50" value = "<?php echo $old_submission[0];?>"><br>
 コメント:<br>
    <textarea name="comment" cols="50" rows="5"><?php if($old_submission[2] == 1){echo $old_submission[1];}?></textarea><br>
 <input type="submit" value="送信" onclick="document.charset='UTF-8';">
</form>
<hr>
<form action="mission_3-5.php" method="post">
    削除番号:<br>
    <input type="text" name="delete_num" size="3"><br>
    パスワード:<br>
    <input type="text" name = "password_delete" size = "50"><br>
    <input type="submit" value="削除">
</form>
<hr>
<form action="mission_3-5.php" method="post">
    編集番号:<br>
    <input type="text" name="edit_num" size="3"><br>
    パスワード:<br>
    <input type="text" name = "password_edit" size = "50"><br>
 <input type="submit" value="編集" onclick="document.charset='UTF-8';">
</form>

<hr>
<?php 
    $filename = "mission_3-5.txt";
        
    //実行モードの制御
    if(empty($_POST["new_edit_num"]) == FALSE){
        editComments($filename, $_POST["new_edit_num"], $_POST["name"] ,$_POST["comment"]);
    }elseif(empty($_POST["comment"]) == FALSE){
        submission($filename);
    }elseif(empty($_POST["delete_num"]) == FALSE){
        deleteComments($filename, $_POST["delete_num"]);        
    }

    echo "<br>～投稿一覧～<br>";
    viewComments($filename);
    
    //コメントを表示する関数
    function viewComments($file){
        $content = file_get_contents($file);   
        $arr = explode("\r\n", $content);
        foreach($arr as $comment_raw){               
            if(empty($comment_raw) == FALSE){
               
                foreach($comment as $elements){                 
                    echo $elements;
                }
            }       
            
            
        }
    }
    
    //投稿を受け付ける関数
        function submission($file){
        $date = date("Y/m/d H:i:s");    //日付取得

        $fp = fopen($file, "a");
        $content = file_get_contents($file);
        $arr=explode("\r\n", $content); //改行をデリミタとして配列に格納
        $count = count($arr) + 1;           //行数取得
        
 //投稿番号
    if(file_exists($file)){
        $line=file($file,FILE_IGNORE_NEW_LINES);
        $lastline=end($line);
        $last_element=explode("<>",$lastline);
        $lastnum=$last_element[0];
        $count1=$lastnum +1;
    }
    else{
        
    $count1=1;
    
    }
        fwrite($fp, $count1."<>".$_POST["name"]."<>".($_POST["comment"])."<>".$date."<br>\r\n");
        echo $_POST["name"]."さんのコメント".$_POST["comment"]."を受け付けました。<br>";        
        fclose($fp);
    }
   
    //投稿を削除する関数
    
    function deleteComments($file, $num){
        $content = file_get_contents($file);    //読み込み用のファイルポインタ
        $arr = explode("\r\n", $content);       //１行ずつ配列に格納
        $output_fp = fopen($file, "w");         //書き込み用のファイルポインタ
        $count = count($arr);                   //ファイルの行数
        $i = 1;                                 //更新後の投稿番号の初期化
        
       // echo "mission3-5.txt内部<br>".$content;
        foreach($arr as $comment_raw){               //1行ずつ順番に見ていく
            if(empty($comment_raw) == FALSE){        //空の行は見ない
                $comment = explode("<>", $comment_raw);  //<>で分割して配列に格納(0...投稿番号, 1...名前, 2...コメント, 3...日付, 4...パスワード)

                if($comment[0] != $num || $_POST["password_delete"] != $comment[4]){                    //投稿番号を削除番号と比較
                    //echo "comment[4]:".$comment[4]."<br>";
                    fwrite($output_fp, $i."<>".$comment[1]."<>".$comment[2]."<>".$comment[3]."<>".$comment[4]."<>"."<br>\r\n");   //新しい投稿番号をつけてファイルに書き込み
                    $i++;                           //投稿番号の更新
                }
            }
        }
        fclose($output_fp);
    }

    //編集する際フォームに編集中の文字を取得する関数
    function getOldComment($file, $num){ 
        $content = file_get_contents($file);    //読み込み用のファイルポインタ
        $arr = explode("\r\n", $content);       //１行ずつ配列に格納
        foreach($arr as $comment_raw){               //1行ずつ順番に見ていく
            if(empty($comment_raw) == FALSE){            //空の行は見ない
                $comment = explode("<>", $comment_raw);  //<>で分割して配列に格納(0...投稿番号, 1...名前, 2...コメント, 3...日付)
                if($comment[0] == $num){                    //投稿番号を削除番号と比較
                    $old_name = $comment[1];
                    $old_comment = $comment[2];//新しい投稿番号をつけてファイルに書き込み
                    $flag = 1;
                    if($comment[4] != $_POST["password_edit"]){
                        $flag = 0;                              //パスワードと違う場合はフラグを0にする。
                    }
                }
            }
        }
        return array ($old_name, $old_comment, $flag, $_POST["password_edit"]);
    }

    //編集機能で既存の投稿を新たな名前とコメントに置換する関数
    function editComments($file, $num, $new_name, $new_comment){
        
        $date = date("Y/m/d H:i:s");    //日付取得
        $content = file_get_contents($file);
        $arr = explode("\r\n", $content);
        $fp = fopen($file, "w");
        foreach($arr as $comment_raw){              
            if(empty($comment_raw) == FALSE){                   //空の行は見ない。
                $comment = explode("<>", $comment_raw);
                if(empty($_POST["password"]) == FALSE){
                    if($comment[0] == $num && $_POST["password"] == $comment[4]){
                        fwrite($fp, $comment[0]."<>".$new_name."<>".htmlspecialchars($new_comment)."<>".$date."<>".$comment[4]."<>"."<br>\r\n");    
                    }else{
                        fwrite($fp, $comment[0]."<>".$comment[1]."<>".htmlspecialchars($comment[2])."<>".$comment[3]."<>".$comment[4]."<>"."<br>\r\n");
                    }    
                }
            }         
        }
    }
    //空以外の行数を数える関数
    function countWithoutNewLine($file){
        $line = 0;
        $content = file_get_contents($file);
        $arr = explode("\r\n", $content);       //１行ずつ配列に格納
        foreach($arr as $comment_raw){               //1行ずつ順番に見ていく
            if(empty($comment_raw) == FALSE){
                $line++;              
            }
        }
        return $line;
    }
    $getFile = fopen('mission_3-5.txt', 'r');
        if ($getFile) {
            if (flock($getFile, LOCK_SH)) {
                //一行ごとに処理を行う
                while (!feof($getFile)) {
                    $echo_text = fgets($getFile);
                    echo "<p>" . $echo_text . "</p>";
                }
                flock($getFile, LOCK_UN);
            }else {
            //ファイルの展開に失敗
            }
        }
?>


</body>
</html>