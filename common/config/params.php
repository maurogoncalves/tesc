<?php
$arrayAnos = []; 
$anoInicial = 2010;
for ($ano = intval(date('Y')); $ano >= $anoInicial; $ano--)
    $arrayAnos[$ano] = $ano;

return [
    'adminEmail' => 'contato@miniportal.com.br',
    'supportEmail' => 'contato@miniportal.com.br',
    'user.passwordResetTokenExpire' => 3600,
    'arrayBoolean' => [0 => 'Não', 1 => 'Sim'],
    'arrayAnos' => $arrayAnos,
    'arrayMeses' => [
        1 => 'Janeiro',
        2 => 'Fevereiro',
        3 => 'Março',
        4 => 'Abril',
        5 => 'Maio',
        6 => 'Junho',
        7 => 'Julho',
        8 => 'Agosto',
        9 => 'Setembro',
        10 => 'Outubro',
        11 => 'Novembro',
        12 => 'Dezembro'
    ],
    'arrayMesesCurtos' => [
        1 => 'Jan',
        2 => 'Fev',
        3 => 'Mar',
        4 => 'Abr',
        5 => 'Mai',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Ago',
        9 => 'Set',
        10 => 'Out',
        11 => 'Nov',
        12 => 'Dez'
    ],
];
