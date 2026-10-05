<?php
const FIELDS=['Computer Science & AI'=>1,'Data Science & Analytics'=>1.05,'Software Engineering'=>1,'Electrical & Electronic Engineering'=>1,'Mechanical & Civil Engineering'=>1,'Business & MBA'=>1.2,'Finance & Economics'=>1.1,'Law'=>1.05,'Medicine & Health Sciences'=>1.9,'Natural Sciences'=>.95,'Architecture & Design'=>1,'Arts & Humanities'=>.85,'Social Sciences'=>.85];
const LEVELS=['Master'=>1,'Bachelor'=>.9];
const ACC=['Shared room'=>1,'University dorm'=>.9,'Private studio'=>1.8];
const VISA=[ // exact places only where a source was found; always re-verify
 'uk'=>['VFS Global UK Visa Application Centre, Dhaka','Nafi Tower, 53 Gulshan Avenue, Gulshan-1, Dhaka 1212','https://visa.vfsglobal.com/bgd/en/gbr','Book online, then give biometrics in person. This centre has moved before, so recheck the booking page.'],
 'us'=>['US Embassy, Dhaka (F-1 interview)','Madani Avenue, Baridhara, Dhaka 1212','https://bd.usembassy.gov','Fill DS-160 online, then attend the interview at the embassy.'],
 'ie'=>['Irish visa processing, New Delhi, India','C-17 Malcha Marg, Chanakyapuri, New Delhi 110021','https://www.irishimmigration.ie','No visa desk in Dhaka. Apply online and follow the instructions for documents; budget a trip to India.'],
];
function world(){
  $d=[];$cur=null;
  foreach(file(__DIR__.'/data/world.txt',FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $l){
    if($l[0]=='#'){ [$k,$n,$fl,$fx,$vs,$pr,$pub,$pri,$liv,$rent]=explode('|',substr($l,1));
      $d[$k]=['code'=>$k,'name'=>$n,'flag'=>$fl,'flight'=>(int)$fx,'visa'=>(int)$vs,'pr'=>$pr,'pub'=>(int)$pub,'pri'=>(int)$pri,'liv'=>(int)$liv,'rent'=>(int)$rent,'cities'=>[]];$cur=$k;continue;}
    [$cn,$us]=explode(':',$l,2); [$city,$m]=array_pad(explode('*',$cn),2,1);
    $c=['mult'=>(float)$m,'unis'=>[]];
    foreach(explode(',',$us) as $u){$u=trim($u);$p=$u[0]=='+';$u=ltrim($u,'+');[$un,$t]=array_pad(explode('^',$u),2,1);
      $c['unis'][]=['name'=>$un,'type'=>$p?'private':'public','tier'=>(float)$t];}
    $d[$cur]['cities'][$city]=$c;
  }
  return $d;
}
function visaInfo($c){
  $v=VISA[$c['code']]??['Embassy or visa centre for '.$c['name'].' (not verified here)','','','Exact centre not verified in this project. Use the map search, then confirm on the official immigration site.'];
  $q=$v[1]?:($c['name'].' embassy visa application centre Dhaka');
  return ['place'=>$v[0],'addr'=>$v[1],'url'=>$v[2],'note'=>$v[3],'verified'=>(bool)$v[1],'map'=>'https://www.google.com/maps/search/?api=1&query='.urlencode($q)];
}
function baseFee($c,$u){return ($u['type']=='public'?$c['pub']:$c['pri'])*$u['tier'];}
function cost($c,$city,$u,$f,$l,$acc,$trips){
  $m=$city['mult'];$liv=$c['liv']*$m;
  return ['Tuition fee'=>round(baseFee($c,$u)*FIELDS[$f]*LEVELS[$l]),'Room rent'=>round($c['rent']*$m*ACC[$acc]*12),'Food'=>round($liv*.55*12),'Local transport'=>round($liv*.15*12),'Other living costs'=>round($liv*.30*12),'Return flights'=>$c['flight']*$trips];
}
function years($code,$l){return $l=='Master'?(in_array($code,['uk','ie'])?1:2):(in_array($code,['uk','au'])?3:4);}
