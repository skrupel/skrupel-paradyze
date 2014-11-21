        <div style="font-family: starcraft; font-size: 20px;">Neuigkeiten <a href="http://www.iceflame.net/feed/blog"><img src="../bilder/feed.png" alt="RSS" title="RSS" /></a></div>
        <br />
<?php /*
    $news_conn = mysql_connect('www.iceflame.net', 'd00dbb68', 'gwq8cs8JySCwbBd6');
    mysql_select_db('d00dbb68', $news_conn);
	$sql = mysql_query('SELECT p.*, u.username AS author_name FROM iceflame_blog_posts p, iceflame_blog_tags t, iceflame_users u WHERE t.post = p.id AND u.id = p.author AND t.word = "paradyze" GROUP BY p.id ORDER BY date DESC LIMIT 7');
    while ($data = mysql_fetch_assoc($sql)):
?>
		<br />
		<div class="box" style="font-size: 11px;">
            <span style="color:#aaaaaa; float: right;"><?php echo strftime('%d.%m.%Y, %H:%M', $data['date'])?></span><b><?php echo htmlspecialchars($data['title'])?></b><br><br>
			<span style="color:#aaaaaa;"><?php echo htmlspecialchars($data['author_name'])?></span> &nbsp; <?php echo htmlspecialchars($data)?><br><br>
            <div style="text-align: center;"><a href="http://www.iceflame.net/news/<?php echo $data['id']?>">Kommentare</a></div>
		</div>
<?php
    endwhile;
    mysql_close($news_conn);
*/ ?>