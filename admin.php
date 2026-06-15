<?php
session_start();
require_once 'db.php';

// 1. SEGURIDAD: Solo tú puedes entrar
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql_admin = "SELECT correo FROM usuarios WHERE id = '$user_id'";
$res_admin = $conn->query($sql_admin);
$user_admin = $res_admin->fetch_assoc();

// Correo autorizado (Tu correo)
$admin_email = "cristianmiro3@gmail.com";

if ($user_admin['correo'] !== $admin_email) {
    header("Location: dashboard.php");
    exit();
}

$msg = "";
$archivo_estado = "estado_sorteo.json";

// --- ACCIONES DE ADMINISTRACIÓN ---

// A. Bloquear / Desbloquear Usuario
if (isset($_POST['toggle_ban'])) {
    $target_id = $_POST['user_id'];
    $status = $_POST['status']; 
    $conn->query("UPDATE usuarios SET baneado = $status WHERE id = '$target_id'");
    $msg = $status == 1 ? "🚫 Usuario bloqueado correctamente." : "✅ Usuario desbloqueado.";
}

// B. Editar Saldo (Fijar valor)
if (isset($_POST['edit_saldo_directo'])) {
    $target_id = $_POST['user_id'];
    $new_amount = intval($_POST['new_amount']);
    $conn->query("UPDATE usuarios SET saldo = $new_amount WHERE id = '$target_id'");
    $msg = "💰 Saldo fijado en $new_amount CUP.";
}

// C. Sumar Saldo (Recarga rápida)
if (isset($_POST['add_saldo'])) {
    $target_id = $_POST['user_id'];
    $amount = intval($_POST['amount']);
    $conn->query("UPDATE usuarios SET saldo = saldo + $amount WHERE id = '$target_id'");
    $msg = "✅ Se han sumado $amount CUP al usuario.";
}

