<?php
session_start();
require_once 'db.php';

// Redirigir si no ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$ticket_price = 600;
$total_slots = 120;
$max_per_person = 3;

// 1. Obtener datos completos del usuario
$sql_user = "SELECT * FROM usuarios WHERE id = '$user_id'";
$res_user = $conn->query($sql_user);
$user_data = $res_user->fetch_assoc();

if (empty($user_data['tarjeta_pago'])) {
    header("Location: configurar_pago.php");
    exit();
}

$saldo_actual = $user_data['saldo'];
$nombre_usuario = $user_data['nombre'];

// 2. Lógica de Compra y Estado de Tickets
$res_check_full = $conn->query("SELECT tickets.*, usuarios.nombre as u_nombre FROM tickets JOIN usuarios ON tickets.usuario_id = usuarios.id");
$tickets_vendidos_data = [];
$mis_tickets = [];

while ($row = $res_check_full->fetch_assoc()) {
    $num = intval($row['numero_ticket']);
    $tickets_vendidos_data[$num] = $row;
    if ($row['usuario_id'] == $user_id) {
        $mis_tickets[] = $num;
    }
}

$total_vendidos = count($tickets_vendidos_data);
$sorteo_lleno = ($total_vendidos >= $total_slots);

// MENSAJES DE ADVERTENCIA
$error_compra = "";
$exito_compra = "";

// 3. Procesar Compra con Validaciones
if (isset($_POST['comprar_ticket']) && !$sorteo_lleno) {
    $num_t = intval($_POST['numero_ticket']);
    
    // VALIDACIÓN 1: Límite de tickets
    if (count($mis_tickets) >= $max_per_person) {
        $error_compra = "Límite alcanzado. Solo puedes comprar un máximo de $max_per_person tickets por sorteo.";
    } 
    // VALIDACIÓN 2: Saldo insuficiente
    elseif ($saldo_actual < $ticket_price) {
        $error_compra = "Saldo insuficiente. El ticket cuesta $ticket_price CUP y tu saldo es de $saldo_actual CUP.";
    }
    // VALIDACIÓN 3: Ticket ya vendido
    elseif (isset($tickets_vendidos_data[$num_t])) {
        $error_compra = "Lo sentimos, el ticket #$num_t acaba de ser vendido.";
    }
    else {
        $conn->query("INSERT INTO tickets (usuario_id, numero_ticket) VALUES ('$user_id', '$num_t')");
        $conn->query("UPDATE usuarios SET saldo = saldo - $ticket_price WHERE id = '$user_id'");
        header("Location: dashboard.php?compra=success");
        exit();
    }
}

if(isset($_GET['compra']) && $_GET['compra'] == 'success') {
    $exito_compra = "¡Ticket comprado con éxito! Mucha suerte.";
}

// Lógica de Sorteo (JSON)
$archivo_estado = "estado_sorteo.json";
$estado = file_exists($archivo_estado) ? json_decode(file_get_contents($archivo_estado), true) : ["finalizado" => false, "ganadores" => []];

$es_ganador = false;
foreach ($estado['ganadores'] as $ganador) {
    if ($ganador['id'] == $user_id) { $es_ganador = true; break; }
}

$mostrar_espera = false;
$segundos_restantes = 0;
if ($sorteo_lleno) {
    if (!isset($estado['finalizado']) || $estado['finalizado'] === false) {
        if (!isset($estado['temp_finalizado_en'])) {
            $estado['temp_finalizado_en'] = time();
            file_put_contents($archivo_estado, json_encode($estado));
        }
        $pasado = time() - $estado['temp_finalizado_en'];
        if ($pasado < 7200) { 
            $mostrar_espera = true;
            $segundos_restantes = 7200 - $pasado;
        }
    }
}

$faltan = $total_slots - $total_vendidos;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0f172a">
    <link rel="apple-touch-icon" href="icon-192.png">
    <title>Panel | Cuba-Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <!-- Iconos Lucide -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: white; background-image: radial-gradient(circle at 0% 0%, #1e293b 0%, #0f172a 100%); background-attachment: fixed; }
        .glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.08); }
        .sidebar { transition: all 0.4s ease; left: -300px; }
        .sidebar.open { left: 0; }
        .overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 50; backdrop-filter: blur(4px); }
        .overlay.open { display: block; }
        .ticket-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(50px, 1fr)); gap: 8px; }
        #modal-retiro { display: none; }
        #modal-retiro.active { display: flex; }
        .apk-card { background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(30, 58, 138, 0.1) 100%); border: 1px solid rgba(59, 130, 246, 0.2); }
    </style>
