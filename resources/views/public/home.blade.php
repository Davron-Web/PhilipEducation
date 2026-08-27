<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Philip Education — Академия английского будущего</title>
<meta name="description" content="Philip Education — платформа для изучения английского языка: уровни от A1 до C2, ИИ-наставник Phil, живой словарь и геймификация.">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@500;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>

<style>
/* ══════════════ ПАЛИТРА ЛЕНДИНГА (только эта страница — гостевая
   главная; остальной сайт остаётся на сине-голубой палитре) ══════════════ */
:root{
  --royal:    #5D2B7D;
  --gold:     #FFD700;
  --armor:    #1E222A;
  --cyan:     #00FFFF;
  --teal:     #4DEEEA;
  --steel:    #5C6B73;
  --electric: #00E5FF;
  --armor-2:  #252B36;
  --ink:      #EAF0F6;
  --muted:    #9AA7B2;
  --line:     rgba(92,107,115,.35);
  --line-cyan:rgba(0,255,255,.22);
  --line-gold:rgba(255,215,0,.35);
  --font-display:'Unbounded',system-ui,sans-serif;
  --font-body:'Manrope',system-ui,sans-serif;
}
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
body{font-family:var(--font-body);background:var(--armor);color:var(--ink);overflow-x:hidden;line-height:1.6}
::selection{background:var(--royal);color:#fff}
img{max-width:100%;display:block}
a{text-decoration:none;color:inherit}
.container{width:min(1200px,92%);margin:0 auto}

#scrollProgress{position:fixed;top:0;left:0;height:3px;width:0;z-index:120;
  background:linear-gradient(90deg,var(--gold),var(--cyan),var(--electric));box-shadow:0 0 12px rgba(0,255,255,.55)}

.site-header{position:fixed;inset:0 0 auto 0;z-index:100;background:rgba(30,34,42,.72);
  backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border-bottom:1px solid var(--line-cyan);
  transition:box-shadow .3s,background .3s}
