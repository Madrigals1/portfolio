<?php
    session_start();
    if(!isset($_SESSION['user_id'])){
        header("Location: /admin");
    }
    require('connect.php');
    require('../utils/imageResizer.php');
    require('../utils/functions.php');

    $name = $_POST['name'];
    $alias = preg_replace('/[^\w]/', '', $_POST['alias']);
    $queue = $_POST['queue'];
    $short_desc = $_POST['short_desc'];
    $long_desc = $_POST['long_desc'];
    $is_visible = $_POST['is_visible'] == "Yes" ? 1 : 0;
    $date = $_POST['date'];
    $lnf = $_POST['lnf'];
    $play_link = $_POST['play_link'];
    $github_link = $_POST['github_link'];
    $visit_link = $_POST['visit_link'];

    $folder = "/portfolio/img/projects";
    $res = get_static_path($folder);
    $target_dir = $res[0];
    $url = $res[1];

    if($_FILES["big_pic"]["tmp_name"]){
        $temp_big = explode(".", $_FILES["big_pic"]["name"]);
        $filename_big = round(microtime(true)) . '-big.' . end($temp_big);
        $target_file_big  = $target_dir . $filename_big;
        $url_big = $url . $filename_big;

        if (move_uploaded_file($_FILES["big_pic"]["tmp_name"], $target_file_big)) {
            $file_uploaded_big = true;
        } else {
            header("Location: /log/helper.php?error=picture_upload_error");
        }
    }
    
    if($_FILES["small_pic"]["tmp_name"]){
        $temp_small = explode(".", $_FILES["small_pic"]["name"]);
        $filename_small = round(microtime(true)) . '-small.' . end($temp_small);
        $target_file_small = $target_dir . $filename_small;
        $url_small = $url . $filename_small;
    
        if(move_uploaded_file($_FILES["small_pic"]["tmp_name"], $target_file_small)){
            $file_uploaded_small = true;
            $resize = new ResizeImage($target_file_small);
            $resize->resizeTo(400, 300, "exact");
            $resize->saveImage($target_file_small);
        } else {
            header("Location: /log/helper.php?error=picture_upload_error");
        }
    }
    
    $result_project = mysqli_query($con,"
        INSERT into `projects` 
        (
            `name`,
            `alias`,
            `queue`,
            `short_desc`,
            `long_desc`,
            `is_visible`,
            `date`,
            `lnf`,
            `play_link`,
            `github_link`,
            `visit_link`,
            `big_pic`,
            `small_pic`
        )
        VALUES 
        (
            '$name',
            '$alias',
            '$queue',
            '$short_desc',
            '$long_desc',
            '$is_visible',
            '$date',
            '$lnf',
            '$play_link',
            '$github_link',
            '$visit_link',
            '$url_big',
            '$url_small'
        );"
    );
    

    if($result_project){
        header("Location: /projects.php");
    } else {
        echo mysqli_error($con);
        header("Location: /log/helper.php?error=project_add_error");
    }
?>