</head>
<body class="min-h-screen">

    <div id="overlay" onclick="closeAll()" class="overlay"></div>

    <!-- Modal de Retiro -->
    <div id="modal-retiro" class="fixed inset-0 z-[100] items-center justify-center p-4">
        <div class="glass max-w-sm w-full p-8 rounded-[2.5rem] border-blue-500/30 shadow-2xl text-center">
            <div class="w-20 h-20 bg-blue-600/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="text-4xl" id="modal-icon">💰</span>
            </div>
            <h3 class="text-2xl font-black mb-4 uppercase tracking-tighter" id="modal-title">Solicitud de Retiro</h3>
            <p class="text-slate-400 text-sm leading-relaxed mb-8" id="modal-desc">Cargando información...</p>
            <button onclick="closeAll()" class="w-full bg-slate-800 py-4 rounded-2xl font-bold uppercase tracking-widest hover:bg-slate-700 transition-all">Entendido</button>
        </div>
    </div>

    <nav class="glass sticky top-0 z-40 h-20 flex items-center justify-between px-6 border-b border-white/5">
        <div class="flex items-center gap-4">
            <button onclick="toggleSidebar()" class="p-2 hover:bg-white/10 rounded-2xl transition-all">
                <i data-lucide="menu" class="w-8 h-8 text-blue-500"></i>
            </button>
            <h1 class="font-black italic text-xl uppercase tracking-tighter hidden sm:block">Cuba<span class="text-blue-500">-Ticket</span></h1>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="bg-emerald-500/10 border border-emerald-500/20 px-4 py-2 rounded-2xl flex items-center gap-3">
                <div class="text-right">
                    <span class="text-[9px] uppercase font-bold text-emerald-400 tracking-widest block">Saldo</span>
                    <span class="font-black text-emerald-400"><?php echo number_format($saldo_actual, 0); ?> CUP</span>
                </div>
                <a href="recargar.php" class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white font-bold hover:scale-110 transition-transform shadow-lg shadow-emerald-500/20">+</a>
            </div>
            <button onclick="handleWithdraw()" class="bg-blue-600/20 border border-blue-500/30 p-2.5 rounded-xl hover:bg-blue-600 transition-all group">
                <i data-lucide="wallet" class="w-6 h-6 text-blue-400 group-hover:text-white"></i>
            </button>
        </div>
    </nav>

    <aside id="sidebar" class="sidebar fixed inset-y-0 w-72 glass z-[60] p-8 flex flex-col shadow-2xl">
        <div class="mb-10 text-center">
            <div class="w-16 h-16 bg-blue-600 rounded-2xl mx-auto mb-4 flex items-center justify-center text-2xl font-black">
                <?php echo strtoupper(substr($nombre_usuario, 0, 1)); ?>
            </div>
            <h3 class="font-bold truncate"><?php echo $nombre_usuario; ?></h3>
        </div>
        <nav class="flex-1 space-y-2">
            <a href="dashboard.php" class="flex items-center gap-4 p-4 bg-blue-600 text-white rounded-2xl font-bold">
                <i data-lucide="home" class="w-5 h-5"></i> Inicio
            </a>
            <a href="recargar.php" class="flex items-center gap-4 p-4 hover:bg-white/5 rounded-2xl text-slate-300">
                <i data-lucide="circle-dollar-sign" class="w-5 h-5"></i> Recargar
            </a>
            <a href="#" onclick="handleWithdraw()" class="flex items-center gap-4 p-4 hover:bg-white/5 rounded-2xl text-slate-300">
                <i data-lucide="banknote" class="w-5 h-5"></i> Retirar Ganancias
            </a>
            <a href="perfil.php" class="flex items-center gap-4 p-4 hover:bg-white/5 rounded-2xl text-slate-300">
                <i data-lucide="user" class="w-5 h-5"></i> Mi Perfil
            </a>
            <a href="resultado.php" class="flex items-center gap-4 p-4 hover:bg-white/5 rounded-2xl text-slate-300">
                <i data-lucide="layout-grid" class="w-5 h-5"></i> Resultado Florida
            </a>
        </nav>
        <a href="logout.php" class="flex items-center gap-4 p-4 text-red-400 font-bold hover:bg-red-500/10 rounded-2xl mt-auto">
            <i data-lucide="log-out" class="w-5 h-5"></i> Salir
        </a>
    </aside>

    <main class="max-w-6xl mx-auto p-6">
        
        <!-- ALERTAS DE SISTEMA -->
        <?php if ($error_compra): ?>
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-2xl flex items-center gap-4 animate-bounce">
                <i data-lucide="alert-triangle" class="text-red-500"></i>
                <p class="text-red-200 font-bold text-sm"><?php echo $error_compra; ?></p>
            </div>
        <?php endif; ?>

        <?php if ($exito_compra): ?>
            <div class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/50 rounded-2xl flex items-center gap-4">
                <i data-lucide="check-circle" class="text-emerald-500"></i>
                <p class="text-emerald-200 font-bold text-sm"><?php echo $exito_compra; ?></p>
            </div>
        <?php endif; ?>

        <?php if ($mostrar_espera): ?>
            <div class="text-center py-20 bg-blue-600/5 rounded-[4rem] border border-blue-500/10">
                <h2 class="text-7xl font-black mb-4 animate-pulse font-mono tracking-tighter" id="reloj">00:00:00</h2>
                <p class="text-blue-400 uppercase tracking-[0.4em] font-bold text-xs">Calculando la suerte...</p>
            </div>
        <?php elseif(isset($estado['finalizado']) && $estado['finalizado'] === true): ?>
            <div class="text-center mb-10">
                <h2 class="text-4xl font-black italic uppercase tracking-tighter text-amber-500">🏆 Resultados del Sorteo</h2>
                <p class="text-slate-400 text-sm mt-2">Felicidades a los 10 afortunados.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($estado['ganadores'] as $gan): 
                    $soy_yo = ($gan['id'] == $user_id);
                ?>
                    <div class="glass p-6 rounded-[2.5rem] border-2 <?php echo $soy_yo ? 'border-emerald-500 bg-emerald-500/10' : 'border-white/5'; ?>">
                        <div class="flex justify-between items-start mb-4">
                            <span class="bg-blue-600 text-[10px] font-black px-3 py-1 rounded-full uppercase">Ticket #<?php echo $gan['numero_ticket']; ?></span>
                        </div>
                        <h4 class="font-black text-lg"><?php echo $soy_yo ? "ERES TÚ" : $gan['nombre']; ?></h4>
                        <p class="text-[10px] text-slate-500 font-bold uppercase mt-1">Premio: 5,000 CUP</p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-8">
                <div>
                    <h2 class="text-3xl font-black italic uppercase tracking-tighter">HOLA, <?php echo strtoupper(explode(' ', $nombre_usuario)[0]); ?> 👋</h2>
                    <p class="text-slate-400">Hoy puede ser tu gran día. 10 premios de 5,000 CUP.</p>
                </div>
                <div class="flex flex-wrap gap-4 w-full lg:w-auto">
                    <div class="glass flex-1 lg:flex-none p-4 px-6 rounded-3xl border-l-4 border-amber-500">
                        <span class="text-[9px] uppercase font-bold text-slate-500 block tracking-widest">Disponibles</span>
                        <span class="text-xl font-black text-amber-500"><?php echo $faltan; ?> Cupos</span>
                    </div>
                    <div class="glass flex-1 lg:flex-none p-4 px-6 rounded-3xl border-l-4 border-blue-600">
                        <span class="text-[9px] uppercase font-bold text-slate-500 block tracking-widest">Tus Tickets</span>
                        <span class="text-xl font-black"><?php echo count($mis_tickets); ?> <small class="text-slate-600">/ 3</small></span>
                    </div>
                </div>
            </div>

            <!-- APARTADO DE DESCARGA APK INTEGRADO -->
            <div class="apk-card p-6 rounded-[2.5rem] mb-10 flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden relative">
                <div class="relative z-10">
                    <h4 class="text-xl font-black uppercase tracking-tighter mb-2 italic">App Oficial Cuba-Ticket</h4>
                    <p class="text-slate-400 text-sm max-w-md mb-4">Descarga la versión 1.1 para recibir alertas instantáneas y gestionar tus tickets más rápido.</p>
                    <a href="Cuba_Ticket-1-1.apk" download class="inline-flex items-center gap-3 bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-6 rounded-2xl transition-all text-sm uppercase tracking-widest">
                        <i data-lucide="download" class="w-5 h-5"></i> Descargar APK
                    </a>
                </div>
                <div class="hidden md:block opacity-20">
                    <i data-lucide="smartphone" class="w-32 h-32 text-blue-500"></i>
                </div>
            </div>

            <div class="glass p-6 md:p-10 rounded-[3rem] mb-8">
                <div class="flex flex-wrap gap-6 text-[10px] font-bold uppercase mb-8 opacity-60">
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-slate-800 border border-white/5"></span> Libre</div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-red-600/40"></span> Vendido</div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-emerald-500"></span> Tuyo</div>
                </div>

                <div class="ticket-grid">
                    <?php for($i=1; $i<=$total_slots; $i++): 
                        $is_mine = false;
                        $is_taken = isset($tickets_vendidos_data[$i]);
                        if($is_taken) { $is_mine = ($tickets_vendidos_data[$i]['usuario_id'] == $user_id); }
                    ?>
                        <?php if($is_taken): ?>
                            <div class="h-12 rounded-xl flex items-center justify-center font-black text-xs transition-all
                                <?php echo $is_mine ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30' : 'bg-red-600/10 text-red-500/20 border border-red-500/10'; ?>">
                                <?php echo $i; ?>
                            </div>
                        <?php else: ?>
                            <form action="dashboard.php" method="POST" onsubmit="return confirm('¿Comprar ticket #<?php echo $i; ?>?');">
                                <input type="hidden" name="numero_ticket" value="<?php echo $i; ?>">
                                <button type="submit" name="comprar_ticket" 
                                    class="h-12 w-full rounded-xl bg-white/5 border border-white/10 flex items-center justify-center font-black text-xs hover:bg-blue-600 transition-all">
                                    <?php echo $i; ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script>
    // Inicializar Iconos
    lucide.createIcons();

    // 1. Registro del Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('sw.js')
                .then(reg => console.log("✅ App lista para instalar"))
                .catch(err => console.error("❌ Error en Service Worker:", err));
        });
    }

    // 2. Lógica del Menú y Retiros
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('overlay').classList.toggle('open');
    }
    function closeAll() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('overlay').classList.remove('open');
        document.getElementById('modal-retiro').classList.remove('active');
    }
    function handleWithdraw() {
        const esGanador = <?php echo $es_ganador ? 'true' : 'false'; ?>;
        const modal = document.getElementById('modal-retiro');
        if (!esGanador) {
            document.getElementById('modal-icon').innerText = "❌"; 
            document.getElementById('modal-title').innerText = "Acceso Denegado";
            document.getElementById('modal-desc').innerHTML = `Exclusivo para ganadores.`;
        } else {
            document.getElementById('modal-icon').innerText = "🎉"; 
            document.getElementById('modal-title').innerText = "¡Ganaste!";
            document.getElementById('modal-desc').innerHTML = `Tus 5,000 CUP están en camino.`;
        }
        modal.classList.add('active');
        document.getElementById('overlay').classList.add('open');
    }

    // 3. Reloj (Solo si el sorteo está lleno)
    <?php if ($mostrar_espera): ?>
    let timeLeft = <?php echo $segundos_restantes; ?>;
    const display = document.getElementById('reloj');
    setInterval(() => {
        if (timeLeft <= 0) location.reload();
        let h = Math.floor(timeLeft / 3600);
        let m = Math.floor((timeLeft % 3600) / 60);
        let s = timeLeft % 60;
        display.innerText = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        timeLeft--;
    }, 1000);
    <?php endif; ?>
    </script>
</body>
</html>

