<?php 

// 大量のupdate文を小分けにして実行するphpプログラム

include_once('../batch_setting.php');                          // 環境設定ファイルから情報を取得
$test_environment = $batch_set['test_environment'];         //0:本番環境　1:テスト環境
// $log_path =$batch_set['log_path'].'SocketClient_'.date("Ym").'.log';
$log_path =$batch_set['log_path'].'TestShiroiwa_'.date("Ym").'.log';
$connect_path = $batch_set['connect_path'];
$key_path = $batch_set['key_path'];
$factory = require_once $batch_set['const_path'];
require_once $batch_set['DBConnection_path'];
require_once $batch_set['PlcFunctions_path'];
require $batch_set['LoadSocket_path'];
require($connect_path);
ini_set( "date.timezone", "Asia/Tokyo" );
$pdo = new connect();
$con = $pdo->pdo($connect_path, $key_path);

if ($argc == 1) {
    echo "[ USAGE ] php update_tdomp.php from='YYYY-MM-DD HH:m*'\n";
    exit;
}



// update.sqlを配列形式で読み込む
$file_name = 'update_t_drain_operating_mgt_print.sql';
$array_sql = file($file_name);


// コマンド引数で期間指定
$kv = preg_split("/\=/", $argv[1]);
$from = $kv[1];

// update.sqlから期間指定の部分を抽出
$sqls = preg_grep('!'.$from.'!',$array_sql);
print_r($sqls);

// PDOで実行
$con->beginTransaction();
try {
    foreach ($sqls as $key => $sql) {
        print_r($sql);
        $stmt = $con->prepare($sql);
        $result = $stmt->execute();
        $datas = $stmt->fetchAll();
    }
    $con->commit();
} catch (\Throwable $th) {
    $con->rollBack();
    throw $th;
}
?>


