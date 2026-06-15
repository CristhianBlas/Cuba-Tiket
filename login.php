<?php
session_start();
require_once 'db.php';

$message = "";
$error = false;

// Verificar si viene de un registro exitoso
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "¡Registro exitoso! Ya puedes iniciar sesión.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = mysqli_real_escape_string($conn, $_POST['correo']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE correo = '$correo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verificar contraseña encriptada
        if (password_verify($password, $user['password'])) {
            // Guardar datos en la sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nombre'] = $user['nombre'];
            $_SESSION['user_saldo'] = $user['saldo'];
            
            // Redirigir al panel principal
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Contraseña incorrecta.";
            $error = true;
        }
    } else {
        $message = "Este correo no está registrado.";
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Cuba-Ticket</title>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h12m-5 10v-4m0 0V10m0 0V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-4z" />
                </svg>
            </div>
            <h1 class="text-3xl font-black text-white italic uppercase tracking-tighter">Bienvenido a Cuba<span class="text-blue-500">-Ticket</span></h1>
            <p class="text-slate-400 text-sm mt-2">Ingresa tus credenciales para continuar</p>
        </div>

        <?php if($message !== ""): ?>
            <div class="mb-6 p-4 rounded-xl text-sm font-medium <?php echo $error ? 'bg-red-500/20 text-red-200 border border-red-500/50' : 'bg-green-500/20 text-green-200 border border-green-500/50'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-6">
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Correo Electrónico</label>
                <div class="relative">
                    <input type="email" name="correo" required placeholder="tu@email.com" 
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 text-white placeholder:text-slate-600 focus:outline-none focus:border-blue-500 transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Contraseña</label>
                <div class="relative">
                    <input type="password" name="password" required placeholder="••••••••" 
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 text-white placeholder:text-slate-600 focus:outline-none focus:border-blue-500 transition-all">
                </div>
            </div>

            <button type="submit" 
                class="w-full bg-blue-600 text-white font-black py-5 rounded-2xl shadow-xl shadow-blue-600/30 hover:bg-blue-500 transition-all uppercase tracking-widest mt-4 active:scale-95">
                Entrar al Sistema
            </button>
        </form>

        <div class="mt-10 text-center">
            <p class="text-slate-500 text-sm">¿Aún no tienes cuenta? 
                <a href="register.php" class="text-blue-500 font-bold hover:underline">Regístrate gratis</a>
            </p>
            <div class="mt-6 pt-6 border-t border-white/5">
                <a href="index.html" class="text-slate-500 text-xs hover:text-white transition-colors">← Volver al inicio</a>
            </div>
        </div>
    </div>

</body>
</html>

