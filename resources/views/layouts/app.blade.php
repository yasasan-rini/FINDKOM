<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@auth<meta name="ringkas-url" content="{{ route('api.ringkas') }}">@endauth
<title>@yield('judul', 'FINDKOM') | Findkom — FILKOM UB</title>
<link rel="icon" href="{{ asset('img/favicon.svg') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,700;0,9..144,900;1,9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<style>
:root{--ink:#0F2E30;--ink2:#143A3D;--teal:#12676B;--teal2:#0E4F52;--teal3:#2A9D8F;--mint:#7FD1C0;--mintsoft:#D9EFE9;--amber:#F2B705;--amber2:#B57E00;--ambersoft:#FFF3CC;--coral:#E4573D;--coral2:#C53A3A;--coralsoft:#FCEAE5;--green:#2E9E5B;--green2:#1F7A43;--greensoft:#E4F4EA;--blue:#2D7DD2;--bluesoft:#E4EEF9;--purple:#7B5EA7;--bg:#F4F8F7;--paper:#fff;--soft:#EAF2F0;--line:#DCE8E5;--line2:#C8DAD6;--tx:#17252A;--dim:#4D6166;--faint:#7D8F93;--er:#D64545;--sh:0 4px 16px rgba(15,46,48,.07);--sh2:0 12px 34px rgba(15,46,48,.14);--top:64px;--fd:'Fraunces',Georgia,serif;--fb:'Plus Jakarta Sans',system-ui,sans-serif;--fm:'JetBrains Mono',monospace}
*{box-sizing:border-box;margin:0;padding:0}
body{background:var(--bg);color:var(--tx);font:15px/1.6 var(--fb);-webkit-font-smoothing:antialiased;overflow-x:hidden}
::selection{background:var(--mint);color:var(--ink)}
*{scrollbar-width:thin;scrollbar-color:var(--teal3) var(--soft)}
::-webkit-scrollbar{width:10px;height:10px}::-webkit-scrollbar-track{background:var(--soft);border-radius:99px}
::-webkit-scrollbar-thumb{background:linear-gradient(180deg,var(--teal3),var(--teal));border-radius:99px;border:2px solid var(--soft)}
h1,h2,h3{font-family:var(--fd);color:var(--ink);line-height:1.15;letter-spacing:-.01em}
h1{font-size:clamp(24px,3vw,32px);font-weight:900;margin:4px 0 6px}h2{font-size:20px;font-weight:700;margin:22px 0 12px}h3{font-size:16px;font-weight:700}
a{color:var(--teal);text-decoration:none}a:hover{color:var(--teal2)}
img{display:block;max-width:100%}button{font:inherit;cursor:pointer}
input,select,textarea{font:inherit;color:var(--tx)}
.mono{font-family:var(--fm);font-weight:700;letter-spacing:.02em}.mut{color:var(--dim)}.faint{color:var(--faint)}
.small{font-size:12.5px}.tiny{font-size:11.5px}.er{color:var(--er)}.ok{color:var(--green)}
:focus-visible{outline:3px solid rgba(42,157,143,.55);outline-offset:2px;border-radius:8px}
#bg{position:fixed;inset:0;z-index:-1;overflow:hidden;pointer-events:none}
#bg i{position:absolute;border-radius:50%;filter:blur(90px)}
#bg .b1{width:560px;height:560px;left:-160px;top:-180px;background:radial-gradient(circle,#BFE3DA,transparent 70%);opacity:.5;animation:drift1 26s ease-in-out infinite alternate}
#bg .b2{width:520px;height:520px;right:-180px;top:22%;background:radial-gradient(circle,#FBE9B7,transparent 70%);opacity:.4;animation:drift2 32s ease-in-out infinite alternate}
#bg .b3{width:460px;height:460px;left:28%;bottom:-240px;background:radial-gradient(circle,#CFE6F5,transparent 70%);opacity:.35;animation:drift1 38s ease-in-out infinite alternate-reverse}
#bg .grain{position:absolute;inset:0;border-radius:0;filter:none;opacity:.05;mix-blend-mode:multiply;background:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2'/%3E%3C/filter%3E%3Crect width='160' height='160' filter='url(%23n)'/%3E%3C/svg%3E")}
@keyframes drift1{to{transform:translate(70px,50px) scale(1.12)}}@keyframes drift2{to{transform:translate(-60px,-40px) scale(1.08)}}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid var(--line2);background:var(--paper);color:var(--tx);border-radius:12px;padding:10px 18px;font-weight:700;font-size:14px;transition:transform .15s,box-shadow .2s,background .2s,border-color .2s;white-space:nowrap}
.btn:hover{transform:translateY(-1px);box-shadow:var(--sh);border-color:var(--teal3);color:var(--teal2)}
.btn:active{transform:scale(.98)}
.btn.pr{background:linear-gradient(135deg,var(--teal3),var(--teal) 55%,var(--teal2));border-color:var(--teal2);color:#fff;box-shadow:0 6px 18px rgba(18,103,107,.3)}
.btn.pr:hover{color:#fff;filter:brightness(1.06)}
.btn.dn{background:linear-gradient(135deg,#F0704F,var(--coral2));border-color:var(--coral2);color:#fff}
.btn.dn:hover{color:#fff;filter:brightness(1.05)}
.btn.rd,.btn.dno{color:var(--coral2);border-color:#EFC4B8}.btn.rd:hover,.btn.dno:hover{background:var(--coralsoft);border-color:var(--coral);color:var(--coral2)}
.btn.gh{background:transparent;border-color:transparent;color:var(--dim)}.btn.gh:hover{background:var(--soft);box-shadow:none;color:var(--teal2)}
.btn.am{background:linear-gradient(135deg,#FFD34D,var(--amber));border-color:var(--amber2);color:#4A3600}
.btn.sm{padding:7px 13px;font-size:13px;border-radius:10px}.btn.xs{padding:4px 10px;font-size:12px;border-radius:8px}
.btn.w{width:100%}.btn:disabled{opacity:.45;cursor:not-allowed;transform:none!important;box-shadow:none!important;filter:none!important}
.bd{display:inline-flex;align-items:center;gap:6px;padding:3px 11px;border-radius:99px;font-size:11.5px;font-weight:800;background:var(--soft);color:var(--teal2)}
.bd::before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor}
.bd.cand{background:var(--ambersoft);color:#8A6100}.bd.ver{background:var(--bluesoft);color:#1D5CA0}
.bd.wait{background:#FFE9A8;color:#7A5600}.bd.wait::before{animation:pulse 1.6s infinite}
.bd.done{background:var(--greensoft);color:var(--green2)}.bd.closed{background:#E2ECEA;color:var(--ink2)}.bd.rej{background:var(--coralsoft);color:var(--coral2)}
.bd.flash{animation:flash 1.6s ease}
@keyframes flash{0%{box-shadow:0 0 0 4px rgba(242,183,5,.6)}100%{box-shadow:none}}
.ty{display:inline-flex;align-items:center;font-size:11px;font-weight:800;padding:3px 10px;border-radius:99px;letter-spacing:.04em;text-transform:uppercase;color:var(--coral2);background:var(--coralsoft)}
.ty.found{color:var(--green2);background:var(--greensoft)}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(.7)}}
.card{background:var(--paper);border:1px solid var(--line);border-radius:16px;padding:22px;box-shadow:var(--sh)}
.fl{margin-bottom:15px}.fl label{display:block;font-size:12.5px;font-weight:800;color:var(--dim);margin-bottom:6px}
.fl input,.fl select,.fl textarea,.tin{width:100%;padding:11px 14px;border:1.5px solid var(--line2);border-radius:12px;background:var(--paper);transition:.18s}
.fl input:focus,.fl select:focus,.fl textarea:focus,.tin:focus{outline:0;border-color:var(--teal3);box-shadow:0 0 0 4px rgba(42,157,143,.15)}
.fl textarea{resize:vertical;min-height:96px}.fl .hint{font-size:11.5px;color:var(--faint);margin-top:5px}
.g2{display:grid;grid-template-columns:1fr 1fr;gap:0 16px}.g3{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}.g4{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
.row{display:flex;align-items:center;gap:10px;flex-wrap:wrap}.sp{justify-content:space-between}
.top{position:fixed;top:0;left:0;right:0;height:var(--top);z-index:60;display:flex;align-items:center;gap:18px;padding:0 22px;background:linear-gradient(100deg,var(--ink),var(--teal2) 55%,var(--teal));border-bottom:1px solid rgba(127,209,192,.25);transition:box-shadow .25s}
.top.scrolled{box-shadow:0 8px 30px rgba(15,46,48,.35)}
.brand{display:flex;align-items:center;gap:10px;color:#fff;flex:none}.brand:hover{color:#fff}
.brand img{height:36px;width:36px}.brand b{font-family:var(--fd);font-weight:900;font-size:20px}
.brand .tag{font-size:9.5px;font-weight:800;letter-spacing:.14em;background:rgba(127,209,192,.18);border:1px solid rgba(127,209,192,.4);color:var(--mint);padding:2px 8px;border-radius:99px}
.nav{display:flex;gap:4px;flex:1;min-width:0;overflow-x:auto;scrollbar-width:none}.nav::-webkit-scrollbar{display:none}
.nav a{display:inline-flex;align-items:center;gap:7px;padding:8px 13px;border-radius:10px;color:#C7DED9;font-weight:700;font-size:13.5px;white-space:nowrap;position:relative}
.nav a:hover{background:rgba(255,255,255,.09);color:#fff}
.nav a.on{background:rgba(127,209,192,.16);color:var(--mint)}
.nav a.on::after{content:'';position:absolute;left:13px;right:13px;bottom:3px;height:2px;border-radius:2px;background:var(--amber)}
.topact{display:flex;align-items:center;gap:10px;flex:none;position:relative}
.bell{position:relative;width:40px;height:40px;border-radius:12px;border:1px solid rgba(127,209,192,.3);background:rgba(255,255,255,.07);color:#DFF2EE;display:grid;place-items:center;font-size:17px}
.bell:hover{background:rgba(255,255,255,.15)}
.bell .n{position:absolute;top:-6px;right:-6px;min-width:19px;height:19px;padding:0 5px;border-radius:99px;background:linear-gradient(135deg,#FFD34D,var(--amber));color:#4A3600;font-size:10.5px;font-weight:800;display:grid;place-items:center;border:2px solid var(--teal2)}
.uchip{display:flex;align-items:center;gap:9px;padding:5px 12px 5px 5px;border-radius:12px;border:1px solid rgba(127,209,192,.3);background:rgba(255,255,255,.07);color:#fff}
.ava{width:32px;height:32px;border-radius:10px;display:grid;place-items:center;font-weight:800;font-size:13px;color:#fff;background:linear-gradient(135deg,var(--teal3),var(--blue));flex:none}
.ava.sec{background:linear-gradient(135deg,var(--amber),var(--coral))}
.uchip .un{font-size:13px;font-weight:700;line-height:1.15;text-align:left}.uchip .ur{font-size:10px;color:var(--mint);font-weight:800;letter-spacing:.08em;text-transform:uppercase}
.npanel{position:absolute;top:calc(100% + 10px);right:0;width:min(380px,92vw);background:var(--paper);border:1px solid var(--line);border-radius:16px;box-shadow:var(--sh2);padding:12px;display:none;z-index:70;max-height:70vh;overflow-y:auto}
.npanel.open{display:block;animation:popIn .18s}
.npanel .nh{display:flex;justify-content:space-between;align-items:center;padding:4px 6px 10px;border-bottom:1px solid var(--line);margin-bottom:8px}
.notif{display:flex;gap:10px;padding:10px 12px;border-radius:12px;border:1px solid var(--line);background:var(--paper);margin-bottom:8px;color:var(--tx);position:relative}
.notif:hover{border-color:var(--teal3);color:var(--tx)}
.notif.unread{background:#FFFBEA;border-color:#F0D98A}
.notif.unread::after{content:'';position:absolute;top:12px;right:11px;width:8px;height:8px;border-radius:50%;background:var(--amber)}
.notif p{font-size:12.5px;line-height:1.45;padding-right:12px}.notif small{color:var(--faint);font-weight:700;font-size:10.5px}
.wrap{max-width:1180px;margin:0 auto;padding:calc(var(--top) + 26px) 24px 60px;animation:pageIn .35s ease}
@keyframes pageIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
@keyframes popIn{from{opacity:0;transform:scale(.92)}}
.sub{color:var(--dim);margin-bottom:20px;max-width:660px}
.foot{max-width:1180px;margin:0 auto;padding:18px 24px 30px;border-top:1px solid var(--line);display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;color:var(--faint);font-size:12.5px}
.js .reveal{opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease}
.js .reveal.in{opacity:1;transform:none}
.toolbar{display:flex;gap:11px;flex-wrap:wrap;align-items:flex-end;background:var(--paper);border:1px solid var(--line);border-radius:16px;padding:15px 17px;box-shadow:var(--sh);margin-bottom:18px}
.toolbar .fl{margin:0;flex:1;min-width:150px}.toolbar .fl.wide{flex:2;min-width:210px}
.searchwrap{position:relative}.searchwrap::before{content:'⌕';position:absolute;left:12px;top:50%;transform:translateY(-52%);color:var(--faint);font-size:18px;pointer-events:none}
.searchwrap input{padding-left:36px}
.tblwrap{border:1px solid var(--line);border-radius:14px;background:var(--paper);overflow:auto;box-shadow:var(--sh)}
table{width:100%;border-collapse:collapse;font-size:13.5px;min-width:640px}
th{font-size:11px;font-weight:800;letter-spacing:.07em;text-transform:uppercase;color:var(--faint);text-align:left;padding:12px 15px;border-bottom:1.5px solid var(--line);background:#FAFCFB}
td{padding:11px 15px;border-bottom:1px solid var(--line);vertical-align:middle}
tr:last-child td{border-bottom:0}tr.cl{cursor:pointer}tr.cl:hover td,tr.cl:focus-visible td{background:#F4FAF8}
.usage{font-size:11px;font-weight:800;color:var(--dim);background:var(--soft);padding:2px 9px;border-radius:99px}
.rgrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(238px,1fr));gap:16px}
.rc{background:var(--paper);border:1px solid var(--line);border-radius:16px;overflow:hidden;box-shadow:var(--sh);transition:.18s;display:flex;flex-direction:column;color:var(--tx)}
.rc:hover{transform:translateY(-3px);box-shadow:var(--sh2);color:var(--tx)}
.rc .ph{height:128px;position:relative;overflow:hidden;background:var(--soft)}
.rc .ph img{width:100%;height:100%;object-fit:cover;transition:transform .4s}.rc:hover .ph img{transform:scale(1.06)}
.rc .ph .ty{position:absolute;top:10px;left:10px;box-shadow:0 3px 10px rgba(0,0,0,.15)}
.rc .ph .bd{position:absolute;bottom:10px;left:10px}
.rc .body{padding:13px 15px 15px;display:flex;flex-direction:column;gap:5px;flex:1}
.rc .body b{font-size:14.5px;color:var(--ink)}.rc .meta{font-size:12px;color:var(--dim)}
.rc .code{font-family:var(--fm);font-size:11px;color:var(--faint);font-weight:700;margin-top:auto;padding-top:6px}
.hscroll{display:flex;gap:14px;overflow-x:auto;padding:4px 2px 14px;scroll-snap-type:x mandatory}
.hscroll .rc{min-width:236px;scroll-snap-align:start}
.tl{position:relative;display:grid;grid-template-columns:repeat(5,1fr);margin:18px 0 6px}
.tl .track{position:absolute;left:10%;right:10%;top:7px;height:3px;background:var(--line);border-radius:3px}
.tl .track i{position:absolute;left:0;top:0;bottom:0;background:linear-gradient(90deg,var(--teal3),var(--teal));border-radius:3px;transform-origin:left;animation:tlg .8s cubic-bezier(.2,.8,.2,1)}
@keyframes tlg{from{transform:scaleX(0)}}
.tl .step{position:relative;text-align:center;padding-top:20px;font-size:10.5px;font-weight:700;color:var(--faint)}
.tl .step::before{content:'';position:absolute;top:1px;left:50%;transform:translateX(-50%);width:15px;height:15px;border-radius:50%;background:var(--paper);border:3px solid var(--line2);z-index:1}
.tl .step.done{color:var(--teal2)}.tl .step.done::before{background:var(--teal);border-color:var(--teal)}
.tl .step.cur{color:var(--ink);font-weight:800}.tl .step.cur::before{background:var(--amber);border-color:var(--amber2);box-shadow:0 0 0 5px rgba(242,183,5,.25);animation:pulse 2s infinite}
.notice{display:flex;gap:12px;align-items:flex-start;background:var(--ambersoft);border:1px solid #F0D98A;border-radius:13px;padding:13px 16px;font-size:13.5px;color:#6B4E00;margin:10px 0}
.notice.green{background:var(--greensoft);border-color:#BFE3CC;color:var(--green2)}
.notice.blue{background:var(--bluesoft);border-color:#BBD4EE;color:#1D5CA0}
.empty{border:2px dashed var(--line2);border-radius:16px;padding:40px 24px;text-align:center;color:var(--dim);background:rgba(255,255,255,.6)}
.empty b{color:var(--ink);display:block;font-size:15px;margin-bottom:4px}
.stat{background:var(--paper);border:1px solid var(--line);border-radius:16px;padding:18px 20px;box-shadow:var(--sh);position:relative;overflow:hidden;color:var(--tx);display:block}
.stat:hover{color:var(--tx);transform:translateY(-2px);box-shadow:var(--sh2)}
.stat::after{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--sc,var(--teal3))}
.stat b{display:block;font-family:var(--fd);font-size:34px;font-weight:900;color:var(--ink);line-height:1.1}
.stat span{font-size:12px;font-weight:800;color:var(--dim);letter-spacing:.03em;text-transform:uppercase}
.dashgrid{display:grid;grid-template-columns:1.6fr 1fr;gap:20px;align-items:start}
.urgent{border:1px solid var(--line);border-left:4px solid var(--amber);background:var(--paper);border-radius:13px;padding:14px 16px;margin-bottom:11px;transition:.18s}
.urgent:hover{box-shadow:var(--sh);transform:translateX(3px)}
.urgent.hot{border-left-color:var(--coral)}
.age{font-size:11px;font-weight:800;color:#8A6100;background:var(--ambersoft);padding:2px 9px;border-radius:99px}
.urgent.hot .age{color:var(--coral2);background:var(--coralsoft);animation:pulse 2s infinite}
.feed{list-style:none;position:relative;padding-left:20px}
.feed::before{content:'';position:absolute;left:5px;top:6px;bottom:6px;width:2px;background:var(--line)}
.feed li{position:relative;padding:0 0 15px 8px;font-size:13px}
.feed li::before{content:'';position:absolute;left:-19px;top:6px;width:10px;height:10px;border-radius:50%;background:var(--paper);border:2.5px solid var(--fc,var(--teal3))}
.feed li small{display:block;color:var(--faint);font-size:11px;font-weight:700}
.chart{width:100%;height:auto}.chart .bar{transform-box:fill-box;transform-origin:bottom;animation:growBar .7s cubic-bezier(.2,.8,.2,1) both}
@keyframes growBar{from{transform:scaleY(0)}}
.legend{display:flex;gap:16px;font-size:12px;font-weight:700;color:var(--dim);margin-top:8px}
.legend i{width:11px;height:11px;border-radius:3px;display:inline-block;margin-right:6px;vertical-align:-1px}
.catbar{margin-bottom:11px}.catbar .lbl{display:flex;justify-content:space-between;font-size:12.5px;font-weight:700;margin-bottom:4px}
.catbar .tr{height:9px;background:var(--soft);border-radius:99px;overflow:hidden}
.catbar .tr i{display:block;height:100%;border-radius:99px;transform-origin:left;animation:tlg .8s cubic-bezier(.2,.8,.2,1) both}
.mgrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px}
.mc{background:var(--paper);border:1px solid var(--line);border-top:3px solid var(--amber);border-radius:16px;padding:17px;box-shadow:var(--sh);display:flex;flex-direction:column;gap:9px;transition:.18s}
.mc:hover{transform:translateY(-3px);box-shadow:var(--sh2)}.mc.hot{border-top-color:var(--coral)}
.mc .pair{display:flex;gap:8px;align-items:center;font-family:var(--fm);font-size:11.5px;font-weight:700;color:var(--dim)}
.mc .thumbs{display:flex;gap:8px}.mc .thumbs img{width:50%;height:74px;object-fit:cover;border-radius:10px;border:1px solid var(--line)}
.mc .scoreline{display:flex;align-items:center;gap:10px}.mc .mini{flex:1}
.mc .mini .m{display:flex;align-items:center;gap:7px;font-size:10.5px;font-weight:700;color:var(--faint);margin-bottom:3px}
.mc .mini .m .tr{flex:1;height:5px;background:var(--soft);border-radius:99px;overflow:hidden}
.mc .mini .m .tr i{display:block;height:100%;border-radius:99px;transform-origin:left;animation:tlg .8s both}
.ring{width:64px;height:64px;flex:none}
.ring .bgc{fill:none;stroke:var(--soft);stroke-width:7}
.ring .fgc{fill:none;stroke-width:7;stroke-linecap:round;transform:rotate(-90deg);transform-origin:center;animation:ringIn 1s cubic-bezier(.2,.8,.2,1)}
@keyframes ringIn{from{stroke-dashoffset:188.5}}
.ring text{font:800 17px var(--fb);fill:var(--ink)}.ring .pct{font:700 8px var(--fb);fill:var(--faint)}
.cmp{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin:16px 0}
.cmp .side{border:1px solid var(--line);border-radius:14px;overflow:hidden;background:#FBFDFC}
.cmp .side .sh{padding:10px 15px;font-weight:800;font-size:12.5px;letter-spacing:.05em;text-transform:uppercase;display:flex;justify-content:space-between;align-items:center}
.cmp .side.lost .sh{background:var(--coralsoft);color:var(--coral2)}.cmp .side.found .sh{background:var(--greensoft);color:var(--green2)}
.cmp .side img.hero{width:100%;height:170px;object-fit:cover}
.cmp .kv{padding:12px 15px;display:grid;gap:8px}.cmp .kv .k{display:flex;gap:9px;font-size:13px}.cmp .kv .k b{color:var(--ink)}
.same{display:inline-flex;align-items:center;gap:4px;font-size:10.5px;font-weight:800;color:var(--green2);background:var(--greensoft);padding:1px 8px;border-radius:99px;margin-left:6px}
.scorebars{display:grid;gap:9px;background:var(--soft);border-radius:13px;padding:14px 16px;flex:1}
.scorebars .sb{display:grid;grid-template-columns:78px 1fr 44px;align-items:center;gap:10px;font-size:12px;font-weight:800;color:var(--dim)}
.scorebars .tr{height:8px;background:#fff;border-radius:99px;overflow:hidden;border:1px solid var(--line)}
.scorebars .tr i{display:block;height:100%;border-radius:99px;transform-origin:left;animation:tlg .9s both}
.idcard{background:linear-gradient(140deg,#1E5AA8,#2D7DD2);border-radius:16px;color:#fff;padding:18px;position:relative;overflow:hidden;box-shadow:0 12px 30px rgba(30,90,168,.35)}
.idcard::after{content:'';position:absolute;right:-40px;top:-40px;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,.09)}
.idcard .lbl{font-size:9.5px;font-weight:800;letter-spacing:.16em;opacity:.8}
.idcard .nm{font-family:var(--fd);font-size:21px;font-weight:700;margin:8px 0 2px}
.idcard .nim{font-family:var(--fm);font-size:14px;letter-spacing:.08em;color:#CFE3F8}.idcard .fak{font-size:11px;margin-top:12px;opacity:.85}
.evslot{border:1px solid var(--line);border-radius:14px;padding:15px;background:var(--paper)}
.evslot h4{font-size:13px;font-weight:800;color:var(--dim);margin-bottom:10px}
.evslot .done-img{position:relative}.evslot .done-img img{width:100%;height:150px;object-fit:cover;border-radius:11px;border:1px solid var(--line2)}
.evslot .okb{position:absolute;top:9px;right:9px;background:var(--green);color:#fff;border-radius:99px;padding:3px 11px;font-size:11px;font-weight:800}
.checkline{display:flex;gap:11px;align-items:flex-start;background:var(--bluesoft);border:1px solid #BBD4EE;border-radius:13px;padding:13px 15px;font-size:13.5px;color:#1D5CA0;font-weight:600;cursor:pointer;margin:14px 0}
input.cb{width:17px;height:17px;accent-color:var(--teal);cursor:pointer}
.ho-grid{display:grid;grid-template-columns:340px 1fr;gap:20px;align-items:start}
.receipt-item{display:flex;gap:14px;align-items:center;background:var(--paper);border:1px solid var(--line);border-radius:14px;padding:12px 15px;margin-bottom:10px;color:var(--tx);transition:.15s}
.receipt-item:hover{border-color:var(--teal3);box-shadow:var(--sh);transform:translateX(3px);color:var(--tx)}
.receipt-item.on{border-color:var(--teal);background:var(--mintsoft);box-shadow:0 0 0 3px rgba(42,157,143,.18)}
.receipt-item img{width:72px;height:54px;object-fit:cover;border-radius:9px;flex:none}
.receipt-item .in{flex:1;min-width:0}.receipt-item b{font-size:14px;display:block}
.kvgrid{display:grid;grid-template-columns:1fr 1fr;gap:11px;margin:14px 0}
.kv{background:var(--paper);border:1px solid var(--line);border-radius:12px;padding:10px 13px}
.kv span{display:block;font-size:10.5px;font-weight:800;color:var(--faint);letter-spacing:.05em;text-transform:uppercase;margin-bottom:3px}
.kv b{font-size:13.5px;color:var(--ink);overflow-wrap:anywhere}
.descbox{background:var(--paper);border:1px solid var(--line);border-left:4px solid var(--teal3);border-radius:12px;padding:13px 15px;font-size:14px;margin:12px 0}
.dhero{position:relative;border-radius:14px;overflow:hidden;margin-bottom:16px;border:1px solid var(--line)}
.dhero img{width:100%;height:210px;object-fit:cover}
.dhero .ov{position:absolute;inset:auto 0 0 0;padding:34px 16px 12px;background:linear-gradient(transparent,rgba(15,46,48,.82));color:#fff;display:flex;justify-content:space-between;align-items:flex-end;gap:8px}
.hero{display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap;margin-bottom:22px}
.hero h1 em{font-style:italic;color:var(--teal)}
.action-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:8px}
.action-card{position:relative;overflow:hidden;border-radius:20px;padding:26px;color:#fff;display:block;transition:.2s}
.action-card:hover{transform:translateY(-4px);color:#fff}
.action-card.lost{background:linear-gradient(130deg,#B23A28,var(--coral) 60%,#F08A5B);box-shadow:0 14px 34px rgba(197,58,58,.3)}
.action-card.found{background:linear-gradient(130deg,var(--green2),var(--green) 60%,#5BBE84);box-shadow:0 14px 34px rgba(31,122,67,.3)}
.action-card h3{color:#fff;font-size:22px;margin:12px 0 4px}.action-card p{color:rgba(255,255,255,.85);font-size:13.5px;max-width:300px}
.action-card .ic{width:46px;height:46px;border-radius:14px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);display:grid;place-items:center;font-size:21px}
.action-card .go{position:absolute;right:22px;bottom:22px;width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.2);display:grid;place-items:center;transition:.2s}
.action-card:hover .go{background:#fff;color:var(--ink);transform:translateX(4px)}
.action-card .deco{position:absolute;right:-30px;top:-30px;width:170px;height:170px;border-radius:50%;background:rgba(255,255,255,.09)}
.poscard{background:linear-gradient(120deg,var(--ink),var(--teal2));color:#DFF2EE;border-radius:16px;padding:18px 22px;display:flex;gap:16px;align-items:center;flex-wrap:wrap;border:1px solid rgba(127,209,192,.3)}
.poscard b{color:#fff}.poscard .pin{width:44px;height:44px;border-radius:13px;background:rgba(242,183,5,.18);border:1px solid rgba(242,183,5,.45);color:var(--amber);display:grid;place-items:center;font-size:20px;flex:none}
.status-card{background:var(--paper);border:1px solid var(--line);border-radius:16px;padding:18px 20px;box-shadow:var(--sh);margin-bottom:14px}
.tabs,.sg{display:inline-flex;background:var(--soft);border:1px solid var(--line);border-radius:99px;padding:4px;margin-bottom:18px}
.tabs a,.sg a{padding:9px 20px;border-radius:99px;font-weight:800;font-size:13.5px;color:var(--dim);transition:.2s}
.tabs a.on,.sg a.on{background:var(--paper);color:var(--teal2);box-shadow:0 2px 8px rgba(15,46,48,.12)}
.tabs a.on.lost{color:var(--coral2)}.tabs a.on.found{color:var(--green2)}
.chip{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:99px;font-size:12.5px;font-weight:700;border:1px solid var(--line2);background:var(--paper);color:var(--dim)}
.chip:hover{border-color:var(--teal3);color:var(--teal2)}.chip.on{background:var(--teal);border-color:var(--teal2);color:#fff}
.pv{max-height:150px;border-radius:12px;margin:8px 0;border:1px solid var(--line2)}
.cnt,.charcount{font-size:11px;color:var(--faint);text-align:right;font-weight:700;margin-top:4px}
.success-panel{border:1px solid #BFE3CC;background:linear-gradient(135deg,#F0FAF4,#fff);border-radius:16px;padding:20px 22px;margin-bottom:18px;animation:popIn .3s}
.success-panel .code{font-family:var(--fm);font-size:28px;font-weight:700;color:var(--teal2);letter-spacing:.06em}
details.rev{margin-top:12px;border:1px dashed var(--line2);border-radius:12px;padding:10px 14px;background:#FBFDFC}
details.rev summary{cursor:pointer;font-weight:800;font-size:13px;color:var(--dim)}
.auth{min-height:100vh;display:grid;grid-template-columns:1.12fr 1fr}
.auth-brand{position:relative;overflow:hidden;background:linear-gradient(150deg,var(--ink),var(--teal2) 46%,var(--teal) 78%,#1B7F72);color:#fff;padding:52px 56px;display:flex;flex-direction:column;justify-content:center}
.auth-brand .mesh{position:absolute;inset:0;pointer-events:none}
.auth-brand .mesh i{position:absolute;border-radius:50%;filter:blur(70px)}
.auth-brand .m1{width:420px;height:420px;background:#2A9D8F;opacity:.4;left:-100px;top:-80px;animation:drift1 18s ease-in-out infinite alternate}
.auth-brand .m2{width:380px;height:380px;background:#F2B705;opacity:.16;right:-80px;bottom:-60px;animation:drift2 22s ease-in-out infinite alternate}
.auth-brand .m3{width:300px;height:300px;background:#2D7DD2;opacity:.2;left:34%;bottom:16%;animation:drift1 26s ease-in-out infinite alternate-reverse}
.auth-brand>*{position:relative}
.auth-brand .logo{display:flex;align-items:center;gap:12px;margin-bottom:40px}
.auth-brand .logo img{height:44px}.auth-brand .logo b{font-family:var(--fd);font-size:24px;font-weight:900}
.auth-brand h1{color:#fff;font-size:clamp(32px,4vw,52px);font-weight:900;line-height:1.06;margin:0 0 18px;max-width:560px}
.auth-brand h1 em{font-style:italic;color:var(--amber)}
.auth-brand .lead{color:#C7DED9;font-size:16px;max-width:480px;margin-bottom:32px}
.auth-stats{display:flex;gap:14px;flex-wrap:wrap;margin-bottom:30px}
.astat{background:rgba(255,255,255,.07);border:1px solid rgba(127,209,192,.28);border-radius:14px;padding:13px 18px}
.astat b{display:block;font-family:var(--fd);font-size:26px;font-weight:900;color:var(--mint)}
.astat span{font-size:11.5px;color:#A9C8C2;font-weight:700;letter-spacing:.04em;text-transform:uppercase}
.ticker{display:flex;align-items:center;gap:10px;font-size:13px;color:#DFF2EE;background:rgba(15,46,48,.4);border:1px solid rgba(127,209,192,.25);padding:10px 16px;border-radius:99px;width:max-content;max-width:100%}
.ticker .dot{width:8px;height:8px;border-radius:50%;background:var(--amber);animation:pulse 1.8s infinite;flex:none}
.float-card{position:absolute;background:rgba(255,255,255,.96);border-radius:14px;padding:9px 12px 9px 9px;display:flex;align-items:center;gap:10px;box-shadow:0 18px 44px rgba(0,0,0,.35);color:var(--tx);animation:floaty 7s ease-in-out infinite}
.float-card img{width:52px;height:40px;object-fit:cover;border-radius:8px}
.float-card b{font-size:12.5px;display:block;line-height:1.2}.float-card span{font-size:10.5px;font-weight:800}
.fc1{right:8%;top:12%}.fc2{right:16%;top:44%;animation-delay:-2.4s}.fc3{right:5%;bottom:12%;animation-delay:-4.8s}
@keyframes floaty{0%,100%{transform:translateY(0) rotate(-1.5deg)}50%{transform:translateY(-16px) rotate(1.5deg)}}
.auth-panel{display:flex;align-items:center;justify-content:center;padding:44px 30px}
.auth-card{width:min(430px,100%);animation:pageIn .5s ease}
.auth-card .hd{text-align:center;margin-bottom:22px}.auth-card .hd h2{font-size:27px;margin:0 0 4px}
.seg{display:flex;background:var(--soft);border-radius:99px;padding:4px;margin-bottom:20px;border:1px solid var(--line)}
.seg label{flex:1;cursor:pointer}.seg input{position:absolute;opacity:0;pointer-events:none}
.seg span{display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;border-radius:99px;font-weight:800;font-size:13.5px;color:var(--dim);transition:.2s}
.seg input:checked+span{background:var(--paper);color:var(--teal2);box-shadow:0 2px 8px rgba(15,46,48,.12)}
.seg input:focus-visible+span{outline:3px solid rgba(42,157,143,.55)}
.passwrap{position:relative}.passwrap input{padding-right:46px}
.passwrap .eye{position:absolute;right:6px;top:50%;transform:translateY(-50%);width:36px;height:36px;border:0;background:none;color:var(--faint);display:grid;place-items:center;border-radius:9px;font-size:16px}
.passwrap .eye:hover{color:var(--teal);background:var(--soft)}
.autherr{background:var(--coralsoft);border:1px solid #EFC4B8;color:var(--coral2);font-size:13px;font-weight:700;padding:10px 14px;border-radius:11px;margin-bottom:14px;animation:shake .4s}
@keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-7px)}75%{transform:translateX(7px)}}
.demo-row{display:flex;gap:10px;margin-top:16px}.demo-row form{flex:1}.demo-row .btn{width:100%;font-size:12.5px;padding:9px 8px}
.divid{display:flex;align-items:center;gap:12px;color:var(--faint);font-size:11.5px;font-weight:700;margin:18px 0 0;letter-spacing:.08em}
.divid::before,.divid::after{content:'';flex:1;height:1px;background:var(--line2)}
#toasts{position:fixed;bottom:22px;left:50%;transform:translateX(-50%);z-index:120;display:flex;flex-direction:column;gap:10px;width:min(480px,calc(100vw - 32px))}
.toast{display:flex;align-items:center;gap:12px;background:var(--ink);color:#fff;border-radius:14px;padding:13px 16px;box-shadow:0 16px 44px rgba(15,46,48,.45);animation:toastIn .3s cubic-bezier(.2,1.2,.3,1);border:1px solid rgba(127,209,192,.3);position:relative;overflow:hidden}
@keyframes toastIn{from{opacity:0;transform:translateY(20px) scale(.95)}}
.toast.out{transition:.25s;opacity:0;transform:translateY(14px)}
.toast .tic{width:32px;height:32px;border-radius:9px;display:grid;place-items:center;flex:none;font-size:15px}
.toast.ok .tic{background:rgba(46,158,91,.25);color:#7BE3A5}.toast.er .tic{background:rgba(214,69,69,.25);color:#FF9D8A}
.toast.wn .tic{background:rgba(242,183,5,.25);color:#FFD34D}
.toast p{flex:1;font-size:13.5px;font-weight:600;line-height:1.4}
.toast .tx{border:0;background:none;color:#8FA8A5;flex:none;font-size:15px}.toast .tx:hover{color:#fff}
.toast .prog{position:absolute;left:0;bottom:0;height:3px;background:linear-gradient(90deg,var(--teal3),var(--amber));animation:prog 5s linear forwards}
@keyframes prog{from{width:100%}to{width:0}}
.modal-wrap{position:fixed;inset:0;z-index:130;display:none;place-items:center;padding:20px;background:rgba(15,46,48,.5);backdrop-filter:blur(3px)}
.modal-wrap.open{display:grid}
.modal{background:var(--bg);border-radius:20px;width:min(440px,100%);box-shadow:0 30px 80px rgba(15,46,48,.45);animation:popIn .22s;border:1px solid var(--line);padding:28px 26px 22px;text-align:center}
.modal .mic{width:56px;height:56px;border-radius:16px;display:grid;place-items:center;margin:0 auto 14px;font-size:26px;background:var(--coralsoft);color:var(--coral2)}
.modal h3{font-size:19px;margin-bottom:8px}.modal p{color:var(--dim);font-size:14px;margin-bottom:18px}
.pager{display:flex;gap:8px;align-items:center;justify-content:center;margin-top:20px;flex-wrap:wrap}
.pager .info{font-size:12.5px;color:var(--dim);font-weight:700}
@media(max-width:1080px){.dashgrid,.ho-grid{grid-template-columns:1fr}.g4{grid-template-columns:repeat(2,1fr)}.fc2{display:none}}
@media(max-width:880px){:root{--top:106px}.top{flex-wrap:wrap;height:var(--top);padding:10px 16px 0;align-content:flex-start;gap:8px}.nav{order:3;width:100%;padding-bottom:8px}.topact{margin-left:auto}.uchip .un,.uchip .ur{display:none}.uchip{padding:5px}.auth{grid-template-columns:1fr}.auth-brand{padding:36px 26px}.auth-brand h1{font-size:30px}.float-card{display:none}.auth-panel{padding:30px 18px 50px}.cmp{grid-template-columns:1fr}.action-grid{grid-template-columns:1fr}.g3{grid-template-columns:1fr 1fr}}
@media(max-width:620px){.wrap{padding-left:14px;padding-right:14px}.g2,.g3,.g4,.kvgrid{grid-template-columns:1fr}.brand .tag{display:none}.tl .step{font-size:9px}.demo-row{flex-direction:column}.toolbar .fl{min-width:100%}}
@media(prefers-reduced-motion:reduce){*,*::before,*::after{animation:none!important;transition:none!important}}
</style>
@stack('css')
</head>
<body>
<script>document.documentElement.classList.add('js')</script>
<div id="bg"><i class="b1"></i><i class="b2"></i><i class="b3"></i><i class="grain"></i></div>

@auth
@php $u = auth()->user(); $isSec = $u->isSecurity();
  $notifs = $u->notifications()->orderByDesc('createdAt')->limit(6)->get();
  $unread = $u->notifications()->where('isRead', false)->count(); @endphp
<header class="top" id="topbar">
  <a class="brand" href="{{ route($isSec ? 'dashboard' : 'beranda') }}">
    <img src="{{ asset('img/logo.svg') }}" alt="Findkom"><b>Findkom</b><span class="tag">FILKOM UB</span>
  </a>
  <nav class="nav">
    @if ($isSec)
      <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'on' : '' }}">Dashboard</a>
      <a href="{{ route('match.index') }}" class="{{ request()->routeIs('match.*') ? 'on' : '' }}">Kandidat Kecocokan</a>
      <a href="{{ route('receipt.index') }}" class="{{ request()->routeIs('receipt.*') ? 'on' : '' }}">Konfirmasi Penerimaan</a>
      <a href="{{ route('handover.index') }}" class="{{ request()->routeIs('handover.*') ? 'on' : '' }}">Serah Terima</a>
      <a href="{{ route('laporan.semua') }}" class="{{ request()->routeIs('laporan.semua') ? 'on' : '' }}">Semua Laporan</a>
      <a href="{{ route('master.index') }}" class="{{ request()->routeIs('master.*') ? 'on' : '' }}">Master Data</a>
    @else
      <a href="{{ route('beranda') }}" class="{{ request()->routeIs('beranda') ? 'on' : '' }}">Beranda</a>
      <a href="{{ route('kehilangan.form') }}" class="{{ request()->routeIs('kehilangan.*', 'penemuan.*') ? 'on' : '' }}">Buat Laporan</a>
      <a href="{{ route('laporan.status') }}" class="{{ request()->routeIs('laporan.status') ? 'on' : '' }}">Status &amp; Riwayat</a>
      <a href="{{ route('laporan.cari') }}" class="{{ request()->routeIs('laporan.cari') ? 'on' : '' }}">Cari Barang</a>
    @endif
  </nav>
  <div class="topact">
    <button class="bell" id="bellBtn" aria-label="Notifikasi" aria-live="polite">🔔@if ($unread)<span class="n">{{ $unread }}</span>@endif</button>
    <div class="uchip">
      <span class="ava {{ $isSec ? 'sec' : '' }}">{{ $u->initials }}</span>
      <span><span class="un">{{ explode(' ', $u->namaLengkap)[0] }}</span><br><span class="ur">{{ $isSec ? 'Security' : 'Civitas' }}</span></span>
      <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn xs gh" style="color:#C7DED9">Keluar</button></form>
    </div>
    <div class="npanel" id="npanel">
      <div class="nh"><b style="font-size:14px">Notifikasi</b>
        <form method="POST" action="{{ route('notifikasi.bacaSemua') }}">@csrf<button class="btn xs">Tandai semua dibaca</button></form></div>
      <div id="nlist">
        @forelse ($notifs as $n)
          <a class="notif {{ $n->isRead ? '' : 'unread' }}" href="{{ route('notifikasi.baca', $n->id) }}">
            <div><p>{{ $n->message }}</p><small>{{ \Illuminate\Support\Carbon::parse($n->createdAt)->diffForHumans() }}</small></div></a>
        @empty <div class="empty" style="padding:20px"><b>Belum ada notifikasi</b></div> @endforelse
      </div>
    </div>
  </div>
</header>
@endauth

<main>
  @hasSection('full') @yield('full')
  @else <div class="wrap">@yield('konten')</div> @endif
</main>

@auth
<footer class="foot">
  <span><b class="mono" style="color:var(--teal)">FINDKOM</b> © {{ date('Y') }} — Kelompok 4 RSI • FILKOM Universitas Brawijaya</span>
  <span>📍 Pos Security Gedung F • 07.00–21.00 WIB</span>
</footer>
@endauth

<div id="toasts">
  @foreach (['success' => 'ok', 'warning' => 'wn', 'error' => 'er'] as $k => $c)
    @if (session($k))
      <div class="toast {{ $c }}"><span class="tic">{{ $c === 'ok' ? '✓' : ($c === 'wn' ? '!' : '✕') }}</span><p>{{ session($k) }}</p><button class="tx" aria-label="Tutup">✕</button><i class="prog"></i></div>
    @endif
  @endforeach
  @if ($errors->any())
    <div class="toast er"><span class="tic">✕</span><p>Periksa kembali isian form — ada data yang belum valid.</p><button class="tx">✕</button><i class="prog"></i></div>
  @endif
</div>

<div class="modal-wrap" id="confirmWrap">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
    <div class="mic">!</div><h3 id="confirmTitle">Yakin lanjutkan?</h3><p id="confirmMsg"></p>
    <div class="row" style="justify-content:center">
      <button class="btn" id="confirmNo">Batal</button><button class="btn dn" id="confirmYes">Ya, lanjutkan</button>
    </div>
  </div>
</div>

<script>
(function () {
  const $ = (s, r = document) => r.querySelector(s), $$ = (s, r = document) => [...r.querySelectorAll(s)];

  /* ---------- toast ---------- */
  const kill = t => { t.classList.add('out'); setTimeout(() => t.remove(), 260); };
  const pasangToast = t => { const timer = setTimeout(() => kill(t), 5000);
    t.querySelector('.tx').onclick = () => { clearTimeout(timer); kill(t); }; };
  const buatToast = (pesan, jenis = 'ok') => {
    const t = document.createElement('div'); t.className = 'toast ' + jenis;
    const ic = document.createElement('span'); ic.className = 'tic'; ic.textContent = jenis === 'ok' ? '✓' : (jenis === 'wn' ? '!' : '✕');
    const p = document.createElement('p'); p.textContent = pesan;
    const x = document.createElement('button'); x.className = 'tx'; x.textContent = '✕'; x.setAttribute('aria-label', 'Tutup');
    const pr = document.createElement('i'); pr.className = 'prog';
    t.append(ic, p, x, pr); $('#toasts').appendChild(t); pasangToast(t);
  };
  $$('#toasts .toast').forEach(pasangToast);

  /* ---------- reveal & count-up ---------- */
  const io = 'IntersectionObserver' in window ? new IntersectionObserver(es => es.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }), { threshold: .08 }) : null;
  $$('.reveal').forEach(el => io ? io.observe(el) : el.classList.add('in'));
  $$('[data-count]').forEach(el => { const t = +el.dataset.count, t0 = performance.now();
    const step = n => { const p = Math.min(1, (n - t0) / 850); el.textContent = Math.round(t * (1 - Math.pow(1 - p, 3)));
      if (p < 1) requestAnimationFrame(step); }; requestAnimationFrame(step); });

  /* ---------- dialog konfirmasi ---------- */
  let pending = null; const wrap = $('#confirmWrap');
  $$('form[data-confirm]').forEach(f => f.addEventListener('submit', e => {
    if (f.dataset.ok) return; e.preventDefault(); pending = f;
    $('#confirmMsg').textContent = f.dataset.confirm; wrap.classList.add('open'); $('#confirmYes').focus(); }));
  $('#confirmYes').onclick = () => { wrap.classList.remove('open'); if (pending) { pending.dataset.ok = '1'; pending.submit(); } };
  $('#confirmNo').onclick = () => wrap.classList.remove('open');
  wrap.addEventListener('click', e => { if (e.target === wrap) wrap.classList.remove('open'); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') { wrap.classList.remove('open'); $('#npanel')?.classList.remove('open'); } });

  /* ---------- kompresi foto (NFR-SRS-02) + pratinjau ---------- */
  async function kecilkan(file) {
    if (!/^image\/(jpeg|png)$/.test(file.type) || typeof createImageBitmap !== 'function') return file;
    let bmp = null;
    try { bmp = await createImageBitmap(file); } catch (e) { return file; }
    const max = 1280, k = Math.min(1, max / Math.max(bmp.width, bmp.height));
    if (k === 1 && file.size <= 400 * 1024) { bmp.close?.(); return file; }
    const c = document.createElement('canvas');
    c.width = Math.round(bmp.width * k); c.height = Math.round(bmp.height * k);
    c.getContext('2d').drawImage(bmp, 0, 0, c.width, c.height); bmp.close?.();
    const blob = await new Promise(r => c.toBlob(r, 'image/jpeg', .85));
    if (!blob || blob.size >= file.size) return file;
    return new File([blob], file.name.replace(/\.\w+$/, '') + '.jpg', { type: 'image/jpeg', lastModified: Date.now() });
  }
  $$('input[data-photo]').forEach(inp => inp.addEventListener('change', async () => {
    const img = document.getElementById(inp.dataset.photo);
    let f = inp.files[0];
    if (!f) { if (img) img.hidden = true; return; }
    const asli = f.size;
    try {
      const kecil = await kecilkan(f);
      if (kecil !== f && typeof DataTransfer === 'function') {
        const dt = new DataTransfer(); dt.items.add(kecil); inp.files = dt.files; f = kecil;
        buatToast(`Foto diperkecil otomatis: ${Math.round(asli / 1024)} KB → ${Math.round(f.size / 1024)} KB.`, 'ok');
      }
    } catch (e) { /* pakai berkas asli bila peramban tidak mendukung */ }
    if (f.size > 2 * 1024 * 1024) { buatToast('Ukuran foto maksimal 2 MB.', 'er'); inp.value = ''; if (img) img.hidden = true; return; }
    if (img) { img.src = URL.createObjectURL(f); img.hidden = false; }
  }));

  /* ---------- penghitung karakter & lihat sandi ---------- */
  $$('textarea[data-count]').forEach(d => { const c = document.getElementById(d.dataset.count);
    const up = () => { if (c) c.textContent = d.value.length; }; d.addEventListener('input', up); up(); });
  $$('.eye').forEach(b => b.addEventListener('click', () => { const i = b.parentElement.querySelector('input');
    const show = i.type === 'password'; i.type = show ? 'text' : 'password'; b.textContent = show ? '🙈' : '👁'; }));

  /* ---------- filter otomatis ---------- */
  $$('[data-auto]').forEach(el => el.addEventListener('change', () => el.form.submit()));
  let deb; $$('[data-live]').forEach(el => el.addEventListener('input', () => {
    clearTimeout(deb); deb = setTimeout(() => el.form.submit(), 450); }));
  const ph = { CIVITAS: 'nama@student.ub.ac.id', SECURITY: 'nama@ub.ac.id' };
  $$('[name=peran]').forEach(r => r.addEventListener('change', () => {
    const em = $('#li-email'); if (em) em.placeholder = ph[r.value]; }));

  /* ---------- akses keyboard untuk baris tabel (NFR-SRS-06) ---------- */
  $$('tr.cl[onclick]').forEach(tr => {
    tr.tabIndex = 0; tr.setAttribute('role', 'link');
    tr.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); tr.click(); } });
  });

  /* ---------- lonceng notifikasi ---------- */
  const bell = $('#bellBtn'), panel = $('#npanel');
  bell?.addEventListener('click', e => { e.stopPropagation(); panel.classList.toggle('open'); });
  document.addEventListener('click', e => { if (panel && !e.target.closest('#npanel') && !e.target.closest('#bellBtn')) panel.classList.remove('open'); });
  addEventListener('scroll', () => $('#topbar')?.classList.toggle('scrolled', scrollY > 8), { passive: true });

  /* ---------- gerbang serah terima ---------- */
  const chk = $('#ktm-check'), closeBtn = $('#close-btn');
  const gate = () => { if (closeBtn && chk) closeBtn.disabled = !(closeBtn.dataset.bukti === '1' && chk.checked); };
  chk?.addEventListener('change', gate); gate();

  /* ---------- pembaruan langsung (PKB-SRS-04 / PPB-SRS-09) ---------- */
  const liveUrl = document.querySelector('meta[name="ringkas-url"]')?.content;
  const pasangBadge = n => { const b = $('#bellBtn'); if (!b) return; let e = b.querySelector('.n');
    if (n > 0) { if (!e) { e = document.createElement('span'); e.className = 'n'; b.appendChild(e); } e.textContent = n; }
    else e?.remove(); };
  const gambarNotif = list => { const w = $('#nlist'); if (!w) return; w.innerHTML = '';
    if (!list.length) { const d = document.createElement('div'); d.className = 'empty'; d.style.padding = '20px';
      const b = document.createElement('b'); b.textContent = 'Belum ada notifikasi'; d.appendChild(b); w.appendChild(d); return; }
    list.forEach(n => { const a = document.createElement('a'); a.className = 'notif' + (n.baru ? ' unread' : ''); a.href = n.url;
      const d = document.createElement('div'), p = document.createElement('p'), s = document.createElement('small');
      p.textContent = n.pesan; s.textContent = n.waktu; d.append(p, s); a.appendChild(d); w.appendChild(a); }); };
  let pernahSegarkan = false;
  async function segarkan() {
    if (!liveUrl || document.hidden) return;
    try {
      const r = await fetch(liveUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
      if (!r.ok) return;
      const d = await r.json();
      pasangBadge(d.unread); gambarNotif(d.notifikasi || []);
      if (d.stat) Object.entries(d.stat).forEach(([k, v]) => {
        const el = document.querySelector(`[data-stat="${k}"]`); if (el) el.textContent = v; });
      let berubah = false;
      (d.laporan || []).forEach(l => {
        const el = document.querySelector(`[data-status-badge="${l.kode}"]`);
        if (el && el.dataset.status !== l.status) {
          el.dataset.status = l.status; el.className = 'bd ' + l.badge + ' flash'; el.textContent = l.label; berubah = true;
        }
      });
      if (berubah && pernahSegarkan) buatToast('Status laporanmu diperbarui otomatis.', 'ok');
      pernahSegarkan = true;
    } catch (e) { /* jaringan kampus tidak stabil: coba lagi siklus berikutnya */ }
  }
  if (liveUrl) {
    setInterval(segarkan, 25000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) segarkan(); });
  }
})();
</script>
@stack('skrip')
</body>
</html>