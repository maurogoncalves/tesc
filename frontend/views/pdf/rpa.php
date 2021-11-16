
<table style="font-size:8px !important;margin:0px!important;" border="0" cellspacing="0" cellpadding="0" width="100%" class="rpa">
    <tbody>
        <tr>
            <td style=" margin: 0px;"  width="100%" valign="top">
                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tbody>
                        <tr>
                            <td style=" margin: 0px;"  width="739" rowspan="2">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                   <center><strong>RECIBO DE PAGAMENTO AUTÔNOMO - RPA</strong></center>
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="100">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    <center>Nº DO RECIBO</center>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="100">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    <center><strong><?php printf('%02d', $model->numRecibo) ?></strong></center>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tbody>
                        <tr>
                            <td style=" margin: 0px;"  width="600">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    NOME OU RAZÃO SOCIAL DA EMPRESA
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="239">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    MATRÍCULA (CNPJ OU INSS)
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="600">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    <strong>
                                        PREFEITURA MUNICIPAL DE SÃO JOSÉ DOS
                                        CAMPOS
                                    </strong>
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="239">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    <strong>46.643.466/0001-06</strong>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table border="0" cellspacing="0" cellpadding="0" width="724">
                    <tr>
                        <td style=" margin: 0px;" >
                           RECEBI DA EMPRESA CITADA ACIMA IDENTIFICADA, PELA PRESTAÇÃO DOS SERVIÇOS DE <strong><u>FRETE DE TRANSPORTE ESCOLAR (<?= strtoupper($model->nomeMes)?>/<?= $model->ano?>)</u></strong>, A IMPORTÂNCIA DE R$ <strong><u><?= Yii::$app->formatter->asDecimal($model->valor,2) ?></u></strong> (<strong><u><?= Yii::$app->formatter->asExtenso($model->valor) ?></u></strong>), CONFORME DISCRIMINADO ABAIXO: 
                        </td>
                    </tr>    
                </table>
                <table border="0" cellspacing="0" cellpadding="0" width="724">
                    <tbody>
                        <tr>
                            <td style=" margin: 0px;"  width="144" valign="top">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    SALÁRIO - BASE
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="76" colspan="2" valign="top">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    TAXA
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="142" valign="top">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    VALOR
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="227" valign="top">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    <strong>ESPECIFICAÇÃO:</strong>
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="135" valign="top">
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="144" rowspan="3">
                            </td>
                            <td style=" margin: 0px;"  width="76" colspan="2" rowspan="3">
                            </td>
                            <td style=" margin: 0px;"  width="142" rowspan="3">
                            </td>
                            <td style=" margin: 0px;"  width="227" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    I. VALOR DO SERVIÇO
                                    PRESTADO......................
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="135" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    R$                                    <strong><u><?= Yii::$app->formatter->asDecimal($model->valor, 2) ?></u></strong>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="227" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    II.
                                    .............................................................................
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="135" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    R$ ____________________
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="227" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="right">
                                    SOMA ...............
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="135" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                R$ <strong><u><?= Yii::$app->formatter->asDecimal($model->valor, 2) ?></u></strong>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="144" colspan="6">
                            </td>
                         
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="362" colspan="4">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    CARRETEIRO (VALOR BASE P/ CÁLCULO DO INSS)
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="227" valign="bottom">
                            </td>
                            <td style=" margin: 0px;"  width="135" valign="bottom">
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="362" colspan="4" rowspan="3">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    Aplicar 20% sobre o valor da
                                </p>
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    mão de obra (11,71% do frete)
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="227" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    <strong>DESCONTOS:</strong>
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="135" valign="bottom">
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="362" colspan="2" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    III. IMP. RENDA FONTE: R$ _________________
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="362" colspan="2" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    IV. ................................. R$
                                    _________________
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="144" colspan="4">
                            </td>
                         
                            <td style=" margin: 0px;"  width="227" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    V. ................................... R$
                                    _________________
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="135" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    R$ ____________________
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="362" colspan="4">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    NÚMERO DE INSCRIÇÃO
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="227" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="right">
                                    VALOR LÍQUIDO...........
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="135" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    R$ ____________________
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="362" colspan="6">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    NO INSS: <strong><?= $model->condutor->nit ?></strong>
                                </p>
                            </td>
                           
                       
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="362" colspan="6">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    NO CPF: <strong><?= $model->condutor->cpf ?></strong>
                                </p>
                            </td>
                           
                           
                        </tr>
                    
                        <tr>
                            <td style=" margin: 0px;"  width="362" colspan="4">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    DOCUMENTO DE IDENTIDADE
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="362" colspan="2">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    ASSINATURA
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="182" colspan="2">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    NÚMERO
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="180" colspan="2">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    ÓRGÃO EMISSOR
                                </p>
                            </td>
                            <td style=" margin: 0px;" 
                                width="362"
                                colspan="2"
                                rowspan="2"
                                valign="bottom"
                            >
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="182" colspan="2">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    <strong><?= $model->condutor->rg ?></strong>
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="180" colspan="2">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    <strong><?= strtoupper($model->condutor->orgaoEmissor) ?></strong>
                                </p>
                            </td>
                        </tr>
                      
                        <tr>
                            <td style=" margin: 0px;"  width="220" colspan="3">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    LOCALIDADE
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="142">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    DATA
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="362" colspan="2">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    NOME COMPLETO
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="220" colspan="3">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    <strong>SÃO JOSÉ DOS CAMPOS</strong>
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="142">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    <strong><?= date('d/m/Y') ?></strong>
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="362" colspan="2">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    <strong><?= $model->condutor->nome ?></strong>
                                </p>
                            </td>
                        </tr>
                       
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<br>

