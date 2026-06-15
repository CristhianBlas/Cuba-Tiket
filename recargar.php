<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$user_email = "";

$sql = "SELECT correo FROM usuarios WHERE id = '$user_id'";
$res = $conn->query($sql);
if ($row = $res->fetch_assoc()) { $user_email = $row['correo']; }

$tarjeta_cup = "9238-9598-7022-9087";
$confirm_num = "59683233";
$whatsapp_num = "+5359683233";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recargar Saldo | Cuba-Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: white; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body class="min-h-screen pb-10">

    <nav class="glass sticky top-0 z-40 px-6 h-16 flex items-center justify-between">
        <a href="dashboard.php" class="text-slate-400 hover:text-white transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            VOLVER
        </a>
        <span class="font-black italic uppercase">Recarga Saldo</span>
    </nav>

    <main class="max-w-xl mx-auto p-6 pt-10">
        <div class="glass p-8 rounded-[2.5rem] mb-6">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <span class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-sm">1</span>
                Datos de Transferencia
            </h3>
            
            <!-- Tarjeta -->
            <div class="mb-6">
                <label class="text-[10px] uppercase font-bold text-slate-500 mb-2 block tracking-widest">Tarjeta (CUP)</label>
                <div onclick="copyToClipboard('9238959870229087', 'toast-card')" class="bg-black/40 p-4 rounded-xl flex justify-between items-center cursor-pointer hover:bg-white/5 transition-all border border-white/5">
                    <span class="font-mono text-lg"><?php echo $tarjeta_cup; ?></span>
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                </div>
                <p id="toast-card" class="text-[9px] text-blue-400 mt-1 opacity-0 transition-opacity uppercase font-bold">Tarjeta copiada</p>
            </div>

            <!-- Número de Confirmación -->
            <div>
                <label class="text-[10px] uppercase font-bold text-slate-500 mb-2 block tracking-widest">Número para confirmación</label>
                <div onclick="copyToClipboard('59683233', 'toast-num')" class="bg-black/40 p-4 rounded-xl flex justify-between items-center cursor-pointer hover:bg-white/5 transition-all border border-white/5">
                    <span class="font-mono text-lg"><?php echo $confirm_num; ?></span>
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                </div>
                <p id="toast-num" class="text-[9px] text-blue-400 mt-1 opacity-0 transition-opacity uppercase font-bold">Número copiado</p>
            </div>
        </div>

        <div class="glass p-8 rounded-[2.5rem]">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <span class="w-8 h-8 bg-emerald-600 rounded-full flex items-center justify-center text-sm">2</span>
                Reportar Pago
            </h3>
            <input type="number" id="monto" oninput="toggleBtn()" placeholder="Cantidad enviada (CUP)" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 mb-6 focus:outline-none focus:border-blue-500 text-white font-bold text-lg">
            
            <button id="btn-wa" disabled onclick="sendWA()" class="w-full bg-slate-700 text-slate-400 py-5 rounded-2xl font-black uppercase tracking-widest transition-all cursor-not-allowed">
                Ya he transferido
            </button>
        </div>
    </main>

    <script>
        function toggleBtn() {
            const v = document.getElementById('monto').value;
            const btn = document.getElementById('btn-wa');
            btn.disabled = v <= 0;
            if(v > 0) {
                btn.className = "w-full bg-blue-600 text-white py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-blue-500 shadow-xl shadow-blue-600/20 active:scale-95";
            } else {
                btn.className = "w-full bg-slate-700 text-slate-400 py-5 rounded-2xl font-black uppercase tracking-widest cursor-not-allowed";
            }
        }
        function copyToClipboard(text, id) {
            const el = document.createElement('textarea'); el.value = text; document.body.appendChild(el); el.select(); document.execCommand('copy'); document.body.removeChild(el);
            const t = document.getElementById(id); t.style.opacity = "1"; setTimeout(() => t.style.opacity = "0", 2000);
        }
        function sendWA() {
            const m = document.getElementById('monto').value;
            const msg = encodeURIComponent("Hola he recargado mi cuenta\nCorreo: <?php echo $user_email; ?>\nCantidad: " + m + " CUP");
            window.location.href = "https://wa.me/<?php echo $whatsapp_num; ?>?text=" + msg;
        }
    </script>
</body>
</html>

