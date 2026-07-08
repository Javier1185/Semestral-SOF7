<?php
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<section class="hero">
    <h1>Lleva el control de tus finanzas sin depender de una hoja de cálculo</h1>
    <p>
        Cada transacción que no se registra a tiempo es una decisión que se toma con información
        incompleta. Este sistema permite registrar cada movimiento contable con su usuario,
        su fecha y su respaldo, para que los informes financieros reflejen la realidad del
        negocio y puedan auditarse en cualquier momento.
    </p>
</section>

<section class="beneficios">
    <div class="tarjeta">
        <h2>Trazabilidad</h2>
        <p>Cada cuenta y cada transacción queda ligada a quién la registró y cuándo.</p>
    </div>
    <div class="tarjeta">
        <h2>Informes confiables</h2>
        <p>Los informes cerrados quedan firmados digitalmente: si algo cambia después, se nota.</p>
    </div>
    <div class="tarjeta">
        <h2>Control por roles</h2>
        <p>Cada persona ve únicamente lo que le corresponde según su función.</p>
    </div>
</section>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>