<table style="font-size:8px !important;margin:0px!important;" border="0" cellspacing="0" cellpadding="0" width="100%" class="rpa">
    <tbody>
        <tr>
            <td style=" margin: 0px;"  width="100%" valign="top">
                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tbody>
                        <tr>
                            <td style=" margin: 0px;"  width="739" rowspan="2">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    <center><strong>RECIBO DE PAGAMENTO AUTÔNOMO - RPA</strong></center>
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="100">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    <center>Nº DO RECIBO</center>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="100">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    <center><strong><?php printf('%02d', $model->numRecibo) ?></strong></center>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tbody>
                        <tr>
                            <td style=" margin: 0px;"  width="600">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    NOME OU RAZÃO SOCIAL DA EMPRESA
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="239">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    MATRÍCULA (CNPJ OU INSS)
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="600">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    <strong>
                                        PREFEITURA MUNICIPAL DE SÃO JOSÉ DOS
                                        CAMPOS
                                    </strong>
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="239">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    <strong>46.643.466/0001-06</strong>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table border="0" cellspacing="0" cellpadding="0" width="724">
                    <tr>
                        <td style=" margin: 0px;" >
                           RECEBI DA EMPRESA CITADA ACIMA IDENTIFICADA, PELA PRESTAÇÃO DOS SERVIÇOS DE <strong><u>FRETE DE TRANSPORTE ESCOLAR (<?= strtoupper($model->nomeMes)?>/<?= $model->ano?>)</u></strong>, A IMPORTÂNCIA DE R$ <strong><u><?= Yii::$app->formatter->asDecimal($model->valor,2) ?></u></strong> (<strong><u><?= Yii::$app->formatter->asExtenso($model->valor) ?></u></strong>), CONFORME DISCRIMINADO ABAIXO: 
                        </td>
                    </tr>    
                </table>
                <table border="0" cellspacing="0" cellpadding="0" width="724">
                    <tbody>
                        <tr>
                            <td style=" margin: 0px;"  width="144" valign="top">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    SALÁRIO - BASE
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="76" colspan="2" valign="top">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    TAXA
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="142" valign="top">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    VALOR
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="227" valign="top">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    <strong>ESPECIFICAÇÃO:</strong>
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="135" valign="top">
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="144" rowspan="3">
                            </td>
                            <td style=" margin: 0px;"  width="76" colspan="2" rowspan="3">
                            </td>
                            <td style=" margin: 0px;"  width="142" rowspan="3">
                            </td>
                            <td style=" margin: 0px;"  width="227" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    I. VALOR DO SERVIÇO
                                    PRESTADO......................
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="135" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    R$                                    <strong><u><?= Yii::$app->formatter->asDecimal($model->valor, 2) ?></u></strong>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="227" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    II.
                                    .............................................................................
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="135" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    R$ ____________________
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="227" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="right">
                                    SOMA ...............
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="135" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                R$ <strong><u><?= Yii::$app->formatter->asDecimal($model->valor, 2) ?></u></strong>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="144" colspan="6">
                            </td>
                         
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="362" colspan="4">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    CARRETEIRO (VALOR BASE P/ CÁLCULO DO INSS)
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="227" valign="bottom">
                            </td>
                            <td style=" margin: 0px;"  width="135" valign="bottom">
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="362" colspan="4" rowspan="3">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    Aplicar 20% sobre o valor da
                                </p>
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    mão de obra (11,71% do frete)
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="227" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    <strong>DESCONTOS:</strong>
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="135" valign="bottom">
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="362" colspan="2" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    III. IMP. RENDA FONTE: R$ _________________
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="362" colspan="2" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    IV. ................................. R$
                                    _________________
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="144" colspan="4">
                            </td>
                         
                            <td style=" margin: 0px;"  width="227" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    V. ................................... R$
                                    _________________
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="135" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    R$ ____________________
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="362" colspan="4">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    NÚMERO DE INSCRIÇÃO
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="227" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="right">
                                    VALOR LÍQUIDO...........
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="135" valign="bottom">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    R$ ____________________
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="362" colspan="6">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    NO INSS: <strong><?= $model->condutor->nit ?></strong>
                                </p>
                            </td>
                           
                       
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="362" colspan="6">
                                <p style=" padding-bottom: 0px;margin:0px!important"  style=" padding-bottom: 0px;margin:0px!important">
                                    NO CPF: <strong><?= $model->condutor->cpf ?></strong>
                                </p>
                            </td>
                           
                           
                        </tr>
                    
                        <tr>
                            <td style=" margin: 0px;"  width="362" colspan="4">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    DOCUMENTO DE IDENTIDADE
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="362" colspan="2">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    ASSINATURA
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="182" colspan="2">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    NÚMERO
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="180" colspan="2">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    ÓRGÃO EMISSOR
                                </p>
                            </td>
                            <td style=" margin: 0px;" 
                                width="362"
                                colspan="2"
                                rowspan="2"
                                valign="bottom"
                            >
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="182" colspan="2">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    <strong><?= $model->condutor->rg ?></strong>
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="180" colspan="2">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    <strong><?= strtoupper($model->condutor->orgaoEmissor) ?></strong>
                                </p>
                            </td>
                        </tr>
                      
                        <tr>
                            <td style=" margin: 0px;"  width="220" colspan="3">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    LOCALIDADE
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="142">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    DATA
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="362" colspan="2">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    NOME COMPLETO
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style=" margin: 0px;"  width="220" colspan="3">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    <strong>SÃO JOSÉ DOS CAMPOS</strong>
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="142">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    <strong><?= date('d/m/Y') ?></strong>
                                </p>
                            </td>
                            <td style=" margin: 0px;"  width="362" colspan="2">
                                <p style=" padding-bottom: 0px;margin:0px!important"  align="center">
                                    <strong><?= $model->condutor->nome ?></strong>
                                </p>
                            </td>
                        </tr>
                       
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
