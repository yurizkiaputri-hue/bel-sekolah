<?php
header("Content-Type: application/json");
require "koneksi.php";

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
  case 'get_settings':
    $res = $conn->query("SELECT * FROM settings WHERE id=1");
    echo json_encode(["status"=>"success","data"=>$res->fetch_assoc()]);
    break;

  case 'update_settings':
    $name = $conn->real_escape_string($_POST['school_name'] ?? '');
    $enabled = (int)($_POST['is_enabled'] ?? 0);
    $offset = (int)($_POST['time_offset'] ?? 0);
    $logo = $_POST['logo_url'] ?? '';
    $conn->query("UPDATE settings SET school_name='$name', is_enabled=$enabled, time_offset=$offset, logo_url='$logo' WHERE id=1");
    echo json_encode(["status"=>"success"]);
    break;

  case 'get_schedules':
    $res = $conn->query("SELECT * FROM schedules ORDER BY time ASC");
    $data = []; while($r=$res->fetch_assoc()) $data[]=$r;
    echo json_encode(["status"=>"success","data"=>$data]);
    break;

  case 'add_schedule':
    $time = $_POST['time']; $label = $conn->real_escape_string($_POST['label']);
    $audio = $conn->real_escape_string($_POST['audio_filename']);
    $vol = (int)$_POST['volume'];
    $conn->query("INSERT INTO schedules (time,label,audio_filename,volume) VALUES ('$time','$label','$audio',$vol)");
    echo json_encode(["status"=>"success"]);
    break;

  case 'toggle_schedule':
    $id = (int)$_POST['id'];
    $conn->query("UPDATE schedules SET is_enabled = IF(is_enabled=1,0,1) WHERE id=$id");
    echo json_encode(["status"=>"success"]);
    break;

  case 'delete_schedule':
    $id = (int)$_POST['id'];
    $conn->query("DELETE FROM schedules WHERE id=$id");
    echo json_encode(["status"=>"success"]);
    break;

  case 'upload_audio':
    if(!isset($_FILES['audio'])) { echo json_encode(["status"=>"error","msg"=>"No file"]); break; }
    $ext = pathinfo($_FILES['audio']['name'], PATHINFO_EXTENSION);
    $fname = uniqid('bell_') . '.' . $ext;
    if(move_uploaded_file($_FILES['audio']['tmp_name'], "uploads/$fname")) {
      echo json_encode(["status"=>"success","filename"=>$fname]);
    } else { echo json_encode(["status"=>"error","msg"=>"Upload gagal"]); }
    break;

  case 'get_holidays':
    $res = $conn->query("SELECT holiday_date FROM holidays");
    $dates = []; while($r=$res->fetch_assoc()) $dates[]=$r['holiday_date'];
    echo json_encode(["status"=>"success","data"=>$dates]);
    break;

  case 'add_log':
    $time = $_POST['triggered_at']; $label = $conn->real_escape_string($_POST['label']??'');
    $vol = (int)$_POST['volume']; $status = $_POST['status']??'OK';
    $conn->query("INSERT INTO logs (triggered_at,schedule_label,volume,status) VALUES ('$time','$label',$vol,'$status')");
    echo json_encode(["status"=>"success"]);
    break;

  case 'get_logs':
    $res = $conn->query("SELECT * FROM logs ORDER BY id DESC LIMIT 100");
    $data = []; while($r=$res->fetch_assoc()) $data[]=$r;
    echo json_encode(["status"=>"success","data"=>$data]);
    break;

  default: echo json_encode(["status"=>"error","msg"=>"Invalid action"]);
}
$conn->close();
?>