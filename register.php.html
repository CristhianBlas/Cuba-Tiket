<?php
// Iniciar sesión al principio para poder guardar los datos del usuario tras el registro
session_start();
require_once 'db.php';

$message = "";
$error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $correo = mysqli_real_escape_string($conn, $_POST['correo']);
    $password = $_POST['password'];

    // Validar si el correo ya existe
    $checkEmail = "SELECT id FROM usuarios WHERE correo = '$correo'";
    $result = $conn->query($checkEmail);

    if ($result && $result->num_rows > 0) {
        $message = "Este correo ya está registrado.";
        $error = true;
    } else {
        // Encriptar contraseña por seguridad
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        // Insertar usuario con saldo inicial 0
        $sql = "INSERT INTO usuarios (nombre, correo, password, saldo) VALUES ('$nombre', '$correo', '$passwordHash', 0)";
        
        if ($conn->query($sql) === TRUE) {
            // REGISTRO EXITOSO: LOGUEAR AUTOMÁTICAMENTE
            
            // 1. Obtener el ID generado para este nuevo usuario
            $nuevo_id = $conn->insert_id;
            
            // 2. Guardar datos en la sesión (usando las mismas claves que en login.php)
            $_SESSION['user_id'] = $nuevo_id;
            $_SESSION['user_nombre'] = $nombre;
            $_SESSION['user_saldo'] = 0;
            
            // 3. Redirigir directamente al panel de control
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Error al registrar: " . $conn->error;
            $error = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Cuba-Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #0f172a;
            background-image: linear-gradient(to bottom, rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.95)), 
                              url('https://images.unsplash.com/photo-1639762681485-074b7f938ba0?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="glass w-full max-w-md rounded-[2.5rem] p-8 md:p-12 shadow-2xl">
        <div class="text-center mb-10">
            <div class="inline-block bg-blue-600 p-3 rounded-2xl mb-4 shadow-lg shadow-blue-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <h1 class="text-3xl font-black text-white italic uppercase tracking-tighter">Únete a Cuba<span class="text-blue-500">-Ticket</span></h1>
            <p class="text-slate-400 text-sm mt-2">Crea tu cuenta para empezar a ganar</p>
        </div>

        <?php if($message !== ""): ?>
            <div class="mb-6 p-4 rounded-xl text-sm font-medium <?php echo $error ? 'bg-red-500/20 text-red-200 border border-red-500/50' : 'bg-green-500/20 text-green-200 border border-green-500/50'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-5">
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Nombre Completo</label>
                <input type="text" name="nombre" required placeholder="Ej. Juan Pérez" 
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 text-white placeholder:text-slate-600 focus:outline-none focus:border-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Correo Electrónico</label>
                <input type="email" name="correo" required placeholder="tu@email.com" 
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 text-white placeholder:text-slate-600 focus:outline-none focus:border-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Contraseña</label>
                <input type="password" name="password" required placeholder="••••••••" 
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 text-white placeholder:text-slate-600 focus:outline-none focus:border-blue-500 transition-all">
            </div>

            <button type="submit" 
                class="w-full bg-blue-600 text-white font-black py-5 rounded-2xl shadow-xl shadow-blue-600/30 hover:bg-blue-500 transition-all uppercase tracking-widest mt-4 active:scale-95">
                Crear Mi Cuenta
            </button>
        </form>

        <div class="mt-10 text-center">
            <p class="text-slate-500 text-sm">¿Ya tienes cuenta? 
                <a href="login.php" class="text-blue-500 font-bold hover:underline">Inicia sesión aquí</a>
            </p>
        </div>
    </div>

</body>
</html>

