<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['chatbot_historial'])) {
    $_SESSION['chatbot_historial'] = [
        [
            'autor' => 'bot',
            'mensaje' => 'Hola, soy el asistente de ayuda de Gropper. Puedo ayudarte con viajes, invitaciones, alojamientos, presupuesto y pagos.'
        ]
    ];
}

if (isset($_POST['chatbot_reset']) && $_POST['chatbot_reset'] === '1') {
    $_SESSION['chatbot_historial'] = [
        [
            'autor' => 'bot',
            'mensaje' => 'Conversacion reiniciada. Puedes volver a preguntarme sobre el funcionamiento de Gropper.'
        ]
    ];
}

function chatbot_limpiar_texto(string $texto): string
{
    return mb_strtolower(trim($texto), 'UTF-8');
}

function chatbot_responder(string $pregunta): string
{
    $texto = chatbot_limpiar_texto($pregunta);

    if ($texto === '') {
        return 'Escribe una pregunta para que pueda ayudarte.';
    }

    if (
        str_contains($texto, 'diferencia') ||
        str_contains($texto, 'organizador y acompanante') ||
        str_contains($texto, 'organizador y acompañante') ||
        str_contains($texto, 'rol')
    ) {
        return 'El organizador crea y gestiona el viaje: define destino, presupuesto, fechas, añade servicios e invita participantes. El acompañante recibe invitaciones, puede aceptarlas o rechazarlas, consultar sus viajes y pagar su parte de las reservas.';
    }

    if (
        str_contains($texto, 'alojamiento') ||
        str_contains($texto, 'añado un alojamiento') ||
        str_contains($texto, 'anado un alojamiento') ||
        str_contains($texto, 'reservo un alojamiento')
    ) {
        return 'Para añadir un alojamiento, inicia sesion como organizador, entra en "Gestionar viajes", crea un viaje o selecciona uno como activo y accede a la categoria de alojamientos. Desde ahi puedes elegir una opcion y pulsar el boton para reservarla para el grupo.';
    }

    if (
        str_contains($texto, 'invito') ||
        str_contains($texto, 'invitar') ||
        str_contains($texto, 'invitar a alguien')
    ) {
        return 'Para invitar a alguien, entra como organizador en el itinerario del viaje. En la seccion del grupo del viaje aparece un formulario donde introduces el email del participante. Si existe en el sistema y no estaba invitado, se crea la invitacion.';
    }

    if (
        str_contains($texto, 'mis invitaciones') ||
        str_contains($texto, 'donde veo mis invitaciones') ||
        str_contains($texto, 'dónde veo mis invitaciones') ||
        str_contains($texto, 'invitaciones')
    ) {
        return 'Las invitaciones se ven desde el dashboard del acompanante. Ahi aparece una seccion con invitaciones pendientes donde el usuario puede aceptarlas o rechazarlas.';
    }

    if (
        str_contains($texto, 'pago mi parte') ||
        str_contains($texto, 'pagar mi parte') ||
        str_contains($texto, 'como pago') ||
        str_contains($texto, 'cómo pago') ||
        str_contains($texto, 'pago')
    ) {
        return 'Para pagar tu parte, entra en el itinerario del viaje y busca una reserva pendiente. Si aun no has pagado esa reserva, aparecera la opcion de pagar tu parte. El sistema calcula automaticamente el importe segun el numero de personas.';
    }

    if (
        str_contains($texto, 'supero el presupuesto') ||
        str_contains($texto, 'presupuesto') ||
        str_contains($texto, 'presupuesto insuficiente')
    ) {
        return 'Si el servicio supera el presupuesto restante, el sistema no deja añadirlo. Antes de guardar la reserva, calcula el coste total y lo compara con el presupuesto disponible del viaje.';
    }

    if (
        str_contains($texto, 'quien puede borrar una reserva') ||
        str_contains($texto, 'quién puede borrar una reserva') ||
        str_contains($texto, 'borrar una reserva') ||
        str_contains($texto, 'eliminar reserva')
    ) {
        return 'Solo el organizador del viaje puede borrar una reserva. Ademas, el sistema comprueba que esa reserva pertenezca realmente a un viaje creado por ese organizador.';
    }

    if (
        str_contains($texto, 'crear viaje') ||
        str_contains($texto, 'como creo un viaje') ||
        str_contains($texto, 'cómo creo un viaje')
    ) {
        return 'Para crear un viaje, inicia sesion como organizador y entra en "Gestionar viajes". Ahi rellenas destino, presupuesto y fechas, y pulsas en "Crear viaje".';
    }

    if (
        str_contains($texto, 'login') ||
        str_contains($texto, 'iniciar sesion') ||
        str_contains($texto, 'iniciar sesión')
    ) {
        return 'Para iniciar sesion, entra en la pantalla de login, escribe tu email y tu contraseña y pulsa en entrar. Si los datos son correctos, el sistema te redirige al panel correspondiente.';
    }

    if (
        str_contains($texto, 'registro') ||
        str_contains($texto, 'registrarme') ||
        str_contains($texto, 'crear cuenta')
    ) {
        return 'Para registrarte, entra en la pantalla de registro, completa nombre, email, contraseña y elige tu rol. Si el email no existe ya en el sistema, se creara tu cuenta.';
    }

    if (
        str_contains($texto, 'itinerario') ||
        str_contains($texto, 'donde veo el itinerario') ||
        str_contains($texto, 'dónde veo el itinerario')
    ) {
        return 'El itinerario se ve en la pantalla del viaje activo. Ahi aparecen las reservas añadidas, el presupuesto, lo gastado, lo disponible, el gasto por persona y el grupo del viaje.';
    }

    if (
        str_contains($texto, 'viaje activo') ||
        str_contains($texto, 'seleccionar viaje')
    ) {
        return 'El viaje activo es el viaje sobre el que estas trabajando en ese momento. Se selecciona desde el panel del organizador y se guarda en sesion para reutilizarlo en servicios e itinerario.';
    }

    return 'No tengo una respuesta exacta para esa pregunta. Puedes preguntarme, por ejemplo: que diferencia hay entre organizador y acompanante, como añadir un alojamiento, como invitar a alguien, donde ver invitaciones, como pagar tu parte, que pasa si superas el presupuesto o quien puede borrar una reserva.';
}

