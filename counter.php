<?php
# source https://git.tuxnix.ru/grigruss/apache-counter
# Author: grigruss@ya.ru

$log_file="/var/logs/apache2/access.log"; #Укажите свой путь к логу.

$logs=explode("\n",file_get_contents($log_file));
$visit=array();

// Парсим лог построчно. Имейте ввиду, в логе одна строка - одно подключение.
foreach($logs as $row){
    if($row=="")continue; // Если строка пустая - идём дальше
    $v=explode(" ",str_replace(" - -","",$row)); // Разбиваем строку на значения
    $ua=array();
    for($i=9;$i<count($v);$i++)$ua[]=$v[$i]; // Из значений выбираем всё, что касается браузера в одно значение...
    $ua=implode(" ",$ua); // ...и объединяем в строку
    $vv=array('ip'=>$v[0],'time'=>$v[1].' '.$v[2],'query'=>$v[3].' '.$v[4].' '.$v[5],'code'=>$v[6],'source'=>$v[8],'user_agent'=>$ua); // Запихиваем всё в массив...
    if(!is_array($visit[substr($v[1],1,11)]))$visit[substr($v[1],1,11)]=array();
    $visit[substr($v[1],1,11)][substr($v[1],13)][]=$vv; // ...и втыкаем в общий, многомерный массив.
}

// Готовим массив к выводу в браузер
foreach($visit as $day=>$list){
    $content.="<h3 onclick='showItem(this);'>$day (".count($list).")</h3><div style='display:none;'>";
    foreach($list as $time=>$unit){
	$content.="<h4 onclick='showItem(this);'>$time (".count($unit).")</h4><div style='display:none;'>";
	foreach($unit as $kv=>$val){
	    $content.="<p onclick='showItem(this);'>#$kv</p><div style='display:none;'>";
	    foreach($val as $k=>$v){
		$content.="<strong>$k:</strong> $v<br>";
	    }
	    $content.="</div>";
	}
	$content.="</div>";
    }
    $content.="</div><script>
function showItem(a){
    let b=a.nextElementSibling;
    if(b.style.display=='none'){
	b.style.display='block';
    }else{
	b.style.display='none';
    }
}</script>";
}

echo $content; // Выдаём в браузер готовый код HTML.
