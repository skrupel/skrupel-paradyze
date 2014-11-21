<?php
  $browser = getenv("HTTP_USER_AGENT");
  
  if ( preg_match("/^Mozilla\/.*?Firefox\/\d\.\d/",$browser) )
    $browser = "mozilla";
  elseif ( preg_match("/^Mozilla\/.*?Gecko\/\d{8}/",$browser) )
    $browser = "mozilla";
  elseif ( preg_match("/^Mozilla\/.*?\(compatible; MSIE/",$browser) )
    $browser = "ie";
  elseif ( preg_match("/^Mozilla\/.*?\(compatible; Konqueror/",$browser) )
    $browser = "konqueror";
  elseif ( preg_match("/^Opera\/\d\.\d/" , $browser ) )
    $browser = "opera";
  elseif ( preg_match("/^Mozilla\/.*?Safari/",$browser) )
    $browser = "safari";
  else
    $browser = "ka";
?>
