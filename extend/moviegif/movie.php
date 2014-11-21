<?php

$game_id = (int) $_GET['game'];

include ('../../inc.conf.php');

$conn = @mysql_connect($conf_database_server, $conf_database_login, $conf_database_password);
@mysql_select_db($conf_database_database, $conn);

$zeiger = @mysql_query("SELECT phase FROM skrupel_spiele WHERE phase = 1 AND id = $game_id LIMIT 1");

if (@mysql_num_rows($zeiger) == 1) {

    $moviegif_files_path = '../../files/moviegif/';
    $movie_file = $moviegif_files_path . 'movies/moviegif_' . $game_id . '.gif';

    if (!@file_exists($movie_file))  {

        @file_put_contents($movie_file, '');

        $scenes_dir = $moviegif_files_path . 'temp/' . $game_id . '/';

        foreach (scandir($scenes_dir) as $object) {
	        if ($object[0] != '.') {
		        $scenes[] = $scenes_dir . $object;
			    $scenes_dur[] = 120;
	        }
        }

        sort($scenes);

        // create

        include 'gifencoder.class.php';

        $gif = new GifEncoder($scenes, $scenes_dur, 0, 2, 255, 0, 0, 'url');

        @file_put_contents($movie_file, $gif->getAnimation());
        @chmod($movie_file, 0777);

        // cleanup

        foreach ($scenes as $scene) {
		    unlink($scene);
        }

        rmdir($scenes_dir);

    }

    // output

    header('Content-Type: image/gif');
    echo @file_get_contents($movie_file);

}

@mysql_close($conn);

?>
