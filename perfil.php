<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];
$msg = "";
$error = false;

// Obtener datos actuales
$res = $conn->query("SELECT * FROM usuarios WHERE id = '$user_id'");
$user = $res->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $correo = mysqli_real_escape_string($conn, $_POST['correo']);
    $tarjeta = mysqli_real_escape_string($conn, $_POST['tarjeta']);
    $movil = mysqli_real_escape_string($conn, $_POST['movil']);
    
    $update_fields = "nombre='$nombre', correo='$correo', tarjeta_pago='$tarjeta', movil_pago='$movil'";
    
    // Si quiere cambiar contraseña
    if (!empty($_POST['nueva_pass'])) {
        $pass = password_hash($_POST['nueva_pass'], PASSWORD_DEFAULT);
        $update_fields .= ", password='$pass'";
    }

    if ($conn->query("UPDATE usuarios SET $update_fields WHERE id = '$user_id'")) {
        $msg = "Perfil actualizado correctamente.";
        // Actualizar sesión si cambió el nombre
        $_SESSION['user_nombre'] = $nombre;
        header("Refresh: 2; url=perfil.php");
    } else {
        $msg = "Error al actualizar.";
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil | Cuba-Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: white; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body class="pb-10">
    <nav class="glass h-16 px-6 flex items-center justify-between sticky top-0 z-50">
        <a href="dashboard.php" class="text-slate-400 hover:text-white font-bold text-sm">← VOLVER</a>
        <h1 class="font-black italic uppercase">Mi Perfil</h1>
        <div class="w-10"></div>
    </nav>

    <main class="max-w-2xl mx-auto p-6 mt-6">
        <?php if($msg): ?>
            <div class="mb-6 p-4 rounded-2xl <?php echo $error ? 'bg-red-500/20 text-red-200' : 'bg-emerald-500/20 text-emerald-200'; ?> text-sm font-bold">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <form action="perfil.php" method="POST" class="glass p-8 rounded-[2.5rem] space-y-6 shadow-2xl">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Nombre Completo</label>
                    <input type="text" name="nombre" value="<?php echo $user['nombre']; ?>" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Correo Electrónico</label>
                    <input type="email" name="correo" value="<?php echo $user['correo']; ?>" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Tarjeta de Cobro</label>
                    <input type="text" name="tarjeta" id="tarjeta" value="<?php echo $user['tarjeta_pago']; ?>" maxlength="19" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 outline-none focus:border-blue-500 font-mono">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Móvil de Confirmación</label>
                    <input type="text" name="movil" id="movil" value="<?php echo $user['movil_pago']; ?>" maxlength="8" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 outline-none focus:border-blue-500 font-mono">
                </div>
            </div>

            <div class="pt-4 border-t border-white/5">
                <label class="block text-[10px] font-bold text-amber-500 uppercase mb-2 ml-1">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                <input type="password" name="nueva_pass" placeholder="••••••••" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 outline-none focus:border-blue-500">
            </div>

            <button type="submit" class="w-full bg-blue-600 py-4 rounded-2xl font-black uppercase tracking-widest hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/20">
                Actualizar Mis Datos
            </button>
        </form>
    </main>

    <script>
        document.getElementById('tarjeta').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
        });
        document.getElementById('movil').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^\d]/g, '').substring(0, 8);
        });
    </script>
</body>
</html>

