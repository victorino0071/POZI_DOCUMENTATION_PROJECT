<?php

spl_autoload_register(function (string $nomeDaClasse) {
    $pastaRaiz = __DIR__ . '/';

    $caminhoRelativo = str_replace('\\', '/', $nomeDaClasse);

    $caminhoDoArquivo = $pastaRaiz . $caminhoRelativo . '.php';

    if (file_exists($caminhoDoArquivo)) {
        require_once $caminhoDoArquivo;
    }
});