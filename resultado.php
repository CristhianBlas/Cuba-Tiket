<?php
session_start();
if(file_exists('db.php')) { include_once 'db.php'; }
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Florida Pizarra | Cuba-Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #020617; color: white; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .glow-text { text-shadow: 0 0 20px rgba(59, 130, 246, 0.5); }
        .loader { border-top-color: #3b82f6; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body class="min-h-screen pb-10">

    <!-- Navegación Minimalista -->
    <nav class="p-4 flex justify-between items-center bg-slate-900/40 backdrop-blur-md border-b border-white/5 sticky top-0 z-50">
        <div class="flex items-center gap-2">
            <a href="dashboard.php" class="flex items-center gap-2">
                <div class="w-7 h-7 bg-blue-600/20 text-blue-500 rounded flex items-center justify-center font-black border border-blue-500/30">C</div>
                <span class="font-black italic text-lg tracking-tighter uppercase">Cuba<span class="text-blue-500">-Ticket</span></span>
            </a>
        </div>
        <div class="flex gap-3">
            <a href="dashboard.php" class="text-[10px] font-bold uppercase tracking-widest text-slate-400 hover:text-white transition-colors py-2 px-1">Inicio</a>
            <button onclick="fetchAllData()" class="text-[10px] font-bold uppercase tracking-widest text-blue-500 hover:text-blue-400 transition-colors py-2 px-1">Actualizar</button>
        </div>
    </nav>

    <main class="max-w-md mx-auto p-4 mt-4">
        <header class="text-center mb-8">
            <div class="inline-flex items-center gap-2 bg-blue-500/5 text-blue-400/80 px-3 py-1 rounded-full mb-3 border border-blue-500/10">
                <div id="status-dot" class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></div>
                <span class="text-[8px] font-black uppercase tracking-[0.2em]" id="status-text">En Línea</span>
            </div>
            <h1 class="text-4xl font-black tracking-tighter italic uppercase">Florida <span class="text-blue-500">Pizarra</span></h1>
            <p id="date-display" class="text-slate-500 text-[10px] font-bold uppercase mt-1 tracking-widest">Cargando...</p>
        </header>

        <div id="results-container" class="space-y-6">
            <!-- Cargando estado inicial -->
            <div class="flex flex-col items-center justify-center py-20 opacity-30">
                <div class="loader w-8 h-8 border-2 border-white/10 rounded-full mb-4"></div>
                <p class="text-[9px] font-bold uppercase tracking-widest">Sincronizando números...</p>
            </div>
        </div>

        <div class="mt-12">
            <a href="dashboard.php" class="block w-full text-center p-4 rounded-2xl border border-white/5 text-[10px] font-bold uppercase tracking-[0.3em] text-slate-500 hover:bg-white/5 hover:text-white transition-all">
                Ir al Inicio
            </a>
        </div>
    </main>

    <script>
        const PROXIES = [
            "https://api.allorigins.win/get?url=",
            "https://corsproxy.io/?",
            "https://thingproxy.freeboard.io/fetch/"
        ];

        const URLS = {
            dia3: "https://loteriasdehoy.co/pick-3-dia",
            noche3: "https://loteriasdehoy.co/pick-3-noche",
            dia4: "https://loteriasdehoy.co/pick-4-dia",
            noche4: "https://loteriasdehoy.co/pick-4-noche"
        };

        async function smartFetch(targetUrl) {
            for (let proxy of PROXIES) {
                try {
                    const finalUrl = proxy.includes('allorigins') 
                        ? `${proxy}${encodeURIComponent(targetUrl)}&cache=${Date.now()}`
                        : `${proxy}${encodeURIComponent(targetUrl)}`;
                    
                    const response = await fetch(finalUrl);
                    if (!response.ok) continue;
                    
                    const data = await response.json ? await response.json() : { contents: await response.text() };
                    const html = data.contents || data;
                    if (html && html.length > 500) return html;
                } catch (e) { }
            }
            return null;
        }

        function extractWinner(html, isPick4 = false) {
            if (!html) return isPick4 ? "0000" : "000";
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Selector específico para loteriasdehoy.co (usualmente dentro de .result-box o similar)
            // Buscamos el primer número grande que aparezca en el cuerpo
            const selectors = ['.number', '.resultado-valor', 'td b', '.result-box h3'];
            for (let s of selectors) {
                const el = doc.querySelector(s);
                if (el && el.innerText.trim().match(/\d+/)) {
                    let val = el.innerText.trim().replace(/\D/g, '');
                    if (val.length >= (isPick4 ? 4 : 3)) return val;
                }
            }
            
            // Fallback con Regex si el DOM cambia
            const regex = isPick4 ? />\s*(\d{4})\s*</ : />\s*(\d{3})\s*</;
            const match = html.match(regex);
            return match ? match[1] : (isPick4 ? "0000" : "000");
        }

        async function fetchAllData() {
            const statusText = document.getElementById('status-text');
            const statusDot = document.getElementById('status-dot');
            statusText.innerText = "Actualizando...";
            statusDot.className = "w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse";

            const [hD3, hN3, hD4, hN4] = await Promise.all([
                smartFetch(URLS.dia3),
                smartFetch(URLS.noche3),
                smartFetch(URLS.dia4),
                smartFetch(URLS.noche4)
            ]);

            const p3Dia = extractWinner(hD3);
            const p3Noche = extractWinner(hN3);
            const p4Dia = extractWinner(hD4, true);
            const p4Noche = extractWinner(hN4, true);

            // Los corridos son los dos últimos dígitos del Pick 4 del mismo sorteo
            const results = [
                { 
                    label: 'Tarde', 
                    time: '1:30 PM', 
                    centena: p3Dia.charAt(0) || "0", 
                    fijo: p3Dia.substring(1, 3) || "00", 
                    corridos: p4Dia.length >= 4 ? p4Dia.substring(2, 4) : "00"
                },
                { 
                    label: 'Noche', 
                    time: '9:45 PM', 
                    centena: p3Noche.charAt(0) || "0", 
                    fijo: p3Noche.substring(1, 3) || "00", 
                    corridos: p4Noche.length >= 4 ? p4Noche.substring(2, 4) : "00"
                }
            ];

            render(results);
            statusText.innerText = "En Línea";
            statusDot.className = "w-1.5 h-1.5 rounded-full bg-green-500";
            document.getElementById('date-display').innerText = new Date().toLocaleDateString('es-CU', { weekday:'long', day:'numeric', month:'long' });
        }

        function render(items) {
            const container = document.getElementById('results-container');
            container.innerHTML = '';

            items.forEach(item => {
                container.innerHTML += `
                    <div class="glass rounded-[2.5rem] p-6 border-white/5 relative overflow-hidden">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h2 class="text-2xl font-black italic uppercase tracking-tighter">${item.label}</h2>
                                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">${item.time} • Florida</p>
                            </div>
                            <div class="px-2 py-0.5 rounded border border-blue-500/30 text-[7px] font-black text-blue-500 uppercase tracking-widest">Live</div>
                        </div>

                        <div class="flex items-center gap-1 mb-10">
                            <span class="text-7xl font-black tracking-tighter glow-text">${item.centena}</span>
                            <span class="text-4xl font-black text-blue-500/30 mx-1">-</span>
                            <span class="text-7xl font-black tracking-tighter glow-text">${item.fijo}</span>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div class="bg-white/[0.02] rounded-2xl py-4 text-center border border-white/5">
                                <p class="text-[7px] font-bold text-slate-500 uppercase mb-1 tracking-widest">Centena</p>
                                <p class="text-2xl font-black text-white">${item.centena}</p>
                            </div>
                            <div class="bg-blue-600/10 rounded-2xl py-4 text-center border border-blue-500/20">
                                <p class="text-[7px] font-bold text-blue-400 uppercase mb-1 tracking-widest">Fijo</p>
                                <p class="text-2xl font-black text-blue-400">${item.fijo}</p>
                            </div>
                            <div class="bg-white/[0.02] rounded-2xl py-4 text-center border border-white/5">
                                <p class="text-[7px] font-bold text-slate-500 uppercase mb-1 tracking-widest">Corridos</p>
                                <p class="text-2xl font-black text-slate-200">${item.corridos}</p>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        window.onload = fetchAllData;
    </script>
</body>
</html>

