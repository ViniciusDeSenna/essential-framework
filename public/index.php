<?php
echo "<h1>Ambiente Docker Configurado!</h1>";
echo "<p>Versão do PHP: " . phpversion() . "</p>";

// Verifica se o composer está acessível via shell (opcional no script, mas bom para teste)
$output = shell_exec('composer --version');
echo "<pre>Composer: $output</pre>";
