<?php 

session_start();

if(isset($_GET['action'])){
    switch($_GET['action']){
        case 0:
            unlog();
            break;
    }
}

function unlog(){
    $_SESSION['user']['state'] = False;
    session_unset();
    session_destroy();
    header('Location: ../');
    die();
}

function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

function filAriane($arg){
    
    for($i = 0;$i < count($arg); $i++){

        echo "<li><a href='".$arg[$i]['link']."'>".$arg[$i]['title']."</a></li>";
       if($i !== count($arg)-1){
           echo "<li> > </li>";
       }

    }


}