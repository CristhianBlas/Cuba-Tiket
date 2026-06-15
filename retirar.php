<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];

$res = $conn->query("SELECT saldo, tarjeta_pago FROM usuarios WHERE id = '$user_id'");
$user = $res->fetch_assoc();

// Bloqueo: Si no tiene saldo de premio, no puede entrar aquí
if ($user['saldo'] < 500) { header("Location: dashboard.php"); exit(); }

$error = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $monto = intval($_POST['monto']);
    
    if ($monto < 500) { $error = "El retiro mínimo es de 500 CUP."; }
    elseif ($monto > $user['saldo']) { $error = "No tienes suficiente saldo."; }
    else {
        // Congelamos saldo: Restamos del usuario y pasamos a tabla de retiros
        $conn->query("UPDATE usuarios SET saldo = saldo - $monto WHERE id = '$user_id'");
        $tarjeta = $user['tarjeta_pago'];
        $conn->query("INSERT INTO retiros (usuario_id, monto, tarjeta, estado) VALUES ('$user_id', '$monto', '$tarjeta', 'pendiente')");
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retirar Premio | Cuba-Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #020617; color: white; font-family: sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.05); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-md glass p-10 rounded-[3rem] border border-white/10 text-center">
        <?php if($success): ?>
            <h2 class="text-2xl font-black uppercase italic mb-4 text-emerald-400">Solicitud Enviada</h2>
            <p class="text-slate-400 text-sm mb-8">Tu premio está en camino. El botón de retiro se reactivará en tu próximo premio ganado.</p>
            <a href="dashboard.php" class="block w-full bg-blue-600 py-4 rounded-2xl font-black uppercase tracking-widest text-xs">Volver</a>
        <?php else: ?>
            <h2 class="text-3xl font-black uppercase italic mb-8">Retirar <span class="text-blue-500">Premio</span></h2>
            <form method="POST" class="space-y-6">
                <div class="bg-black/40 p-6 rounded-3xl border border-white/5">
                    <span class="text-[10px] text-slate-500 font-bold uppercase block mb-1">Saldo Ganado</span>
                    <span class="text-3xl font-black text-emerald-400"><?php echo number_format($user['saldo'], 0); ?> CUP</span>
                </div>
                <input type="number" name="monto" placeholder="Monto a retirar" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-5 text-center text-xl font-black outline-none focus:border-blue-500">
                <button type="submit" class="w-full bg-blue-600 py-5 rounded-2xl font-black uppercase tracking-widest text-sm shadow-xl shadow-blue-600/20">Solicitar Transferencia</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