if (isset($_POST['chatbot_pregunta'])) {
    $pregunta = trim((string) ($_POST['chatbot_pregunta'] ?? ''));
    if ($pregunta !== '') {
        $_SESSION['chatbot_historial'][] = [
            'autor' => 'usuario',
            'mensaje' => $pregunta
        ];

        $_SESSION['chatbot_historial'][] = [
            'autor' => 'bot',
            'mensaje' => chatbot_responder($pregunta)
        ];
    }
}

$chatbotHistorial = $_SESSION['chatbot_historial'];
?>

<div class="gropper-chatbot">
    <button type="button" class="gropper-chatbot-toggle" id="gropperChatbotToggle" aria-label="Abrir ayuda">
        ?
    </button>

    <div class="gropper-chatbot-panel" id="gropperChatbotPanel">
        <div class="gropper-chatbot-header">
            <div>
                <strong>Asistente Gropper</strong>
                <div class="gropper-chatbot-subtitle">Ayuda sobre el uso de la web</div>
            </div>
            <button type="button" class="gropper-chatbot-close" id="gropperChatbotClose" aria-label="Cerrar chat">
                x
            </button>
        </div>

        <div class="gropper-chatbot-suggestions">
            <div class="gropper-chatbot-suggestions-title">Preguntas sugeridas</div>
            <div class="gropper-chatbot-suggestion-list">
                <button type="button" class="gropper-chatbot-suggestion">¿Qué diferencia hay entre organizador y acompañante?</button>
                <button type="button" class="gropper-chatbot-suggestion">¿Cómo añado un alojamiento?</button>
                <button type="button" class="gropper-chatbot-suggestion">¿Cómo invito a alguien?</button>
                <button type="button" class="gropper-chatbot-suggestion">¿Dónde veo mis invitaciones?</button>
                <button type="button" class="gropper-chatbot-suggestion">¿Cómo pago mi parte?</button>
                <button type="button" class="gropper-chatbot-suggestion">¿Qué pasa si supero el presupuesto?</button>
                <button type="button" class="gropper-chatbot-suggestion">¿Quién puede borrar una reserva?</button>
            </div>
        </div>

        <div class="gropper-chatbot-body" id="gropperChatbotBody">
            <?php foreach ($chatbotHistorial as $item): ?>
                <div class="gropper-chatbot-message <?php echo $item['autor'] === 'usuario' ? 'is-user' : 'is-bot'; ?>">
                    <div class="gropper-chatbot-bubble">
                        <?php echo nl2br(htmlspecialchars($item['mensaje'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="gropper-chatbot-footer">
            <form method="POST" class="gropper-chatbot-form">
                <input
                    type="text"
                    name="chatbot_pregunta"
                    id="gropperChatbotInput"
                    class="gropper-chatbot-input"
                    placeholder="Escribe tu pregunta..."
                    autocomplete="off"
                    required
                >
                <button type="submit" class="gropper-chatbot-send">Enviar</button>
            </form>

            <form method="POST" class="gropper-chatbot-reset-form">
                <input type="hidden" name="chatbot_reset" value="1">
                <button type="submit" class="gropper-chatbot-reset">Reiniciar conversacion</button>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const toggle = document.getElementById('gropperChatbotToggle');
    const panel = document.getElementById('gropperChatbotPanel');
    const closeBtn = document.getElementById('gropperChatbotClose');
    const input = document.getElementById('gropperChatbotInput');
    const body = document.getElementById('gropperChatbotBody');
    const suggestionButtons = document.querySelectorAll('.gropper-chatbot-suggestion');
    const form = document.querySelector('.gropper-chatbot-form');
    const resetForm = document.querySelector('.gropper-chatbot-reset-form');

    if (body) {
        body.scrollTop = body.scrollHeight;
    }

    function openChat() {
        panel.classList.add('is-open');
        localStorage.setItem('gropper_chatbot_open', '1');

        if (input) {
            input.focus();
        }

        if (body) {
            body.scrollTop = body.scrollHeight;
        }
    }

    function closeChat() {
        panel.classList.remove('is-open');
        localStorage.setItem('gropper_chatbot_open', '0');
    }

    if (localStorage.getItem('gropper_chatbot_open') === '1') {
        openChat();
    }

    if (toggle) {
        toggle.addEventListener('click', openChat);
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeChat);
    }

    if (form) {
        form.addEventListener('submit', function () {
            localStorage.setItem('gropper_chatbot_open', '1');
        });
    }

    if (resetForm) {
        resetForm.addEventListener('submit', function () {
            localStorage.setItem('gropper_chatbot_open', '1');
        });
    }

    suggestionButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            if (input) {
                input.value = button.textContent.trim();
                openChat();
            }
        });
    });
})();
</script>
