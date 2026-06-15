<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];

// Obtener datos actuales para mostrarlos si ya existen
$sql = "SELECT tarjeta_pago, movil_pago FROM usuarios WHERE id = '$user_id'";
$res = $conn->query($sql);
$user = $res->fetch_assoc();

$msg = "";
$is_edit = !empty($user['tarjeta_pago']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tarjeta = mysqli_real_escape_string($conn, $_POST['tarjeta_usuario']);
    $movil = mysqli_real_escape_string($conn, $_POST['movil_usuario']);
    
    $sql_update = "UPDATE usuarios SET tarjeta_pago = '$tarjeta', movil_pago = '$movil' WHERE id = '$user_id'";
    
    if ($conn->query($sql_update)) {
        header("Location: dashboard.php?config=success");
        exit();
    } else {
        $msg = "Error al guardar los datos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Pago | Cuba-Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: white; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="glass w-full max-w-md rounded-[2.5rem] p-8 md:p-12 shadow-2xl relative overflow-hidden">
        <div class="text-center mb-8">
            <div class="inline-block p-4 bg-amber-500/20 rounded-3xl mb-4">
                <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            <h1 class="text-2xl font-black italic uppercase">Datos de <span class="text-blue-500">Cobro</span></h1>
            <p class="text-slate-400 text-sm mt-2">
                <?php echo $is_edit ? 'Actualiza tus datos para recibir tus premios.' : 'Configura dónde recibirás el dinero si ganas.'; ?>
            </p>
        </div>

        <?php if($msg): ?>
            <div class="mb-6 p-4 bg-red-500/20 text-red-200 text-xs rounded-xl text-center"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form action="configurar_pago.php" method="POST" class="space-y-6">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Tu Tarjeta CUP</label>
                <input type="text" name="tarjeta_usuario" id="tarjeta" required 
                    value="<?php echo $user['tarjeta_pago']; ?>"
                    placeholder="0000 0000 0000 0000" maxlength="19"
                    class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-blue-500 font-mono text-lg transition-all">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Móvil de Confirmación</label>
                <input type="text" name="movil_usuario" id="movil" required 
                    value="<?php echo $user['movil_pago']; ?>"
                    placeholder="5xxxxxxx" maxlength="8"
                    class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-blue-500 font-mono text-lg transition-all">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-black py-5 rounded-2xl uppercase tracking-widest hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/30 active:scale-95 mt-4">
                <?php echo $is_edit ? 'Actualizar Datos' : 'Guardar y Entrar'; ?>
            </button>
            
            <?php if($is_edit): ?>
                <div class="text-center mt-6">
                    <a href="dashboard.php" class="text-slate-500 text-xs hover:text-white transition-colors">Cancelar y volver</a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <script>
        // Formateador de tarjeta
        document.getElementById('tarjeta').addEventListener('input', function (e) {
            let v = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let matches = v.match(/\d{4,16}/g);
            let match = matches && matches[0] || '';
            let parts = [];
            for (i=0, len=match.length; i<len; i+=4) {
                parts.push(match.substring(i, i+4));
            }
            if (parts.length) {
                e.target.value = parts.join(' ');
            } else {
                e.target.value = v;
            }
        });

        // Solo números para móvil
        document.getElementById('movil').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>