// D. Generar y Publicar Ganadores (Lógica corregida con movil_pago)
if (isset($_POST['publicar_ganadores'])) {
    // CAMBIO AQUÍ: Se usa movil_pago en lugar de telefono
    $res_tickets = $conn->query("SELECT u.id, u.nombre, u.tarjeta_pago, u.movil_pago, t.numero_ticket 
                                 FROM tickets t JOIN usuarios u ON t.usuario_id = u.id");
    $participantes = [];
    while($row = $res_tickets->fetch_assoc()) {
        $participantes[] = $row;
    }
    
    if(count($participantes) >= 10) {
        shuffle($participantes);
        $ganadores = array_slice($participantes, 0, 10);
        $estado = [
            "finalizado" => true,
            "fecha" => date("d/m/Y H:i"),
            "ganadores" => $ganadores
        ];
        file_put_contents($archivo_estado, json_encode($estado));
        $msg = "🏆 ¡Ganadores elegidos al azar y publicados!";
    } else {
        $msg = "❌ Error: Se necesitan al menos 10 tickets vendidos.";
    }
}

// E. Reiniciar Sorteo por completo
if (isset($_POST['reset_sorteo'])) {
    $conn->query("TRUNCATE TABLE tickets");
    if (file_exists($archivo_estado)) {
        unlink($archivo_estado);
    }
    $msg = "🔄 Sistema reiniciado. Tablero en 0 y ganadores borrados.";
}

// --- LÓGICA DE DATOS ---
$search = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : "";
$user_query = "SELECT * FROM usuarios WHERE nombre LIKE '%$search%' OR correo LIKE '%$search%' ORDER BY id DESC";
$users = $conn->query($user_query);

// Estadísticas
$total_users = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
$total_saldo = $conn->query("SELECT SUM(saldo) as total FROM usuarios")->fetch_assoc()['total'];
$tickets_count = $conn->query("SELECT COUNT(*) as total FROM tickets")->fetch_assoc()['total'];
$sorteo_finalizado = file_exists($archivo_estado);
$datos_sorteo = $sorteo_finalizado ? json_decode(file_get_contents($archivo_estado), true) : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Admin | Cuba-Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #020617; color: white; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.05); }
        .input-dark { background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.5rem 0.75rem; font-size: 0.75rem; outline: none; }
        .input-dark:focus { border-color: #3b82f6; }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8 pb-20">

    <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6">
            <div>
                <h1 class="text-4xl font-black italic uppercase tracking-tighter">Cuba<span class="text-blue-500">-Master</span></h1>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mt-1">Sesión: <?php echo $admin_email; ?></p>
            </div>
            <div class="flex flex-wrap justify-center gap-3">
                <?php if($tickets_count >= 10 && !$sorteo_finalizado): ?>
                    <form method="POST">
                        <button type="submit" name="publicar_ganadores" class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-600/20">
                            Elegir Ganadores Ahora
                        </button>
                    </form>
                <?php endif; ?>
                <form method="POST" onsubmit="return confirm('¿Reiniciar todo?');">
                    <button type="submit" name="reset_sorteo" class="bg-red-500/10 hover:bg-red-500/20 text-red-500 border border-red-500/20 px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                        Reiniciar Sistema
                    </button>
                </form>
                <a href="dashboard.php" class="bg-slate-800 hover:bg-white hover:text-black px-6 py-3 rounded-2xl text-xs font-black uppercase transition-all">Ir al Sitio</a>
            </div>
        </div>

        <?php if($msg): ?>
            <div class="mb-8 p-4 bg-blue-500/10 border border-blue-500/20 text-blue-400 rounded-2xl text-sm font-bold text-center animate-pulse">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10 text-center">
            <div class="glass p-6 rounded-[2rem]">
                <span class="block text-slate-500 text-[10px] font-black uppercase mb-2">Usuarios Registrados</span>
                <span class="text-4xl font-black"><?php echo $total_users; ?></span>
            </div>
            <div class="glass p-6 rounded-[2rem] border-blue-500/20">
                <span class="block text-slate-500 text-[10px] font-black uppercase mb-2">Ventas Actuales</span>
                <span class="text-4xl font-black text-blue-500"><?php echo $tickets_count; ?> <small class="text-xs text-slate-600">/ 120</small></span>
            </div>
            <div class="glass p-6 rounded-[2rem]">
                <span class="block text-slate-500 text-[10px] font-black uppercase mb-2">Dinero en Usuarios</span>
                <span class="text-4xl font-black text-emerald-400"><?php echo number_format($total_saldo, 0); ?></span>
            </div>
            <div class="glass p-6 rounded-[2rem]">
                <span class="block text-slate-500 text-[10px] font-black uppercase mb-2">Recaudación Ronda</span>
                <span class="text-4xl font-black text-amber-500"><?php echo number_format($tickets_count * 600, 0); ?></span>
            </div>
        </div>

        <!-- LISTA DE PAGOS PENDIENTES -->
        <?php if($sorteo_finalizado): ?>
            <div class="mb-12">
                <h2 class="text-2xl font-black mb-6 uppercase italic text-amber-500 flex items-center gap-3">
                    <span class="bg-amber-500 text-black px-3 py-1 rounded-lg not-italic">!</span> 
                    Ganadores para Pagar (5,000 CUP c/u)
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach($datos_sorteo['ganadores'] as $gan): ?>
                        <div class="glass p-6 rounded-[2rem] border-amber-500/30 bg-amber-500/5">
                            <div class="flex justify-between items-start mb-4">
                                <span class="bg-amber-500 text-black font-black text-[10px] px-3 py-1 rounded-full uppercase">Ticket #<?php echo $gan['numero_ticket']; ?></span>
                                <!-- WhatsApp con prefijo Cuba +53 -->
                                <a href="https://wa.me/53<?php echo $gan['movil_pago']; ?>" target="_blank" class="text-emerald-500 font-bold text-[10px] uppercase hover:underline">WhatsApp</a>
                            </div>
                            <div class="font-black text-lg mb-1"><?php echo htmlspecialchars($gan['nombre']); ?></div>
                            <div class="text-[12px] font-mono text-emerald-400 bg-black/40 p-2 rounded-lg border border-white/5 mb-2 select-all">
                                <?php echo $gan['tarjeta_pago']; ?>
                            </div>
                            <div class="text-[10px] text-slate-500 font-bold uppercase">Móvil: <?php echo $gan['movil_pago']; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Gestión de Usuarios -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-end gap-4">
            <h2 class="text-xl font-black uppercase tracking-tighter text-blue-400">Gestión de Clientes</h2>
            <form action="" method="GET" class="flex gap-2 w-full md:w-96">
                <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Buscar nombre o correo..." 
                       class="flex-1 input-dark !py-3 !px-4 text-sm">
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 px-6 py-3 rounded-xl font-black uppercase text-[10px]">Filtrar</button>
            </form>
        </div>

        <div class="glass rounded-[2.5rem] overflow-hidden border-white/10 shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white/5 text-[10px] font-black uppercase tracking-widest text-slate-500">
                            <th class="p-6">Usuario</th>
                            <th class="p-6">Cartera</th>
                            <th class="p-6">Datos de Pago</th>
                            <th class="p-6 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php while($u = $users->fetch_assoc()): 
                            $is_banned = isset($u['baneado']) && $u['baneado'] == 1;
                        ?>
                            <tr class="hover:bg-white/[0.02] transition-colors <?php echo $is_banned ? 'bg-red-950/20' : ''; ?>">
                                <td class="p-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-600/20 flex items-center justify-center font-bold text-blue-400">
                                            <?php echo strtoupper(substr($u['nombre'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="font-black text-sm <?php echo $is_banned ? 'text-red-400 line-through opacity-50' : 'text-slate-200'; ?>">
                                                <?php echo htmlspecialchars($u['nombre']); ?>
                                            </div>
                                            <div class="text-[10px] text-slate-500"><?php echo htmlspecialchars($u['correo']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-6">
                                    <div class="font-black text-xl mb-2 text-emerald-400"><?php echo number_format($u['saldo'], 0); ?> <span class="text-[9px] text-slate-600">CUP</span></div>
                                    <form method="POST" class="flex gap-1">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <input type="number" name="amount" placeholder="+Sumar" class="input-dark w-24">
                                        <button type="submit" name="add_saldo" class="bg-emerald-600/20 text-emerald-400 p-2 rounded-xl hover:bg-emerald-600 hover:text-white transition-all">
                                            +
                                        </button>
                                    </form>
                                </td>
                                <td class="p-6">
                                    <div class="text-[10px] font-mono">
                                        <span class="text-slate-600 block">TARJETA:</span>
                                        <span class="text-slate-300"><?php echo $u['tarjeta_pago'] ?: '---'; ?></span>
                                        <span class="text-slate-600 block mt-2">MÓVIL:</span>
                                        <!-- CAMBIO AQUÍ: Se usa movil_pago -->
                                        <span class="text-slate-300"><?php echo $u['movil_pago'] ?: '---'; ?></span>
                                    </div>
                                </td>
                                <td class="p-6 text-center">
                                    <div class="flex flex-col gap-2 items-center">
                                        <form method="POST" class="flex gap-1">
                                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                            <input type="number" name="new_amount" placeholder="Fijar" class="input-dark w-20">
                                            <button type="submit" name="edit_saldo_directo" class="bg-blue-600 text-[9px] font-black uppercase px-3 py-2 rounded-xl">Fijar</button>
                                        </form>

                                        <form method="POST">
                                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                            <?php if($is_banned): ?>
                                                <input type="hidden" name="status" value="0">
                                                <button type="submit" name="toggle_ban" class="text-[9px] font-black uppercase text-emerald-500 px-3 py-1 rounded-lg">Reactivar</button>
                                            <?php else: ?>
                                                <input type="hidden" name="status" value="1">
                                                <button type="submit" name="toggle_ban" class="text-[9px] font-black uppercase text-red-500 px-3 py-1 rounded-lg">Bloquear</button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>