.site-header.scrolled{background:rgba(30,34,42,.92);box-shadow:0 10px 40px -12px rgba(0,0,0,.7),0 0 24px -12px rgba(0,255,255,.25)}
.header-inner{display:flex;align-items:center;gap:28px;padding:16px 0}
.logo{display:flex;align-items:center;gap:12px;margin-right:auto}
.logo-badge{width:42px;height:42px;border-radius:12px;display:grid;place-items:center;
  background:linear-gradient(135deg,#FFE75E,var(--gold) 60%,#E0B400);box-shadow:0 0 18px rgba(255,215,0,.5)}
.logo-text{font-family:var(--font-display);font-weight:800;font-size:16px;letter-spacing:.02em;color:var(--gold);text-shadow:0 0 18px rgba(255,215,0,.45)}
.logo-text small{display:block;font-family:var(--font-body);font-weight:600;font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:var(--teal)}
.main-nav{display:flex;gap:26px}
.main-nav a{font-weight:700;font-size:14.5px;color:var(--muted);position:relative;transition:color .25s}
.main-nav a::after{content:'';position:absolute;left:0;bottom:-6px;height:2px;width:0;
  background:linear-gradient(90deg,var(--cyan),var(--electric));box-shadow:0 0 8px var(--cyan);transition:width .3s}
.main-nav a:hover{color:var(--cyan)}
.main-nav a:hover::after{width:100%}

.btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;border-radius:14px;
  font-family:var(--font-body);font-weight:800;cursor:pointer;border:none;transition:all .28s;letter-spacing:.02em}
.btn-neon{padding:11px 22px;font-size:14px;color:var(--cyan);background:rgba(0,255,255,.05);
  border:1px solid var(--cyan);box-shadow:0 0 14px rgba(0,255,255,.18),inset 0 0 14px rgba(0,255,255,.08)}
.btn-neon:hover{background:rgba(0,255,255,.14);box-shadow:0 0 26px rgba(0,255,255,.45);transform:translateY(-2px)}
.btn-gold{padding:16px 34px;font-size:16px;color:#171325;
  background:linear-gradient(180deg,#FFE75E 0%,var(--gold) 55%,#E0B400 100%);
  box-shadow:0 0 26px rgba(255,215,0,.4),0 12px 30px -8px rgba(0,0,0,.6)}
.btn-gold:hover{transform:translateY(-3px);box-shadow:0 0 44px rgba(255,215,0,.65)}
.btn-ghost{padding:15px 30px;font-size:16px;color:var(--teal);background:transparent;border:1px solid rgba(77,238,234,.4)}
.btn-ghost:hover{border-color:var(--teal);background:rgba(77,238,234,.08);box-shadow:0 0 22px rgba(77,238,234,.3)}

.burger{display:none;width:44px;height:44px;border:1px solid var(--line-cyan);border-radius:12px;background:transparent;cursor:pointer;flex-direction:column;align-items:center;justify-content:center;gap:5px}
.burger span{width:20px;height:2px;background:var(--cyan);box-shadow:0 0 6px var(--cyan);transition:.3s}
.burger.open span:nth-child(1){transform:translateY(7px) rotate(45deg)}
.burger.open span:nth-child(2){opacity:0}
.burger.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}

.hero{position:relative;padding:170px 0 110px;overflow:hidden}
.hero::before{content:'';position:absolute;inset:0;
  background:radial-gradient(700px 420px at 12% 8%,rgba(93,43,125,.35),transparent 60%),
    radial-gradient(640px 420px at 88% 30%,rgba(0,229,255,.12),transparent 60%),
    radial-gradient(520px 380px at 50% 100%,rgba(93,43,125,.22),transparent 65%)}
#particleCanvas{position:absolute;inset:0;z-index:1;pointer-events:none}
.hero-inner{position:relative;z-index:2;display:grid;grid-template-columns:1.05fr .95fr;gap:56px;align-items:center}
.hero-badge{display:inline-flex;align-items:center;gap:10px;padding:9px 18px;border-radius:999px;
  border:1px solid var(--line-gold);background:rgba(255,215,0,.06);font-size:12px;font-weight:800;letter-spacing:.16em;text-transform:uppercase;color:var(--gold)}
.hero-badge i{width:8px;height:8px;border-radius:50%;background:var(--cyan);box-shadow:0 0 10px var(--cyan);animation:pulse 2s infinite}
@keyframes pulse{50%{opacity:.35}}
.hero h1{font-family:var(--font-display);font-weight:800;font-size:clamp(2rem,4.6vw,3.6rem);line-height:1.14;margin:26px 0 20px;letter-spacing:-.01em}
.grad-gold{background:linear-gradient(92deg,var(--gold) 10%,#FFF3A0 50%,var(--gold) 90%);-webkit-background-clip:text;background-clip:text;color:transparent}
.grad-cyan{background:linear-gradient(92deg,var(--cyan),var(--electric) 60%,var(--teal));-webkit-background-clip:text;background-clip:text;color:transparent}
.hero-sub{max-width:520px;color:var(--muted);font-size:17px;font-weight:500;margin-bottom:34px}
.hero-sub b{color:var(--teal);font-weight:700}
.hero-actions{display:flex;gap:16px;flex-wrap:wrap;margin-bottom:44px}
.hero-stats{display:flex;gap:38px;flex-wrap:wrap}
.hstat{position:relative;padding-left:18px}
.hstat::before{content:'';position:absolute;left:0;top:6px;bottom:6px;width:2px;background:linear-gradient(180deg,var(--cyan),transparent)}
.hstat b{display:block;font-family:var(--font-display);font-size:24px;font-weight:700;color:var(--ink)}
.hstat span{font-size:12.5px;color:var(--muted);font-weight:600;letter-spacing:.06em;text-transform:uppercase}

.hero-art{position:relative;display:flex;justify-content:center}
.hero-art .aura{position:absolute;top:50%;left:50%;width:460px;height:460px;transform:translate(-50%,-50%);border-radius:50%;pointer-events:none;
  background:radial-gradient(circle,rgba(0,229,255,.22) 0%,rgba(93,43,125,.28) 42%,transparent 70%);filter:blur(6px);animation:auraPulse 5s ease-in-out infinite}
@keyframes auraPulse{50%{transform:translate(-50%,-50%) scale(1.07);opacity:.8}}
.hero-art .ring{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);border-radius:50%;border:1px dashed rgba(0,255,255,.35);animation:spin 40s linear infinite}
.ring.r1{width:410px;height:410px}
.ring.r2{width:490px;height:490px;border-color:rgba(255,215,0,.22);animation-direction:reverse;animation-duration:60s}
@keyframes spin{to{transform:translate(-50%,-50%) rotate(360deg)}}
.mentor-badge{position:relative;z-index:2;width:min(360px,80%);aspect-ratio:1;border-radius:50%;
  background:radial-gradient(circle at 35% 30%,#2C3340,var(--armor) 70%);
  border:1px solid var(--line-cyan);box-shadow:0 0 0 1px rgba(0,255,255,.15),0 0 60px -10px rgba(0,229,255,.35),0 30px 70px -20px rgba(0,0,0,.8);
  display:grid;place-items:center}
.mentor-badge svg{filter:drop-shadow(0 0 20px rgba(255,215,0,.5))}
.rune{position:absolute;z-index:3;font-size:20px;color:var(--cyan);text-shadow:0 0 12px var(--cyan);animation:floatRune 7s ease-in-out infinite;pointer-events:none}
.rune.gold{color:var(--gold);text-shadow:0 0 12px rgba(255,215,0,.8)}
@keyframes floatRune{0%,100%{transform:translateY(0);opacity:.85}50%{transform:translateY(-18px);opacity:.4}}

section{position:relative;padding:96px 0}
.ornament{display:flex;align-items:center;gap:16px;margin-bottom:22px}
.ornament i{flex:1;height:1px;background:linear-gradient(90deg,transparent,var(--line-cyan))}
.ornament i:last-child{background:linear-gradient(90deg,var(--line-cyan),transparent)}
.ornament b{width:10px;height:10px;transform:rotate(45deg);background:var(--gold);box-shadow:0 0 12px rgba(255,215,0,.7)}
.sec-kicker{font-size:12px;font-weight:800;letter-spacing:.3em;text-transform:uppercase;color:var(--teal);text-align:center}
.sec-title{font-family:var(--font-display);font-weight:700;font-size:clamp(1.6rem,3.4vw,2.5rem);text-align:center;margin:10px 0 14px}
.sec-title .gold{color:var(--gold)}
.sec-title .cyan{color:var(--cyan)}
.sec-sub{text-align:center;color:var(--muted);max-width:640px;margin:0 auto 56px;font-size:16px}
.reveal{opacity:0;transform:translateY(34px);transition:opacity .7s ease,transform .7s cubic-bezier(.22,1,.36,1)}
.reveal.visible{opacity:1;transform:none}

.levels-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;align-items:stretch}
.level-card{position:relative;display:flex;flex-direction:column;padding:30px 26px;border-radius:22px;
  background:linear-gradient(180deg,var(--armor-2),var(--armor));border:1px solid var(--line);transition:all .35s cubic-bezier(.22,1,.36,1)}
.level-card:hover{transform:translateY(-8px);border-color:var(--cyan);box-shadow:0 0 0 1px rgba(0,255,255,.35),0 0 36px -6px rgba(0,255,255,.3),0 26px 60px -20px rgba(0,0,0,.8)}
.level-card.featured{border-color:var(--line-gold);background:linear-gradient(180deg,#2A2438,var(--armor))}
.level-card.featured:hover{border-color:var(--gold);box-shadow:0 0 0 1px rgba(255,215,0,.5),0 0 52px -6px rgba(255,215,0,.4)}
.level-sigil{width:56px;height:56px;border-radius:16px;display:grid;place-items:center;margin-bottom:16px;background:rgba(92,107,115,.14);border:1px solid var(--line)}
.level-code{font-size:11px;font-weight:800;letter-spacing:.24em;text-transform:uppercase;color:var(--teal)}
.level-card h3{font-family:var(--font-display);font-size:19px;font-weight:700;margin:4px 0 8px}
.level-card p{color:var(--muted);font-size:13.5px;flex:1;margin-bottom:16px}
.level-lessons{font-size:12.5px;font-weight:700;color:#C6D2DB;margin-bottom:18px}

.perks-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
.perk{padding:30px 28px;border-radius:20px;background:rgba(37,43,54,.7);border:1px solid var(--line);transition:all .3s}
.perk:hover{transform:translateY(-6px);border-color:var(--line-cyan);box-shadow:0 0 30px -6px rgba(0,255,255,.25)}
.perk-icon{width:56px;height:56px;border-radius:16px;display:grid;place-items:center;margin-bottom:20px;background:rgba(92,107,115,.16);border:1px solid rgba(92,107,115,.4);transition:all .3s}
.perk:hover .perk-icon{border-color:var(--cyan);box-shadow:0 0 20px rgba(0,255,255,.3)}
.perk h3{font-family:var(--font-display);font-size:17px;font-weight:700;margin-bottom:10px}
.perk p{color:var(--muted);font-size:14px}

.reviews-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:26px}
.review-card{padding:32px 28px;border-radius:22px;background:linear-gradient(180deg,var(--armor-2),var(--armor));border:1px solid var(--line);display:flex;flex-direction:column;gap:18px;transition:all .3s}
.review-card:hover{transform:translateY(-6px);border-color:var(--line-gold);box-shadow:0 0 30px -8px rgba(255,215,0,.25)}
.stars span{color:var(--gold);font-size:17px;text-shadow:0 0 10px rgba(255,215,0,.65)}
.review-card blockquote{color:#C9D4DD;font-size:14.5px;font-weight:500;flex:1}
.review-card blockquote b{color:var(--teal)}
.review-user{display:flex;align-items:center;gap:14px}
.avatar{width:46px;height:46px;border-radius:50%;display:grid;place-items:center;font-weight:800;font-size:15px;color:#fff;border:1px solid rgba(255,255,255,.25)}
.av-1{background:linear-gradient(135deg,var(--royal),#8A4BB8)}
.av-2{background:linear-gradient(135deg,#0E7C8C,var(--electric))}
.av-3{background:linear-gradient(135deg,#8A6A00,var(--gold));color:#171325}
.review-user b{display:block;font-size:14.5px}
.review-user span{font-size:12px;color:var(--steel);font-weight:700;letter-spacing:.06em;text-transform:uppercase}

.cta-band{padding:70px 0}
.cta-inner{position:relative;overflow:hidden;border-radius:26px;padding:56px 40px;text-align:center;
  background:linear-gradient(135deg,var(--royal) 0%,#3A1B52 55%,var(--armor) 100%);border:1px solid var(--line-gold);
  box-shadow:0 0 60px -12px rgba(93,43,125,.7)}
.cta-inner h2{font-family:var(--font-display);font-size:clamp(1.5rem,3vw,2.3rem);margin-bottom:12px}
.cta-inner p{color:#CBB8DE;max-width:520px;margin:0 auto 30px}

.site-footer{background:var(--armor);border-top:1px solid var(--line-cyan);padding:64px 0 0}
.footer-grid{display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr;gap:40px;padding-bottom:48px}
.footer-brand p{color:var(--muted);font-size:14px;max-width:280px;margin:16px 0 22px}
.socials{display:flex;gap:12px}
.socials a{width:40px;height:40px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);color:var(--steel);transition:all .25s}
.socials a:hover{color:var(--cyan);border-color:var(--cyan);box-shadow:0 0 16px rgba(0,255,255,.35);transform:translateY(-3px)}
.footer-col h4{font-family:var(--font-display);font-size:13px;letter-spacing:.16em;text-transform:uppercase;color:var(--gold);margin-bottom:18px}
.footer-col a{display:block;color:var(--muted);font-size:14px;font-weight:600;padding:5px 0;transition:all .2s}
.footer-col a:hover{color:var(--cyan);transform:translateX(4px)}
.footer-bottom{border-top:1px solid rgba(92,107,115,.25);padding:20px 0;display:flex;justify-content:space-between;gap:14px;flex-wrap:wrap;color:var(--steel);font-size:13px;font-weight:600}

@media(max-width:1020px){.levels-grid,.perks-grid,.reviews-grid{grid-template-columns:repeat(2,1fr)}.footer-grid{grid-template-columns:1fr 1fr}}
@media(max-width:880px){
  .main-nav{position:fixed;top:76px;left:0;right:0;flex-direction:column;gap:0;padding:18px 4%;
    background:rgba(30,34,42,.97);border-bottom:1px solid var(--line-cyan);transform:translateY(-130%);
    transition:transform .4s cubic-bezier(.22,1,.36,1);backdrop-filter:blur(20px)}
  .main-nav.open{transform:none}
  .main-nav a{padding:14px 6px;border-bottom:1px solid rgba(92,107,115,.2)}
  .burger{display:flex}
  .header-cta{display:none}
  .hero{padding-top:130px}
  .hero-inner{grid-template-columns:1fr;gap:70px}
  .hero-art{order:2}
}
@media(max-width:640px){
  .levels-grid,.perks-grid,.reviews-grid{grid-template-columns:1fr}
  .footer-grid{grid-template-columns:1fr}
  .hero h1{font-size:1.9rem}
  .cta-inner{padding:44px 22px}
}
@media(prefers-reduced-motion:reduce){*,*::before,*::after{animation:none!important;transition:none!important}.reveal{opacity:1;transform:none}}
</style>
</head>
<body>

<div id="scrollProgress"></div>

<header class="site-header" id="siteHeader">
  <div class="container header-inner">
    <a href="{{ route('home') }}" class="logo">
      <span class="logo-badge">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#171325" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l2 3" /><path d="M18 6l-2 3" /><ellipse cx="12" cy="13" rx="7" ry="8" /><circle cx="9" cy="12" r="1.4" fill="#171325" stroke="none" /><circle cx="15" cy="12" r="1.4" fill="#171325" stroke="none" /></svg>
      </span>
      <span class="logo-text">PHILIP EDUCATION<small>академия английского</small></span>
    </a>

    <nav class="main-nav" id="mainNav">
      <a href="#levels">Уровни</a>
      <a href="#perks">Преимущества</a>
      <a href="#reviews">Отзывы</a>
      <a href="{{ route('login') }}">Войти</a>
    </nav>

    <a href="{{ route('register') }}" class="btn btn-neon header-cta">Начать бесплатно</a>

    <button class="burger" id="burger" aria-label="Меню"><span></span><span></span><span></span></button>
  </div>
</header>

<section class="hero">
  <canvas id="particleCanvas"></canvas>
  <div class="container hero-inner">
    <div class="hero-copy">
      <span class="hero-badge"><i></i>ИИ-наставник + геймификация</span>
      <h1>Английский — это <span class="grad-cyan">путь героя</span>,<br>а <span class="grad-gold">Phil</span> — твой наставник</h1>
      <p class="hero-sub">Уроки, словарь и грамматика превращаются в <b>прокачку навыка</b>: проходи уровни от A1 до C2, держи <b>streak</b> и получай достижения на каждом шаге.</p>
      <div class="hero-actions">
        <a href="{{ route('register') }}" class="btn btn-gold">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 5.3L20 8l-4 4 1 5.8-5-2.8-5 2.8L8 12 4 8l5.6-.7z" /></svg>
          Начать бесплатно
        </a>
        <a href="#levels" class="btn btn-ghost">Выбрать уровень</a>
      </div>
      <div class="hero-stats">
        <div class="hstat"><b><span data-count="{{ $totalUsers }}">0</span>+</b><span>учеников платформы</span></div>
        <div class="hstat"><b><span data-count="{{ $totalWords }}">0</span>+</b><span>слов в словаре</span></div>
        <div class="hstat"><b><span data-count="{{ $totalLessons }}">0</span>+</b><span>уроков</span></div>
      </div>
    </div>

    <div class="hero-art" id="heroArt">
      <div class="aura"></div>
      <div class="ring r1"></div>
      <div class="ring r2"></div>
      <span class="rune" style="top:8%;left:10%">✦</span>
      <span class="rune gold" style="top:14%;right:6%;animation-delay:-2s">A1</span>
      <span class="rune" style="bottom:16%;left:2%;animation-delay:-4s">B2</span>
      <span class="rune gold" style="bottom:6%;right:14%;animation-delay:-1s">✧</span>
      <div class="mentor-badge" id="mentorBadge">
        <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
          <rect x="5" y="8" width="14" height="12" rx="3" />
          <circle cx="9.5" cy="14" r="1.4" fill="#FFD700" stroke="none" />
          <circle cx="14.5" cy="14" r="1.4" fill="#FFD700" stroke="none" />
          <path d="M9 17.5h6" />
          <path d="M12 8V5" />
          <circle cx="12" cy="3.5" r="1.3" fill="#FFD700" stroke="none" />
        </svg>
      </div>
    </div>
  </div>
</section>

<section id="levels">
  <div class="container">
    <div class="ornament reveal"><i></i><b></b><i></i></div>
    <p class="sec-kicker reveal">Уровни · CEFR</p>
    <h2 class="sec-title reveal">Выбери свой <span class="gold">уровень</span></h2>
    <p class="sec-sub reveal">От первого «Hello» до свободной академической речи — шесть уровней по общеевропейской шкале CEFR.</p>

    <div class="levels-grid">
      @foreach ($levels as $level)
        <article class="level-card reveal {{ $level['code'] === 'B1' ? 'featured' : '' }}">
          <div class="level-sigil">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="{{ $level['code'] === 'B1' ? '#FFD700' : '#5C6B73' }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 4v10l-7 4-7-4V7z" /><path d="M12 11l3.5 2v4L12 19l-3.5-2v-4z" stroke="#4DEEEA" /></svg>
          </div>
          <span class="level-code">{{ $level['code'] }}</span>
          <h3>{{ $level['name'] }}</h3>
          <p>{{ \Illuminate\Support\Str::limit($level['description'], 90) }}</p>
          <p class="level-lessons">{{ $level['lessons_count'] }} {{ $level['lessons_count'] === 1 ? 'урок' : 'уроков' }}</p>
          <a href="{{ route('register') }}" class="btn {{ $level['code'] === 'B1' ? 'btn-gold' : 'btn-neon' }}" style="width:100%;{{ $level['code'] === 'B1' ? 'padding:13px 20px;font-size:15px' : '' }}">Начать с {{ $level['code'] }}</a>
        </article>
      @endforeach
    </div>
  </div>
</section>

<section id="perks">
  <div class="container">
    <div class="ornament reveal"><i></i><b></b><i></i></div>
    <p class="sec-kicker reveal">Почему мы</p>
    <h2 class="sec-title reveal">Учиться <span class="cyan">по-другому</span></h2>
    <p class="sec-sub reveal">Классическая методика преподавания вместе с геймификацией и ИИ-поддержкой.</p>

    <div class="perks-grid">
      <article class="perk reveal">
        <div class="perk-icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#5C6B73" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2c1 4-4 5-4 9a4 4 0 0 0 8 0c0-2-1-3-1-3s3 1 3 5a6 6 0 0 1-12 0c0-6 6-7 6-11z" stroke="#FFD700" /></svg></div>
        <h3>Streak и достижения</h3>
        <p>Ежедневная серия занятий, бейджи-медальоны и календарь активности — прогресс виден каждый день.</p>
      </article>
      <article class="perk reveal">
        <div class="perk-icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#5C6B73" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="8" width="14" height="12" rx="3" stroke="#00E5FF" /><circle cx="9.5" cy="14" r="1" fill="#00E5FF" stroke="none" /><circle cx="14.5" cy="14" r="1" fill="#00E5FF" stroke="none" /></svg></div>
        <h3>Phil — ИИ-наставник</h3>
        <p>Отвечает на вопросы по грамматике 24/7, объясняет ошибки и подсказывает — прямо на любой странице сайта.</p>
      </article>
      <article class="perk reveal">
        <div class="perk-icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#5C6B73" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V4H6.5A2.5 2.5 0 0 0 4 6.5v13Z" stroke="#4DEEEA" /></svg></div>
        <h3>Живой словарь</h3>
        <p>Карточки с 3D-переворотом, произношение и интервальное повторение — слова запоминаются сами.</p>
      </article>
      <article class="perk reveal">
        <div class="perk-icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#5C6B73" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4" stroke="#00FFFF" /><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" /></svg></div>
        <h3>Грамматика по темам</h3>
        <p>16 разделов от местоимений до продвинутых конструкций — с примерами и разбором частых ошибок.</p>
      </article>
      <article class="perk reveal">
        <div class="perk-icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#5C6B73" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" stroke="#FFD700" /></svg></div>
        <h3>Тесты и упражнения</h3>
        <p>Проверяй себя после каждого раздела — сразу видно, что подтянуть перед следующим уровнем.</p>
      </article>
      <article class="perk reveal">
        <div class="perk-icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#5C6B73" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="5" stroke="#4DEEEA" /><path d="M9 13l-2 8 5-3 5 3-2-8" /></svg></div>
        <h3>Книги для чтения</h3>
        <p>Адаптированные тексты по уровням с сохранением прогресса чтения — практика в контексте.</p>
      </article>
    </div>
  </div>
</section>

<section id="reviews">
  <div class="container">
    <div class="ornament reveal"><i></i><b></b><i></i></div>
    <p class="sec-kicker reveal">Отзывы</p>
    <h2 class="sec-title reveal">Что говорят <span class="gold">ученики</span></h2>
    <p class="sec-sub reveal">Истории тех, кто уже прошёл часть пути.</p>

    <div class="reviews-grid">
      <article class="review-card reveal">
        <div class="stars"><span>★★★★★</span></div>
        <blockquote>Дошла от A1 до B1 за полгода. После победы над <b>Present Perfect</b> я наконец поняла его навсегда — спасибо Phil за терпение с моими вопросами.</blockquote>
        <div class="review-user"><div class="avatar av-1">АК</div><div><b>Анна Ковалёва</b><span>Уровень B1</span></div></div>
      </article>
      <article class="review-card reveal">
        <div class="stars"><span>★★★★★</span></div>
        <blockquote>Держал streak <b>140 дней</b> подряд — календарь активности реально затягивает. На собеседовании впервые ответил на английском без паники.</blockquote>
        <div class="review-user"><div class="avatar av-2">ДС</div><div><b>Дмитрий Соколов</b><span>Уровень C1</span></div></div>
      </article>
      <article class="review-card reveal">
        <div class="stars"><span>★★★★★</span></div>
        <blockquote>Карточки слов — гениальная штука: слово возвращается ровно тогда, когда начинаешь забывать. <b>+300 слов</b> за пару месяцев без зубрёжки.</blockquote>
        <div class="review-user"><div class="avatar av-3">МЛ</div><div><b>Мария Лебедева</b><span>Уровень A2</span></div></div>
      </article>
    </div>
  </div>
</section>

<section class="cta-band">
  <div class="container">
    <div class="cta-inner reveal">
      <h2>Готов начать <span class="grad-gold" style="background:linear-gradient(92deg,#FFD700,#FFF3A0);-webkit-background-clip:text;background-clip:text;color:transparent">свой путь</span>?</h2>
      <p>Регистрация бесплатна. Выбери уровень, встреть Phil и пройди первый урок уже сегодня.</p>
      <a href="{{ route('register') }}" class="btn btn-gold">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 5.3L20 8l-4 4 1 5.8-5-2.8-5 2.8L8 12 4 8l5.6-.7z" /></svg>
        Начать бесплатно
      </a>
    </div>
  </div>
</section>

<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="logo">
          <span class="logo-badge" style="width:34px;height:34px">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#171325" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l2 3" /><path d="M18 6l-2 3" /><ellipse cx="12" cy="13" rx="7" ry="8" /></svg>
          </span>
          <span class="logo-text" style="font-size:15px">PHILIP EDUCATION<small>академия английского</small></span>
        </div>
        <p>Платформа для изучения английского языка с ИИ-наставником, геймификацией и живым словарём.</p>
        <div class="socials">
          <a href="#" aria-label="Telegram"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M21.9 4.6L19 19.3c-.2 1-.8 1.2-1.6.8l-4.5-3.3-2.2 2.1c-.2.2-.4.4-.9.4l.3-4.6L18.6 7c.4-.3-.1-.5-.6-.2L7.7 13.2l-4.4-1.4c-1-.3-1-1 .2-1.4L20.6 3.2c.8-.3 1.5.2 1.3 1.4z" /></svg></a>
          <a href="#" aria-label="Instagram"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5" /><circle cx="12" cy="12" r="4" /><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none" /></svg></a>
          <a href="#" aria-label="YouTube"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23 8s-.2-1.6-.9-2.3c-.8-.9-1.8-.9-2.2-1C16.8 4.5 12 4.5 12 4.5s-4.8 0-7.9.2c-.4.1-1.4.1-2.2 1C1.2 6.4 1 8 1 8S.8 9.9.8 11.8v1.7C.8 15.4 1 17.3 1 17.3s.2 1.6.9 2.3c.8.9 1.9.8 2.4 1 1.8.2 7.7.2 7.7.2s4.8 0 7.9-.2c.4-.1 1.4-.1 2.2-1 .7-.7.9-2.3.9-2.3s.2-1.9.2-3.8v-1.7C23.2 9.9 23 8 23 8zM9.7 15.1V8.9l6 3.1z" /></svg></a>
        </div>
      </div>
      <div class="footer-col">
        <h4>Платформа</h4>
        <a href="#levels">Уровни</a>
        <a href="#perks">Преимущества</a>
        <a href="#reviews">Отзывы</a>
      </div>
      <div class="footer-col">
        <h4>Обучение</h4>
        <a href="{{ route('register') }}">Уроки</a>
        <a href="{{ route('register') }}">Словарь</a>
        <a href="{{ route('register') }}">Грамматика</a>
        <a href="{{ route('register') }}">Тесты</a>
      </div>
      <div class="footer-col">
        <h4>Аккаунт</h4>
        <a href="{{ route('login') }}">Войти</a>
        <a href="{{ route('register') }}">Регистрация</a>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; {{ date('Y') }} Philip Education. Все права защищены.</span>
    </div>
  </div>
</footer>

<script>
(function(){
  var headerEl = document.getElementById('siteHeader');
  var progressEl = document.getElementById('scrollProgress');
  function onScroll(){
    headerEl.classList.toggle('scrolled', window.scrollY > 30);
    var max = document.documentElement.scrollHeight - window.innerHeight;
    progressEl.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0) + '%';
  }
  window.addEventListener('scroll', onScroll, { passive:true });
  onScroll();

  var burgerBtn = document.getElementById('burger');
  var navEl = document.getElementById('mainNav');
  burgerBtn.addEventListener('click', function(){
    burgerBtn.classList.toggle('open');
    navEl.classList.toggle('open');
  });
  navEl.querySelectorAll('a').forEach(function(link){
    link.addEventListener('click', function(){ burgerBtn.classList.remove('open'); navEl.classList.remove('open'); });
  });

  var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  var revealObserver = new IntersectionObserver(function(entries){
    entries.forEach(function(entry){
      if(entry.isIntersecting){ entry.target.classList.add('visible'); revealObserver.unobserve(entry.target); }
    });
  }, { threshold:.15 });
  document.querySelectorAll('.reveal').forEach(function(el){ revealObserver.observe(el); });

  var countObserver = new IntersectionObserver(function(entries){
    entries.forEach(function(entry){
      if(!entry.isIntersecting) return;
      var el = entry.target;
      var target = parseInt(el.getAttribute('data-count'), 10) || 0;
      if (prefersReduced) { el.textContent = target.toLocaleString('ru-RU'); countObserver.unobserve(el); return; }
      var start = performance.now();
      function tick(now){
        var p = Math.min(1, (now - start) / 1400);
        var eased = 1 - Math.pow(1 - p, 3);
        el.textContent = Math.round(target * eased).toLocaleString('ru-RU');
        if(p < 1) requestAnimationFrame(tick);
      }
      requestAnimationFrame(tick);
      countObserver.unobserve(el);
    });
  }, { threshold:.6 });
  document.querySelectorAll('[data-count]').forEach(function(el){ countObserver.observe(el); });

  if (!prefersReduced) {
    var heroArt = document.getElementById('heroArt');
    var mentorBadge = document.getElementById('mentorBadge');
    heroArt.addEventListener('mousemove', function(e){
      var rect = heroArt.getBoundingClientRect();
      var dx = (e.clientX - rect.left - rect.width / 2) / rect.width;
      var dy = (e.clientY - rect.top - rect.height / 2) / rect.height;
      mentorBadge.style.transform = 'translate(' + (dx * 14) + 'px,' + (dy * 12) + 'px)';
    });
    heroArt.addEventListener('mouseleave', function(){ mentorBadge.style.transform = 'none'; });
  }

  var canvas = document.getElementById('particleCanvas');
  if (canvas && !prefersReduced) {
    var ctx = canvas.getContext('2d');
    var sparkList = [];
    var sparkColors = ['0,255,255', '0,229,255', '77,238,234', '255,215,0', '138,75,184'];
    function sizeCanvas(){ canvas.width = canvas.offsetWidth; canvas.height = canvas.offsetHeight; }
    function seedSparks(){
      sparkList = [];
      var amount = Math.min(70, Math.floor(canvas.width / 22));
      for (var i = 0; i < amount; i++) {
        sparkList.push({
          posX: Math.random() * canvas.width, posY: Math.random() * canvas.height,
          rad: Math.random() * 1.8 + .5, velY: -(Math.random() * .35 + .08), velX: (Math.random() - .5) * .25,
          col: sparkColors[Math.floor(Math.random() * sparkColors.length)], phase: Math.random() * Math.PI * 2
        });
      }
    }
    function drawSparks(t){
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      sparkList.forEach(function(s){
        s.posY += s.velY; s.posX += s.velX;
        if (s.posY < -6) { s.posY = canvas.height + 6; s.posX = Math.random() * canvas.width; }
        if (s.posX < -6) s.posX = canvas.width + 6;
        if (s.posX > canvas.width + 6) s.posX = -6;
        var tw = .35 + Math.abs(Math.sin(t / 900 + s.phase)) * .65;
        ctx.beginPath(); ctx.arc(s.posX, s.posY, s.rad, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(' + s.col + ',' + tw.toFixed(2) + ')';
        ctx.shadowColor = 'rgba(' + s.col + ',.9)'; ctx.shadowBlur = 8; ctx.fill();
      });
      requestAnimationFrame(drawSparks);
    }
    sizeCanvas(); seedSparks(); requestAnimationFrame(drawSparks);
    window.addEventListener('resize', function(){ sizeCanvas(); seedSparks(); });
  }
})();
</script>
</body>
</html>
