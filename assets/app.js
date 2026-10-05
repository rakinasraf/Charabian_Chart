const $=id=>document.getElementById(id),W=window.W,bdt=n=>'৳ '+Math.round(n).toLocaleString('en-BD');
const root=document.documentElement;let cur=null;
function theme(t){root.dataset.theme=t;$('theme').textContent=t=='dark'?'☀ Raise the sun':'☾ Wait for moonrise';try{localStorage.setItem('theme',t)}catch(e){}}
let s='light';try{s=localStorage.getItem('theme')||(matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light')}catch(e){}
theme(s);$('theme').onclick=()=>theme(root.dataset.theme=='dark'?'light':'dark');
const opts=(a,fn)=>a.map(fn).join(''),ph='<option value="">Select...</option>';
function list(f=''){ $('countries').innerHTML=''; W.filter(c=>c.name.toLowerCase().includes(f.toLowerCase())).forEach(c=>{
 const b=document.createElement('button');b.type='button';b.setAttribute('aria-pressed',cur&&cur.code==c.code);b.innerHTML=`<span>${c.flag}</span>${c.name}`;b.onclick=()=>pick(c);$('countries').appendChild(b)});}
list();$('q').oninput=e=>list(e.target.value);
$('level').innerHTML=opts(Object.keys(LEVELS),k=>`<option>${k}</option>`);
function fee(base){return base*FIELDS[$('field').value]*LEVELS[$('level').value]}
function fieldOpts(){const u=uni();const k=$('field').value;$('field').innerHTML=ph+opts(Object.keys(FIELDS),f=>`<option value="${f}">${f}${u?': '+bdt(u.base*FIELDS[f]*LEVELS[$('level').value])+'/yr':''}</option>`);$('field').value=k}
const city=()=>cur&&cur.cities[$('city').value],uni=()=>{const c=city();return c&&c.unis.find(u=>u.name==$('uni').value)};
function pick(c){cur=c;list($('q').value);const v=c.vinfo;
 $('infoList').innerHTML=[['Cities charted',Object.keys(c.cities).join(', ')],
 ['Visa centre',`<b>${v.place}</b>${v.addr?'<br>'+v.addr:''}${v.verified?'':'<br><i>Exact address not verified</i>'}<br>${v.note}<br><a href="${v.map}" target="_blank" rel="noopener">Open on map</a>${v.url?` · <a href="${v.url}" target="_blank" rel="noopener">Official site</a>`:''}`],
 ['Visa cost',bdt(c.visa)],['Return flight',bdt(c.flight)],['Permanent residence',c.pr]].map(([k,x])=>`<dt>${k}</dt><dd>${x}</dd>`).join('');
 $('city').innerHTML=ph+opts(Object.keys(c.cities),n=>`<option>${n}</option>`);$('uni').innerHTML=ph;$('acc').innerHTML=ph;fieldOpts();
 ['info','plan'].forEach(i=>$(i).classList.remove('hide'));$('result').classList.add('hide');$('info').scrollIntoView({behavior:'smooth'})}
$('city').onchange=()=>{const ct=city();if(!ct)return;
 const g=t=>{const l=ct.unis.filter(u=>u.type==t);return l.length?`<optgroup label="${t=='public'?'Public':'Private'} universities">`+opts(l,u=>`<option>${u.name}</option>`)+'</optgroup>':''};
 $('uni').innerHTML=ph+g('public')+g('private');$('acc').innerHTML=ph+opts(Object.entries(ct.acc),([n,r])=>`<option value="${n}">${n}: ${bdt(r)}/month</option>`);fieldOpts();calc()};
$('uni').onchange=()=>{fieldOpts();calc()};$('level').onchange=()=>{fieldOpts();calc()};
['field','acc','trips'].forEach(i=>$(i).onchange=calc);
async function calc(){const b={country:cur.code,city:$('city').value,uni:$('uni').value,field:$('field').value,level:$('level').value,acc:$('acc').value,trips:$('trips').value};
 if(!b.city||!b.uni||!b.field||!b.acc){$('result').classList.add('hide');return}
 const j=await(await fetch('calculate.php',{method:'POST',body:JSON.stringify(b)})).json();$('result').classList.remove('hide');
 if(j.error){$('err').textContent=j.error;$('out').innerHTML='';return}$('err').textContent='';
 const rows=Object.entries(j.items).map(([k,v])=>`<tr><td>${k}</td><td><i class="bar"><u style="width:${(v/j.recurring*100).toFixed(1)}%"></u></i></td><td>${(v/j.recurring*100).toFixed(0)}%</td><td>${bdt(v)}</td></tr>`).join('');
 $('out').innerHTML=`<table>${rows}<tr><td>Visa (first year only)</td><td></td><td></td><td>${bdt(j.visa)}</td></tr></table>
 <div class="chest"><div><small>Year 1 total</small><strong>${bdt(j.year1)}</strong><small>${bdt(j.monthly)} a month</small></div>
 <div><small>Each later year</small><strong>${bdt(j.recurring)}</strong><small>${bdt(j.monthlyLater)} a month</small></div>
 <div><small>Whole degree (${j.years} yr${j.years>1?'s':''})</small><strong>${bdt(j.program)}</strong><small>typical length</small></div></div>
 <p class="rank">For this subject and level, ${cur.name} ranks <b>#${j.rank}</b> of ${j.n} for lowest first-year cost (shared room, one flight, first listed university).</p>
 <h3>Cheapest ports for the same course</h3><ol>${opts(j.cheapest,x=>`<li>${x.flag} ${x.name}: ${bdt(x.y1)}</li>`)}</ol>`}
