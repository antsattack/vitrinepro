<?php

// Gera o Hash da string 'william'
$hash = password_hash('teste123', PASSWORD_DEFAULT);
echo 'Hash gerado: ' . $hash . '<br><br>';
$hash2 = '$2y$10$hBsVD4ZWawFNEwZZMxSFAOZ7stpQkQB2E57UNaW3.bPaJmEDAT4Sy';
 
 
// Exibe informações sobre o Hash gerado
echo 'Informações sobre o Hash gerado: <br>';
var_dump(password_get_info($hash));
 
 
// Verifica se o Hash não foi gerado com mesmo algorítimo e opções passados como parâmetro
$options = array('cost' => 11);
echo '<br />Verifica se o Hash não foi gerado com as opções informadas: ' . password_needs_rehash($hash, PASSWORD_DEFAULT, $options) . '<br><br>';
 
 
// Compara a string com o Hash gerado
echo 'Resultado da comparação: ' . password_verify('teste123', $hash);