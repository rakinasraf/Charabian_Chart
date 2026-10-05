<?php
require __DIR__.'/lib.php'; header('Content-Type: application/json');
$in=json_decode(file_get_contents('php://input'),true)?:[]; $W=world();
$c=$W[$in['country']??'']??null; $city=$c['cities'][$in['city']??'']??null; $u=null;
if($city) foreach($city['unis'] as $x) if($x['name']===($in['uni']??'')) $u=$x;
$f=$in['field']??''; $l=$in['level']??''; $a=$in['acc']??'';
if(!$c||!$city||!$u||!isset(FIELDS[$f],LEVELS[$l],ACC[$a])){http_response_code(400);echo json_encode(['error'=>'Choose country, city, university, level, subject and accommodation.']);exit;}
$trips=max(0,min(4,(int)($in['trips']??1)));
$items=cost($c,$city,$u,$f,$l,$a,$trips); $rec=array_sum($items); $y1=$rec+$c['visa']; $yrs=years($c['code'],$l);
$all=[]; foreach($W as $k=>$x){$ci=reset($x['cities']);$un=$ci['unis'][0];$all[]=['name'=>$x['name'],'flag'=>$x['flag'],'y1'=>array_sum(cost($x,$ci,$un,$f,$l,'Shared room',1))+$x['visa']];}
usort($all,fn($p,$q)=>$p['y1']<=>$q['y1']); $rank=1; foreach($all as $i=>$x) if($x['name']==$c['name']) $rank=$i+1;
echo json_encode(['items'=>$items,'visa'=>$c['visa'],'recurring'=>$rec,'year1'=>$y1,'monthly'=>round($y1/12),'monthlyLater'=>round($rec/12),'years'=>$yrs,'program'=>$y1+($yrs-1)*$rec,'rank'=>$rank,'n'=>count($all),'cheapest'=>array_slice($all,0,5)]);
